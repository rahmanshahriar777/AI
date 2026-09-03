<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = [
        'customer_id',
        'contact_firstname',
        'contact_lastname',
        'contact_phone',
        'contact_email',
        'address',
        'county',
        'postcode',
        'country',
        'location',
        'address_type'
    ];
}
