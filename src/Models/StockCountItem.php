<?php

namespace Dev3bdulrahman\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockCountItem extends Model
{
    protected $table = 'inventory_stock_count_items';

    protected $fillable = [
        'stock_count_id',
        'product_id',
        'expected_qty',
        'actual_qty',
        'difference',
    ];

    protected $casts = [
        'expected_qty' => 'decimal:4',
        'actual_qty' => 'decimal:4',
        'difference' => 'decimal:4',
    ];

    public function stockCount(): BelongsTo
    {
        return $this->belongsTo(StockCount::class, 'stock_count_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
