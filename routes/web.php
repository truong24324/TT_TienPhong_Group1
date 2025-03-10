<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShippingController;


Route::get('/', function () {
    return view('welcome');
});
Route::post('/shipping/calculate', [ShippingController::class, 'calculateShippingFee']);
Route::post('/shipping/zone', [ShippingController::class, 'createShippingZone']);
Route::post('/shipping/method', [ShippingController::class, 'createShippingMethod']);
Route::get('/shipping/zone', [ShippingController::class, 'getShippingZones']);
Route::get('/shipping/method', [ShippingController::class, 'getShippingMethods']); 

