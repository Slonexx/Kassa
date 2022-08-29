<?php

namespace App\Http\Controllers;

use App\Services\ticket\TicketService;
use Illuminate\Http\Request;

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

    public function initTicket(Request $request){
        $data = $request->validate([
            "accountId" => "required|string",
            "id_entity" => "required||string",
            "entity_type" => "required|string",
            "money_card" => "required",
            "money_cash" => "required",
            "pay_type" => "required|string",
            "positions" => "required|array",
        ]);

        return response(
            $this->ticketService->createTicket($data)
        );

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
