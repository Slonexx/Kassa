<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\Config\DeleteVendorApiController;
use App\Http\Controllers\deleteData\deleteDevice;
use App\Http\Controllers\Popup\demandController;
use App\Http\Controllers\Popup\fiscalizationController;
use App\Http\Controllers\Popup\salesreturnController;
use App\Http\Controllers\Web\getSetting\BaseController;
use App\Http\Controllers\Web\getSetting\DeviceController;
use App\Http\Controllers\Web\getSetting\DocumentController;
use App\Http\Controllers\Web\getSetting\WorkerController;
use App\Http\Controllers\Web\indexController;
use App\Http\Controllers\Web\postSetting\postBaseController;
use App\Http\Controllers\Web\postSetting\postDeviceController;
use App\Http\Controllers\Web\postSetting\postDocumentController;
use App\Http\Controllers\Web\postSetting\postWorkerController;
use App\Http\Controllers\Web\settingController;
use App\Http\Controllers\Widget\customerorder\customerorderEditController;
use App\Http\Controllers\Widget\demandEditController;
use App\Http\Controllers\Widget\salesreturnEditController;
use Illuminate\Support\Facades\Route;

Route::get('delete/{accountId}/', [DeleteVendorApiController::class, 'delete']);
Route::get('setAttributes/{accountId}/{tokenMs}', [AttributeController::class, 'setAllAttributesVendor']);

Route::get('/', [indexController::class, 'index']);
Route::get('/{accountId}/', [indexController::class, 'indexShow'])->name('main');

Route::get('/widget/InfoAttributes/', [indexController::class, 'widgetInfoAttributes']);

Route::get('/widget/customerorder', [customerorderEditController::class, 'customerorder']);
Route::get('/widget/demand', [demandEditController::class, 'demand']);
Route::get('/widget/salesreturn', [salesreturnEditController::class, 'salesreturn']);

Route::get('/Popup/customerorder', [fiscalizationController::class, 'fiscalizationPopup']);
Route::get('/Popup/customerorder/show', [fiscalizationController::class, 'ShowFiscalizationPopup']);
Route::get('/Popup/customerorder/send', [fiscalizationController::class, 'SendFiscalizationPopup']);
Route::get('/Popup/customerorder/closeShift', [fiscalizationController::class, 'closeShiftPopup']);

Route::get('/Popup/demand', [demandController::class, 'DemandPopup']);
Route::get('/Popup/demand/show', [demandController::class, 'ShowDemandPopup']);
Route::get('/Popup/demand/send', [demandController::class, 'SendDemandPopup']);

Route::get('/Popup/salesreturn', [salesreturnController::class, 'salesreturnPopup']);
Route::get('/Popup/salesreturn/show', [salesreturnController::class, 'ShowSalesreturnPopup']);
Route::get('/Popup/salesreturn/send', [salesreturnController::class, 'SendSalesreturnPopup']);



Route::get('/Setting/Device/{accountId}', [DeviceController::class, 'getDevice'])->name('getDevices');
Route::post('/Setting/Device/{accountId}', [postDeviceController::class, 'postDevice']);


Route::get('/Setting/Document/{accountId}', [DocumentController::class, 'getDocument'])->name('getDocument');
Route::post('/Setting/Document/{accountId}', [postDocumentController::class, 'postDocument']);


Route::get('/Setting/Worker/{accountId}', [WorkerController::class, 'getWorker'])->name('getWorker');
Route::post('/Setting/Worker/{accountId}', [postWorkerController::class, 'postWorker']);


Route::get('/delete/Device/{znm}', [deleteDevice::class, 'delete']);
