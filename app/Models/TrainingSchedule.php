<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    protected $fillable = [
        'training_program_id', 'label', 'delivery_mode',
        'start_date', 'end_date', 'start_time', 'end_time',
        'days_of_week', 'venue', 'instructor',
        'max_seats', 'booked_seats', 'status', 'notes',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
    ];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->max_seats - $this->booked_seats);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->booked_seats >= $this->max_seats;
    }

    public function getDaysLabelAttribute(): string
    {
        return match($this->days_of_week) {
            'mon_wed_fri' => 'Mon / Wed / Fri',
            'tue_thu'     => 'Tue / Thu',
            'weekdays'    => 'Mon – Fri',
            'weekends'    => 'Sat – Sun',
            default       => ucfirst($this->days_of_week),
        };
    }
}