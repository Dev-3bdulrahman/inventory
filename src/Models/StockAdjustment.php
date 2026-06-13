<?php

namespace Dev3bdulrahman\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAdjustment extends Model
{
    use BelongsToCompany;

    protected $table = 'inventory_stock_adjustments';

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'adjustment_number',
        'adjustment_date',
        'status', // draft, completed
        'notes',
        'created_by',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAdjustmentItem::class, 'stock_adjustment_id');
    }
}
