<?php

namespace App\Http\Controllers\integration;

use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Clients\testKassClient;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices\AttributeService;
use App\Services\ticket\integrationTicketService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class actionClientController extends Controller
{
    public function sendTicket(Request $request): JsonResponse
    {
        return (new integrationTicketService())->createTicket(json_decode(json_encode($request->all())));
    }

}
