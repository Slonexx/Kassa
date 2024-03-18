<?php

namespace App\Http\Controllers\Web;

use App\Clients\MsClient;
use App\Http\Controllers\Config\Lib\VendorApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\getData\getSetting;
use App\Services\Settings\SettingsService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;

class indexController extends Controller
{
    public function Index(Request $request){

            $contextKey = $request->contextKey;
            if ($contextKey == null) {
                return view("main.dump");
            }
            $vendorAPI = new VendorApiController();
            $employee = $vendorAPI->context($contextKey);
            $accountId = $employee->accountId;

        $isAdmin = $employee->permissions->admin->view;

        return redirect()->route('main', [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,
        ] );
    }

    public function indexShow($accountId, Request $request){
        $isAdmin = $request->isAdmin;
        return view("main.index" , [
            'accountId' => $accountId,
            'isAdmin' => $isAdmin,
        ] );
    }

    public function widgetInfoAttributes(Request $request){
        $ticket_id = null;

        $accountId = $request->accountId;
        $entity_type = $request->entity_type;
        $objectId = $request->objectId;

        $url = $this->getUrlEntity($entity_type, $objectId);
        $Setting = new getSetting($accountId);
        $Client = new MsClient($Setting->tokenMs);
        $body = $Client->get($url);

        if (property_exists($body, 'attributes')){
            foreach ($body->attributes as $item){
                if ($item->name == 'id-билета (ReKassa)'){
                    if ($item->value != null) $ticket_id = $item->value;
                    break;
                }
            }
        }
        return response()->json(['ticket_id' => $ticket_id]);
    }

    private function getUrlEntity($enType,$enId){
        return match ($enType) {
            "customerorder" => "https://api.moysklad.ru/api/remap/1.2/entity/customerorder/" . $enId,
            "demand" => "https://api.moysklad.ru/api/remap/1.2/entity/demand/" . $enId,
            "salesreturn" => "https://api.moysklad.ru/api/remap/1.2/entity/salesreturn/" . $enId,
            default => null,
        };
    }



    public function searchEmployeeByID($login){
        $allSettings = app(SettingsService::class)->getSettings();

        foreach ($allSettings as $setting){

            try {
                $ClientCheckMC = new MsClient($setting->TokenMoySklad);
                $body = $ClientCheckMC->get('https://api.moysklad.ru/api/remap/1.2/entity/employee?filter=uid~'.$login)->rows;

                if ($body!=[]){
                    dd($body);
                }

            } catch (BadResponseException $e) {
                continue;
            }

        }
    }

}
