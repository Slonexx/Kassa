<?php

namespace App\Http\Controllers\integration;

use App\Clients\KassClient;
use App\Clients\MsClient;
use App\Http\Controllers\Controller;
use App\Services\AdditionalServices\AttributeService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class connectController extends Controller
{
    public function connectClient(Request $request): \Illuminate\Http\JsonResponse
    {

        $data = (object) [
            'serial_number' => $request->serial_number ?? '',
            'password' => $request->serial_number ?? '',
        ];
        try {

            $Client = new KassClient($data->serial_number, $data->password, "6784dad7-6679-4950-b257-2711ff63f9bb");
            $StatusCode = $Client->getStatusCode();
            if ($StatusCode == 200) {
                return response()->json([
                    'status' => true,
                ]);
            } else {
                return response()->json([
                    'status' => true,
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
