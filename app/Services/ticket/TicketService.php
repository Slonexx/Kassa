<?php

namespace App\Services\ticket;

use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Http\Controllers\getData\getSetting;
use App\Services\AdditionalServices\DocumentService;
use App\Services\MetaServices\MetaHook\AttributeHook;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class TicketService
{

    private AttributeHook $attributeHook;
    private DocumentService $documentService;

    /**
     * @param AttributeHook $attributeHook
     * @param DocumentService $documentService
     */
    public function __construct(AttributeHook $attributeHook, DocumentService $documentService)
    {
        $this->attributeHook = $attributeHook;
        $this->documentService = $documentService;
    }

    // Create ticket
    public function createTicket($data){
        $accountId = $data['accountId'];
        $id_entity = $data['id_entity'];
        $entity_type = $data['entity_type'];
        //$positionDevice = $data['position'];
        $payType = $data['pay_type'];
        $moneyType = $data['money_type'];

        //$setting = new getSetting($accountId);
        //$setting->tokenMs;
        //take settings by accountId
        $apiKeyMs = "f59a9e8d8011257f92f13ac0ad12a2d25c1e668f";
        $paymentOption = 2;

        $apiKey = "f5ac6559-b5cd-4e0e-89e5-7fd32a6d60a5";
        $numKassa = "VTH5DEV4-AQM";
        $password = "Qi1_CS0y5weXk09Lg3erA4*72dMuqYFM";

        //take positions from entity
        $urlEntity = $this->getUrlEntity($entity_type,$id_entity);
        $client = new MsClient($apiKeyMs);
        $jsonEntity = $client->get($urlEntity);

        $sumOrder = $jsonEntity->sum / 100;

        if (property_exists($jsonEntity,'positions')){
            $items = $this->getItemsByHrefPositions($jsonEntity->positions->meta->href,$apiKeyMs);
            if (count($items) > 0 ){
                $payments = [
                    0 => [
                        "type" => $this->getMoneyType($moneyType),
                        "sum" => [
                            "bills" => intval($sumOrder),
                            "coins" => ($jsonEntity->sum % 100),
                        ],
                    ]
                ];
                $amounts = [
                    "total" => [
                        "bills" => intval($sumOrder),
                        "coins" => ($jsonEntity->sum % 100),
                    ],
                ];
                $clientK = new KassClient($numKassa,$password,$apiKey);
                $id = $clientK->getNewJwtToken()->id;
                $body = [
                    "dateTime" => $this->getNowDateTime(),
                    "items" => $items,
                    "payments" => $payments,
                    "amounts" => $amounts,
                ];
                $isPayIn = null;
                if ($payType == "sell"){
                    $body["operation"] = "OPERATION_SELL";
                    $isPayIn = true;
                }
                elseif($payType == "return") {
                    $body["operation"] = "OPERATION_SELL_RETURN";
                    $isPayIn = false;
                }
                try {
                    $response = $clientK->post("crs/".$id."/tickets",$body);
                    $jsonEntity = $this->writeToAttrib($response->id,$urlEntity,$entity_type,$apiKeyMs);
                    if ($isPayIn){
                        $this->documentService->initPayDocument($paymentOption,$jsonEntity,$apiKeyMs);
                    } else {
                        $isReturn = ($entity_type == "salesreturn");
                        $this->documentService->initPayReturnDocument(
                            $paymentOption,$isReturn,$jsonEntity,$apiKeyMs
                        );
                    }
                    //dd($response);
                    return [
                        "message" => "Ticket created!"
                    ];
                } catch (ClientException $exception){
                    return [
                        "message" => "Ticket not created!",
                        "error" => json_decode($exception->getResponse()->getBody()),
                    ];
                    //dd($exception->getMessage());
                }
            }
        }
        else {
            return [
                "message" => "Entity haven't got positions!",
            ];
        }
        return [];
    }

    private function getItemsByHrefPositions($href,$apiKeyMs){
        $positions = [];
        $client = new MsClient($apiKeyMs);
        $jsonPositions = $client->get($href);
        //$count = 1;
        //dd($jsonPositions);
        foreach ($jsonPositions->rows as $row){
            $discount = $row->discount;

            $positionPrice = $row->price / 100;
            $positionPriceCoins = $row->price;

            $sumPrice = $positionPrice - ( $positionPrice * ($discount/100) ) ;
            $sumPriceCoins = $row->price - ( $row->price * ($discount/100) ) ;

            $product = $this->getProductByAssortMeta($row->assortment->meta->href,$apiKeyMs);

            for ($i = 1; $i <= $row->quantity; $i++){
                $position["type"] = "ITEM_TYPE_COMMODITY";
                $position["commodity"] = [
                    "name" => $product->name,
                    "sectionCode" => "0",
                    "quantity" => 1000,
                    "price" => [
                        "bills" => "".intval($positionPrice),
                        "coins" => ($positionPriceCoins % 100),
                    ],
                    "sum" => [
                        "bills" => "".intval($sumPrice),
                        "coins" => ($sumPriceCoins % 100),
                    ],
                    "measureUnitCode" => $this->getUomCode($product->uom->meta->href,$apiKeyMs),
                ];

                if (property_exists($row,'trackingCodes')){
                    $position["commodity"]["excise_stamp"] = $row->trackingCodes[$i-1]->cis;
                }

                $positions [] = $position;
            }

        }
        return $positions;
    }

    private function getUrlEntity($enType,$enId){
        $url = null;
        switch ($enType){
            case "customerorder":
                $url = "https://online.moysklad.ru/api/remap/1.2/entity/customerorder/".$enId;
            break;
            case "demand":
                $url = "https://online.moysklad.ru/api/remap/1.2/entity/demand/".$enId;
            break;
            case "salesreturn":
                $url = "https://online.moysklad.ru/api/remap/1.2/entity/salesreturn/".$enId;
            break;
        }
        return $url;
    }

    private function getProductByAssortMeta($href,$apiKeyMs){
        $client = new MsClient($apiKeyMs);
        return $client->get($href);
    }

    private function getUomCode($href,$apiKeyMs){
        $client = new MsClient($apiKeyMs);
        return $client->get($href)->code;
    }

    private function getNowDateTime()
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

    public function writeToAttrib($id_ticket, $urlEntity, $entityType, $apiKeyMs)
    {
        $client = new MsClient($apiKeyMs);

        if (is_null($id_ticket)){
            $flag = false;
        } else {
            $flag = true;
        }

        $metaIdTicket = $this->getMeta("id-билета (ReKassa)",$entityType,$apiKeyMs);
        $metaTicketFlag = $this->getMeta("Фискализация (ReKassa)",$entityType,$apiKeyMs);
        $body = [
            "attributes" => [
                0 => [
                    "meta" => $metaIdTicket,
                    "value" => "".$id_ticket,
                ],
                1 => [
                    "meta" => $metaTicketFlag,
                    "value" => $flag,
                ],
            ],
        ];
        return $client->put($urlEntity,$body);
    }

    private function getMeta($attribName,$entityType,$apiKeyMs){
        $meta = null;
        switch ($entityType){
            case "customerorder":
                $meta = $this->attributeHook->getOrderAttribute($attribName,$apiKeyMs);
            break;
            case "demand":
                $meta = $this->attributeHook->getDemandAttribute($attribName,$apiKeyMs);
            break;
            case "salesreturn":
                $meta = $this->attributeHook->getSalesReturnAttribute($attribName,$apiKeyMs);
            break;
        }
        return $meta;
    }

    private function getMoneyType($moneyType){
        $typeKass = "";
        switch ($moneyType){
            case "Наличные":
                $typeKass = "PAYMENT_CASH";
                break;
            case "Банковская карта":
                $typeKass = "PAYMENT_CARD";
                break;
            case "Мобильные":
                $typeKass = "PAYMENT_MOBILE";
                break;
        }
        return $typeKass;
    }

    //Cancel ticket
    /*public function cancelTicket($data){
        $accountId = $data['accountId'];
        $id_entity = $data['id_entity'];
        $entity_type = $data['entity_type'];
        //$positionDevice = $data['position'];

        //take settings by accountId
        $apiKeyMs = "f59a9e8d8011257f92f13ac0ad12a2d25c1e668f";
        $apiKey = "f5ac6559-b5cd-4e0e-89e5-7fd32a6d60a5";
        $numKassa = "VTH5DEV4-AQM";
        $password = "Qi1_CS0y5weXk09Lg3erA4*72dMuqYFM";

        //take id from entity
        $url = $this->getUrlEntity($entity_type,$id_entity);
        $client = new MsClient($apiKeyMs);
        $json = $client->get($url);
        //dd($json);

        if (property_exists($json,'attributes')){
            $idTicket = null;
            foreach ($json->attributes as $attribute){
                if ($attribute->name == "id-билета (ReKassa)" && $attribute->type == "string"){
                    $idTicket = $attribute->value;
                    break;
                }
            }
            if (!is_null($idTicket)){
                try {
                    $clientK = new KassClient($numKassa,$password,$apiKey);
                    $id = $clientK->getNewJwtToken()->id;
                    $response = $clientK->delete('crs/'.$id.'/tickets/'.$idTicket);
                    $this->writeToAttrib(null,$url,$entity_type,$apiKeyMs);
                    return [
                        "message" => "Ticket not canceled!",
                    ];
                } catch (ClientException $exception){
                    return [
                        "message" => "Ticket not canceled!",
                        "error" => json_decode($exception->getResponse()->getBody()),
                    ];
                    //dd($exception->getMessage());
                }
                //dd($response);
            }
        }
        return [];
    }*/

}
