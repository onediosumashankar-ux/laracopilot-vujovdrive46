<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingEnrollment extends Model
{
    protected $fillable = [
        'employee_id', 'training_program_id', 'training_schedule_id',
        'status', 'completed_at', 'score',
        'reschedule_count', 'previous_schedule_id',
        'rescheduled_at', 'reschedule_reason', 'attendance_status',
    ];

    protected $casts = [
        'completed_at'   => 'datetime',
        'rescheduled_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function trainingSchedule()
    {
        return $this->belongsTo(TrainingSchedule::class);
    }

    public function previousSchedule()
    {
        return $this->belongsTo(TrainingSchedule::class, 'previous_schedule_id');
    }
}