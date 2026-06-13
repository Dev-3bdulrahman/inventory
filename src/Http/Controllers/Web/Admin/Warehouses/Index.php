<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Web\Admin\Warehouses;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Dev3bdulrahman\Inventory\Services\WarehouseService;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use App\Models\Branch;

class Index extends Component
{
    use WithPagination;

    // Filters
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = '';

    // Form fields
    public ?int $warehouseId = null;
    public string $name = '';
    public string $code = '';
    public string $address = '';
    public string $status = 'active';
    public bool $is_main = false;
    public ?int $branch_id = null;

    // Modal
    public bool $showFormModal = false;

    protected $listeners = ['delete' => 'deleteWarehouse'];

    #[Layout('layouts.admin')]
    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->warehouseId = null;
        $this->name = '';
        $this->code = '';
        $this->address = '';
        $this->status = 'active';
        $this->is_main = false;
        $this->branch_id = null;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $warehouse = Warehouse::findOrFail($id);

        $this->warehouseId = $warehouse->id;
        $this->name = $warehouse->name;
        $this->code = $warehouse->code;
        $this->address = $warehouse->address ?? '';
        $this->status = $warehouse->status;
        $this->is_main = (bool)$warehouse->is_main;
        $this->branch_id = $warehouse->branch_id;

        $this->showFormModal = true;
    }

    public function save(WarehouseService $service)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'is_main' => 'required|boolean',
            'branch_id' => 'nullable|exists:branches,id',
        ];

        $validated = $this->validate($rules);
        $validated['company_id'] = session('active_company_id') ?: auth()->user()->company_id;
        $validated['created_by'] = auth()->id();

        if ($this->warehouseId) {
            $service->updateWarehouse($this->warehouseId, $validated);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('inventory::inventory.success_updated')]);
        } else {
            $service->createWarehouse($validated);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('inventory::inventory.success_created')]);
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function toggleStatus(int $id): void
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->status = $warehouse->status === 'active' ? 'inactive' : 'active';
        $warehouse->save();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('inventory::inventory.success_updated'),
        ]);
    }

    public function deleteWarehouse(WarehouseService $service, $id)
    {
        $targetId = is_array($id) ? ($id['id'] ?? null) : $id;
        if ($targetId) {
            $service->deleteWarehouse($targetId);
            $this->dispatch('notify', ['type' => 'success', 'message' => __('inventory::inventory.success_deleted')]);
        }
    }

    public function render(WarehouseService $service)
    {
        $query = Warehouse::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $warehouses = $query->paginate(10);
        $branches = Branch::all();

        return view('inventory::livewire.admin.warehouses.index', [
            'warehouses' => $warehouses,
            'branches' => $branches,
        ])->title(__('inventory::inventory.warehouses'));
    }
}
