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
        $accountId = '1dd5bd55-d141-11ec-0a80-055600047495';
       /* $contextKey = $request->contextKey;
        $vendorAPI = new VendorApiController();
        $employee = $vendorAPI->context($contextKey);
        $accountId = $employee->accountId;

        $entity = 'counterparty';*/

        $getObjectUrl = "";

        return view( 'widget.customerorder', [
            'accountId' => $accountId,
            'getObjectUrl' => $getObjectUrl,
        ] );
    }
}
