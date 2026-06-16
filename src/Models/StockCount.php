<?php

namespace Dev3bdulrahman\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockCount extends Model
{
    use BelongsToCompany;

    protected $table = 'inventory_stock_counts';

    protected $fillable = [
        'company_id',
        'warehouse_id',
        'count_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'count_date' => 'date',
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
        return $this->hasMany(StockCountItem::class, 'stock_count_id');
    }
}
