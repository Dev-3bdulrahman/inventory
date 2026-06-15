<?php

namespace Dev3bdulrahman\Inventory;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Dev3bdulrahman\Inventory\Events\StockAdjustmentApproved;
use Dev3bdulrahman\Inventory\Events\StockBelowMinimum;
use Dev3bdulrahman\Inventory\Listeners\LogStockAdjustment;
use Dev3bdulrahman\Inventory\Listeners\SendStockAlert;
use Dev3bdulrahman\Inventory\Models\StockAdjustment;
use Dev3bdulrahman\Inventory\Models\StockTransfer;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use Dev3bdulrahman\Inventory\Policies\StockAdjustmentPolicy;
use Dev3bdulrahman\Inventory\Policies\StockTransferPolicy;
use Dev3bdulrahman\Inventory\Policies\WarehousePolicy;

class InventoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load package routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        // Load package views
        $this->loadViewsFrom(__DIR__ . '/Views', 'inventory');

        // Load package translations
        $this->loadTranslationsFrom(__DIR__ . '/Translations', 'inventory');

        // Register Policies
        Gate::policy(Warehouse::class, WarehousePolicy::class);
        Gate::policy(StockAdjustment::class, StockAdjustmentPolicy::class);
        Gate::policy(StockTransfer::class, StockTransferPolicy::class);

        // Register Event Listeners
        Event::listen(StockBelowMinimum::class, SendStockAlert::class);
        Event::listen(StockAdjustmentApproved::class, LogStockAdjustment::class);

        // Register Livewire Components
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('inventory-warehouses-index', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Warehouses\Index::class);
            \Livewire\Livewire::component('inventory-stock-index', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Stock\Index::class);
            \Livewire\Livewire::component('inventory-adjustments-index', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Adjustments\Index::class);
            \Livewire\Livewire::component('inventory-transfers-index', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Transfers\Index::class);
            \Livewire\Livewire::component('inventory-moves-index', \Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Moves\Index::class);
        }
    }
}
