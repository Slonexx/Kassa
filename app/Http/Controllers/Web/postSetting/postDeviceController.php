<?php

namespace App\Http\Controllers\Web\postSetting;

use App\Clients\KassClient;
use App\Http\Controllers\Config\Lib\AppInstanceContoller;
use App\Http\Controllers\Config\Lib\cfg;
use App\Http\Controllers\Config\Lib\VendorApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getDeviceFirst;
use App\Http\Controllers\getData\getDevices;
use App\Http\Controllers\getData\getSetting;
use App\Services\workWithBD\DataBaseService;
use Illuminate\Http\Request;

class postDeviceController extends Controller
{
    public function postDevice(Request $request, $accountId){ $isAdmin = $request->isAdmin;
        $Setting = new getSetting($accountId);


        $ZHM_1 = $request->ZHM_1;
        $PASSWORD_1 = $request->PASSWORD_1;
        /*$ZHM_2 = $request->ZHM_2;
        $PASSWORD_2 = $request->PASSWORD_2;*/

        if ($ZHM_1 != null and $PASSWORD_1 != null) {
            try {

                //ПРОВЕРКА НА КЛИЕНТА ААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААААА

                $Client = new KassClient($ZHM_1, $PASSWORD_1, $Setting->apiKey);
                $StatusCode = $Client->getStatusCode();
                if ($StatusCode == 200 ){
                    $Device = new getDeviceFirst($ZHM_1);
                    if ($Device->accountId == null) DataBaseService::createDevice($ZHM_1, $PASSWORD_1, 1, $accountId);
                    else DataBaseService::updateDevice($ZHM_1, $PASSWORD_1, 1, $accountId);
                } else {

                    $message = [
                        'alert' => ' alert alert-danger alert-dismissible fade show in text-center ',
                        'message' => ' Заводской номер кассового аппарата или паролем не правильные ! ',
                    ];
                    $Devices = new getDevices($accountId);
                    return view('setting.device', [
                        'accountId' => $accountId,
                        'isAdmin' => $isAdmin,
                        'devices' => $Devices->devices,
                        'message' => $message,
                    ]);
                }


            } catch (\Throwable $e) {

            }
        }
        /* if ($ZHM_2 != null and $PASSWORD_2 != null) {
             try {
                 $Device = new getDeviceFirst($ZHM_2);
                 if ($Device->accountId == null) DataBaseService::createDevice($ZHM_2, $PASSWORD_2, 2, $accountId);
                 else DataBaseService::updateDevice($ZHM_2, $PASSWORD_2, 2, $accountId);
             } catch (\Throwable $e) {

             }
         }*/

        $cfg = new cfg();
        $app = AppInstanceContoller::loadApp($cfg->appId, $accountId);
        $app->status = AppInstanceContoller::ACTIVATED;
        $vendorAPI = new VendorApiController();
        $vendorAPI->updateAppStatus($cfg->appId, $accountId, $app->getStatusName());
        $app->persist();

        return redirect()->route('getWorker', ['accountId' => $accountId, 'isAdmin' => $isAdmin]);
    }
}
