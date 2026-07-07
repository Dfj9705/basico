<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeaponBranch extends Model
{
    protected $fillable = ['name', 'is_active', 'order'];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
}
