<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Enquiry extends Model
{
    use HasSlug;
    protected $fillable = [
        'enquiry_name',
        'enquiry',
        'enquiry_description',
        'customer_id',
        'enquiry_type',
        'enquiry_category',
        'enquiry_priority',
        'enquiry_source',
        'enquiry_status',
        'enquiry_closed_by',
        'enquiry_closed_at',
        'enquiry_close_reason',
        'annual_maintenance',
        'installations',
        'repairs',
        'testing'
    ];
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('enquiry_name')
            ->saveSlugsTo('slug');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function enquiryaddress()
    {
        return $this->hasOne(EnquiryAddress::class);
    }
    public function enquiryimages()
    {
        return $this->hasMany(EnquiryImage::class);
    }

    public function enquirynotes()
    {
        return $this->hasMany(EnquiryNote::class);
    }

}
