<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Moves;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Dev3bdulrahman\Inventory\Models\StockMove;
use Dev3bdulrahman\Inventory\Models\Warehouse;

class Index extends Component
{
    use WithPagination;

    // Filters
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $typeFilter = '';

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

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingWarehouseFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = StockMove::with(['product', 'warehouse', 'creator']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function ($pq) {
                      $pq->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->warehouseFilter) {
            $query->where('warehouse_id', $this->warehouseFilter);
        }

        $moves = $query->latest()->paginate(15);
        $warehouses = Warehouse::all();

        return view('inventory::livewire.admin.moves.index', [
            'moves' => $moves,
            'warehouses' => $warehouses,
        ])->title(__('inventory::inventory.moves'));
    }
}
