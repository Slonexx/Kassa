<?php

namespace App\Services\ticket;

use App\Clients\KassClient;
use App\Clients\MsClient;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use GuzzleHttp\Exception\GuzzleException;

class TicketService
{

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function init($data){
        $accountId = $data['accountId'];
        $id_entity = $data['id_entity'];
        $entity_type = $data['entity_type'];
        $positionDevice = $data['position'];

        //take settings by accountId
        $apiKeyMs = "f59a9e8d8011257f92f13ac0ad12a2d25c1e668f";
        $apiKey = "f5ac6559-b5cd-4e0e-89e5-7fd32a6d60a5";
        $numKassa = "VTH5DEV4-AQM";
        $password = "Qi1_CS0y5weXk09Lg3erA4*72dMuqYFM";

        //take positions from entity
        $url = $this->getUrlEntity($entity_type,$id_entity);
        $client = new MsClient($apiKeyMs);
        $json = $client->get($url);

        $sumOrder = $json->sum / 100;

        if (property_exists($json,'positions')){

            $items = $this->getItemsByHrefPositions($json->positions->meta->href,$apiKeyMs);

            if (count($items) > 0 ){

                $now = Carbon::now();

                $dataTime = [
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

                $payments = [
                    0 => [
                        "type" => "PAYMENT_CASH",
                        "sum" => [
                            "bills" => intval($sumOrder),
                            "coins" => ($json->sum % 100),
                        ],
                    ]
                ];

                $amounts = [
                    "total" => [
                        "bills" => intval($sumOrder),
                        "coins" => ($json->sum % 100),
                    ],
                ];

                $clientK = new KassClient($numKassa,$password,$apiKey);
                $id = $clientK->getNewJwtToken()->id;

                $body = [
                    "operation" => "OPERATION_SELL",
                    "dateTime" => $dataTime,
                    "items" => $items,
                    "payments" => $payments,
                    "amounts" => $amounts,
                ];

                try {
                    $response = $clientK->post("crs/".$id."/tickets",$body);
                } catch (GuzzleException $exception){
                    dd($exception->getMessage());
                }

            }
        } else{
           //dd(false);
        }



    }

    private function getItemsByHrefPositions($href,$apiKeyMs){
        $positions = [];
        $client = new MsClient($apiKeyMs);
        $jsonPositions = $client->get($href);
        $count = 1;
        //dd($jsonPositions);
        foreach ($jsonPositions->rows as $row){
            $discount = $row->discount;

            $positionPrice = $row->price / 100;
            $positionPriceCoins = $row->price;

            $sumPrice = $positionPrice - ( $positionPrice * ($discount/100) ) ;
            $sumPriceCoins = $row->price - ( $row->price * ($discount/100) ) ;

            $position["type"] = "ITEM_TYPE_COMMODITY";
            $position["commodity"] = [
                "name" => $this->getNameByAssortMeta($row->assortment->meta->href,$apiKeyMs),
                "sectionCode" => "".$count,
                "quantity" => $row->quantity * 1000,
                "price" => [
                    "bills" => "".intval($positionPrice),
                    "coins" => ($positionPriceCoins % 100),
                ],
                "sum" => [
                    "bills" => "".intval($sumPrice),
                    "coins" => ($sumPriceCoins % 100),
                ],
                "auxiliary" => [
                    0 => [
                        "key" => "UNIT_TYPE",
                        "value" => "PIECE",
                    ],
                ],
            ];
            $positions [] = $position;
            $count++;
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
        }
        return $url;
    }

    private function getNameByAssortMeta($href,$apiKeyMs){
        $client = new MsClient($apiKeyMs);
        return $client->get($href)->name;
    }

}
