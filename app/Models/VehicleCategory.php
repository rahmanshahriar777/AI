<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'is_active'
    ];

    public function vehicles()
    {
        return $this->hasMany(VehicleDetail::class, 'category_id');
    }
}
