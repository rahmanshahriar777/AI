<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobAttribute extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function attributedetails()
    {
        return $this->hasMany(JobAttributeDetail::class, 'job_attribute_id');
    }
}
