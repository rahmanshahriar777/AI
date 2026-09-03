<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockWarehouse extends Model
{
    protected $fillable = [
        'name',
        'location',
        'description',
        'is_active',
    ];
}
