<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItemRequisitionDetail extends Model
{
    protected $fillable = [
        'stock_item_requisition_id',
        'stock_item_variant_id',
        'quantity',
        'unit',
        'notes', // pending, approved, rejected
    ];

    public function stockItemRequisition()
    {
        return $this->belongsTo(StockItemRequisition::class);
    }
    public function stockitemvariant()
    {
        return $this->belongsTo(StockItemVariant::class, 'stock_item_variant_id');
    }
}
