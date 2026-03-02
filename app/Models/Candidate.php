<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'tenant_id', 'job_posting_id', 'first_name', 'last_name', 'email',
        'phone', 'resume_path', 'cover_letter', 'status', 'notes',
        'experience_years', 'current_salary', 'expected_salary',
        'interview_date', 'source',
    ];

    protected $casts = [
        'interview_date' => 'datetime',
        'current_salary' => 'decimal:2',
        'expected_salary' => 'decimal:2',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}