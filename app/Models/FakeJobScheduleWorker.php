<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakeJobScheduleWorker extends Model
{
    protected $fillable = [
        'fake_job_schedule_id',
        'user_id',
        'assigned_date',
        'completion_date',
        'status',
        'notes',
    ];

    public function fakeJobSchedule()
    {
        return $this->belongsTo(FakeJobSchedule::class);
    }

    public function worker() // if you want to reference the user
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
