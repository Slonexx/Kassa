<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Config\Lib\VendorApiController;
use App\Http\Controllers\Controller;
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
}
