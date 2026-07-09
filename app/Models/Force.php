<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Force extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function weaponBranches()
    {
        return $this->hasMany(WeaponBranch::class);
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            WeaponBranch::class,
            'force_id',
            'weapon_branch_id',
            'id',
            'id'
        );
    }
}
