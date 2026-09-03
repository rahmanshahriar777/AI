<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItemVariantAttribute extends Model
{
    protected $fillable = [
        'stock_item_variant_id',
        'stock_attribute_id',
        'value',
    ];
}
