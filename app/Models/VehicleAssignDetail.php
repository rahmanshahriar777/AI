<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleAssignDetail extends Model
{
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'assigned_date',
        'unassigned_date',
        'status',
        'notes'
    ];

    public function vehicle()
    {
        return $this->belongsTo(VehicleDetail::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
