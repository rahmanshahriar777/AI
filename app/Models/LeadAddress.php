<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAddress extends Model
{
    protected $fillable = [
        'lead_id',
        'contact_firstname',
        'contact_lastname',
        'contact_phone',
        'contact_mobile',
        'contact_email',
        'address',
        'county',
        'postcode',
        'country',
        'location',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
