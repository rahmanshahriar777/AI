<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Lead extends Model
{
    use HasSlug;
    protected $fillable = [
        'enquiry_id',
        'enquiry',
        'lead_name',
        'lead_title',
        'lead_description',
        'customer_id',
        'lead_type',
        'lead_category',
        'lead_priority',
        'lead_status',
        'lead_close_reason',
        'annual_maintenance',
        'installations',
        'repairs',
        'testing',
        'assigned_to'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('lead_name')
            ->saveSlugsTo('slug');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function leadaddress()
    {
        return $this->hasOne(LeadAddress::class);
    }

    public function leadimages()
    {
        return $this->hasMany(LeadImage::class);
    }

    public function leadnotes()
    {
        return $this->hasMany(LeadNote::class);
    }

    public function leadattachments()
    {
        return $this->hasMany(LeadAttachment::class);
    }

    public function completedquotation()
    {
        return $this->hasOne(Quotation::class)->where('status', 'accepted');
    }
}
