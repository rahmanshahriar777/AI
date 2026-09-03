<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobQuotation extends Model
{
    protected $fillable = [
        'fjob_id',
        'quotation_id',
        'quotation_number',
        'quotation_date',
        'valid_until',
        'total_amount',
        'status'
    ];

    public function fjob()
    {
        return $this->belongsTo(Fjob::class);
    }
}
