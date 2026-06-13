<?php

namespace Dev3bdulrahman\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use BelongsToCompany, SoftDeletes;

    protected $table = 'inventory_warehouses';

    protected $fillable = [
        'company_id',
        'branch_id',
        'name',
        'code',
        'address',
        'status',
        'is_main',
        'created_by',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(WarehouseLocation::class, 'warehouse_id');
    }

    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class, 'warehouse_id');
    }
}
