<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAttachment extends Model
{
    protected $fillable = [
        'lead_id',
        'attachment_name',
        'description',
        'attachment_path',
        'attachment_url',
        'attachment_type',
        'source',
        'status',
        'uploaded_by'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
