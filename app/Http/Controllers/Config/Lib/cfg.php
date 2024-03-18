<?php

namespace App\Http\Controllers\Config\Lib;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class cfg extends Controller
{
    public $appId;
    public $appUid;
    public $secretKey;
    public $appBaseUrl;
    public $moyskladVendorApiEndpointUrl;
    public $moyskladJsonApiEndpointUrl;


    public function __construct()
    {
        $this->appId = "ae75a400-6677-4b47-a9d7-a92a216ee489";
        $this->appUid = 'rekassa.smartinnovations';
        $this->secretKey = "9j3XnrRgYOByB3Ugp5nwC0y44eYFJLnQvgh4xEEMssUPEuGJ0dUfHRfXx7WeBHT29q9WhvWOBwWqteEuMxBrqWImrIz16TBB3ML8EVLxG45IFgAdakKQeeqGi3C3tQFA";
        $this->appBaseUrl ='https://smartrekassa.kz/';
        $this->moyskladVendorApiEndpointUrl = 'https://apps-api.moysklad.ru/api/vendor/1.0';
        $this->moyskladJsonApiEndpointUrl = 'https://api.moysklad.ru/api/remap/1.2';
    }


}
