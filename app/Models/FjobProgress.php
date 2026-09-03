<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobProgress extends Model
{
    protected $table = 'fjob_progresses';

    protected $fillable = [
        'fjob_id',
        'job_title',
        'customer_id',
        'start_date',
        'end_date',
        'status',
        'progress_percent',
        'priority',
        'notes',
    ];

    // Relationships
    public function fjob()
    {
        return $this->belongsTo(Fjob::class, 'fjob_id');
    }
    public function tasks()
    {
        return $this->hasMany(FjobProgressTask::class, 'fjob_progress_id');
    }

    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
