<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HasApiResponse;
use Dev3bdulrahman\Inventory\Http\Requests\Api\StoreWarehouseApiRequest;
use Dev3bdulrahman\Inventory\Http\Requests\Api\UpdateWarehouseApiRequest;
use Dev3bdulrahman\Inventory\Http\Resources\WarehouseResource;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use Dev3bdulrahman\Inventory\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseApiController extends Controller
{
    use HasApiResponse;

    protected WarehouseService $service;

    public function __construct(WarehouseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Warehouse::class);

        $companyId = session('active_company_id') ?: auth()->user()->company_id;
        $warehouses = $this->service->getCompanyWarehouses($companyId);

        return $this->success(
            data: WarehouseResource::collection($warehouses),
            message: 'inventory::inventory.warehouses_retrieved',
        );
    }

    public function store(StoreWarehouseApiRequest $request): JsonResponse
    {
        $this->authorize('create', Warehouse::class);

        $validated = $request->validated();
        $validated['company_id'] = session('active_company_id') ?: auth()->user()->company_id;
        $validated['created_by'] = auth()->id();

        $warehouse = $this->service->createWarehouse($validated);

        return $this->success(
            data: new WarehouseResource($warehouse),
            message: 'inventory::inventory.warehouse_created',
            code: 201,
        );
    }

    public function show(Warehouse $warehouse): JsonResponse
    {
        $this->authorize('view', $warehouse);

        return $this->success(
            data: new WarehouseResource($warehouse),
            message: 'inventory::inventory.warehouse_retrieved',
        );
    }

    public function update(UpdateWarehouseApiRequest $request, Warehouse $warehouse): JsonResponse
    {
        $this->authorize('update', $warehouse);

        $validated = $request->validated();
        $this->service->updateWarehouse($warehouse->id, $validated);

        return $this->success(
            data: new WarehouseResource($warehouse->fresh()),
            message: 'inventory::inventory.warehouse_updated',
        );
    }

    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $this->authorize('delete', $warehouse);

        $this->service->deleteWarehouse($warehouse->id);

        return $this->success(
            data: null,
            message: 'inventory::inventory.warehouse_deleted',
        );
    }
}
