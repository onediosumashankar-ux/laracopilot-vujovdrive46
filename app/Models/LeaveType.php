<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = ['tenant_id', 'name', 'days_allowed', 'carry_forward', 'paid'];

    protected $casts = [
        'carry_forward' => 'boolean',
        'paid' => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}