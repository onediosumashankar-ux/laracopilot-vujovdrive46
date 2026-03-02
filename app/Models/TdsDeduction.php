<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TdsDeduction extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'payroll_id', 'financial_year',
        'month', 'year', 'gross_salary', 'taxable_income_monthly',
        'tds_amount', 'surcharge', 'cess', 'total_tds',
        'status', 'deduction_date', 'challan_number',
    ];

    protected $casts = [
        'deduction_date' => 'date',
        'tds_amount'     => 'decimal:2',
        'total_tds'      => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}