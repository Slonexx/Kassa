<?php

namespace App\Http\Controllers\Web\getSetting;

use App\Clients\MsClient;
use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getDevices;
use App\Http\Controllers\getData\getSetting;
use App\Http\Controllers\getData\getWorkers;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function getWorker($accountId){

        $Device = new getDevices($accountId);
        $Device = $Device->devices;

        $Workers = null;

        foreach ($Device as $item){
            $Workers = new getWorkers($item->znm);
        }

        if ($Workers != null) $Workers = $Workers->workers;

        $Setting = new getSetting($accountId);
        $tokenMs = $Setting->tokenMs;
        $url_employee = 'https://online.moysklad.ru/api/remap/1.2/entity/employee';
        $Client = new MsClient($tokenMs);
        $Body_employee = $Client->get($url_employee)->rows;
        $security = [];
        //СДЕЛАТЬ ПАРАЛЕЛЬНЫЙ ЗАПРОС
        foreach ($Body_employee as $id=>$item){
            $url_security = $url_employee.'/'.$item->id.'/security';
            $Body_security = $Client->get($url_security)->role;
            $security[$id] = mb_substr ($Body_security->meta->href, 53);
        }

        return view('setting.worker', [
            'accountId' => $accountId,
            'employee' => $Body_employee,
            'security' => $security,
            'workers' => $Workers,
        ]);
    }
}
