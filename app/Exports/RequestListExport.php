<?php

namespace App\Exports;

use App\Models\MedicineRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RequestListExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithMapping
{
    protected $requests;

    public function __construct()
    {
        $this->requests = MedicineRequest::with(['medicine', 'patients'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                $r->medicine_name = $r->medicine?->medicine_name ?? 'N/A';
                $r->dosage        = $r->medicine?->dosage ?? 'N/A';
                return $r;
            });
    }

    public function collection() { return $this->requests; }

    public function headings(): array
    {
        return ['No.', 'Patient Name', 'Medicine', 'Dosage', 'Quantity', 'Status', 'Date Requested'];
    }

    public function map($r): array
    {
        static $i = 0; $i++;
        return [
            $i,
            $r->requester_name,
            $r->medicine_name,
            $r->dosage,
            $r->quantity_requested,
            ucfirst($r->status),
            \Carbon\Carbon::parse($r->created_at)->format('M d, Y h:i A'),
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 25, 'C' => 25, 'D' => 15, 'E' => 12, 'F' => 14, 'G' => 22];
    }

    public function styles(Worksheet $sheet)
    {
        $last = $this->requests->count() + 1;
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4CAF50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);
        for ($row = 2; $row <= $last; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9F9F9']]]);
            }
        }
        $sheet->getStyle('A1:A' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E1:F' . $last)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}