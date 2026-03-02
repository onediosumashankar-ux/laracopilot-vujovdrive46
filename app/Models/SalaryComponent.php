<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    protected $fillable = [
        'salary_structure_id', 'name', 'code', 'type',
        'calculation_type', 'value', 'formula',
        'taxable', 'pf_applicable', 'esi_applicable',
        'max_limit', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'value'          => 'decimal:2',
        'max_limit'      => 'decimal:2',
        'taxable'        => 'boolean',
        'pf_applicable'  => 'boolean',
        'esi_applicable' => 'boolean',
        'is_active'      => 'boolean',
    ];

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }
}