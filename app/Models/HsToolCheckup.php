<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsToolCheckup extends Model
{
    protected $table = 'hs_tool_checkups';

    protected $fillable = [
        'hs_tool_id',
        'tool_name',
        'checkup_name',
        'checkup_date',
        'performed_by',
        'remarks',
        'status',
        'next_checkup_date',
    ];

    public function hsTool()
    {
        return $this->belongsTo(HsTool::class, 'hs_tool_id');
    }

    public function checklist()
    {
        return $this->hasMany(HsToolCheckDetails::class, 'hs_tool_checkup_id');
    } 
}
