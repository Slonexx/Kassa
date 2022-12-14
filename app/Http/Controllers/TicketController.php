<?php

namespace App\Http\Controllers;

use App\Services\ticket\TicketService;
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

        $serviceRes =  $this->ticketService->createTicket($data);

        return response($serviceRes["res"],$serviceRes["code"]);

    }

    public function createTicket($data): array
    {

        $serviceRes =  $this->ticketService->createTicket($data);

        return $serviceRes['res'];
    }


    public function getUrlTicket(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $data = $request->validate([
            "accountId" => "required|string",
            "id_ticket" => "required||string",
        ]);

        $res = $this->ticketService->showTicket($data);
        return response($res);
    }

    public function cancelTicket(Request $request){
        /*  $data = $request->validate([
              "accountId" => "required|string",
              "id_entity" => "required||string",
              "entity_type" => "required|string",
          ]);
          //"position" => "required|integer",
          return response(
              $this->ticketService->cancelTicket($data)
          );*/
    }

}
