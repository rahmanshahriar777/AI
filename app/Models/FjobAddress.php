<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobAddress extends Model
{
    protected $fillable = [
        'fjob_id',
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
        'type'
    ];
    public function fjob()
    {
        return $this->belongsTo(Fjob::class);
    }
}
