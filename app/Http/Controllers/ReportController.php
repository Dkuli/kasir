<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;


use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
// Ensure this is imported


class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        // Validate date range and optional parameters
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_name' => 'nullable|string|max:255',
            'transaction_code' => 'nullable|string|max:255',
        ]);

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        // Start building the query
        $query = Transaction::with('products')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Apply filters
        if ($request->filled('product_name')) {
            $query->whereHas('products', function($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->input('product_name') . '%');
            });
        }

        if ($request->filled('transaction_code')) {
            $query->where('kode_transaksi', 'like', '%' . $request->input('transaction_code') . '%');
        }

        $transactions = $query->get();

        // Generate PDF report
        $pdf = PDF::loadView('reports.sales', compact('transactions', 'startDate', 'endDate'));

        return $pdf->download('sales_report_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.pdf');
    }

    public function showReportForm()
    {
        return view('reports.form');
    }
}
