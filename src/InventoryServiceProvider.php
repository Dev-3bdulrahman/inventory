<?php

namespace Dev3bdulrahman\Inventory;

use Illuminate\Support\ServiceProvider;

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
