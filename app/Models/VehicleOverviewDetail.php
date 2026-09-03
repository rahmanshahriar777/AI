<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleOverviewDetail extends Model
{
    protected $fillable = [
        'vehicle_overview_id',
        'vehicle_check_categories_id',
        'vehicle_check_categories_child_id',
        'vehicle_check_checklists_id',
        'has_attribute',
        'vehicle_check_checklist_attributes_id',
        'overview_value',
        'notes',
    ];

    public function overview()
    {
        return $this->belongsTo(VehicleOverview::class, 'vehicle_overview_id');
    }
}
