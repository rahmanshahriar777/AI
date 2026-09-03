<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationTemplateSectionSample extends Model
{
    protected $fillable = [
        'quotation_template_section_id',
        'sample_type',
        'sample_source',
    ];

    public function quotationTemplateSection()
    {
        return $this->belongsTo(QuotationTemplateSection::class);
    }
}
