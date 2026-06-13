<?php

namespace Dev3bdulrahman\Inventory\Services;

use Dev3bdulrahman\Inventory\Models\StockItem;
use Dev3bdulrahman\Inventory\Models\Batch;
use Dev3bdulrahman\Inventory\Models\SerialNumber;

class StockService
{
    /**
     * Get stock quantity of a product.
     */
    public function getStockQuantity(int $companyId, int $productId, ?int $warehouseId = null, ?int $variantId = null): float
    {
        $query = StockItem::where('company_id', $companyId)
            ->where('product_id', $productId);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        return (float) $query->sum('quantity');
    }

    /**
     * Create product batch.
     */
    public function createBatch(array $data): Batch
    {
        return Batch::create($data);
    }

    /**
     * Create product serial number.
     */
    public function createSerialNumber(array $data): SerialNumber
    {
        return SerialNumber::create($data);
    }
}
