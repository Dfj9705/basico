<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_date',
        'user_id',
        'amount',
        'reference',
        'description',
        'divisible',
        'receipt',
        'force_id',
        'payment_receipt',
    ];

    protected $casts = [
        'divisible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function force()
    {
        return $this->belongsTo(Force::class);
    }

    public function splits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }
}
