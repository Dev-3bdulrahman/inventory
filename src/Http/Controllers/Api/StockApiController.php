<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HasApiResponse;
use Dev3bdulrahman\Inventory\Http\Resources\StockItemResource;
use Dev3bdulrahman\Inventory\Models\StockItem;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockApiController extends Controller
{
    use HasApiResponse;

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Warehouse::class);

        $query = StockItem::query();

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->get('warehouse_id'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->get('product_id'));
        }

        $stock = $query->get();

        return $this->success(
            data: StockItemResource::collection($stock),
            message: 'inventory::inventory.stock_retrieved',
        );
    }
}
