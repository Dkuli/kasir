<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Daily income for today
        $dailyIncome = Transaction::whereDate('created_at', Carbon::today())->sum('total_harga');

        // Daily customer count
        $dailyCustomers = Transaction::whereDate('created_at', Carbon::today())->count();

        // Total income
        $totalIncome = Transaction::sum('total_harga');

        // Set date range for total income display (for example: current year)
        $dateRange = '2024';

        // Recent transactions
        $transactions = Transaction::latest()->limit(5)->get();

        // Return the view with data
        return view('dashboard', compact('dailyIncome', 'dailyCustomers', 'totalIncome', 'dateRange', 'transactions'));
    }
}
