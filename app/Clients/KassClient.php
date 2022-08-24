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

    /**
     * @throws GuzzleException
     */
    public function get($uri){
        $jsonWithToken = $this->getNewJwtToken();
        $res = $this->client->get($uri,[
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$jsonWithToken->token,
            ],
        ]);
        return json_decode($res->getBody());
    }

    /**
     * @throws GuzzleException
     */
    public function post($uri, $body){
        $jsonWithToken = $this->getNewJwtToken();
        $res = $this->client->post($uri,[
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$jsonWithToken->token,
            ],
            'json' => $body
        ]);
        return json_decode($res->getBody());
    }

    public function getStatusCode(){
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
        return $res->getStatusCode();
    }

}
