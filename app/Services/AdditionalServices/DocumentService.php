<?php

namespace App\Services\AdditionalServices;

use App\Clients\MsClient;
use App\Services\MetaServices\MetaHook\AttributeHook;
use App\Services\MetaServices\MetaHook\ExpenseItemHook;

class DocumentService
{

    private AttributeHook $attributeHook;
    private ExpenseItemHook $expenseItemHook;

    /**
     * @param AttributeHook $attributeHook
     * @param ExpenseItemHook $expenseItemHook
     */
    public function __construct(AttributeHook $attributeHook, ExpenseItemHook $expenseItemHook)
    {
        $this->attributeHook = $attributeHook;
        $this->expenseItemHook = $expenseItemHook;
    }

    public function initPayDocument($paymentOption, $formattedOrder, $apiKey, $reKassaBody): void
    {
        if($paymentOption > 0){
            $sum = $formattedOrder->sum;
            $description = '['.( (int) date('H') + 6 ).date(':i:s').' '. date('Y-m-d') .'] ' ;
            if ($paymentOption == 2) {
                $uri = "https://online.moysklad.ru/api/remap/1.2/entity/paymentin";
                $this->createPayInDocument($uri, $apiKey,  2, $formattedOrder, $sum, $description.'Автоматическое создание документа на основе настроек приложение');
            } elseif($paymentOption == 1) {
                $uri = "https://online.moysklad.ru/api/remap/1.2/entity/cashin";
                $this->createPayInDocument($uri, $apiKey, 1, $formattedOrder, $sum, $description.'Автоматическое создание документа на основе настроек приложение');
            } elseif ($paymentOption == 3) {

                foreach ($reKassaBody['payments'] as $item){

                   if ($item['type'] == 'PAYMENT_CASH'){
                       $uri = "https://online.moysklad.ru/api/remap/1.2/entity/cashin";
                       $sum = ($item['sum']['bills'] + ($item['sum']['coins'] / 100)) * 100;
                       $this->createPayInDocument($uri, $apiKey,  1, $formattedOrder, $sum, $description.'Автоматическое создание документа на основе настроек приложение. Оплата наличными, на сумму: '.$sum/100);
                   }

                    if ($item['type'] == 'PAYMENT_CARD'){
                        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/paymentin";
                        $sum = ($item['sum']['bills'] + ($item['sum']['coins'] / 100)) * 100;
                        $this->createPayInDocument($uri, $apiKey,  2, $formattedOrder, $sum, $description.'Автоматическое создание документа на основе настроек приложение. Оплата картой, на сумму: '.$sum/100);
                    }

                    if ($item['type'] == 'PAYMENT_MOBILE'){
                        $uri = "https://online.moysklad.ru/api/remap/1.2/entity/paymentin";
                        $sum = ($item['sum']['bills'] + ($item['sum']['coins'] / 100)) * 100;
                        $this->createPayInDocument($uri, $apiKey,  2, $formattedOrder, $sum, $description.'Автоматическое создание документа на основе настроек приложение. Оплата мобильная, на сумму: '.$sum/100);
                    }


                }

            }


        }
    }

    public function initPayReturnDocument($payments, $paymentOption,$isReturn,$formattedEntity,$apiKey): void
    {
        $sum = $formattedEntity->sum;
        if ($isReturn){
            $metaReturn = $formattedEntity->meta;
        } else {
            $metaReturn = null;
        }

        foreach ($payments as $item){
            if($paymentOption > 0){
                $this->createPayOutDocument($item, $apiKey,$metaReturn,$paymentOption,$formattedEntity,$sum);
            }
        }


    }

    private function createPayInDocument($URL, $apiKey, $type,  $formattedOrder, $sum, $description): void
    {
        $client = new MsClient($apiKey);
        $docBody = [
            "agent" => $formattedOrder->agent,
            "organization" => $formattedOrder->organization,
            "rate" => $formattedOrder->rate,
            "sum" => $sum,
            "operations" => [
                0=> [
                    "meta" => $formattedOrder->meta,
                ],
            ],
            "description" => $description,
        ];

        foreach ($formattedOrder->attributes as $attribute){
            if ($type == 1){
                $meta = $this->attributeHook->getCashInAttribute($attribute->name,$apiKey);
                if (!is_null($meta))
                $docBody["attributes"][] = [
                    "meta" => $meta,
                    "value" => $attribute->value,
                ];
            }elseif ($type ==2){
                $meta = $this->attributeHook->getPaymentInAttribute($attribute->name,$apiKey);
                if (!is_null($meta))
                $docBody["attributes"][] = [
                    "meta" => $meta,
                    "value" => $attribute->value,
                ];
            }
        }

        if(property_exists($formattedOrder,"salesChannel")){ $docBody["salesChannel"] = $formattedOrder->salesChannel; }
        if(property_exists($formattedOrder,"project")){ $docBody["project"] = $formattedOrder->project; }
        if(property_exists($formattedOrder,"organizationAccount")){ $docBody["organizationAccount"] = $formattedOrder->organizationAccount; }

        $client->post($URL, $docBody);
    }

    private function createPayOutDocument($payment, $apiKey, $metaReturn, $isPayment, $formattedEntity, $sum): void
    {
        $uri = null;
        if ($isPayment == 2) {
            $uri = "https://online.moysklad.ru/api/remap/1.2/entity/paymentout";
        } elseif($isPayment == 1) {
            $uri = "https://online.moysklad.ru/api/remap/1.2/entity/cashout";
        }elseif($isPayment == 3) {
            switch ($payment['type']){
                case 'PAYMENT_CASH':{
                    $uri = "https://online.moysklad.ru/api/remap/1.2/entity/cashout";
                    $sum = ($payment['sum']['bills'] + ($payment['sum']['coins'] / 100) * 1000);
                    break;
                }
                case 'PAYMENT_CARD' or 'PAYMENT_MOBILE':{
                    $uri = "https://online.moysklad.ru/api/remap/1.2/entity/paymentout";
                    $sum = ($payment['sum']['bills'] + ($payment['sum']['coins'] / 100) * 1000);
                    break;
                }
            }
        }

        $client = new MsClient($apiKey);
        $docBody = [
            "agent" => $formattedEntity->agent,
            "organization" => $formattedEntity->organization,
            "expenseItem" => [
                "meta" => $this->expenseItemHook->getExpenseItem('Возврат',$apiKey),
            ],
            "sum" => $sum,
        ];

        if ($metaReturn != null){
            $docBody["operations"] = [
                0=> [
                    "meta" => $metaReturn,
                ],
            ];
        }

        foreach ($formattedEntity->attributes as $attribute){
            if ($isPayment == 1){
                $meta = $this->attributeHook->getCashOutAttribute($attribute->name,$apiKey);
                if (!is_null($meta))
                $docBody["attributes"][] = [
                    "meta" => $meta,
                    "value" => $attribute->value,
                ];
            }elseif ($isPayment ==2){
                $meta = $this->attributeHook->getPaymentOutAttribute($attribute->name,$apiKey);
                if (!is_null($meta))
                $docBody["attributes"][] = [
                    "meta" => $meta,
                    "value" => $attribute->value,
                ];
            }
        }

        if(property_exists($formattedEntity,"salesChannel")){
            $docBody["salesChannel"] = $formattedEntity->salesChannel;
        }

        if(property_exists($formattedEntity,"project")){
            $docBody["project"] = $formattedEntity->project;
        }

        if(property_exists($formattedEntity,"organizationAccount")){
            $docBody["organizationAccount"] = $formattedEntity->organizationAccount;
        }
        $client->post($uri,$docBody);
    }

}
