<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\BalanceHistory;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')->get();
        $users = User::select('id', 'name')->get();
        return view('transactions.index', compact('transactions','users'));
    }

        public function store(Request $request)
        {
            // Check if all required fields are present
        if (!$request->filled(['type', 'amount'])) {
            return redirect()->back()->withInput()->with('error', 'Please fill in all required fields.');
        }

        // Check if amount is numeric
        if (!is_numeric($request->amount)) {
            return redirect()->back()->withInput()->with('error', 'Amount must be numeric.');
        }

        // Handle file upload for proof of top-up (if applicable)
        Log::info($request);
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('proofs', 'public'); // Adjust storage path as needed
        } else {
            $proofPath = null;
        }
        // Create a new transaction instance
        $transaction = new Transaction();
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->proof = "storage/".$proofPath;
        $transaction->user_id = auth()->id(); // Set the user_id directly
        $transaction->save();
    
        // Update balance history
        $balanceHistory = new BalanceHistory();
        $balanceHistory->user_id = auth()->id();
        $balanceHistory->transaction_id = $transaction->id;
        $balanceHistory->previous_balance = $this->getUserBalance(auth()->id()); // Get previous balance
        if ($request->type === 'topup') {
            $balanceHistory->new_balance = $balanceHistory->previous_balance + $transaction->amount; // Add to balance
        } elseif ($request->type === 'transaction') {
            $balanceHistory->new_balance = $balanceHistory->previous_balance - $transaction->amount; // Subtract from balance
        }
        $balanceHistory->save();
    
        // Redirect back with success message
        return redirect()->back()->with('success', 'Transaction successfully recorded.');
    }
    
    private function getUserBalance($userId)
    {
        // Get the latest balance history record for the user
        $latestBalanceHistory = BalanceHistory::where('user_id', $userId)
            ->latest('created_at')
            ->first();
    
        if ($latestBalanceHistory) {
            // If a balance history record exists, return the new balance
            return $latestBalanceHistory->new_balance;
        } else {
            // If no balance history record exists, assume the balance is 0
            return 0;
        }
    }
    
    
}
