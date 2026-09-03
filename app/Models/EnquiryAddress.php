<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryAddress extends Model
{
    protected $fillable = [
        'enquiry_id',
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
}
