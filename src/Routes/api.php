<?php

use Illuminate\Support\Facades\Route;
use Dev3bdulrahman\Inventory\Http\Controllers\Api\WarehouseApiController;
use Dev3bdulrahman\Inventory\Http\Controllers\Api\StockApiController;
use Dev3bdulrahman\Inventory\Http\Controllers\Api\StockAdjustmentApiController;
use Dev3bdulrahman\Inventory\Http\Controllers\Api\StockTransferApiController;

Route::prefix('api/v1/inventory')->middleware(['auth:sanctum', 'throttle:60,1', 'api.tenant'])->group(function () {
    // Warehouses API
    Route::get('/warehouses', [WarehouseApiController::class, 'index'])->middleware('can:inventory.warehouses.view')->name('api.v1.inventory.warehouses.index');
    Route::post('/warehouses', [WarehouseApiController::class, 'store'])->middleware('can:inventory.warehouses.create')->name('api.v1.inventory.warehouses.store');
    Route::get('/warehouses/{warehouse}', [WarehouseApiController::class, 'show'])->middleware('can:inventory.warehouses.view')->name('api.v1.inventory.warehouses.show');
    Route::put('/warehouses/{warehouse}', [WarehouseApiController::class, 'update'])->middleware('can:inventory.warehouses.edit')->name('api.v1.inventory.warehouses.update');
    Route::delete('/warehouses/{warehouse}', [WarehouseApiController::class, 'destroy'])->middleware('can:inventory.warehouses.delete')->name('api.v1.inventory.warehouses.destroy');

    // Stock Balances API
    Route::get('/stock', [StockApiController::class, 'index'])->middleware('can:inventory.stock.view')->name('api.v1.inventory.stock.index');

    // Stock Adjustments
    Route::get('/adjustments', [StockAdjustmentApiController::class, 'index'])->middleware('can:inventory.adjustments.view')->name('api.v1.inventory.adjustments.index');
    Route::post('/adjustments', [StockAdjustmentApiController::class, 'store'])->middleware('can:inventory.adjustments.create')->name('api.v1.inventory.adjustments.store');
    Route::get('/adjustments/{stockAdjustment}', [StockAdjustmentApiController::class, 'show'])->middleware('can:inventory.adjustments.view')->name('api.v1.inventory.adjustments.show');

    // Stock Transfers
    Route::get('/transfers', [StockTransferApiController::class, 'index'])->middleware('can:inventory.transfers.view')->name('api.v1.inventory.transfers.index');
    Route::post('/transfers', [StockTransferApiController::class, 'store'])->middleware('can:inventory.transfers.create')->name('api.v1.inventory.transfers.store');
    Route::get('/transfers/{stockTransfer}', [StockTransferApiController::class, 'show'])->middleware('can:inventory.transfers.view')->name('api.v1.inventory.transfers.show');

    // Stock Moves
    Route::get('/moves', [StockApiController::class, 'moves'])->middleware('can:inventory.stock.view')->name('api.v1.inventory.moves.index');
});
