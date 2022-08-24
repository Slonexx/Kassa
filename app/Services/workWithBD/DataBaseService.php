<?php

namespace App\Services\workWithBD;

use App\Models\Device;

class DataBaseService
{

    public function createDevice($znm,$password,$position,$accountId){
        Device::create([
            'znm' => $znm,
            'accountId' => $accountId,
            'password' => $password,
            'position' => $position,
        ]);
    }

    //public function createSetting

}
