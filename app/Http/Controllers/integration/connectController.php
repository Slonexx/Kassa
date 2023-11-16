<?php

namespace App\Http\Controllers\integration;

use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Clients\testKassClient;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices\AttributeService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class connectController extends Controller
{
    public function connectClient(Request $request, $accountId): \Illuminate\Http\JsonResponse
    {

        $data = (object) [
            'serial_number' => $request->serial_number ?? '',
            'password' => $request->serial_number ?? '',
        ];

        if ($accountId == '1dd5bd55-d141-11ec-0a80-055600047495') {
            $Client = new testKassClient($data->serial_number, $data->password);
            $StatusCode = $Client->getStatusCode();
        } else {
            $Client = new KassClient($data->serial_number, $data->password, "6784dad7-6679-4950-b257-2711ff63f9bb");
            $StatusCode = $Client->getStatusCode();
        }

        try {
            if ($StatusCode == 200) {
                return response()->json([
                    'status' => true,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "Не верный знм или пароль",
                ]);
            }

        } catch (BadResponseException $e){
            return response()->json([
                'status' => true,
                'message' => $e->getMessage(),
                'content' => $e->getResponse()->getBody()->getContents()
            ]);
        }

    }

}
