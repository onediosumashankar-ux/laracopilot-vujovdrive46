<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'date', 'type', 'description', 'recurring',
    ];

    protected $casts = [
        'date' => 'date',
        'recurring' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}