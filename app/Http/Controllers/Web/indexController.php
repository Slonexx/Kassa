<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Config\Lib\VendorApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class indexController extends Controller
{
    public function Index(Request $request){

        $contextKey = $request->contextKey;
        $vendorAPI = new VendorApiController();
        $employee = $vendorAPI->context($contextKey);
        $accountId = $employee->accountId;
        //$isAdmin = $employee->permissions->admin->view;

        return redirect()->route('main', [
            'accountId' => $accountId,
            //'isAdmin' => $isAdmin,
        ] );
    }

    public function indexShow($accountId){
        return view("main.index" , [
            'accountId' => $accountId,
        ] );
    }
}