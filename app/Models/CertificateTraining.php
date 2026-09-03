<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTraining extends Model
{
    protected $table = 'certificate_trainings';

    protected $fillable = [
        'certificate_id',
        'user_id',
        'training_details',
        'start_date',
        'completion_date',
        'status',
        'remarks',
    ];

    public function courseCertificate()
    {
        return $this->belongsTo(CourseCertificate::class, 'certificate_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
