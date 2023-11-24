<?php

namespace App\Http\Controllers\integration;

use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Clients\testKassClient;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices\AttributeService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class actionClientController extends Controller
{
    public function sendTicket(Request $request) {
        return response()->json($request->all());
    }

}
