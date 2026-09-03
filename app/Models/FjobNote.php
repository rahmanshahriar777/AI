<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobNote extends Model
{
    protected $fillable = [
        'fjob_id',
        'note',
        'note_by',
        'note_type',
        'note_status',
    ];

    public function fjob()
    {
        return $this->belongsTo(Fjob::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'note_by');
    }
}
