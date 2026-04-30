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

class ExpiringSoonExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithMapping
{
    protected $medicines;

    public function __construct()
    {
        $this->medicines = Medicine::where('expiry_status', 'Expiring Soon')
            ->select('medicine_name', 'dosage', 'stock', 'stock_status', 'expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    public function collection() { return $this->medicines; }

    public function headings(): array
    {
        return ['No.', 'Medicine Name', 'Dosage', 'Stock', 'Stock Status', 'Expiry Date'];
    }

    public function map($m): array
    {
        static $i = 0; $i++;
        return [
            $i,
            $m->medicine_name,
            $m->dosage,
            $m->stock,
            $m->stock_status,
            \Carbon\Carbon::parse($m->expiry_date)->format('M d, Y'),
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 28, 'C' => 15, 'D' => 10, 'E' => 16, 'F' => 18];
    }

    public function styles(Worksheet $sheet)
    {
        $last = $this->medicines->count() + 1;
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4CAF50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);
        for ($row = 2; $row <= $last; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:F{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9F9F9']]]);
            }
        }
        $sheet->getStyle('A1:A' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D1:F' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}