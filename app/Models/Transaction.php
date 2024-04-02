<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'description', 'proof',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Generate transaction code in the format 'trans-iddate'
            $transaction->transaction_code = 'trans-' . $transaction->id . '-' . Carbon::now()->format('YmdHis');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
