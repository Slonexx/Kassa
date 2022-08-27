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
        $employeeId = $request->employeeId;

        $Setting = new getSetting($accountId);

        $Workers = new getWorkerID($employeeId);

        dd($Workers);

        $Client = new MsClient('');
    }

}
