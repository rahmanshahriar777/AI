<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class QuotationTemplate extends Model
{
    use HasSlug;
    protected $fillable = [
        'job_type_id',
        'job_type_name',
        'template_name',
        'status',
    ];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('template_name')
            ->saveSlugsTo('template_slug');
    }

    public function jobType()
    {
        return $this->belongsTo(JobType::class, 'job_type_id');
    }
    public function sections()
    {
        return $this->hasMany(QuotationTemplateSection::class, 'quotation_template_id');
    }
}
