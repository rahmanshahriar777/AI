<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItemAttribute extends Model
{
    protected $fillable = [
        'stock_item_id',
        'stock_attribute_id',
    ];

    public function attribute(){
        return $this->belongsTo(StockAttribute::class, 'stock_attribute_id');
    }
}
