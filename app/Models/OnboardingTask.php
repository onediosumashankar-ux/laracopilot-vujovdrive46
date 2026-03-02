<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingTask extends Model
{
    protected $fillable = ['onboarding_plan_id', 'title', 'due_date', 'assigned_to', 'status'];

    protected $casts = ['due_date' => 'date'];

    public function onboardingPlan()
    {
        return $this->belongsTo(OnboardingPlan::class);
    }
}