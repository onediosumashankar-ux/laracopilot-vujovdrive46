<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $fillable = ['employee_id', 'wellness_survey_id', 'responses', 'submitted_at'];

    protected $casts = ['submitted_at' => 'datetime'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function wellnessSurvey()
    {
        return $this->belongsTo(WellnessSurvey::class);
    }
}