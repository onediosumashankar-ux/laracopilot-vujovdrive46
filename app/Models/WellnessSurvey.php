<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellnessSurvey extends Model
{
    protected $fillable = [
        'tenant_id', 'title', 'description', 'questions',
        'start_date', 'end_date', 'anonymous', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'anonymous' => 'boolean',
    ];

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }
}