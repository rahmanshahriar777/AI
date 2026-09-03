<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HsChecklistAttribute extends Model
{
    protected $fillable = [
        'hs_checklist_id',
        'title',
        'short_details',
        'status',
    ];
}
