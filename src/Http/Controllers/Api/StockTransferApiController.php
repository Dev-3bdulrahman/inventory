<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HasApiResponse;
use Dev3bdulrahman\Inventory\Models\StockTransfer;
use Dev3bdulrahman\Inventory\Services\StockTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockTransferApiController extends Controller
{
    use HasApiResponse;

    protected StockTransferService $service;

    public function __construct(StockTransferService $service)
    {
        $this->service = $service;
    }

    /**
     * List stock transfers paginated.
     */
    public function index(Request $request): JsonResponse
    {
        $companyId = session('active_company_id') ?: auth()->user()->company_id;
        $perPage = (int) $request->get('per_page', 15);

        $transfers = StockTransfer::where('company_id', $companyId)
            ->with(['fromWarehouse', 'toWarehouse', 'creator'])
            ->latest()
            ->paginate($perPage);

        return $this->success(
            data: $transfers->items(),
            message: 'inventory::inventory.transfers_retrieved',
            meta: [
                'current_page' => $transfers->currentPage(),
                'last_page' => $transfers->lastPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total(),
            ]
        );
    }

    /**
     * Create a new stock transfer.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source_warehouse_id' => 'required|integer|exists:inventory_warehouses,id',
            'destination_warehouse_id' => 'required|integer|exists:inventory_warehouses,id|different:source_warehouse_id',
            'status' => 'sometimes|in:draft,in_transit,completed',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.product_variant_id' => 'nullable|integer',
            'items.*.batch_id' => 'nullable|integer',
            'items.*.quantity' => 'required|numeric|min:0.0001',
        ]);

        $companyId = session('active_company_id') ?: auth()->user()->company_id;

        $data = [
            'company_id' => $companyId,
            'from_warehouse_id' => $validated['source_warehouse_id'],
            'to_warehouse_id' => $validated['destination_warehouse_id'],
            'status' => $validated['status'] ?? 'draft',
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ];

        $transfer = $this->service->createTransfer($data, $validated['items']);

        return $this->success(
            data: $transfer->load('items'),
            message: 'inventory::inventory.transfer_created',
            code: 201,
        );
    }

    /**
     * Show a single stock transfer.
     */
    public function show(StockTransfer $stockTransfer): JsonResponse
    {
        $stockTransfer->load(['items', 'fromWarehouse', 'toWarehouse', 'creator']);

        return $this->success(
            data: $stockTransfer,
            message: 'inventory::inventory.transfer_retrieved',
        );
    }
}
