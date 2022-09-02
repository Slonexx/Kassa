<?php

namespace App\Http\Controllers\Web\postSetting;

use App\Http\Controllers\Config\getSettingVendorController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getSetting;
use App\Services\workWithBD\DataBaseService;
use Illuminate\Http\Request;

class postBaseController extends Controller
{
    public function postBase(Request $request, $accountId): \Illuminate\Http\RedirectResponse
    {
        $isAdmin = $request->isAdmin;
        $apiKey = 'f5ac6559-b5cd-4e0e-89e5-7fd32a6d60a5';
        $Setting = new getSettingVendorController($accountId);
        $app = new getSetting($accountId);
        try {
            if ($app->tokenMs == null){
                DataBaseService::createSetting($accountId, $Setting->TokenMoySklad, $apiKey,
                    $request->paymentDocument, null,null);
            } else {
                DataBaseService::updateSetting($accountId, $Setting->TokenMoySklad, $apiKey,
                    $request->paymentDocument,null,null);
            }
        } catch (\Throwable $e){

        }

        return redirect()->route('getDevices', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,
        ]);
    }
}
