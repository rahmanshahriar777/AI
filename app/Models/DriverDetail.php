<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverDetail extends Model
{
    protected $fillable = [
        'user_id',
        'license_no',
        'license_issue_date',
        'license_expiry',
        'license_category',
        'digital_tacho_card',
        'dbs_check_passed',
        'medical_check_passed',
        'last_medical_check_date',
        'next_medical_due_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function vehicleAssignDetails()
    {
        return $this->hasMany(VehicleAssignDetail::class, 'user_id');
    }
    public function vehicleAssigned()
    {
        return $this->hasOne(VehicleAssignDetail::class, 'user_id')->where('status', 'active');
    }

}
