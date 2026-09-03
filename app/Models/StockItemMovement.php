<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItemMovement extends Model
{
    protected $fillable = [
        'item_variant_id',
        'quantity',
        'unit', // Unit of measurement for the movement
        'cost',
        'movement_type',
        'movement_date',
        'user_id',
        'reference',
        'source_warehouse_id',
        'destination_warehouse_id',
        'source',
        'destination',
        'status',
        'reason'
    ];

    public function itemVariant()
    {
        return $this->belongsTo(StockItemVariant::class, 'item_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
