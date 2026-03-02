<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferLetter extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'candidate_id',
        'employee_salary_structure_id', 'offer_number',
        'position', 'department', 'joining_date', 'offer_expiry',
        'ctc_annual', 'employment_type', 'work_location',
        'custom_clauses', 'status', 'sent_at', 'responded_at', 'created_by',
    ];

    protected $casts = [
        'joining_date'  => 'date',
        'offer_expiry'  => 'date',
        'sent_at'       => 'datetime',
        'responded_at'  => 'datetime',
        'ctc_annual'    => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function employeeSalaryStructure()
    {
        return $this->belongsTo(EmployeeSalaryStructure::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}