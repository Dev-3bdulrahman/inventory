<?php

namespace Dev3bdulrahman\Inventory\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'warehouse' => [
                'id' => $this->warehouse_id,
                'name' => $this->warehouse?->name,
            ],
            'product' => [
                'id' => $this->product_id,
                'name' => $this->product?->name,
                'sku' => $this->product?->sku,
            ],
            'quantity' => (float)$this->quantity,
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
