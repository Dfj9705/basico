<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftType extends Model
{
    protected $fillable = ['name', 'frequency', 'start_time', 'end_time', 'description'];

    public function assignments(): HasMany
    {
        return $this->hasMany(ShiftAssignment::class);
    }
}
