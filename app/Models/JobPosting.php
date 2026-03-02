<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $fillable = [
        'tenant_id', 'title', 'department', 'location', 'type',
        'description', 'requirements', 'salary_min', 'salary_max',
        'deadline', 'status', 'vacancies',
    ];

    protected $casts = [
        'deadline' => 'date',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}