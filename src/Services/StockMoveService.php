<?php

namespace Dev3bdulrahman\Inventory\Services;

use Dev3bdulrahman\Inventory\Models\StockMove;
use Dev3bdulrahman\Inventory\Models\StockItem;
use Illuminate\Support\Facades\DB;

class StockMoveService
{
    /**
     * Log an inventory move and update the stock balance.
     */
    public function logMove(array $data): StockMove
    {
        return DB::transaction(function () use ($data) {
            // Log the stock move
            $move = StockMove::create($data);

            // Update the running stock item balance
            $stockItem = StockItem::firstOrCreate([
                'company_id'            => $data['company_id'],
                'warehouse_id'          => $data['warehouse_id'],
                'warehouse_location_id' => $data['warehouse_location_id'] ?? null,
                'product_id'            => $data['product_id'],
                'product_variant_id'    => $data['product_variant_id'] ?? null,
                'batch_id'              => $data['batch_id'] ?? null,
            ], [
                'quantity' => 0.0000,
            ]);

            if ($data['type'] === 'in') {
                $stockItem->quantity += $data['quantity'];
            } elseif ($data['type'] === 'out') {
                $stockItem->quantity -= $data['quantity'];
            }

            $stockItem->save();

            return $move;
        });
    }
}
