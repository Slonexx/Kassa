<?php

use App\Http\Controllers\Config\DeleteVendorApiController;
use App\Http\Controllers\Web\indexController;
use App\Http\Controllers\Web\settingController;
use Illuminate\Support\Facades\Route;

Route::get('delete/{accountId}', [DeleteVendorApiController::class, 'delete']);

Route::get('/', [indexController::class, 'index']);
Route::get('/{accountId}', [indexController::class, 'indexShow'])->name('main');


Route::get('/Setting/{accountId}', [settingController::class, 'getBase']);
Route::post('/Setting/{accountId}', [settingController::class, 'postBase']);


Route::get('/Setting/Device/{accountId}', [settingController::class, 'getDevice']);
Route::post('/Setting/Device/{accountId}', [settingController::class, 'postDevice']);
