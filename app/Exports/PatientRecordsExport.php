<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PatientRecordsExport implements FromCollection, WithEvents, ShouldAutoSize
{
    protected Collection $rows;
    protected array $filters;
    protected string $reportTitle;   // e.g. "Vaccination Records"
    protected array $columns;
    /*
     * $columns format:
     * [
     *   ['label' => 'Full Name', 'key' => 'full_name'],
     *   ['label' => 'Age',       'key' => 'age'],
     *   ['label' => 'Sex',       'key' => fn($row) => $row->sex ?? '—'],
     * ]
     * 'key' can be a string (property name) or a Closure for custom formatting.
     */

    public function __construct(
        Collection $rows,
        array $filters,
        string $reportTitle,
        array $columns
    ) {
        $this->rows        = $rows;
        $this->filters     = $filters;
        $this->reportTitle = $reportTitle;
        $this->columns     = $columns;
    }

    public function collection(): Collection
    {
        return collect();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $lastCol    = $this->columnLetter(count($this->columns));
                $row        = 1;

                // Auto landscape for wide tables
                if (count($this->columns) > 10) {
                    $sheet->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LEGAL);
                }

                // ── Row 1: System title ───────────────────────────────────
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->setCellValue("A{$row}", 'Health Center Information Management System');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a7a4a']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(24);
                $row++;

               

                // ── Row 2: Brgy subtitle ──────────────────────────────────
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->setCellValue("A{$row}", 'Brgy. Hugo Perez');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a7a4a']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;

                // ── Row 3: Report title ───────────────────────────────────
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->setCellValue("A{$row}", $this->reportTitle);
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                // ── Filters block ─────────────────────────────────────────
                $f          = $this->filters;
                $filterLines = [];

                if (!empty($f['search']))
                    $filterLines[] = ['Search',     $f['search']];
                if (!empty($f['status']) && $f['status'] !== 'all')
                    $filterLines[] = ['Status',     ucfirst(strtolower($f['status']))];
                if (!empty($f['purok']) && $f['purok'] !== 'all')
                    $filterLines[] = ['Purok',      $f['purok']];
                if (!empty($f['type']) && $f['type'] !== 'all')
                    $filterLines[] = ['Type',       ucwords(str_replace('-', ' ', $f['type']))];
                if (!empty($f['dateFrom']) && !empty($f['dateTo']))
                    $filterLines[] = ['Date Range', $f['dateFrom'] . ' to ' . $f['dateTo']];

                if (!empty($filterLines)) {
                    $row++;
                    $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                    $sheet->setCellValue("A{$row}", 'Applied Filters:');
                    $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                    $row++;

                    foreach ($filterLines as [$label, $value]) {
                        $sheet->setCellValue("A{$row}", $label . ':');
                        $sheet->setCellValue("B{$row}", $value);
                        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                        if (count($this->columns) > 1) {
                            $sheet->mergeCells("B{$row}:{$lastCol}{$row}");
                        }
                        $row++;
                    }
                }

                $row++; // spacer before table

                // ── Table header ──────────────────────────────────────────
                foreach ($this->columns as $i => $col) {
                    $sheet->setCellValue($this->columnLetter($i + 1) . $row, $col['label']);
                }
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28a745']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
                $row++;

                // ── Table data ────────────────────────────────────────────
                $dataStart = $row;
                foreach ($this->rows as $i => $record) {
                    foreach ($this->columns as $j => $col) {
                        $value = isset($col['key']) && $col['key'] instanceof \Closure
                            ? ($col['key'])($record)
                            : ($record->{$col['key']} ?? '—');

                        $sheet->setCellValue($this->columnLetter($j + 1) . $row, $value);
                    }

                    // Zebra striping
                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('f2f2f2');
                    }
                    $row++;
                }

                // Borders on data
                if ($row > $dataStart) {
                    $sheet->getStyle("A{$dataStart}:{$lastCol}" . ($row - 1))->applyFromArray([
                        'borders' => ['allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'CCCCCC'],
                        ]],
                    ]);
                }

                // ── Footer ────────────────────────────────────────────────
                $row++;
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->setCellValue("A{$row}", 'Total Records: ' . $this->rows->count());
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                $row++;
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $sheet->setCellValue("A{$row}", 'Generated on: ' . now()->format('F d, Y h:i A'));
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['italic' => true, 'color' => ['rgb' => '888888']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
            },
        ];
    }

    /**
     * Convert 1-based column index to Excel letter (1=A, 2=B, 27=AA, etc.)
     */
    private function columnLetter(int $index): string
    {
        $letter = '';
        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index  = intdiv($index, 26);
        }
        return $letter;
    }
}
