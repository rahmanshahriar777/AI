<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryImage extends Model
{
    protected $fillable = [
        'enquiry_id',
        'image_path',
        'image_name',
        'image_caption',
        'image_url',
        'source'
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }
}
