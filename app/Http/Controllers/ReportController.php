<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['products', 'user']);

        // Apply date range filter
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);

        $transactions = $query->paginate(15);

        // Calculate summary statistics
        $totalRevenue = $query->sum('total_harga');
        $totalTransactions = $query->count();
        $averageTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Get top selling products
        $topProducts = Product::select('products.nama_barang', DB::raw('SUM(transaction_items.quantity) as total_sold'))
        ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
        ->groupBy('products.id', 'products.nama_barang')
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();

        // Get daily sales for chart
        $dailySales = Transaction::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_harga) as total_sales'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.index', compact('transactions', 'totalRevenue', 'totalTransactions', 'averageTransactionValue', 'topProducts', 'dailySales'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new TransactionsExport($request), 'transactions.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Transaction::with(['products', 'user']);

        // Apply filters (same as in index)
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->get();

        $totalRevenue = $transactions->sum('total_harga');
        $totalTransactions = $transactions->count();
        $averageTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Fetch top-selling products and daily sales
        $topProducts = Product::select('products.nama_barang', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->groupBy('products.id', 'products.nama_barang')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $dailySales = Transaction::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_harga) as total_sales'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $pdf = PDF::loadView('reports.pdf', compact('transactions', 'totalRevenue', 'totalTransactions', 'averageTransactionValue', 'dailySales', 'topProducts'))
                  ->setPaper('A4', 'portrait'); // Adjust paper size

        return $pdf->download('transactions_report.pdf');
    }


}

