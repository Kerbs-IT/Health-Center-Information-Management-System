<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MedicineInventoryExport implements
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
            'category' => fn($q) => $q->withTrashed(),
            'batches'  => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])->get();
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
            'Category',
            'Dosage',
            'Age Range',
            'Total Stock',
            'Stock Status',
            'Expiry Status',
            'Earliest Expiry Date',
        ];
    }

    public function map($medicine): array
    {
        static $index = 0;
        $index++;

        $fifo = $medicine->batches->first();

        return [
            $index,
            $medicine->medicine_name,
            $medicine->category->category_name ?? 'N/A',
            $medicine->dosage,
            $this->formatAgeRange($medicine->min_age_months, $medicine->max_age_months),
            $medicine->stock,
            $medicine->stock_status,
            $fifo?->expiry_status ?? 'No Batches',
            $fifo?->expiry_date?->format('M d, Y') ?? 'N/A',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 30,
            'C' => 30,
            'D' => 15,
            'E' => 22,
            'F' => 14,
            'G' => 16,
            'H' => 16,
            'I' => 22,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->medicines->count() + 1;

        // Header row — green background, white bold text, centered
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF198754'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Alternate row shading
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF8F9FA'],
                    ],
                ]);
            }
        }

        // Center-align No., Stock, and right-side columns
        $sheet->getStyle('A1:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F1:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function formatAgeRange($minMonths, $maxMonths): string
    {
        if (is_null($minMonths) && is_null($maxMonths)) return 'All ages';

        $fmt = function ($m) {
            if ($m < 12) return $m . ' months';
            $y  = floor($m / 12);
            $mo = $m % 12;
            $s  = $y . ' ' . ($y == 1 ? 'year' : 'years');
            if ($mo > 0) $s .= ' ' . $mo . ' months';
            return $s;
        };

        if (is_null($minMonths)) return 'Up to ' . $fmt($maxMonths);
        if (is_null($maxMonths)) return $fmt($minMonths) . '+';
        return $fmt($minMonths) . ' - ' . $fmt($maxMonths);
    }
}