<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakeJobSchedule extends Model
{
    protected $fillable = [
        'job_id',
        'job_name',
        'job_title',
        'start_date',
        'end_date',
        'instructions',
        'status',
    ];

    public function job()
    {
        return $this->belongsTo(Fjob::class, 'job_id');
    }

    public function workers()
    {
        return $this->belongsToMany(User::class, 'fake_job_schedule_workers', 'fake_job_schedule_id', 'user_id')
            ->withPivot('assigned_date', 'completion_date', 'status', 'notes')
            ->withTimestamps();
    }

    public function attachments()
    {
        return $this->hasMany(JobScheduleAttachment::class);
    }
}
