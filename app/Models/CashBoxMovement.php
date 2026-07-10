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
        'expense_split_id',
        'expense_id',
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

    public static function getTotalByForce($force_id)
    {
        $ingresos = self::where('force_id', $force_id)->whereIn('type', ['ingreso', 'transferencia'])->sum('quantity');
        $egresos = self::where('force_id', $force_id)->whereIn('type', ['egreso'])->sum('quantity');
        return $ingresos - $egresos;
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function expenseSplit()
    {
        return $this->belongsTo(ExpenseSplit::class);
    }
}
