<?php

namespace Dev3bdulrahman\Inventory\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dev3bdulrahman\Inventory\Http\Resources\StockItemResource;
use Dev3bdulrahman\Inventory\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StockItem::query();

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->get('warehouse_id'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->get('product_id'));
        }

        $stock = $query->get();

        return response()->json([
            'success' => true,
            'message' => __('Stock balances retrieved successfully'),
            'data' => StockItemResource::collection($stock),
            'errors' => []
        ]);
    }
}
