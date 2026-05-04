<?php

namespace App\Exports;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class LowStockExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithMapping
{
    protected $medicines;

    public function __construct(string $startDate = null, string $endDate = null)
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfYear();
        $end   = $endDate   ? Carbon::parse($endDate)->endOfDay()     : Carbon::now()->endOfYear();

        $this->medicines = Medicine::with([
            'batches'    => fn($q) => $q->where('expiry_date', '>', now())->orderBy('expiry_date', 'asc'),
            'allBatches' => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])
        ->where('stock_status', 'Low Stock')
        ->whereBetween('updated_at', [$start, $end])
        ->orderBy('stock', 'asc')
        ->get()
        ->map(function ($medicine) {
            $medicine->available_stock = $medicine->batches
                ->sum(fn($b) => max(0, $b->quantity - $b->reserved_quantity));
            $fifo = $medicine->batches->first();
            $medicine->fifo_expiry_date = $fifo?->expiry_date;
            $last = $medicine->allBatches->last();
            if ($last) {
                $days = now()->diffInDays($last->expiry_date, false);
                $medicine->computed_expiry_status = $days < 0 ? 'Expired' : ($days <= 30 ? 'Expiring Soon' : 'Valid');
            } else {
                $medicine->computed_expiry_status = 'N/A';

            }
            $medicine->batch_count = $medicine->allBatches->count();
            return $medicine;
        });
    }

    public function collection()
    {
        return $this->medicines;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Medicine Name',
            'Dosage',
            'Available Stock',
            'Current Batch Expiry',
            'Expiry Status',
        ];
    }

    public function map($m): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $m->medicine_name,
            $m->dosage,
            $m->available_stock,
            $m->fifo_expiry_date
                ? Carbon::parse($m->fifo_expiry_date)->format('M d, Y')
                : 'N/A',
            $m->computed_expiry_status,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 28,
            'C' => 15,
            'D' => 16,
            'E' => 22,
            'F' => 16,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $last = $this->medicines->count() + 1;

        // Header row
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4CAF50'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Alternating row shading
        for ($row = 2; $row <= $last; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF9F9F9'],
                    ],
                ]);
            }
        }

        // Centre-align numeric/status columns
        $sheet->getStyle('A1:A' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D1:G' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}