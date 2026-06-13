<?php

namespace Dev3bdulrahman\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerialNumber extends Model
{
    use BelongsToCompany;

    protected $table = 'inventory_serial_numbers';

    protected $fillable = [
        'company_id',
        'product_id',
        'serial_number',
        'status',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
