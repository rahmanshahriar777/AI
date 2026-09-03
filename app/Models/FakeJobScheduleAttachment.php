<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FakeJobScheduleAttachment extends Model
{
    protected $fillable = [
        'fake_job_schedule_id',
        'attachment_name',
        'attachment_path',
        'attachment_type',
        'status',
    ];

    public function jobSchedule()
    {
        return $this->belongsTo(JobSchedule::class);
    }
}
