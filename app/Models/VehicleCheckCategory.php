<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

use Illuminate\Database\Eloquent\Model;

class VehicleCheckCategory extends Model
{
    use HasSlug;
    protected $fillable = ['name', 'is_parent', 'parent_id', 'description', 'status'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function parent()
    {
        return $this->belongsTo(VehicleCheckCategory::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasMany(VehicleCheckCategory::class, 'parent_id');
    }

    public function checklists()
    {
        return $this->hasMany(VehicleCheckChecklist::class, 'vehicle_check_category_id');
    }
}
