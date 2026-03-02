<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TdsDeclaration extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'financial_year', 'tax_regime',
        'residential_status', 'is_senior_citizen', 'is_super_senior',
        'hra_exemption', 'lta_exemption',
        'section_80c', 'section_80ccd1b', 'section_80d', 'section_80dd',
        'section_80e', 'section_80g', 'section_80tta', 'section_24b',
        'other_deductions',
        'hra_actual', 'basic_salary_annual', 'rent_paid_annual', 'metro_city',
        'gross_annual_income', 'total_exemptions', 'total_deductions',
        'taxable_income', 'annual_tax', 'surcharge', 'health_education_cess',
        'total_tax_liability', 'monthly_tds', 'tds_already_deducted', 'notes',
    ];

    protected $casts = [
        'is_senior_citizen'  => 'boolean',
        'is_super_senior'    => 'boolean',
        'metro_city'         => 'boolean',
        'section_80c'        => 'decimal:2',
        'section_80d'        => 'decimal:2',
        'annual_tax'         => 'decimal:2',
        'monthly_tds'        => 'decimal:2',
        'total_tax_liability'=> 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tdsDeductions()
    {
        return $this->hasMany(TdsDeduction::class, 'employee_id', 'employee_id')
            ->where('financial_year', $this->financial_year);
    }
}