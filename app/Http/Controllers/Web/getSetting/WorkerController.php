<?php

namespace App\Http\Controllers\Web\getSetting;

use App\Clients\MsClient;
use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getDevices;
use App\Http\Controllers\getData\getSetting;
use App\Http\Controllers\getData\getWorkers;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WorkerController extends Controller
{
    public function getWorker($accountId){

        $Device = new getDevices($accountId);
        $Device = $Device->devices;

        $Workers = null;

        foreach ($Device as $item){
            $Workers = new getWorkers($item->znm);
        }

        try {
            if ($Workers != null) $Workers = $Workers->workers;
        } catch (\Throwable $e) {
            $Workers = null;
        }


        $Setting = new getSetting($accountId);
        $tokenMs = $Setting->tokenMs;
        $url_employee = 'https://online.moysklad.ru/api/remap/1.2/entity/employee';
        $Client = new MsClient($tokenMs);
        $Body_employee = $Client->get($url_employee)->rows;
        $security = [];
        //СДЕЛАТЬ ПАРАЛЕЛЬНЫЙ ЗАПРОС
        $urls = [];
        foreach ($Body_employee as $id=>$item){
            $url_security = $url_employee.'/'.$item->id.'/security';
            $urls [] = $url_security;
            //$Body_security = $Client->get($url_security)->role;
            //$security[$item->id] = mb_substr ($Body_security->meta->href, 53);
        }

        $pools = function (Pool $pool) use ($urls,$tokenMs){
            foreach ($urls as $url){
                $arrPools [] = $pool->withToken($tokenMs)->get($url);
            }
            return $arrPools;
        };

        $responses = Http::pool($pools);
        $count = 0;
        foreach ($Body_employee as $id=>$item){
            $Body_security = $responses[$count]->object()->role;
            $security[$item->id] = mb_substr ($Body_security->meta->href, 53);
            $count++;
        }

        return view('setting.worker', [
            'accountId' => $accountId,
            'employee' => $Body_employee,
            'security' => $security,
            'workers' => $Workers,
        ]);
    }
}
