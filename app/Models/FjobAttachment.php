<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobAttachment extends Model
{
    protected $fillable = [
        'fjob_id',
        'attachment_name',
        'description',
        'attachment_path',
        'attachment_url',
        'attachment_type',
        'source',
        'status',
        'uploaded_by'
    ];

    public function fjob()
    {
        return $this->belongsTo(Fjob::class);
    }
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
