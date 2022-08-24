<?php

namespace App\Services\workWithBD;

use App\Models\Device;
use App\Models\Setting;
use App\Models\Worker;

class DataBaseService
{

    public static function createDevice($znm,$password,$position,$accountId){
        Device::create([
            'znm' => $znm,
            'accountId' => $accountId,
            'password' => $password,
            'position' => $position,
        ]);
    }

    public static function createSetting($accountId,$tokenMs,$saleChannel,$paymentDocument,$project,$apiKey){
        Setting::create([
            'accountId' => $accountId,
            'tokenMs' => $tokenMs,
            'saleChannel' => $saleChannel,
            'paymentDocument' => $paymentDocument,
            'project' => $project,
            'apiKey' => $apiKey,
        ]);
    }

    public static function createWorker($id,$znm,$access){
        Worker::create([
            'id' => $id,
            'znm' => $znm,
            'access' => $access,
        ]);
    }

    public static function deleteDevice($znm): void
    {
        Device::query()->where('znm',$znm)->delete();
    }

    public static function deleteSetting($accountId): void
    {
       Setting::query()->where('accountId',$accountId)->delete();
    }

    public static function deleteWorker($id){
        Worker::query()->where('id',$id)->delete();
    }

}
