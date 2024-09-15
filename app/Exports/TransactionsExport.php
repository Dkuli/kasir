<?php

namespace App\Exports;

use App\Models\Transaction;
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

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithCustomStartCell
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
            'Transaction Code',
            'User',
            'Total (Rp)',
            'Date',
            'Products'
        ];
    }

    public function map($transaction): array
    {
        $this->rowNumber++;
        return [
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
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Style for headers
        $sheet->getStyle('A5:E5')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Style for data cells
        $sheet->getStyle('A6:E'.$lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Zebra striping
        for ($row = 6; $row <= $lastRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A'.$row.':'.$lastColumn.$row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F3F3']],
                ]);
            }
        }

        // Auto-filter
        $sheet->setAutoFilter('A5:E'.$lastRow);

        return [
            5 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 15,
            'D' => 20,
            'E' => 50,
        ];
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Add title
                $sheet->setCellValue('A1', 'Transaction Report');

                // Add summary information
                $totalRevenue = $this->query()->sum('total_harga');
                $totalTransactions = $this->query()->count();
                $averageTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

                $sheet->setCellValue('A2', 'Total Revenue: Rp ' . number_format($totalRevenue, 0, ',', '.'));
                $sheet->setCellValue('A3', 'Total Transactions: ' . $totalTransactions);
                $sheet->setCellValue('A4', 'Average Transaction Value: Rp ' . number_format($averageTransactionValue, 0, ',', '.'));

                // Freeze panes
                $sheet->freezePane('A6');
            },
        ];
    }
}
