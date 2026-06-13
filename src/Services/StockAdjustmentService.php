<?php

namespace Dev3bdulrahman\Inventory\Services;

use Dev3bdulrahman\Inventory\Models\StockAdjustment;
use Dev3bdulrahman\Inventory\Models\StockAdjustmentItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockAdjustmentService
{
    protected StockMoveService $stockMoveService;

    public function __construct(StockMoveService $stockMoveService)
    {
        $this->stockMoveService = $stockMoveService;
    }

    /**
     * Create and process a stock adjustment.
     */
    public function createAdjustment(array $data, array $items): StockAdjustment
    {
        return DB::transaction(function () use ($data, $items) {
            if (empty($data['adjustment_number'])) {
                $data['adjustment_number'] = 'ADJ-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }

            $adjustment = StockAdjustment::create($data);

            foreach ($items as $item) {
                $adjItem = $adjustment->items()->create([
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'batch_id'           => $item['batch_id'] ?? null,
                    'type'               => $item['type'], // addition, subtraction
                    'quantity'           => $item['quantity'],
                    'unit_cost'          => $item['unit_cost'] ?? 0.0000,
                ]);

                if ($adjustment->status === 'completed') {
                    $this->stockMoveService->logMove([
                        'company_id'         => $adjustment->company_id,
                        'warehouse_id'       => $adjustment->warehouse_id,
                        'product_id'         => $adjItem->product_id,
                        'product_variant_id' => $adjItem->product_variant_id,
                        'batch_id'           => $adjItem->batch_id,
                        'type'               => $adjItem->type === 'addition' ? 'in' : 'out',
                        'source_type'        => 'StockAdjustment',
                        'source_id'          => $adjustment->id,
                        'quantity'           => $adjItem->quantity,
                        'reference'          => $adjustment->adjustment_number,
                        'created_by'         => $adjustment->created_by,
                    ]);
                }
            }

            return $adjustment;
        });
    }
}
