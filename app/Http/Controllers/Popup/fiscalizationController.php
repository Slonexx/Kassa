<?php

namespace App\Http\Controllers\Popup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class fiscalizationController extends Controller
{
    public function fiscalizationPopup(Request $request){

        return view( 'popup.fiscalization', [

        ] );
    }
}
