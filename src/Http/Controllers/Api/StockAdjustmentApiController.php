<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HasApiResponse;
use Dev3bdulrahman\Inventory\Models\StockAdjustment;
use Dev3bdulrahman\Inventory\Services\StockAdjustmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockAdjustmentApiController extends Controller
{
    use HasApiResponse;

    protected StockAdjustmentService $service;

    public function __construct(StockAdjustmentService $service)
    {
        $this->service = $service;
    }

    /**
     * List stock adjustments paginated.
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = session('active_company_id') ?: auth()->user()->company_id;
        $perPage = (int) $request->get('per_page', 15);

        $query = StockAdjustment::where('company_id', $companyId);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->get('warehouse_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $adjustments = $query->with(['items', 'warehouse', 'creator'])
            ->latest()
            ->paginate($perPage);

        return $this->success(
            data: $adjustments->items(),
            message: 'inventory::inventory.adjustments_retrieved',
            meta: [
                'current_page' => $adjustments->currentPage(),
                'last_page' => $adjustments->lastPage(),
                'per_page' => $adjustments->perPage(),
                'total' => $adjustments->total(),
            ]
        );
    }

    /**
     * Create a new stock adjustment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|integer|exists:inventory_warehouses,id',
            'status' => 'sometimes|in:draft,completed',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.product_variant_id' => 'nullable|integer',
            'items.*.batch_id' => 'nullable|integer',
            'items.*.type' => 'required|in:addition,subtraction',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        $companyId = session('active_company_id') ?: auth()->user()->company_id;

        $data = [
            'company_id' => $companyId,
            'warehouse_id' => $validated['warehouse_id'],
            'status' => $validated['status'] ?? 'draft',
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ];

        $adjustment = $this->service->createAdjustment($data, $validated['items']);

        return $this->success(
            data: $adjustment->load('items'),
            message: 'inventory::inventory.adjustment_created',
            code: 201,
        );
    }

    /**
     * Show a single stock adjustment.
     */
    public function show(StockAdjustment $stockAdjustment): JsonResponse
    {
        $stockAdjustment->load(['items', 'warehouse', 'creator']);

        return $this->success(
            data: $stockAdjustment,
            message: 'inventory::inventory.adjustment_retrieved',
        );
    }
}
