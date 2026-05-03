<?php

namespace App\Exports;

use App\Models\MedicineRequestLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DistributedListExport implements
    FromQuery,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithMapping,
    WithChunkReading,
    WithEvents   // ← add this
{
    protected $totalQuantity;
    protected $totalRecords;


    public function __construct()
    {
        $this->totalQuantity = MedicineRequestLog::where('action', 'dispensed')->sum('quantity');
        $this->totalRecords  = MedicineRequestLog::where('action', 'dispensed')->count();
    }
    public function query()
    {
        return MedicineRequestLog::whereIn('action', ['dispensed'])
            ->select('patient_name', 'medicine_name', 'dosage', 'quantity', 'action', 'performed_at')
            ->orderByDesc('performed_at');
    }

    public function chunkSize(): int
    {
        return 500;
    }

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
            $d->dosage ?? 'N/A',
            $d->quantity,
            \Carbon\Carbon::parse($d->performed_at)->format('M d, Y h:i A'),
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastRow  = $this->totalRecords + 2; // +1 for header, +1 for next row

                // Total row
                $sheet->setCellValue("A{$lastRow}", 'TOTAL');
                $sheet->setCellValue("E{$lastRow}", $this->totalQuantity);

                $sheet->getStyle("A{$lastRow}:F{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE8F5E9'],
                    ],
                ]);
            },
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 6, 'B' => 25, 'C' => 25, 'D' => 15, 'E' => 12, 'F' => 24];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4CAF50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getStyle('A1:A1048576')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E1:F1048576')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}