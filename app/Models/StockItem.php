<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class StockItem extends Model
{
    use HasSlug;
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'is_active',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function category()
    {
        return $this->belongsTo(StockCategory::class, 'category_id');
    }

    public function attributes()
    {
        return $this->hasMany(StockItemAttribute::class, 'stock_item_id');
    }
    public function variants()
    {
        return $this->hasMany(StockItemVariant::class, 'stock_item_id');
    }

}
