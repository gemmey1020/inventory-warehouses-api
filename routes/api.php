<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WarehouseInventoryController;
use App\Http\Controllers\StockTransferController;
use Illuminate\Support\Facades\Route;

Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// القراءة مفتوحة
Route::get('/inventory', [InventoryController::class, 'index']);
Route::get('/warehouses/{warehouse}/inventory', [WarehouseInventoryController::class, 'show']);

// الكتابة محمية
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/stock-transfers', [StockTransferController::class, 'store']);
});
