<?php

namespace App\Http\Controllers;

use App\Clients\KassClient;
use App\Clients\testKassClient;
use App\Http\Controllers\getData\getDeviceFirst;
use App\Http\Controllers\getData\getDevices;
use App\Http\Controllers\getData\getSetting;
use App\Services\ticket\TicketService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class TicketController extends Controller
{

    private TicketService $ticketService;

    /**
     * @param TicketService $ticketService
     */
    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function initTicket(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $data = $request->validate([
            "accountId" => "required|string",
            "id_entity" => "required||string",
            "entity_type" => "required|string",

            "money_card" => "required",
            "money_cash" => "required",
            "money_mobile" => "required",

            "pay_type" => "required|string",
            "positions" => "required|array",
        ]);

        $serviceRes = $this->ticketService->createTicket($data);

        return response($serviceRes["res"], $serviceRes["code"]);

    }

    public function createTicket($data): array
    {

        $serviceRes = $this->ticketService->createTicket($data);

        return $serviceRes['res'];
    }


    /**
     * @throws GuzzleException
     */
    public function getUrlTicket(Request $request): string
    {
        $data = [
            'accountId' => $request->accountId ?? '',
            'id_ticket' => $request->id_ticket ?? '',
            'integration' => $request->integration ?? false,
            'serial_number' => $request->serial_number ?? '',
            'pass' => $request->pass ?? '',
        ];

        if ($data['integration']) {

            if ($data['accountId'] == '1dd5bd55-d141-11ec-0a80-055600047495') $idKassa = (new testKassClient($data['serial_number'], $data['pass']))->getNewJwtToken()->id;
            else $idKassa = (new KassClient($data['serial_number'], $data['pass'], ''))->getNewJwtToken()->id;


        } else {
            $Device = new getDevices($data['accountId']);

            $znm = $Device->devices[0]->znm;
            $Device = new getDeviceFirst($znm);

            $numKassa = $Device->znm;
            $password = $Device->password;

            $idKassa = (new KassClient($numKassa, $password, ''))->getNewJwtToken()->id;
        }

        return "print/" . $idKassa . "/" . $data['id_ticket'];
    }

}
