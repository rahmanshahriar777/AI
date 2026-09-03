<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCertificate extends Model
{
    protected $table = 'course_certificates';

    protected $fillable = [
        'certificate_name',
        'description',
        'authorized_by',
        'validity_period',
        'status',
    ];
}
