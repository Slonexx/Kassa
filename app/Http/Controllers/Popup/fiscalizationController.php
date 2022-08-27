<?php

namespace App\Http\Controllers\Popup;

use App\Clients\MsClient;
use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getSetting;
use App\Http\Controllers\getData\getWorkerID;
use Illuminate\Http\Request;

class fiscalizationController extends Controller
{
    public function fiscalizationPopup(Request $request){

        return view( 'popup.fiscalization', [

        ] );
    }

    public function ShowFiscalizationPopup(Request $request){
        $object_Id = $request->object_Id;
        $accountId = $request->accountId;
        $Setting = new getSetting($accountId);

        $json = $this->info_object_Id($object_Id, $Setting);

        return response()->json($json);
    }

    public function info_object_Id($object_Id, $Setting){
        $url = "https://online.moysklad.ru/api/remap/1.2/entity/customerorder/".$object_Id;
        $Client = new MsClient($Setting->tokenMs);
        $Body = $Client->get($url);
        $positions = $Client->get($Body->positions->meta->href)->rows;

        $vatEnabled = $Body->vatEnabled;
        $vat = null;
        $products = [];

        foreach ($positions as $id=>$item){

            if ($vatEnabled == true) {
                if ($Body->vatIncluded == false) {
                    $final = $item->price / 100 * $item->quantity;
                    $final = $final + ( $final * ($item->vat/100) );
                }
            } else $final = $item->price / 100 * $item->quantity;


            $products[$id] = [
                'position' => $item->id,
                'name' => $Client->get($item->assortment->meta->href)->name,
                'quantity' => $item->quantity,
                'price' => $item->price / 100 ?: 0,
                'vatEnabled' => $item->vatEnabled,
                'vat' => $item->vat,
                'discount' => $item->discount,
                'final' => $final - ( $final * ($item->discount/100) ),
            ];
        }

        if ($vatEnabled == true) {
            $vat = [
                'vatEnabled' => $Body->vatEnabled,
                'vatIncluded' => $Body->vatIncluded,
                'vatSum' => $Body->vatSum,
            ];
        };
        return [
            'id' => $Body->id,
            'name' => $Body->name,
            'sum' => $Body->sum,
            'vat' => $vat,
            'products' => $products,
        ];
    }

}
