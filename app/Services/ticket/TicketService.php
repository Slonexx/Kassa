<?php

namespace App\Services\ticket;

use App\Clients\KassClient;

class TicketService
{

    public function init($data){
        $apiKey = $data['apiKey'];
        $password = $data['password'];
        $numKassa = $data['num_kassa'];

        $client = new KassClient($numKassa,$password,$apiKey);
       $json = $client->getNewJwtToken();
       dd($json);
    }

}
