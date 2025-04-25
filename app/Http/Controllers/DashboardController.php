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

        // Define start and end dates for the date range (e.g., current month)
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        // Recent transactions
        $transactions = Transaction::latest()->limit(5)->get();

        // Return the view with data
        return view('dashboard', compact('dailyIncome', 'dailyCustomers', 'totalIncome', 'startDate', 'endDate', 'transactions'));
    }
    public function getWeeklyProductData()
    {
        // Get weekly sales by product
        $weeklySales = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->select(
                'products.nama_barang as product_name',
                DB::raw('SUM(transaction_items.quantity) as total_sold'),
                DB::raw('SUM(transaction_items.quantity * transaction_items.price) as total_revenue')
            )
            ->whereBetween('transaction_items.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->groupBy('products.nama_barang')
            ->orderByDesc('total_sold')
            ->get();

        // Get daily sales for current week
        $dailySales = DB::table('transaction_items')
            ->select(
                DB::raw('DATE(transaction_items.created_at) as date'),
                DB::raw('SUM(transaction_items.quantity * transaction_items.price) as daily_revenue')
            )
            ->whereBetween('transaction_items.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'weeklySales' => [
                'labels' => $weeklySales->pluck('product_name'),
                'quantities' => $weeklySales->pluck('total_sold'),
                'revenues' => $weeklySales->pluck('total_revenue'),
            ],
            'dailySales' => [
                'labels' => $dailySales->pluck('date'),
                'values' => $dailySales->pluck('daily_revenue'),
            ]
        ]);
    }

}
