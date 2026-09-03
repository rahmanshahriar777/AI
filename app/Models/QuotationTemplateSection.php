<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class QuotationTemplateSection extends Model
{
    use HasSlug;
    protected $fillable = [
        'quotation_template_id',
        'section_name',
        'section_type',
        'is_required',
        'has_attachments',
        'attachment_type',
        'order',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('section_name')
            ->saveSlugsTo('section_slug');
    }

    public function quotationTemplate()
    {
        return $this->belongsTo(QuotationTemplate::class, 'quotation_template_id');
    }

    public function samples()
    {
        return $this->hasMany(QuotationTemplateSectionSample::class, 'quotation_template_section_id');
    }
    public function attachments()
    {
        return $this->hasMany(QuotationTemplateSectionAttchment::class, 'quotation_template_section_id');
    }
}
