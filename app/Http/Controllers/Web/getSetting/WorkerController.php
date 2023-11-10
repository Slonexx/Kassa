<?php

namespace App\Http\Controllers\Web\getSetting;

use App\Clients\MsClient;
use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getDevices;
use App\Http\Controllers\getData\getSetting;
use App\Http\Controllers\getData\getWorkers;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WorkerController extends Controller
{
    public function getWorker($accountId, Request $request){
        $isAdmin = $request->isAdmin;
        $message = $request->message;

        $Device = new getDevices($accountId);
        $Device = $Device->devices;

        if ( !$Device ){
            return view('setting.no', [
                'accountId' => $accountId,
                'isAdmin' => $isAdmin,
            ]);
        }

        $Workers = null;
        foreach ($Device as $item){
            $Workers = new getWorkers($item->znm);
        }

        if ( array_key_exists(0, $Workers->workers) ){
            $Workers = '';
        } else $Workers = $Workers->workers;



        $Setting = new getSetting($accountId);
        $tokenMs = $Setting->tokenMs;


        $url_employee = 'https://api.moysklad.ru/api/remap/1.2/entity/employee';
        try {
            $Client = new MsClient($tokenMs);
            $Body_employee = $Client->get($url_employee)->rows;
        } catch (BadResponseException $e) {
            return view('setting.error', [
                'accountId' => $accountId,
                'isAdmin' => $isAdmin,
                'message' => $e->getResponse()->getBody()->getContents()
            ]);
        }



        foreach ($Body_employee as $id=>$item){
            $json = $Client->get( $url_employee.'/'.$item->id.'/security');
            if (property_exists($json, 'role')) {
                if (mb_substr ($json->role->meta->href, 53)== "cashier") {
                    unset($Body_employee[$id]);
                }
            }
        }

        return view('setting.worker', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,
            'message'=>$message,
            'employee' => $Body_employee,
            'workers' => $Workers,
        ]);
    }
}
