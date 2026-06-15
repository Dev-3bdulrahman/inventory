<?php

namespace Dev3bdulrahman\Inventory\Events;

use Dev3bdulrahman\Inventory\Models\StockItem;
use Dev3bdulrahman\Inventory\Models\Warehouse;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockBelowMinimum
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public StockItem $stockItem,
        public Warehouse $warehouse,
        public int $currentQuantity,
        public int $minimumQuantity,
        public int $companyId,
    ) {}
}
