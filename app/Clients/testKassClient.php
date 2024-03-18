<?php

namespace App\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;

class testKassClient
{

    private $apiKey;
    private $password;
    private $kassaNumber;

    private Client $client;


    public function __construct($kassaNumber,$password)
    {
        $this->apiKey = "f5ac6559-b5cd-4e0e-89e5-7fd32a6d60a5";
        $this->kassaNumber = $kassaNumber;
        $this->password = $password;

        $this->client = new Client([
            'base_uri' => 'https://api-test.rekassa.kz/api/',
        ]);
    }


    public function getNewJwtToken(): mixed
    {
        //$uuid_v4 = Str::uuid();
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

    public function get($uri){
        $jsonWithToken = $this->getNewJwtToken();
        //$uuid_v4 = Str::uuid();
        $res = $this->client->get($uri,[
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$jsonWithToken->token,
            ],
        ]);
        return json_decode($res->getBody());
    }

    public function post($uri, $body){
        $jsonWithToken = $this->getNewJwtToken();
        //$uuid_v4 = Str::uuid();
        $res = $this->client->post($uri,[
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$jsonWithToken->token,
            ],
            'json' => $body
        ]);
        return json_decode($res->getBody());
    }

    public function getStatusCode(): int
    {
        //$uuid_v4 = Str::uuid();
        $res =  $this->client->post('auth/login',[
            'http_errors' => false,
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
        return $res->getStatusCode();
    }


}
