<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobImage extends Model
{
    protected $fillable = [
        'fjob_id',
        'image_name',
        'image_path',
        'image_url',
        'source',
        'uploaded_by'
    ];

    public function fjob()
    {
        return $this->belongsTo(Fjob::class);
    }
}
