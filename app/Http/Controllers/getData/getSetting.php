<?php

namespace App\Http\Controllers\getData;

use App\Http\Controllers\Controller;
use App\Services\workWithBD\DataBaseService;
use Illuminate\Http\Request;

class getSetting extends Controller
{
    var $accountId;
    var $tokenMs;
    var $apiKey;
    var $saleChannel;
    var $paymentDocument;
    var $payment_type;
    var $project;

    public function __construct($accountId)
    {
        $app = DataBaseService::showSetting($accountId);
        $this->accountId = $app['accountId'];
        $this->tokenMs = $app['tokenMs'];
        $this->apiKey = $app['apiKey'];
        $this->saleChannel = $app['saleChannel'];
        $this->paymentDocument = $app['paymentDocument'];
        $this->payment_type = $app['payment_type'];
        $this->project = $app['project'];
    }


}
