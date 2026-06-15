<?php

namespace Dev3bdulrahman\Inventory\Events;

use Dev3bdulrahman\Inventory\Models\StockTransfer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockTransferCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public StockTransfer $transfer,
        public int $userId,
        public int $companyId,
    ) {}
}
