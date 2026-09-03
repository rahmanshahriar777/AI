<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItemVariant extends Model
{
    protected $fillable = [
        'stock_item_id',
        'name',
        'sku',
        'avg_price',
        'quantity',
        'unit',
        'warehouse_id',
        'is_active',
    ];

    // In StockItemVariant.php
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }
    public function attributes()
    {
        return $this->hasMany(StockItemVariantAttribute::class, 'stock_item_variant_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(StockWarehouse::class, 'warehouse_id');
    }
}
