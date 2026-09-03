<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class QuotationSection extends Model
{
    use HasSlug;
    protected $fillable = [
        'quotation_id',
        'section_name',
        'section_type',
        'has_attachments',
        'order',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('section_name')
            ->saveSlugsTo('section_slug');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function attachments()
    {
        return $this->hasMany(QuotationSectionAttachment::class);
    }

    public function content()
    {
        return $this->hasOne(QuotationSectionContent::class);
    }
}
