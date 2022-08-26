<?php

namespace App\Http\Controllers\deleteData;

use App\Http\Controllers\Controller;
use App\Services\workWithBD\DataBaseService;
use Illuminate\Http\Request;

class deleteDevice extends Controller
{
    public function delete($znm): \Illuminate\Http\JsonResponse
    {
        try {
            DataBaseService::deleteDevice($znm);
            $message = 'delete';
        } catch (\Throwable $e){
            $message = $e->getMessage();
        }
        return response()->json($message);
    }
}
