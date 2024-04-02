<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Transaction;

class BalanceHistory extends Model
{
    protected $table = 'balance_history';
    protected $fillable = [
        'user_id', 'transaction_id', 'previous_balance', 'new_balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }
}

