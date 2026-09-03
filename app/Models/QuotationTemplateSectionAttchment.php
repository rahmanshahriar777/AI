<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationTemplateSectionAttchment extends Model
{
    protected $fillable = [
        'quotation_template_section_id',
        'attachment_name',
        'attachment_path',
        'attachment_type',
    ];

    public function quotationTemplateSection()
    {
        return $this->belongsTo(QuotationTemplateSection::class);
    }
}
