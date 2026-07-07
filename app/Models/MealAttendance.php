<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealAttendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'breakfast',
        'lunch',
        'dinner',
    ];

    protected $casts = [
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
