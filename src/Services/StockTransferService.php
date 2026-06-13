<?php

namespace Dev3bdulrahman\Inventory\Services;

use Dev3bdulrahman\Inventory\Models\StockTransfer;
use Dev3bdulrahman\Inventory\Models\StockTransferItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferService
{
    protected StockMoveService $stockMoveService;

    public function __construct(StockMoveService $stockMoveService)
    {
        $this->stockMoveService = $stockMoveService;
    }

    /**
     * Create a stock transfer.
     */
    public function createTransfer(array $data, array $items): StockTransfer
    {
        return DB::transaction(function () use ($data, $items) {
            if (empty($data['transfer_number'])) {
                $data['transfer_number'] = 'TRsf-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }

            $transfer = StockTransfer::create($data);

            foreach ($items as $item) {
                $transferItem = $transfer->items()->create([
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'batch_id'           => $item['batch_id'] ?? null,
                    'quantity'           => $item['quantity'],
                ]);

                if ($transfer->status === 'completed') {
                    $this->executeTransferMoves($transfer, $transferItem);
                }
            }

            return $transfer;
        });
    }

    /**
     * Complete a pending/draft transfer.
     */
    public function completeTransfer(int $id): StockTransfer
    {
        return DB::transaction(function () use ($id) {
            $transfer = StockTransfer::findOrFail($id);
            if ($transfer->status === 'completed') {
                return $transfer;
            }

            $transfer->update(['status' => 'completed']);

            foreach ($transfer->items as $transferItem) {
                $this->executeTransferMoves($transfer, $transferItem);
            }

            return $transfer;
        });
    }

    /**
     * Execute moves for a completed transfer.
     */
    protected function executeTransferMoves(StockTransfer $transfer, StockTransferItem $transferItem): void
    {
        // 1. OUT from source warehouse
        $this->stockMoveService->logMove([
            'company_id'         => $transfer->company_id,
            'warehouse_id'       => $transfer->from_warehouse_id,
            'product_id'         => $transferItem->product_id,
            'product_variant_id' => $transferItem->product_variant_id,
            'batch_id'           => $transferItem->batch_id,
            'type'               => 'out',
            'source_type'        => 'StockTransfer',
            'source_id'          => $transfer->id,
            'quantity'           => $transferItem->quantity,
            'reference'          => $transfer->transfer_number,
            'created_by'         => $transfer->created_by,
        ]);

        // 2. IN to destination warehouse
        $this->stockMoveService->logMove([
            'company_id'         => $transfer->company_id,
            'warehouse_id'       => $transfer->to_warehouse_id,
            'product_id'         => $transferItem->product_id,
            'product_variant_id' => $transferItem->product_variant_id,
            'batch_id'           => $transferItem->batch_id,
            'type'               => 'in',
            'source_type'        => 'StockTransfer',
            'source_id'          => $transfer->id,
            'quantity'           => $transferItem->quantity,
            'reference'          => $transfer->transfer_number,
            'created_by'         => $transfer->created_by,
        ]);
    }
}
