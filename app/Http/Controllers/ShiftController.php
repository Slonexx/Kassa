<?php

namespace App\Http\Controllers;

use App\Services\shift\ShiftService;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    private ShiftService $shiftService;

    /**
     * @param ShiftService $shiftService
     */
    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function closeShift(Request $request){
        $data = $request->validate([
            "accountId" => "required|string",
            "position" => "required|integer",
            "pincode" => "required|string",
        ]);

        $this->shiftService->closeShift($data);
    }

}
