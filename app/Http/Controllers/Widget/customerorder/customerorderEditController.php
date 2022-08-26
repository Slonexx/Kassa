<?php

namespace App\Http\Controllers\Widget\customerorder;

use App\Http\Controllers\Config\Lib\cfg;
use App\Http\Controllers\Config\Lib\VendorApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class customerorderEditController extends Controller
{
    public function customerorder(Request $request){
        $cfg = new cfg();

        $contextKey = $request->contextKey;
        $vendorAPI = new VendorApiController();
        $employee = $vendorAPI->context($contextKey);
        $accountId = $employee->accountId;

        $entity = 'counterparty';

        $getObjectUrl = $cfg->appBaseUrl . "CounterpartyObject/$accountId/$entity/";

        return view( 'widget.customerorder', [
            'accountId' => $accountId,

            'getObjectUrl' => $getObjectUrl,
        ] );
    }
}
