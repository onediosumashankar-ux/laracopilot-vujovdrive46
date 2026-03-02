<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'reviewer_id', 'review_period',
        'review_date', 'overall_rating', 'quality_of_work', 'productivity',
        'teamwork', 'communication', 'attendance_rating',
        'goals_achieved', 'strengths', 'areas_for_improvement',
        'feedback', 'status',
    ];

    protected $casts = [
        'review_date' => 'date',
        'overall_rating' => 'float',
        'quality_of_work' => 'float',
        'productivity' => 'float',
        'teamwork' => 'float',
        'communication' => 'float',
        'attendance_rating' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}