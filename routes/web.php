<?php

use App\Http\Controllers\Config\DeleteVendorApiController;
use App\Http\Controllers\deleteData\deleteDevice;
use App\Http\Controllers\Popup\fiscalizationController;
use App\Http\Controllers\Web\getSetting\BaseController;
use App\Http\Controllers\Web\getSetting\DeviceController;
use App\Http\Controllers\Web\getSetting\WorkerController;
use App\Http\Controllers\Web\indexController;
use App\Http\Controllers\Web\postSetting\postBaseController;
use App\Http\Controllers\Web\postSetting\postDeviceController;
use App\Http\Controllers\Web\postSetting\postWorkerController;
use App\Http\Controllers\Web\settingController;
use App\Http\Controllers\Widget\customerorder\customerorderEditController;
use Illuminate\Support\Facades\Route;

Route::get('delete/{accountId}', [DeleteVendorApiController::class, 'delete']);

Route::get('/', [indexController::class, 'index']);
Route::get('/{accountId}/', [indexController::class, 'indexShow'])->name('main');


Route::get('/widget/customerorder', [customerorderEditController::class, 'customerorder']);

Route::get('/Popup/customerorder', [fiscalizationController::class, 'fiscalizationPopup']);
Route::get('/Popup/customerorder/show/{popupParameters}', [fiscalizationController::class, 'ShowFiscalizationPopup']);


Route::get('/Setting/{accountId}', [BaseController::class, 'getBase'])->name('getBase');
Route::post('/Setting/{accountId}', [postBaseController::class, 'postBase']);


Route::get('/Setting/Device/{accountId}', [DeviceController::class, 'getDevice'])->name('getDevices');
Route::post('/Setting/Device/{accountId}', [postDeviceController::class, 'postDevice']);


Route::get('/Setting/Worker/{accountId}', [WorkerController::class, 'getWorker'])->name('getWorker');
Route::post('/Setting/Worker/{accountId}', [postWorkerController::class, 'postWorker']);


Route::get('/delete/Device/{znm}', [deleteDevice::class, 'delete']);
