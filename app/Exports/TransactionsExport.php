<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithCustomStartCell, WithEvents
{
    use Exportable;

    protected $request;
    protected $rowNumber = 1;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = Transaction::query()->with(['user', 'products']);

        if ($this->request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->request->start_date)->startOfDay(),
                Carbon::parse($this->request->end_date)->endOfDay()
            ]);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No.',
            'Transaction Code',
            'User',
            'Total (Rp)',
            'Date',
            'Products'
        ];
    }

    public function map($transaction): array
    {
        return [
            $this->rowNumber++,
            $transaction->kode_transaksi,
            $transaction->user->name,
            number_format($transaction->total_harga, 0, ',', '.'),
            $transaction->created_at->format('d M Y H:i'),
            $transaction->products->pluck('nama_barang')->implode(', ')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Style for title
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']]
        ]);

        // Style for summary section
        $sheet->getStyle('A3:C8')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle('A3:B3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']],
        ]);

        // Style for top products section
        $sheet->getStyle('E3:F8')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle('E3:F3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']],
        ]);

        // Style for headers
        $sheet->getStyle('A10:F10')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Style for data cells
        $sheet->getStyle('A11:F'.$lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Zebra striping
        for ($row = 11; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A'.$row.':'.$lastColumn.$row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
                ]);
            }
        }

        // Auto-filter
        $sheet->setAutoFilter('A10:F'.$lastRow);

        // Set text wrap for product column
        $sheet->getStyle('F11:F'.$lastRow)->getAlignment()->setWrapText(true);

        return [
            10 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 30,
            'D' => 15,
            'E' => 20,
            'F' => 50,
        ];
    }

    public function startCell(): string
    {
        return 'A10';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Add title
                $sheet->setCellValue('A1', 'Transaction Report');

                // Add date range info
                if ($this->request->filled(['start_date', 'end_date'])) {
                    $sheet->setCellValue('A2', 'Date Range: ' . Carbon::parse($this->request->start_date)->format('d M Y') . ' - ' . Carbon::parse($this->request->end_date)->format('d M Y'));
                }

                // Add summary information
                $totalRevenue = $this->query()->sum('total_harga');
                $totalTransactions = $this->query()->count();
                $averageTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

                $sheet->setCellValue('A3', 'Summary Statistics');
                $sheet->setCellValue('A4', 'Total Revenue:');
                $sheet->setCellValue('B4', 'Rp ' . number_format($totalRevenue, 0, ',', '.'));
                $sheet->setCellValue('A5', 'Total Transactions:');
                $sheet->setCellValue('B5', $totalTransactions);
                $sheet->setCellValue('A6', 'Average Transaction Value:');
                $sheet->setCellValue('B6', 'Rp ' . number_format($averageTransactionValue, 0, ',', '.'));

                // Add top products
                $topProducts = Product::select('products.nama_barang', DB::raw('SUM(transaction_items.quantity) as total_sold'))
                    ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
                    ->groupBy('products.id', 'products.nama_barang')
                    ->orderByDesc('total_sold')
                    ->limit(5)
                    ->get();

                $sheet->setCellValue('E3', 'Top 5 Products');
                $sheet->setCellValue('E4', 'Product Name');
                $sheet->setCellValue('F4', 'Total Sold');

                $row = 5;
                foreach ($topProducts as $product) {
                    $sheet->setCellValue('E' . $row, $product->nama_barang);
                    $sheet->setCellValue('F' . $row, $product->total_sold);
                    $row++;
                }

                // Freeze panes
                $sheet->freezePane('A11');

                // Set print area
                $sheet->getPageSetup()->setPrintArea('A1:F' . $sheet->getHighestRow());

                // Set to landscape orientation
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                // Fit to 1 page wide by infinite pages tall
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}
