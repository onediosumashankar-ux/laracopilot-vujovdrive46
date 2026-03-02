<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'type', 'description',
        'amount', 'frequency', 'eligibility', 'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}