<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Customer extends Model
{
    use HasSlug;
    protected $fillable = [
        'company_name',
        'contact_firstname',
        'contact_lastname',
        'contact_phone',
        'contact_mobile',
        'contact_email',
        'status',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('company_name')
            ->saveSlugsTo('slug');
    }

    public function billingaddress()
    {
        return $this->hasOne(CustomerAddress::class, 'customer_id', 'id')->where('address_type', 'billing');
    }
    public function siteaddress()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id', 'id')->where('address_type', 'site');
    }
    public function findetails()
    {
        return $this->hasOne(CustomerDetails::class, 'customer_id', 'id');
    }
}
