<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobAttributeDetail extends Model
{
    protected $fillable = [
        'fjob_attribute_id',
        'job_attribute_detail_id',
        'detail_value',
        'remarks'
    ];
}
