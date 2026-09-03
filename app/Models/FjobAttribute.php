<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobAttribute extends Model
{
    protected $fillable = [
        'fjob_id',
        'job_attribute_id',
        'attribute_value',
        'remarks'
    ];

    public function details(){
        return $this->hasMany(FjobAttributeDetail::class);
    }
}
