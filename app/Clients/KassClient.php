<?php

namespace App\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class KassClient
{

    private $apiKey;
    private $password;
    private $kassaNumber;

    private Client $client;

    /**
     * @param $apiKey
     */
    public function __construct($kassaNumber,$password,$apiKey)
    {
        $this->apiKey = $apiKey;
        $this->kassaNumber = $kassaNumber;
        $this->password = $password;

        $this->client = new Client([
            'base_uri' => 'https://api-test.rekassa.kz/api/',
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function getNewJwtToken() {
        $res =  $this->client->post('auth/login',[
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => [
                'apiKey' => $this->apiKey,
                'format' => 'json',
            ],
            'json' => [
                'number' => $this->kassaNumber,
                'password' => $this->password,
            ],
        ]);
        return json_decode($res->getBody());
    }

}
