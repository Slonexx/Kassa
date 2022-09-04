<?php

namespace App\Http\Controllers\Web\getSetting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getDevices;
use App\Http\Controllers\getData\getSetting;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function getDevice($accountId, Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {   $isAdmin = $request->isAdmin;
        $Setting = new getSetting($accountId);
        if ( !$Setting->tokenMs ){
            return view('setting.no', [
                'accountId' => $accountId,
                'isAdmin' => $isAdmin,
            ]);
        }

        $Devices = new getDevices($accountId);

        return view('setting.device', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,
            'devices' => $Devices->devices,
        ]);
    }
}
