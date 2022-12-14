<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WebHookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::post('attributes',[AttributeController::class,'setAllAttributes']);

Route::post('ticket',[TicketController::class,'initTicket']);

Route::get('ticket',[TicketController::class,'getUrlTicket']);
//Route::post('cancelTicket',[TicketController::class,'cancelTicket']);

Route::post('closeShift',[ShiftController::class,'closeShift']);

Route::post('webhook/{accountId}/customerorder',[WebHookController::class,'newOrder']);
Route::post('webhook/{accountId}/demand',[WebHookController::class,'newDemand']);



