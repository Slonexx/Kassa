<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\integration\actionClientController;
use App\Http\Controllers\integration\connectController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Web\WebhookMSController;
use App\Http\Controllers\WebHookController;
use Illuminate\Support\Facades\Route;



Route::post('attributes',[AttributeController::class,'setAllAttributes']);

Route::post('ticket',[TicketController::class,'initTicket']);

Route::get('ticket',[TicketController::class,'getUrlTicket']);
//Route::post('cancelTicket',[TicketController::class,'cancelTicket']);

Route::post('closeShift',[ShiftController::class,'closeShift']);

Route::post('webhook/{accountId}/customerorder',[WebHookController::class,'newOrder']);
Route::post('webhook/{accountId}/demand',[WebHookController::class,'newDemand']);

Route::post('/webhook/customerorder/',[WebhookMSController::class, 'customerorder']);
Route::post('/webhook/demand/',[WebhookMSController::class, 'customerorder']);
Route::post('/webhook/salesreturn/',[WebhookMSController::class, 'customerorder']);


Route::group(["prefix" => "integration"], function () {
    Route::get('client/connect/{accountId}', [connectController::class, 'connectClient']);
    Route::get('client/get/ticket/', [TicketController::class, 'getUrlTicket']);
    Route::get('client/send/ticket/', [actionClientController::class, 'sendTicket']);
});

