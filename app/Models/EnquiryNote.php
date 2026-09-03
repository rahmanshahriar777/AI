<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryNote extends Model
{
    protected $fillable = [
        'enquiry_id',
        'note',
        'note_by',
        'note_type',
        'note_status'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'note_by');
    }
}
