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
         ]);
    }
}
