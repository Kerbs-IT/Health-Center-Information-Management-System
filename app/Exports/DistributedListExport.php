<?php

namespace App\Exports;

use App\Models\MedicineRequestLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DistributedListExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithMapping
{
    protected $distributed;

    public function __construct()
    {
        $this->distributed = MedicineRequestLog::where('action', 'approved')
            ->select('patient_name', 'medicine_name', 'dosage', 'quantity', 'performed_at')
            ->orderByDesc('performed_at')
            ->get();
    }

    public function collection() { return $this->distributed; }

    public function headings(): array
    {
        return ['No.', 'Patient Name', 'Medicine', 'Dosage', 'Quantity', 'Date Distributed'];
    }

    public function map($d): array
    {
        static $i = 0; $i++;
        return [
            $i,
            $d->patient_name,
            $d->medicine_name,
            $d->dosage,
            $d->quantity,
            \Carbon\Carbon::parse($d->performed_at)->format('M d, Y h:i A'),
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 25, 'C' => 25, 'D' => 15, 'E' => 12, 'F' => 24];
    }

    public function styles(Worksheet $sheet)
    {
        $last = $this->distributed->count() + 1;
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
        $sheet->getStyle('E1:F' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}