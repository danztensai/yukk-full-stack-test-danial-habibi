<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        // Generate dummy transactions
        Transaction::create([
            'user_id' => 1,
            'type' => 'Top Up',
            'amount' => 100.00,
            'description' => 'Initial top-up',
            'proof' => 'https://example.com/proof1.png', // Replace with actual proof URL
        ]);

        Transaction::create([
            'user_id' => 1,
            'type' => 'Regular',
            'amount' => 50.00,
            'description' => 'Regular transaction',
            'proof' => null, // No proof available
        ]);

        // Add more transactions as needed
    }
}
