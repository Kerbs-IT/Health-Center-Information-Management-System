<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MedicineListExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithMapping
{
    protected $medicines;

    public function __construct()
    {
        $this->medicines = Medicine::with([
            'category',
            'batches'    => fn($q) => $q->orderBy('expiry_date', 'asc'),
            'allBatches' => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])
        ->orderBy('medicine_name')
        ->get()
        ->map(fn($m) => tap($m, fn($m) => $m->category_name = $m->category?->category_name ?? 'N/A'));
    }

    public function collection() { return $this->medicines; }

    public function headings(): array
    {
        return [
            'No.',
            'Medicine Name',
            'Category',
            'Dosage',
            'Available Stock',
            'Stock Status',
            'Current Batch Expiry Date',
        ];
    }

    public function map($m): array
    {
        static $i = 0; $i++;

        $fifo      = $m->batches->first();    // current-use batch (FIFO)
        $lastBatch = $m->allBatches->last();  // latest expiry → for expiry status

        // Stock status based on available stock
        $available   = $m->available_stock;
        $stockStatus = $available <= 0 ? 'Out of Stock'
                     : ($available <= 10 ? 'Low Stock' : 'In Stock');

        // Expiry status based on last batch's expiry date
        $expiryStatus = 'No Batches';
        if ($lastBatch) {
            $days         = now()->diffInDays($lastBatch->expiry_date, false);
            $expiryStatus = $days < 0 ? 'Expired'
                          : ($days <= 30 ? 'Expiring Soon' : 'Valid');
        }

        return [
            $i,
            $m->medicine_name,
            $m->category_name,
            $m->dosage,
            $available,
            $stockStatus,
            $fifo?->expiry_date?->format('M d, Y') ?? 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 28,
            'C' => 20,
            'D' => 15,
            'E' => 16,  // Available Stock
            'F' => 16,  // Stock Status
            'G' => 24,  // Current Batch Expiry Date
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $last = $this->medicines->count() + 1;

        // Header row
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4CAF50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Alternate row shading
        for ($row = 2; $row <= $last; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9F9F9']],
                ]);
            }
        }

        // Center-align No. column and right-side columns
        $sheet->getStyle('A1:A' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E1:H' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}