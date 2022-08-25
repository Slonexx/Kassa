<?php

namespace App\Services\ticket;

use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Services\MetaServices\MetaHook\AttributeHook;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use GuzzleHttp\Exception\GuzzleException;

class TicketService
{

    private AttributeHook $attributeHook;

    /**
     * @param AttributeHook $attributeHook
     */
    public function __construct(AttributeHook $attributeHook)
    {
        $this->attributeHook = $attributeHook;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    // Create ticket

    public function createTicket($data){
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
        $urlEntity = $this->getUrlEntity($entity_type,$id_entity);
        $client = new MsClient($apiKeyMs);
        $json = $client->get($urlEntity);

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
                    $this->writeIdToAttrib($response->id,$urlEntity,$entity_type,$apiKeyMs);
                    dd($response);
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

    //Cancel ticket

    public function cancelTicket($data){
        $accountId = $data['accountId'];
        $id_entity = $data['id_entity'];
        $entity_type = $data['entity_type'];
        $positionDevice = $data['position'];

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
                $clientK = new KassClient($numKassa,$password,$apiKey);
                $id = $clientK->getNewJwtToken()->id;
                try {
                    $response = $clientK->delete('crs/'.$id.'/tickets/'.$idTicket);
                } catch (GuzzleException $exception){
                    dd($exception);
                }

                $this->writeIdToAttrib(null,$url,$entity_type,$apiKeyMs);
                dd($response);
            }
        }
    }

    public function writeIdToAttrib($id_ticket, $urlEntity,$entityType, $apiKeyMs)
    {
        $client = new MsClient($apiKeyMs);
        $meta = $this->getMeta("id-билета (ReKassa)",$entityType,$apiKeyMs);
        $body = [
            "attributes" => [
                0 => [
                    "meta" => $meta,
                    "value" => "".$id_ticket,
                ],
            ],
        ];
        $client->put($urlEntity,$body);
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
        }
        return $meta;
    }

}
