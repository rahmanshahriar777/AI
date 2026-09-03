<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationSectionAttachment extends Model
{
    protected $fillable = [
        'quotation_section_id',
        'attachment_name',
        'attachment_path',
        'attachment_url',
        'source',
    ];

    public function quotationSection()
    {
        return $this->belongsTo(QuotationSection::class);
    }
}
