<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnboardingPlan extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'start_date', 'end_date',
        'buddy_name', 'notes', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function tasks()
    {
        return $this->hasMany(OnboardingTask::class);
    }
}