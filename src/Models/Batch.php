<?php

namespace Dev3bdulrahman\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batch extends Model
{
    use BelongsToCompany;

    protected $table = 'inventory_batches';

    protected $fillable = [
        'company_id',
        'product_id',
        'batch_number',
        'expiry_date',
        'production_date',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'production_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
