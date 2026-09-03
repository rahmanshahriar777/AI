<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationSectionContent extends Model
{
    protected $fillable = [
        'quotation_section_id',
        'sample_type',
        'sample_source',
    ];

    public function quotationSection()
    {
        return $this->belongsTo(QuotationSection::class);
    }
}
