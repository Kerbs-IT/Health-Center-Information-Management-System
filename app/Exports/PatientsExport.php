<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PatientsExport implements FromCollection, WithEvents, ShouldAutoSize
{
    protected Collection $rows;
    protected array $filters;

    public function __construct(Collection $rows, array $filters)
    {
        $this->rows    = $rows;
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        return collect(); // data is written manually in AfterSheet
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $col   = 'I'; // last column (A–I = 9 cols)
                $row   = 1;

                // ── Row 1: System title ───────────────────────────────────
                $sheet->mergeCells("A{$row}:{$col}{$row}");
                $sheet->setCellValue("A{$row}", 'Health Center Information Management System');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a7a4a']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(22);
                $row++;

                // ── Row 2: Brgy subtitle ──────────────────────────────────
                $sheet->mergeCells("A{$row}:{$col}{$row}");
                $sheet->setCellValue("A{$row}", 'Brgy. Hugo Perez');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a7a4a']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(18);
                $row++;

                // ── Row 3: Report label ───────────────────────────────────
                $sheet->mergeCells("A{$row}:{$col}{$row}");
                $sheet->setCellValue("A{$row}", 'Patient Records');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                // ── Filters block ─────────────────────────────────────────
                $f          = $this->filters;
                $hasFilters = ($f['status'] !== 'all')
                    || ($f['purok']  !== 'all')
                    || ($f['type']   !== 'all')
                    || (!empty($f['dateFrom']) && !empty($f['dateTo']));

                if ($hasFilters) {
                    $row++; // blank spacer
                    $sheet->mergeCells("A{$row}:{$col}{$row}");
                    $sheet->setCellValue("A{$row}", 'Applied Filters:');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    $filterLines = [];
                    if ($f['status'] !== 'all')
                        $filterLines[] = ['Status', ucfirst(strtolower($f['status']))];
                    if ($f['purok'] !== 'all')
                        $filterLines[] = ['Purok', $f['purok']];
                    if ($f['type'] !== 'all')
                        $filterLines[] = ['Type of Patient', ucwords(str_replace('-', ' ', $f['type']))];
                    if (!empty($f['dateFrom']) && !empty($f['dateTo']))
                        $filterLines[] = ['Date Range', $f['dateFrom'] . ' to ' . $f['dateTo']];

                    foreach ($filterLines as [$label, $value]) {
                        $sheet->setCellValue("A{$row}", $label . ':');
                        $sheet->setCellValue("B{$row}", $value);
                        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                        $sheet->mergeCells("B{$row}:{$col}{$row}");
                        $row++;
                    }
                }

                $row++; // blank spacer before table

                // ── Table header ──────────────────────────────────────────
                $headers = [
                    '#',
                    'Name',
                    'Sex',
                    'Age',
                    'Contact Number',
                    'Type of Patient',
                    'Purok',
                    'Status',
                    'Date Registered'
                ];
                $col_idx = 'A';
                foreach ($headers as $header) {
                    $sheet->setCellValue("{$col_idx}{$row}", $header);
                    $col_idx++;
                }
                $sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28a745']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $headerRow = $row;
                $row++;

                // ── Table data ────────────────────────────────────────────
                $dataStart = $row;
                foreach ($this->rows as $i => $patient) {
                    $sheet->setCellValue("A{$row}", $i + 1);
                    $sheet->setCellValue("B{$row}", $patient->full_name);
                    $sheet->setCellValue("C{$row}", $patient->sex);
                    $sheet->setCellValue("D{$row}", $patient->ageDisplay ?? $patient->age);
                    $sheet->setCellValue("E{$row}", $patient->contact_number);
                    $sheet->setCellValue("F{$row}", $patient->type_of_case
                        ? ucwords(str_replace('-', ' ', $patient->type_of_case))
                        : '—');
                    $sheet->setCellValue("G{$row}", $patient->purok ?? '—');
                    $sheet->setCellValue("H{$row}", $patient->case_status ?? '—');
                    $sheet->setCellValue("I{$row}", $patient->created_at
                        ? \Carbon\Carbon::parse($patient->created_at)->format('Y-m-d')
                        : '—');

                    // Zebra striping
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$row}:I{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('f2f2f2');
                    }
                    $row++;
                }

                // Borders on data rows
                if ($row > $dataStart) {
                    $sheet->getStyle("A{$dataStart}:I" . ($row - 1))->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                    ]);
                }

                // ── Row count summary at bottom ───────────────────────────
                $row++;
                $sheet->mergeCells("A{$row}:I{$row}");
                $sheet->setCellValue("A{$row}", 'Total Records: ' . $this->rows->count());
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Generated on timestamp
                $row++;
                $sheet->mergeCells("A{$row}:I{$row}");
                $sheet->setCellValue("A{$row}", 'Generated on: ' . now()->format('F d, Y h:i A'));
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['italic' => true, 'color' => ['rgb' => '888888']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
            },
        ];
    }
}
