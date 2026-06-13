<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Adjustments;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Dev3bdulrahman\Inventory\Models\StockAdjustment;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use App\Models\Product;
use Dev3bdulrahman\Inventory\Services\StockAdjustmentService;

class Index extends Component
{
    use WithPagination;

    // Filters
    #[Url(as: 'q')]
    public string $search = '';

    // Modal Form properties
    public bool $showFormModal = false;
    public ?int $warehouse_id = null;
    public string $adjustment_date = '';
    public string $notes = '';
    public array $items = [];

    #[Layout('layouts.admin')]
    public function mount()
    {
        $this->adjustment_date = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->warehouse_id = null;
        $this->adjustment_date = date('Y-m-d');
        $this->notes = '';
        $this->items = [];
        $this->addItem();
        $this->showFormModal = true;
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'type' => 'addition',
            'quantity' => 1,
            'unit_cost' => 0.00,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(StockAdjustmentService $service)
    {
        $this->validate([
            'warehouse_id' => 'required|exists:inventory_warehouses,id',
            'adjustment_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.type' => 'required|in:addition,subtraction',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        $data = [
            'company_id' => session('active_company_id') ?: auth()->user()->company_id,
            'warehouse_id' => $this->warehouse_id,
            'adjustment_number' => null, // Generated in service
            'adjustment_date' => $this->adjustment_date,
            'status' => 'completed', // Autocomplete physical adjustments immediately
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ];

        $service->createAdjustment($data, $this->items);

        $this->dispatch('notify', ['type' => 'success', 'message' => __('inventory::inventory.success_created')]);
        $this->showFormModal = false;
    }

    public function render()
    {
        $query = StockAdjustment::with(['warehouse', 'creator']);

        if ($this->search) {
            $query->where('adjustment_number', 'like', '%' . $this->search . '%');
        }

        $adjustments = $query->latest()->paginate(10);
        $warehouses = Warehouse::where('status', 'active')->get();
        $products = Product::all();

        return view('inventory::livewire.admin.adjustments.index', [
            'adjustments' => $adjustments,
            'warehouses' => $warehouses,
            'products' => $products,
        ])->title(__('inventory::inventory.adjustments'));
    }
}
