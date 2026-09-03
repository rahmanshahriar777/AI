<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsToolCheckDetails extends Model
{
    protected $fillable = [
        'hs_tool_checkup_id',
        'hs_checklist_id',
        'hs_checklist_title',
        'value',
        'remarks',
    ];

    public function toolCheckup()
    {
        return $this->belongsTo(HsToolCheckup::class, 'hs_tool_checkup_id');
    }
}
