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

        // Query for balance histories
        $balanceHistories = BalanceHistory::query();

        // Filter by user ID if selected
        if ($userId) {
            $balanceHistories->where('user_id', $userId);
        }

        // Apply search criteria
        if ($search) {
            $balanceHistories->where(function ($query) use ($search) {
                $query->where('transaction_id', $search)
                    ->orWhereHas('transaction', function ($query) use ($search) {
                        $query->where('description', 'like', '%' . $search . '%');
                    });
            });
        }

        // Paginate the results
        $balanceHistories = $balanceHistories->paginate(10);

        // Load the view with data
         // Fetch users for dropdown
         $users = User::all();

         // Load the view with data
         return view('balance_history.index', [
             'balanceHistories' => $balanceHistories,
             'users' => $users, // Pass users to the view
         ]);
    }
}
