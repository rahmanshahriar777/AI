<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCheckChecklist extends Model
{
    protected $fillable = ['vehicle_check_category_id', 'title', 'has_attributes', 'status'];   

    public function attributes()
    {
        return $this->hasMany(VehicleCheckChecklistAttribute::class, 'vehicle_check_checklist_id');
    }
}
