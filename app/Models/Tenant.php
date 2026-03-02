<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'domain', 'email', 'phone', 'address',
        'plan', 'status', 'office_lat', 'office_lng', 'logo',
    ];

    protected $casts = [
        'office_lat' => 'float',
        'office_lng' => 'float',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}