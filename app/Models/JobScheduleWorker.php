<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobScheduleWorker extends Model
{
    protected $fillable = [
        'job_schedule_id',
        'user_id',
        'assigned_date',
        'completion_date',
        'status',
        'notes',
    ];

    public function jobSchedule()
    {
        return $this->belongsTo(JobSchedule::class);
    }

    public function worker() // if you want to reference the user
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
