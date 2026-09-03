<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobProgressTask extends Model
{
    protected $table = 'fjob_progress_tasks';

    protected $fillable = [
        'fjob_progress_id',
        'task_name',
        'description',
        'start_date',
        'end_date',
        'status',
        'progress_percent',
        'assigned_to',
        'parent_task_id',
        'dependency_task_id',
        'duration',
        'planned_hours',
        'actual_hours',
    ];

    // Relationships
    public function fjobProgress()
    {
        return $this->belongsTo(FjobProgress::class, 'fjob_progress_id');
    }

    public function resource(){
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
