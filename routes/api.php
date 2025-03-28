<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\ShippingMethodController;

// API Routes
Route::prefix('shipping')->group(function () {
    Route::post('/zone', [ShippingController::class, 'createShippingZone']);
    Route::post('/calculate', [ShippingController::class, 'calculateShippingFee']);
    Route::post('/method', [ShippingMethodController::class, 'createShippingMethod']);
    Route::get('/zone', [ShippingController::class, 'getShippingZones']);
    Route::get('/method', [ShippingController::class, 'getShippingMethods']);

    // Routes cho ShippingMethodController
    Route::get('/method/{id}', [ShippingMethodController::class, 'show']);   // Lấy thông tin theo ID
    Route::put('/method/{id}', [ShippingMethodController::class, 'update']); // Cập nhật thông tin
    Route::delete('/method/{id}', [ShippingMethodController::class, 'destroy']); // Xóa phương thức vận chuyển
});
