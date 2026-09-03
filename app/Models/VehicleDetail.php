<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDetail extends Model
{
    protected $fillable = [
        'category_id',
        'registration_no',
        'serial_no',
        'vin_number',
        'mechanical_code',
        'electronic_code',
        'radio_code',
        'deadlock_key_duplication_codes',
        'manufacturer',
        'model',
        'engine',
        'color',
        'year',
        'fuel_type',
        'mileage',
        'tyre_size_front',
        'tyre_size_rear',
        'mot_expiry_date',
        'tax_expiry_date',
        'insurance_expiry_date',
        'loler_expire_date',
        'last_service_date',
        'next_service_date',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }
    public function vehicleAssignDetail()
    {
        return $this->hasMany(VehicleAssignDetail::class, 'vehicle_id');
    }
}
