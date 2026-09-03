<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAttribute extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'data_type',
    ];
}
