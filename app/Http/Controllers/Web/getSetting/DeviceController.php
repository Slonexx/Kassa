<?php

namespace App\Http\Controllers\Web\getSetting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getDevices;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function getDevice($accountId): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $Devices = new getDevices($accountId);

        return view('setting.device', [
            'accountId' => $accountId,
            'devices' => $Devices->devices,
        ]);
    }
}
