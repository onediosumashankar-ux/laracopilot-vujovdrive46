<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'tenant_id', 'branch_id', 'first_name', 'last_name',
        'email', 'phone', 'department', 'position',
        'employment_type', 'status', 'salary',
        'hire_date', 'date_of_birth', 'gender',
        'address', 'bank_account', 'tax_id', 'manager',
    ];

    protected $casts = [
        'hire_date'     => 'date',
        'date_of_birth' => 'date',
        'salary'        => 'decimal:2',
    ];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function trainingEnrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function salaryStructures()
    {
        return $this->hasMany(EmployeeSalaryStructure::class);
    }

    public function currentSalaryStructure()
    {
        return $this->hasOne(EmployeeSalaryStructure::class)->where('is_current', true)->latest();
    }
}