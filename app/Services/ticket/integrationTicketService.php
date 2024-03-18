<?php

namespace App\Services\ticket;

use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Clients\testKassClient;
use App\Http\Controllers\getData\getDeviceFirst;
use App\Http\Controllers\getData\getDevices;
use App\Http\Controllers\getData\getSetting;
use App\Services\AdditionalServices\DocumentService;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;


class integrationTicketService
{
    private mixed $data;
    private MsClient $msClient;
    private DocumentService $documentService;

    public function __construct()
    {
        $this->documentService = app(DocumentService::class);
    }

    public function createTicket($data): JsonResponse
    {
        $this->data = $data;
        $this->msClient = new MsClient($data->connection->ms_token);

        $accountId = $data->accountId;
        $id_entity = $data->object_Id;
        $entity_type = $data->entity_type;

        $money_card = $data->data->money_card;
        $money_cash = $data->data->money_cash;
        $money_mobile = $data->data->money_mobile;

        $total = $data->data->total;
        $payType = $data->data->pay_type;

        $positions = $data->data->position;

        $tookSum = $total;


        if ($data->data->money_card <= 0 && $data->data->money_cash <= 0 && $data->data->money_mobile <= 0) return response()->json([
            'status' => false,
            'message' => 'Отсутствуют информации о суммах, перезагрузите бразуер (F5)',
        ]);

        //take positions from entity
        $urlEntity = $this->getUrlEntity($entity_type, $id_entity);

        $jsonEntity = $this->msClient->get($urlEntity);

        $totalSum = $this->getTotalSum($positions, $urlEntity, $jsonEntity);

        if ($tookSum < $totalSum)
            return response()->json([
                'status' => false,
                'message' => "Недостаточно денег для завершения транзакции",
            ]);


        $change = $tookSum - $totalSum;

        $items = $this->getItemsByHrefPositions($jsonEntity->positions->meta->href, $positions, $jsonEntity);

        if (count($items) > 0) {

            $payments = [];
            $tempSum = $totalSum;

            if (intval($money_cash) > 0 && $tempSum > 0) {
                if ($tempSum > $money_cash) {
                    $pay = $money_cash;
                    $tempSum -= $pay;
                } else {
                    $pay = $tempSum;
                    $tempSum = 0;
                }

                $paymentsSumBills = intval($pay);
                $paymentsSumCoins = intval(round(floatval($pay) - intval($pay), 2) * 100);
                if ($paymentsSumCoins >= 100) {
                    $paymentsSumBills = $paymentsSumBills + (intval($paymentsSumCoins / 100));
                    $paymentsSumCoins = $paymentsSumCoins - (intval($paymentsSumCoins / 100) * 100);
                }

                $payments[] = [
                    "type" => $this->getMoneyType("Наличные"),
                    "sum" => [
                        "bills" => "" . $paymentsSumBills,
                        "coins" => "" . $paymentsSumCoins,
                    ],
                ];

            }
            if (intval($money_card) > 0 && $tempSum > 0) {
                if ($tempSum > $money_card) {
                    $pay = $money_card;
                    $tempSum -= $pay;
                } else {
                    $pay = $tempSum;
                    $tempSum = 0;
                }

                $paymentsSumBills = intval($pay);
                $paymentsSumCoins = intval(round(floatval($pay) - intval($pay), 2) * 100);
                if ($paymentsSumCoins >= 100) {
                    $paymentsSumBills = $paymentsSumBills + (intval($paymentsSumCoins / 100));
                    $paymentsSumCoins = $paymentsSumCoins - (intval($paymentsSumCoins / 100) * 100);
                }

                $payments[] = [
                    "type" => $this->getMoneyType("Банковская карта"),
                    "sum" => [
                        "bills" => "" . $paymentsSumBills,
                        "coins" => "" . $paymentsSumCoins,
                    ],
                ];

            }
            if (intval($money_mobile) > 0 && $tempSum > 0) {
                $pay = min($tempSum, $money_mobile);

                $paymentsSumBills = intval($pay);
                $paymentsSumCoins = intval(round(floatval($pay) - intval($pay), 2) * 100);
                if ($paymentsSumCoins >= 100) {
                    $paymentsSumBills = $paymentsSumBills + (intval($paymentsSumCoins / 100));
                    $paymentsSumCoins = $paymentsSumCoins - (intval($paymentsSumCoins / 100) * 100);
                }

                $payments[] = [
                    "type" => $this->getMoneyType("Мобильные"),
                    "sum" => [
                        "bills" => "" . $paymentsSumBills,
                        "coins" => "" . $paymentsSumCoins,
                    ],
                ];

            }

            $taken = 0;
            if ($payType != 'return') $taken = $money_cash;

            $amountsSumBills = intval($totalSum);
            $amountsSumCoins = intval(round(floatval($totalSum) - intval($totalSum), 2) * 100);
            if ($amountsSumCoins >= 100) {
                $amountsSumBills = $amountsSumBills + (intval($amountsSumCoins / 100));
                $amountsSumCoins = $amountsSumCoins - (intval($amountsSumCoins / 100) * 100);
            }

            $amounts = [
                "total" => [
                    "bills" => "" . $amountsSumBills,
                    "coins" => "" . $amountsSumCoins,
                ],
                "taken" => [
                    "bills" => "" . intval($taken),
                    "coins" => "" . intval(round(floatval($taken) - intval($taken), 2) * 100),
                ],
                "change" => [
                    "bills" => "" . intval($change),
                    "coins" => "" . intval(round(floatval($change) - intval($change), 2) * 100),
                ],
            ];
            if ($accountId == '1dd5bd55-d141-11ec-0a80-055600047495') $clientK = new testKassClient($this->data->setting_main->serial_number, $this->data->setting_main->password);
            else $clientK = new KassClient($this->data->setting_main->serial_number, $this->data->setting_main->password, '');

            $id = $clientK->getNewJwtToken()->id;
            $body = [
                "dateTime" => $this->getNowDateTime(),
                "items" => $items,
                "payments" => $payments,
                "amounts" => $amounts,
            ];

            $isPayIn = null;
            if ($payType == "sell") {
                $body["operation"] = "OPERATION_SELL";
                $isPayIn = true;
            } elseif ($payType == "return") {
                $body["operation"] = "OPERATION_SELL_RETURN";
                $isPayIn = false;
            }

            $ExtensionOptions = $this->getUUH($id_entity, $entity_type);
            if ($ExtensionOptions) $body = $body + ['extension_options' => $ExtensionOptions];


            //dd($body);

            try {
                $response = $clientK->post("crs/" . $id . "/tickets", $body);
                $jsonEntity = $this->writeToAttrib($response, $urlEntity, $entity_type, $positions);

                if ($isPayIn) {
                    if ($this->data->setting_document->paymentDocument != null) $this->createPaymentDocument($entity_type, $jsonEntity, $body);
                } else {
                    $isReturn = ($entity_type == "salesreturn");
                    $this->documentService->initPayReturnDocument($body['payments'], $this->data->setting_document->paymentDocument, $isReturn, $jsonEntity, $this->data->connection->ms_token);
                }

                return response()->json([
                    'status' => true,
                    'response' => $response,
                ]);
            } catch (ClientException $exception) {
                return response()->json([
                    'status' => false,
                    'message' => json_decode($exception->getResponse()->getBody()),
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'message' => "Отсутствует количество товаров",
        ]);
    }

    private function getItemsByHrefPositions($href, $positionsEntity, $jsonEntity): array
    {
        //dd($href,$positionsEntity,$jsonEntity,$apiKeyMs);
        $positions = [];
        $jsonPositions = $this->msClient->get($href);

        foreach ($jsonPositions->rows as $row) {
            foreach ($positionsEntity as $item) {
                if ($row->id == $item->id) {

                    $discount = $row->discount;
                    $positionPrice = $row->price / 100;
                    $sumPrice = $positionPrice - ($positionPrice * ($discount / 100));
                    $product = $this->msClient->get($row->assortment->meta->href);

                    if (property_exists($product, 'characteristics')) {
                        $check_uom = $this->msClient->get($product->product->meta->href);
                        $this->getProductByUOM($check_uom->uom->meta->href);
                    } else $this->getProductByUOM($product->uom->meta->href);


                    if (!property_exists($row, 'trackingCodes')) {
                        $SumBills = intval($sumPrice) * $item->quantity;
                        $SumCoins = intval(round(floatval($sumPrice) - intval($sumPrice), 2) * 100) * $item->quantity;
                        if ($SumCoins >= 100) {
                            $SumBills = $SumBills + (intval($SumCoins / 100));
                            $SumCoins = $SumCoins - (intval($SumCoins / 100) * 100);
                        }

                        $position["type"] = "ITEM_TYPE_COMMODITY";
                        $position["commodity"] = [
                            "name" => $product->name,
                            "sectionCode" => "0",
                            "quantity" => (integer)($item->quantity * 1000),
                            "price" => [
                                "bills" => "" . intval($positionPrice),
                                "coins" => "" . intval(round(floatval($positionPrice) - intval($positionPrice), 2) * 100),
                            ],
                            "sum" => [
                                "bills" => "" . $SumBills,
                                "coins" => "" . $SumCoins,
                            ],
                        ];

                        if (property_exists($product, 'characteristics')) {
                            $check_uom = $this->msClient->get($product->product->meta->href);
                            $position["commodity"]['measureUnitCode'] = $this->getUomCode($check_uom->uom->meta->href);
                        } else  $position["commodity"]['measureUnitCode'] = $this->getUomCode($product->uom->meta->href);

                        if (property_exists($row, 'vat') && property_exists($jsonEntity, 'vatIncluded')) {

                            if ($jsonEntity->vatIncluded) {
                                $sumVat = $sumPrice * ($row->vat / (100 + $row->vat)); //Цена включает НДС
                            } else {
                                $sumVat = $sumPrice * ($row->vat / 100); //Цена выключает НДС
                            }
                            if ($row->vat != 0) {
                                $TaxesSumBills = intval($sumVat);
                                $TaxesSumCoins = intval(round(floatval($sumVat) - intval($sumVat), 2) * 100);
                                if ($TaxesSumCoins >= 100) {
                                    $TaxesSumBills = $TaxesSumBills + (intval($TaxesSumCoins / 100));
                                    $TaxesSumCoins = $TaxesSumCoins - (intval($TaxesSumCoins / 100) * 100);
                                }
                                $position["commodity"]["taxes"] = [
                                    0 => [
                                        "sum" => [
                                            "bills" => "" . $TaxesSumBills,
                                            "coins" => "" . $TaxesSumCoins,
                                        ],
                                        "percent" => $row->vat * 1000,
                                        "taxType" => 100,
                                        "isInTotalSum" => $jsonEntity->vatIncluded,
                                        "taxationType" => 100,
                                    ],
                                ];
                            }
                        }

                        $positions [] = $position;
                    } else {
                        for ($i = 1; $i <= $row->quantity; $i++) {
                            $position["type"] = "ITEM_TYPE_COMMODITY";
                            $position["commodity"] = [
                                "name" => $product->name,
                                "sectionCode" => "0",
                                "quantity" => 1000,
                                "price" => [
                                    "bills" => "" . intval($positionPrice),
                                    "coins" => "" . intval(round(floatval($positionPrice) - intval($positionPrice), 2) * 100),
                                ],
                                "sum" => [
                                    "bills" => "" . intval($sumPrice),
                                    "coins" => "" . intval(round(floatval($sumPrice) - intval($sumPrice), 2) * 100),
                                ],
                            ];

                            if (property_exists($product, 'characteristics')) {
                                $check_uom = $this->msClient->get($product->product->meta->href);
                                $position["commodity"]['measureUnitCode'] = $this->getUomCode($check_uom->uom->meta->href);
                            } else  $position["commodity"]['measureUnitCode'] = $this->getUomCode($product->uom->meta->href);

                            if (property_exists($row, 'trackingCodes')) {
                                $position["commodity"]["excise_stamp"] = $row->trackingCodes[$i - 1]->cis;
                            }

                            if (property_exists($row, 'vat') && property_exists($jsonEntity, 'vatIncluded')) {
                                if ($jsonEntity->vatIncluded) {
                                    $sumVat = $sumPrice * ($row->vat / (100 + $row->vat)); //Цена включает НДС
                                } else {
                                    $sumVat = $sumPrice * ($row->vat / 100); //Цена выключает НДС
                                }
                                if ($row->vat != 0) {
                                    $TaxesSumBills = intval($sumVat);
                                    $TaxesSumCoins = intval(round(floatval($sumVat) - intval($sumVat), 2) * 100);
                                    if ($TaxesSumCoins >= 100) {
                                        $TaxesSumBills = $TaxesSumBills + (intval($TaxesSumCoins / 100));
                                        $TaxesSumCoins = $TaxesSumCoins - (intval($TaxesSumCoins / 100) * 100);
                                    }
                                    $position["commodity"]["taxes"] = [
                                        0 => [
                                            "sum" => [
                                                "bills" => "" . $TaxesSumBills,
                                                "coins" => "" . $TaxesSumCoins,
                                            ],
                                            "percent" => $row->vat * 1000,
                                            "taxType" => 100,
                                            "isInTotalSum" => $jsonEntity->vatIncluded,
                                            "taxationType" => 100,
                                        ],
                                    ];
                                }
                            }

                            $positions [] = $position;
                        }
                    }


                } else continue;
            }

        }

        return $positions;
    }

    private function getUrlEntity($enType, $enId): ?string
    {
        $url = null;
        switch ($enType) {
            case "customerorder":
                $url = "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/" . $enId;
                break;
            case "demand":
                $url = "https://api.moysklad.ru/api/remap/1.2/entity/demand/" . $enId;
                break;
            case "salesreturn":
                $url = "https://api.moysklad.ru/api/remap/1.2/entity/salesreturn/" . $enId;
                break;
        }
        return $url;
    }

    private function getProductByUOM($href)
    {
        return $this->msClient->get($href);
    }

    private function getUomCode($href)
    {
        return $this->msClient->get($href)->code;
    }

    private function getNowDateTime(): array
    {
        $now = Carbon::now();
        return [
            "date" => [
                "year" => $now->year,
                "month" => $now->month,
                "day" => $now->day,
            ],
            "time" => [
                "hour" => $now->hour,
                "minute" => $now->minute,
                "second" => $now->second,
            ],
        ];
    }

    public function writeToAttrib($responseClient, $urlEntity, $entityType, $positions)
    {
        $body = null;
        $metaPositions = $this->getMetaPositions($urlEntity, $positions);

        $meta = $this->getMeta($entityType);

        if ($meta['fiscal_number'] != null) $body["attributes"][] = ["meta" => $meta['fiscal_number'], "value" => "" . $responseClient->id];
        if ($meta['link_to_check'] != null) $body["attributes"][] = ["meta" => $meta['link_to_check'], "value" => $responseClient->qrCode];
        if ($meta['fiscalization'] != null) $body["attributes"][] = ["meta" => $meta['fiscalization'], "value" => true];
        if ($meta['kkm_ID'] != null) $body["attributes"][] = ["meta" => $meta['kkm_ID'], "value" => "" . $responseClient->id];

        $body = [
            "attributes" => $body["attributes"],
            'positions' => $metaPositions,
        ];

        return $this->msClient->put($urlEntity, $body);
    }

    private function getMeta($entityType): array
    {

        $url = match ($entityType) {
            "demand" => "https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes",
            "salesreturn" => "https://api.moysklad.ru/api/remap/1.2/entity/salesreturn/metadata/attributes",
            default => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/metadata/attributes",
        };

        $json = $this->msClient->get($url);
        $meta = null;
        foreach ($json->rows as $row) {
            if ($row->name == "фискальный номер") {
                $meta['fiscal_number'] = $row->meta;
            } elseif ($row->name == "Ссылка на чек") {
                $meta['link_to_check'] = $row->meta;
            } elseif ($row->name == "Фискализация") {
                $meta['fiscalization'] = $row->meta;
            } elseif ($row->name == "kkm_ID") {
                $meta['kkm_ID'] = $row->meta;
            }
        }

        return [
            'fiscal_number' => $meta['fiscal_number'] ?? '',
            'link_to_check' => $meta['link_to_check'] ?? '',
            'fiscalization' => $meta['fiscalization'] ?? '',
            'kkm_ID' => $meta['kkm_ID'] ?? '',
        ];
    }

    private function getMoneyType($moneyType): string
    {
        return match ($moneyType) {
            "Наличные" => "PAYMENT_CASH",
            "Банковская карта" => "PAYMENT_CARD",
            "Мобильные" => "PAYMENT_MOBILE",
            default => "",
        };
    }

    private function getTotalSum($positions, $urlEntity, $jsonEntity): float|int
    {
        $total = 0;
        $urlEntityWithPositions = $urlEntity . '/positions';
        $jsonPositions = $this->msClient->get($urlEntityWithPositions);

        foreach ($jsonPositions->rows as $position) {

            foreach ($positions as $item) {
                if ($position->id == $item->id) {
                    $href = $position->assortment->meta->href;
                    $product = $this->msClient->get($href);


                    if (property_exists($product, 'characteristics')) {
                        $check_uom = $this->msClient->get($product->product->meta->href);
                        $checkUOM = $this->getProductByUOM($check_uom->uom->meta->href);
                    } else  $checkUOM = $this->getProductByUOM($product->uom->meta->href);


                    if ($checkUOM->name == "шт") {
                        $discount = $position->discount;
                        $positionPrice = $item->quantity * $position->price / 100;
                        $sumPrice = $positionPrice - ($positionPrice * ($discount / 100));
                    } else {
                        $discount = $position->discount;
                        $positionPrice = $item->quantity * $position->price / 100;
                        $sumPrice = $positionPrice - ($positionPrice * ($discount / 100));
                    }


                    if (property_exists($jsonEntity, 'vatIncluded')) {
                        if ($jsonEntity->vatIncluded) {
                            $sumVat = $sumPrice * ($position->vat / (100 + $position->vat)); //Цена включает НДС
                        } else {
                            $sumVat = $sumPrice * ($position->vat / 100); //Цена выключает НДС
                            $sumPrice += $sumVat;
                        }
                    }

                    $total += $sumPrice;
                }
            }
        }
        return $total;
    }


    private function getMetaPositions($urlEntity, $positions): array
    {
        $data = null;
        $body = $this->msClient->get($urlEntity . '/positions')->rows;
        $index = 0;
        foreach ($body as $item) {
            foreach ($positions as $pos) {
                if ($item->id == $pos->id) {

                    $data[$index] = [
                        'id' => $item->id,
                        'quantity' => (int)$pos->quantity,
                        'price' => $item->price,
                        'discount' => $item->discount,
                        'assortment' => [
                            'meta' => [
                                'href' => $item->assortment->meta->href,
                                'type' => $item->assortment->meta->type,
                                'mediaType' => $item->assortment->meta->mediaType,
                            ]
                        ],
                        'reserve' => 0,
                    ];
                    if (property_exists($item, 'vat')) $data[$index]['vat'] = $item->vat;
                    else $data[$index]['vat'] = 0;
                    $index++;

                } else continue;
            }
        }
        return $data;
    }

    private function getUUH(mixed $id_entity, mixed $entity_type)
    {
        $body = $this->msClient->get('https://api.moysklad.ru/api/remap/1.2/entity/' . $entity_type . '/' . $id_entity);
        $agent = $this->msClient->get($body->agent->meta->href);
        $result = false;

        if (property_exists($agent, 'email')) {
            $result['customer_email'] = $agent->email;
        }
        if (property_exists($agent, 'phone')) {
            $result['customer_phone'] = $agent->phone;
        }
        if (property_exists($agent, 'inn')) {
            $result['customer_iin_or_bin'] = $agent->inn;
        }

        return $result;
    }

    private function createPaymentDocument(string $entity_type, mixed $OldBody, mixed $vars): void
    {
        switch ($this->data->setting_document->paymentDocument) {
            case "1":
            {
                $url = 'https://api.moysklad.ru/api/remap/1.2/entity/';
                if ($entity_type != 'salesreturn') {
                    $url = $url . 'cashin';
                } else {
                    //$url = $url . 'cashout';
                    break;
                }
                $body = [
                    'organization' => ['meta' => [
                        'href' => $OldBody->organization->meta->href,
                        'type' => $OldBody->organization->meta->type,
                        'mediaType' => $OldBody->organization->meta->mediaType,
                    ]],
                    'agent' => ['meta' => [
                        'href' => $OldBody->agent->meta->href,
                        'type' => $OldBody->agent->meta->type,
                        'mediaType' => $OldBody->agent->meta->mediaType,
                    ]],
                    'sum' => $OldBody->sum,
                    'operations' => [
                        0 => [
                            'meta' => [
                                'href' => $OldBody->meta->href,
                                'metadataHref' => $OldBody->meta->metadataHref,
                                'type' => $OldBody->meta->type,
                                'mediaType' => $OldBody->meta->mediaType,
                                'uuidHref' => $OldBody->meta->uuidHref,
                            ],
                            'linkedSum' => $OldBody->sum,
                        ],]
                ];
                $this->msClient->post($url, $body);
                break;
            }
            case "2":
            {
                $url = 'https://api.moysklad.ru/api/remap/1.2/entity/';
                if ($entity_type != 'salesreturn') {
                    $url = $url . 'paymentin';
                } else {
                    //$url = $url . 'paymentout';
                    break;
                }

                $rate_body = $this->msClient->get("https://api.moysklad.ru/api/remap/1.2/entity/currency/")->rows;
                $rate = null;
                foreach ($rate_body as $item) {
                    if ($item->name == "тенге" or $item->fullName == "Казахстанский тенге") {
                        $rate =
                            ['meta' => [
                                'href' => $item->meta->href,
                                'metadataHref' => $item->meta->metadataHref,
                                'type' => $item->meta->type,
                                'mediaType' => $item->meta->mediaType,
                            ],
                            ];
                    }
                }

                $body = [
                    'organization' => ['meta' => [
                        'href' => $OldBody->organization->meta->href,
                        'type' => $OldBody->organization->meta->type,
                        'mediaType' => $OldBody->organization->meta->mediaType,
                    ]],
                    'agent' => ['meta' => [
                        'href' => $OldBody->agent->meta->href,
                        'type' => $OldBody->agent->meta->type,
                        'mediaType' => $OldBody->agent->meta->mediaType,
                    ]],
                    'sum' => $OldBody->sum,
                    'operations' => [
                        0 => [
                            'meta' => [
                                'href' => $OldBody->meta->href,
                                'metadataHref' => $OldBody->meta->metadataHref,
                                'type' => $OldBody->meta->type,
                                'mediaType' => $OldBody->meta->mediaType,
                                'uuidHref' => $OldBody->meta->uuidHref,
                            ],
                            'linkedSum' => $OldBody->sum,
                        ],],
                    'rate' => $rate
                ];
                if ($body['rate'] == null) unlink($body['rate']);
                $this->msClient->post($url, $body);
                break;
            }
            case "3":
            {
                $url = 'https://api.moysklad.ru/api/remap/1.2/entity/';
                if ($entity_type != 'salesreturn') {
                    foreach ($vars['payments'] as $item) {
                        if ($item['type'] == "PAYMENT_CASH") {
                            $url_to_body = $url . 'cashin';
                        } else {
                            $url_to_body = $url . 'paymentin';
                        }

                        $rate_body = $this->msClient->get("https://api.moysklad.ru/api/remap/1.2/entity/currency/")->rows;
                        $rate = null;
                        foreach ($rate_body as $item_rate) {
                            if ($item_rate->name == "тенге" or $item_rate->fullName == "Казахстанский тенге") {
                                $rate =
                                    ['meta' => [
                                        'href' => $item_rate->meta->href,
                                        'metadataHref' => $item_rate->meta->metadataHref,
                                        'type' => $item_rate->meta->type,
                                        'mediaType' => $item_rate->meta->mediaType,
                                    ],
                                    ];
                            }
                        }

                        $body = [
                            'organization' => ['meta' => [
                                'href' => $OldBody->organization->meta->href,
                                'type' => $OldBody->organization->meta->type,
                                'mediaType' => $OldBody->organization->meta->mediaType,
                            ]],
                            'agent' => ['meta' => [
                                'href' => $OldBody->agent->meta->href,
                                'type' => $OldBody->agent->meta->type,
                                'mediaType' => $OldBody->agent->meta->mediaType,
                            ]],
                            'sum' => (float)($item['sum']['bills'] + ($item['sum']['coins'] / 100)) * 100,
                            'operations' => [
                                0 => [
                                    'meta' => [
                                        'href' => $OldBody->meta->href,
                                        'metadataHref' => $OldBody->meta->metadataHref,
                                        'type' => $OldBody->meta->type,
                                        'mediaType' => $OldBody->meta->mediaType,
                                        'uuidHref' => $OldBody->meta->uuidHref,
                                    ],
                                    'linkedSum' => (float)($item['sum']['bills'] + ($item['sum']['coins'] / 100)) * 100,
                                ],],
                            'rate' => $rate
                        ];
                        if ($body['rate'] == null) unlink($body['rate']);
                        $this->msClient->post($url_to_body, $body);
                    }
                }
                break;
            }
            case "4":
            {
                $url = 'https://api.moysklad.ru/api/remap/1.2/entity/';
                $url_to_body = null;
                if ($entity_type != 'salesreturn') {
                    foreach ($vars['payments'] as $item) {
                        if ($item['type'] == "PAYMENT_CASH") {
                            switch ($this->data->setting_document->OperationCash) {
                                case 1:
                                {
                                    $url_to_body = $url . 'cashin';
                                    break;
                                }
                                case 2:
                                {
                                    $url_to_body = $url . 'paymentin';
                                    break;
                                }
                                default:
                                    break;
                            }
                        } elseif ($item['type'] == "PAYMENT_CARD") {
                            switch ($this->data->setting_document->OperationCard) {
                                case 1:
                                {
                                    $url_to_body = $url . 'cashin';
                                    break;
                                }
                                case 2:
                                {
                                    $url_to_body = $url . 'paymentin';
                                    break;
                                }
                                default:
                                    break;
                            }
                        } else {
                            switch ($this->data->setting_document->OperationMobile) {
                                case 1:
                                {
                                    $url_to_body = $url . 'cashin';
                                    break;
                                }
                                case 2:
                                {
                                    $url_to_body = $url . 'paymentin';
                                    break;
                                }
                                default:
                                    break;
                            }
                        }

                        if ($url_to_body == null) continue;

                        $rate_body = $this->msClient->get("https://api.moysklad.ru/api/remap/1.2/entity/currency/")->rows;
                        $rate = null;
                        foreach ($rate_body as $item_rate) {
                            if ($item_rate->name == "тенге" or $item_rate->fullName == "Казахстанский тенге") {
                                $rate = ['meta' =>
                                    [
                                        'href' => $item_rate->meta->href,
                                        'metadataHref' => $item_rate->meta->metadataHref,
                                        'type' => $item_rate->meta->type,
                                        'mediaType' => $item_rate->meta->mediaType,
                                    ],
                                ];
                            }
                        }

                        $body = [
                            'organization' => ['meta' => [
                                'href' => $OldBody->organization->meta->href,
                                'type' => $OldBody->organization->meta->type,
                                'mediaType' => $OldBody->organization->meta->mediaType,
                            ]],
                            'agent' => ['meta' => [
                                'href' => $OldBody->agent->meta->href,
                                'type' => $OldBody->agent->meta->type,
                                'mediaType' => $OldBody->agent->meta->mediaType,
                            ]],
                            'sum' => (float)($item['sum']['bills'] + ($item['sum']['coins'] / 100)) * 100,
                            'operations' => [
                                0 => [
                                    'meta' => [
                                        'href' => $OldBody->meta->href,
                                        'metadataHref' => $OldBody->meta->metadataHref,
                                        'type' => $OldBody->meta->type,
                                        'mediaType' => $OldBody->meta->mediaType,
                                        'uuidHref' => $OldBody->meta->uuidHref,
                                    ],
                                    'linkedSum' => (float)($item['sum']['bills'] + ($item['sum']['coins'] / 100)) * 100,
                                ],],
                            'rate' => $rate
                        ];
                        if ($body['rate'] == null) unset($body['rate']);
                        $this->msClient->post($url_to_body, $body);
                    }
                }


                break;
            }

            default:
            {
                break;
            }
        }

    }


}
