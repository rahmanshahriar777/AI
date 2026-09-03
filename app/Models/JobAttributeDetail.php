<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobAttributeDetail extends Model
{
    protected $fillable = [
        'job_attribute_id',
        'value',
        'description',
        'is_active',
    ];

    public function jobAttribute()
    {
        return $this->belongsTo(JobAttribute::class);
    }
}
