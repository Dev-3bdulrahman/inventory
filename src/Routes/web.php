<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin/inventory')->middleware(['web', 'auth'])->group(function () {
    Route::get('/warehouses', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Warehouses\Index::class)->name('admin.inventory.warehouses.index');
    Route::get('/stock', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Stock\Index::class)->name('admin.inventory.stock.index');
    Route::get('/adjustments', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Adjustments\Index::class)->name('admin.inventory.adjustments.index');
    Route::get('/transfers', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Transfers\Index::class)->name('admin.inventory.transfers.index');
    Route::get('/moves', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Moves\Index::class)->name('admin.inventory.moves.index');
});
