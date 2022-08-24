<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class settingController extends Controller
{
    public function getBase($accountId){
        return view('setting.base', [
            'accountId' => $accountId,
        ]);
    }

    public function postBase(Request $request, $accountId){
        dd($request->request);
        return view('setting.base', [
            'accountId' => $accountId,
        ]);
    }

    public function getDevice($accountId){
        return view('setting.device', [
            'accountId' => $accountId,
        ]);
    }

    public function postDevice(Request $request, $accountId){
        dd($request->request);
        return view('setting.device', [
            'accountId' => $accountId,
        ]);
    }

}
