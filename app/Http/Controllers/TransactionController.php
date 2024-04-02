<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\BalanceHistory;
use App\Http\Controllers\BalanceHistoryController;

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
        if (!$request->filled(['type', 'amount'])) {
            return redirect()->back()->withInput()->with('error', 'Please fill in all required fields.');
        }

        if (!is_numeric($request->amount)) {
            return redirect()->back()->withInput()->with('error', 'Amount must be numeric.');
        }

        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('proofs', 'public'); 
        } else {
            $proofPath = null;
        }

        $transaction = new Transaction();
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->proof = "storage/".$proofPath;
        $transaction->user_id = $request->user_id; 
        $transaction->save();
    
        $balanceHistory = new BalanceHistory();
        $balanceHistory->user_id = $request->user_id;
        $balanceHistory->transaction_id = $transaction->id;
        $balanceHistory->previous_balance = $this->getUserBalance($request->user_id); // Get previous balance
        if ($request->type === 'topup') {
            $balanceHistory->new_balance = $balanceHistory->previous_balance + $transaction->amount; // Add to balance
        } elseif ($request->type === 'transaction') {
            $balanceHistory->new_balance = $balanceHistory->previous_balance - $transaction->amount; // Subtract from balance
        }
        $balanceHistory->save();
        return redirect()->back()->with('success', 'Transaction successfully recorded.');
    }
    
    private function getUserBalance($userId)
    {

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
