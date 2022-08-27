<?php

namespace App\Http\Controllers\Popup;

use App\Clients\MsClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class fiscalizationController extends Controller
{
    public function fiscalizationPopup(Request $request){

        return view( 'popup.fiscalization', [

        ] );
    }

    public function ShowFiscalizationPopup(Request $request, $popupParameters){
        $Client = new MsClient('');
    }

}
