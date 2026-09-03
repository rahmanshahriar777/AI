<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsTool extends Model
{
    protected $table = 'hs_tools';

    protected $fillable = [
        'tool_name',
        'slug',
        'description',
        'status',
    ];

    public function checkups()
    {
        return $this->hasMany(HsToolCheckup::class, 'hs_tool_id');
    }
}
