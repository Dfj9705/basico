<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBoxMovement extends Model
{
    protected $fillable = [
        'force_id',
        'contribution_id',
        'quantity',
        'type',
        'observation',
        'user_id',
    ];

    public function force()
    {
        return $this->belongsTo(Force::class);
    }

    public function contribution()
    {
        return $this->belongsTo(Contribution::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
