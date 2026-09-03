<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'lead_id',
        'customer_id',
        'job_type_id',
        'quotation_version',
        'cover_letter',
        'quotation_template_id',
        'quotation_date',
        'total_amount',
        'remarks',
        'valid_until',
        'status'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function quotationTemplate()
    {
        return $this->belongsTo(QuotationTemplate::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function jobType()
    {
        return $this->belongsTo(JobType::class);
    }

    public function sections()
    {
        return $this->hasMany(QuotationSection::class);
    }
}
