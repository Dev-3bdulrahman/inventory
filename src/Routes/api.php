<?php

use Illuminate\Support\Facades\Route;
use Dev3bdulrahman\Inventory\Http\Controllers\Api\WarehouseApiController;
use Dev3bdulrahman\Inventory\Http\Controllers\Api\StockApiController;

Route::prefix('api/v1/inventory')->middleware(['auth:sanctum', 'throttle:60,1', 'api.tenant'])->group(function () {
    // Warehouses API
    Route::get('/warehouses', [WarehouseApiController::class, 'index'])->name('api.v1.inventory.warehouses.index');
    Route::post('/warehouses', [WarehouseApiController::class, 'store'])->name('api.v1.inventory.warehouses.store');
    Route::get('/warehouses/{warehouse}', [WarehouseApiController::class, 'show'])->name('api.v1.inventory.warehouses.show');
    Route::put('/warehouses/{warehouse}', [WarehouseApiController::class, 'update'])->name('api.v1.inventory.warehouses.update');
    Route::delete('/warehouses/{warehouse}', [WarehouseApiController::class, 'destroy'])->name('api.v1.inventory.warehouses.destroy');

    // Stock Balances API
    Route::get('/stock', [StockApiController::class, 'index'])->name('api.v1.inventory.stock.index');
});
