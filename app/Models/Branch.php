<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'code', 'address', 'city',
        'state', 'pincode', 'country', 'phone', 'email',
        'manager_name', 'is_head_office', 'is_active',
    ];

    protected $casts = [
        'is_head_office' => 'boolean',
        'is_active'      => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->city, $this->state, $this->pincode, $this->country])
            ->filter()
            ->implode(', ');
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->where('status', 'active')->count();
    }
}