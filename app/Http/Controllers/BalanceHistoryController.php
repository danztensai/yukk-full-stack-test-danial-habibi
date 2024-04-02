<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BalanceHistory;
use App\Models\User;


class BalanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        $search = $request->input('search');

        $balanceHistories = BalanceHistory::query();

        if ($userId) {
            $balanceHistories->where('user_id', $userId);
            $currentBalance = $this->getUserBalance($userId);
        }

        if ($search) {
            $balanceHistories->where(function ($query) use ($search) {
                $query->where('transaction_id', $search)
                    ->orWhereHas('transaction', function ($query) use ($search) {
                        $query->where('description', 'like', '%' . $search . '%')
                        ->orWhere('transaction_code', 'like', '%' . $search . '%');;
                    });
            });
        }

        $balanceHistories = $balanceHistories->orderBy('created_at','desc')->paginate(10);

        $users = User::all();
        
        return view('balance_history.index', [
             'balanceHistories' => $balanceHistories,
             'users' => $users,
             'currentBalance' =>$currentBalance,
         ]);
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
