<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Stock;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Dev3bdulrahman\Inventory\Models\StockItem;
use Dev3bdulrahman\Inventory\Models\Warehouse;

class Index extends Component
{
    use WithPagination;

    // Filters
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'warehouse')]
    public string $warehouseFilter = '';

    #[Layout('layouts.admin')]
    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingWarehouseFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = StockItem::with(['product', 'warehouse']);

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->warehouseFilter) {
            $query->where('warehouse_id', $this->warehouseFilter);
        }

        $stockItems = $query->paginate(10);
        $warehouses = Warehouse::all();

        return view('inventory::livewire.admin.stock.index', [
            'stockItems' => $stockItems,
            'warehouses' => $warehouses,
        ])->title(__('inventory::inventory.stock'));
    }
}
