<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadImage extends Model
{
    protected $fillable = [
        'lead_id',
        'image_path',
        'image_name',
        'image_caption',
        'uploaded_by',
        'image_url',
        'source',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
