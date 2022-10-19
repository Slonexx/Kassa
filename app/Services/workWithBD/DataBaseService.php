<?php

namespace App\Services\workWithBD;

use App\Models\Device;
use App\Models\Setting;
use App\Models\Worker;
use SebastianBergmann\CodeCoverage\Driver\Selector;

class DataBaseService
{

    public static function createSetting($accountId,$tokenMs,$apiKey,$paymentDocument,$saleChannel,$project){
        Setting::create([
            'accountId' => $accountId,
            'tokenMs' => $tokenMs,
            'saleChannel' => $saleChannel,
            'paymentDocument' => $paymentDocument,
            'project' => $project,
            'apiKey' => "6784dad7-6679-4950-b257-2711ff63f9bb",
        ]);
    }

    public static function createDevice($znm,$password,$position,$accountId){
        Device::create([
            'znm' => $znm,
            'accountId' => $accountId,
            'password' => $password,
            'position' => $position,
        ]);
    }

    public static function createWorker($id,$znm,$access){
        Worker::create([
            'id' => $id,
            'znm' => $znm,
            'access' => $access,
        ]);
    }



    public static function showSetting($accountId){
        $find = Setting::query()->where('accountId', $accountId)->first();
        try {
            $result = $find->getAttributes();
        } catch (\Throwable $e) {
            $result = [
                "accountId" => $accountId,
                "tokenMs" => null,
                "apiKey" => null,
                "saleChannel" => null,
                "paymentDocument" => null,
                "project" => null,
            ];
        }
        return $result;
    }

    public static function showDeviceFirst($znm){
        $find = Device::query()->where('znm', $znm)->first();
        try {
            $result = $find->getAttributes();
        } catch (\Throwable $e) {
            $result = [
                'znm' => $znm,
                'accountId' => null,
                'password' => null,
                'position' => null,
            ];
        }
        return $result;
    }

    public static function showDevice($accountId){
        $Devices = [];
        $find = Device::query()->where('accountId', $accountId)->orderBy('position')->get();

        foreach ($find as $item) {
            $json = json_encode($item->getAttributes());
            $Devices[] = json_decode($json);
        }

        return $Devices;
    }

    public static function showWorkerFirst($id){

        $find = Worker::query()->where('id', $id)->first();
        try {
            $result = $find->getAttributes();
        } catch (\Throwable $e) {
            $result = [
                'id' => $id,
                'znm' => null,
                'access' => null,
            ];
        }
        return $result;
    }

    public static function showWorkers($znm){

        $Workers = [];
        $find = Worker::query()->where('znm', $znm)->get();

        foreach ($find as $item) {
            $json = json_encode($item->getAttributes());
            $Workers[] = json_decode($json);
        }

        return $Workers;

    }

    public static function updateSetting($accountId,$tokenMs,$apiKey,$paymentDocument,$saleChannel,$project){
       $find = Setting::query()->where('accountId', $accountId);
       $find->update([
           'tokenMs' => $tokenMs,
           'saleChannel' => $saleChannel,
           'paymentDocument' => $paymentDocument,
           'project' => $project,
           'apiKey' => "6784dad7-6679-4950-b257-2711ff63f9bb",
       ]);
    }

    public static function updateDevice($znm,$password,$position,$accountId){
        $find = Device::query()->where('znm', $znm);
        $find->update([
            'accountId' => $accountId,
            'password' => $password,
            'position' => $position,
        ]);

    }

    public static function updateWorker($id,$znm,$access){
        $find = Worker::query()->where('id', $id);
        $find->update([
            'znm' => $znm,
            'access' => $access,
        ]);
    }


    public static function deleteSetting($accountId): void
    {
        Setting::query()->where('accountId',$accountId)->delete();
    }

    public static function deleteDevice($znm): void
    {
        Device::query()->where('znm',$znm)->delete();
    }

    public static function deleteWorker($id){
        Worker::query()->where('id',$id)->delete();
    }


}
