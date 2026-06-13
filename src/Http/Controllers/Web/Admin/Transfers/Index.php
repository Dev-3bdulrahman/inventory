<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Transfers;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Dev3bdulrahman\Inventory\Models\StockTransfer;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use App\Models\Product;
use Dev3bdulrahman\Inventory\Services\StockTransferService;

class Index extends Component
{
    use WithPagination;

    // Filters
    #[Url(as: 'q')]
    public string $search = '';

    // Modal Form properties
    public bool $showFormModal = false;
    public ?int $from_warehouse_id = null;
    public ?int $to_warehouse_id = null;
    public string $transfer_date = '';
    public string $notes = '';
    public array $items = [];

    #[Layout('layouts.admin')]
    public function mount()
    {
        $this->transfer_date = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->from_warehouse_id = null;
        $this->to_warehouse_id = null;
        $this->transfer_date = date('Y-m-d');
        $this->notes = '';
        $this->items = [];
        $this->addItem();
        $this->showFormModal = true;
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(StockTransferService $service)
    {
        $this->validate([
            'from_warehouse_id' => 'required|exists:inventory_warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:inventory_warehouses,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        $data = [
            'company_id' => session('active_company_id') ?: auth()->user()->company_id,
            'from_warehouse_id' => $this->from_warehouse_id,
            'to_warehouse_id' => $this->to_warehouse_id,
            'transfer_number' => null, // Generated in service
            'transfer_date' => $this->transfer_date,
            'status' => 'completed', // Complete transfers immediately
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ];

        $service->createTransfer($data, $this->items);

        $this->dispatch('notify', ['type' => 'success', 'message' => __('inventory::inventory.success_created')]);
        $this->showFormModal = false;
    }

    public function render()
    {
        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'creator']);

        if ($this->search) {
            $query->where('transfer_number', 'like', '%' . $this->search . '%');
        }

        $transfers = $query->latest()->paginate(10);
        $warehouses = Warehouse::where('status', 'active')->get();
        $products = Product::all();

        return view('inventory::livewire.admin.transfers.index', [
            'transfers' => $transfers,
            'warehouses' => $warehouses,
            'products' => $products,
        ])->title(__('inventory::inventory.transfers'));
    }
}
