<?php

namespace App\Http\Controllers\Web\getSetting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getSetting;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function getBase($accountId): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {

        $Setting = new getSetting($accountId);
        $tokenMs = $Setting->tokenMs;
        $apiKey = $Setting->apiKey;
        $paymentDocument = $Setting->paymentDocument;

        if ($tokenMs == null) {
            $apiKey = "";
            $paymentDocument = "0";
        }

        return view('setting.base', [
            'accountId' => $accountId,
            'apiKey' => $apiKey,
            'paymentDocument' => $paymentDocument,
        ]);
    }
}
