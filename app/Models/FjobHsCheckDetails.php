<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FjobHsCheckDetails extends Model
{
    protected $table = 'fjob_hs_check_details';

    protected $fillable = [
        'fjob_hs_check_id',
        'hs_checklist_id',
        'hs_checklist_title',
        'value',
        'remarks',
    ];

    public function fjobHsCheck()
    {
        return $this->belongsTo(FjobHsCheck::class, 'fjob_hs_check_id');
    }

    public function checklist()
    {
        return $this->belongsTo(HsChecklist::class, 'hs_checklist_id');
    }
}
