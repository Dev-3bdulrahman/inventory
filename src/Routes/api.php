<?php

use Illuminate\Support\Facades\Route;
use Dev3bdulrahman\Inventory\Http\Controllers\Api\WarehouseApiController;
use Dev3bdulrahman\Inventory\Http\Controllers\Api\StockApiController;

Route::prefix('api/v1/inventory')->middleware(['auth:sanctum'])->group(function () {
    // Warehouses API
    Route::get('/warehouses', [WarehouseApiController::class, 'index'])->name('api.inventory.warehouses.index');
    Route::post('/warehouses', [WarehouseApiController::class, 'store'])->name('api.inventory.warehouses.store');
    Route::get('/warehouses/{id}', [WarehouseApiController::class, 'show'])->name('api.inventory.warehouses.show');
    Route::put('/warehouses/{id}', [WarehouseApiController::class, 'update'])->name('api.inventory.warehouses.update');
    Route::delete('/warehouses/{id}', [WarehouseApiController::class, 'destroy'])->name('api.inventory.warehouses.destroy');

    // Stock Balances API
    Route::get('/stock', [StockApiController::class, 'index'])->name('api.inventory.stock.index');
});
