<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'code', 'description',
        'type', 'ctc_amount', 'is_active',
    ];

    protected $casts = [
        'ctc_amount' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function components()
    {
        return $this->hasMany(SalaryComponent::class)->orderBy('sort_order');
    }

    public function activeComponents()
    {
        return $this->hasMany(SalaryComponent::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function employeeAssignments()
    {
        return $this->hasMany(EmployeeSalaryStructure::class);
    }
}