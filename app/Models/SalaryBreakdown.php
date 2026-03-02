<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryBreakdown extends Model
{
    protected $fillable = [
        'employee_salary_structure_id', 'salary_component_id',
        'component_name', 'component_code', 'type',
        'monthly_amount', 'annual_amount', 'taxable',
    ];

    protected $casts = [
        'monthly_amount' => 'decimal:2',
        'annual_amount'  => 'decimal:2',
        'taxable'        => 'boolean',
    ];

    public function employeeSalaryStructure()
    {
        return $this->belongsTo(EmployeeSalaryStructure::class);
    }

    public function salaryComponent()
    {
        return $this->belongsTo(SalaryComponent::class);
    }
}