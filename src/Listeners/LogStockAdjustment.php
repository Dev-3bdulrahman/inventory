<?php

namespace Dev3bdulrahman\Inventory\Listeners;

use App\Services\AuditLogService;
use Dev3bdulrahman\Inventory\Events\StockAdjustmentApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogStockAdjustment implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private AuditLogService $auditLogService,
    ) {}

    /**
     * Handle the StockAdjustmentApproved event.
     */
    public function handle(StockAdjustmentApproved $event): void
    {
        try {
            $this->auditLogService->log(
                action: 'stock_adjustment_approved',
                companyId: $event->companyId,
                userId: $event->userId,
                model: $event->adjustment,
                oldValues: null,
                newValues: [
                    'adjustment_id' => $event->adjustment->id,
                    'adjustment_number' => $event->adjustment->adjustment_number,
                    'warehouse_id' => $event->adjustment->warehouse_id,
                    'status' => $event->adjustment->status,
                    'items_count' => $event->adjustment->items()->count(),
                ],
            );
        } catch (\Throwable $e) {
            Log::error('LogStockAdjustment: Failed to log stock adjustment approval.', [
                'error' => $e->getMessage(),
                'adjustment_id' => $event->adjustment->id ?? null,
                'user_id' => $event->userId ?? null,
            ]);
        }
    }
}
