<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealAttendance extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'date',
        'breakfast',
        'lunch',
        'dinner',
    ];

    protected $casts = [
        'week_start' => 'date',
        'date' => 'date',
        'breakfast' => 'boolean',
        'lunch' => 'boolean',
        'dinner' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
