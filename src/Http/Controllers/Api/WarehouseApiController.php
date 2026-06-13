<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dev3bdulrahman\Inventory\Http\Resources\WarehouseResource;
use Dev3bdulrahman\Inventory\Services\WarehouseService;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WarehouseApiController extends Controller
{
    protected WarehouseService $service;

    public function __construct(WarehouseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = session('active_company_id') ?: auth()->user()->company_id;
        $warehouses = $this->service->getCompanyWarehouses($companyId);

        return response()->json([
            'success' => true,
            'message' => __('Warehouses retrieved successfully'),
            'data' => WarehouseResource::collection($warehouses),
            'errors' => []
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'address' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'is_main' => 'nullable|boolean',
        ]);

        $validated['company_id'] = session('active_company_id') ?: auth()->user()->company_id;
        $validated['created_by'] = auth()->id();

        $warehouse = $this->service->createWarehouse($validated);

        return response()->json([
            'success' => true,
            'message' => __('Warehouse created successfully'),
            'data' => new WarehouseResource($warehouse),
            'errors' => []
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => __('Warehouse retrieved successfully'),
            'data' => new WarehouseResource($warehouse),
            'errors' => []
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:50',
            'address' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'is_main' => 'nullable|boolean',
        ]);

        $this->service->updateWarehouse($warehouse->id, $validated);

        return response()->json([
            'success' => true,
            'message' => __('Warehouse updated successfully'),
            'data' => new WarehouseResource($warehouse->fresh()),
            'errors' => []
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $warehouse = Warehouse::findOrFail($id);
        $this->service->deleteWarehouse($warehouse->id);

        return response()->json([
            'success' => true,
            'message' => __('Warehouse deleted successfully'),
            'data' => null,
            'errors' => []
        ]);
    }
}
