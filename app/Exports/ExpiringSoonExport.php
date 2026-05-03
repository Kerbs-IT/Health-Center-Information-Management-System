<?php

namespace App\Exports;

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

class ExpiringSoonExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithMapping
{
    protected $batches;

    public function __construct()
    {
        // One row per expiring-soon BATCH.
        // Eager-load the medicine and its valid batches so we can
        // compute available stock and stock status per row.
        $this->batches = MedicineBatch::with([
            'medicine' => fn($q) => $q->with([
                'batches' => fn($q2) => $q2->where('expiry_date', '>', now()),
            ]),
        ])
        ->where('expiry_status', 'Expiring Soon')
        ->orderBy('expiry_date', 'asc')
        ->get()
        ->map(function ($batch) {
            // Available stock = free units across ALL valid batches of this medicine
            $available = $batch->medicine?->batches
                ->sum(fn($b) => max(0, $b->quantity - $b->reserved_quantity)) ?? 0;

            $batch->available_stock = $available;

            $batch->computed_stock_status = $available <= 0
                ? 'Out of Stock'
                : ($available <= 10 ? 'Low Stock' : 'In Stock');

            return $batch;
        });
    }

    public function collection()
    {
        return $this->batches;
    }

    public function headings(): array
    {
        return [
            'No.',
            'Medicine Name',
            'Dosage',
            'Batch No.',
            'Available Stock',
            'Stock Status',
            'Expiry Date',
        ];
    }

    public function map($batch): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $batch->medicine?->medicine_name ?? 'N/A',
            $batch->medicine?->dosage        ?? 'N/A',
            $batch->batch_number             ?? '—',
            $batch->available_stock,
            $batch->computed_stock_status,
            Carbon::parse($batch->expiry_date)->format('M d, Y'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 28,
            'C' => 15,
            'D' => 18,
            'E' => 16,
            'F' => 16,
            'G' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $last = $this->batches->count() + 1;

        $sheet->getStyle('A1:G1')->applyFromArray([
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

        for ($row = 2; $row <= $last; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF9F9F9'],
                    ],
                ]);
            }
        }

        $sheet->getStyle('A1:A' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D1:G' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}