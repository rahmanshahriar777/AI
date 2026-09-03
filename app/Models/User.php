<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jobSchedules()
    {
        return $this->belongsToMany(JobSchedule::class, 'job_schedule_workers', 'user_id', 'job_schedule_id')
            ->withPivot('assigned_date', 'completion_date', 'status', 'notes')
            ->withTimestamps();
    }

    public function fakeJobSchedules()
    {
        return $this->belongsToMany(FakeJobSchedule::class, 'fake_job_schedule_workers', 'user_id', 'fake_job_schedule_id')
            ->withPivot('assigned_date', 'completion_date', 'status', 'notes')
            ->withTimestamps();
    }
}
