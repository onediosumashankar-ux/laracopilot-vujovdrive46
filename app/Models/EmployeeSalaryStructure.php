<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryStructure extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'salary_structure_id',
        'ctc_override', 'effective_from', 'effective_to',
        'is_current', 'notes', 'created_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to'   => 'date',
        'is_current'     => 'boolean',
        'ctc_override'   => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    public function breakdowns()
    {
        return $this->hasMany(SalaryBreakdown::class);
    }

    public function offerLetters()
    {
        return $this->hasMany(OfferLetter::class);
    }

    /** Effective CTC = override or structure default */
    public function getEffectiveCtcAttribute(): float
    {
        return (float)($this->ctc_override ?? $this->salaryStructure->ctc_amount);
    }
}