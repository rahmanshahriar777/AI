<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleOverview extends Model
{
    protected $fillable = [
        'vehicle_id',
        'overview_date',
        'mileage',
        'next_overview_date',
        'notes',
    ];

    public function details()
    {
        return $this->hasMany(VehicleOverviewDetail::class, 'vehicle_overview_id');
    }
}
