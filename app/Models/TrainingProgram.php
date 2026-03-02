<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    protected $fillable = [
        'tenant_id', 'title', 'description', 'category', 'delivery_mode',
        'duration_hours', 'start_date', 'end_date', 'instructor',
        'max_participants', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function schedules()
    {
        return $this->hasMany(TrainingSchedule::class);
    }

    public function openSchedules()
    {
        return $this->hasMany(TrainingSchedule::class)->where('status', 'open');
    }
}