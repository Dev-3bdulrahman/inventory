<?php

namespace Dev3bdulrahman\Inventory\Events;

use Dev3bdulrahman\Inventory\Models\StockAdjustment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockAdjustmentApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public StockAdjustment $adjustment,
        public int $userId,
        public int $companyId,
    ) {}
}
