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
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class WRAMasterlistExport implements FromCollection, WithEvents, ShouldAutoSize
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
        return collect();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ── Page setup ────────────────────────────────────────────
                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(PageSetup::PAPERSIZE_LEGAL);

                $lastCol = 'U'; // 21 columns A–U
                $row = 1;

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
                $sheet->setCellValue("A{$row}", 'Master List of Women of Reproductive Age for Family Planning Services');
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                // ── Row 4: Quarter/Year ───────────────────────────────────
                $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
                $f = $this->filters;
                $quarterYear = ($f['monthName'] ?? '') . ' - ' . ($f['selectedYear'] ?? date('Y'));
                $sheet->setCellValue("A{$row}", 'For the Quarter/Year: ' . $quarterYear);
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $row++;

                // ── Row 5: Barangay | Midwife | Date ─────────────────────
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->setCellValue("A{$row}", 'Barangay: ' . ($f['selectedBrgy'] ?: 'All Barangays'));
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);

                $sheet->mergeCells("H{$row}:N{$row}");
                $sheet->setCellValue("H{$row}", 'Name of BHS Midwife: ' . ($f['midwifeName'] ?? ''));
                $sheet->getStyle("H{$row}")->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells("O{$row}:{$lastCol}{$row}");
                $sheet->setCellValue("O{$row}", 'Date Prepared: ' . now()->format('M d Y'));
                $sheet->getStyle("O{$row}")->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $row++;

                // ── Filters block ─────────────────────────────────────────
                $filterLines = [];
                if (!empty($f['search']))       $filterLines[] = ['Search', $f['search']];
                if (!empty($f['selectedBrgy'])) $filterLines[] = ['Purok', $f['selectedBrgy']];
                if (!empty($f['withUnmetNeed'])) $filterLines[] = ['Unmet Need', $f['withUnmetNeed']];
                if (!empty($f['selectedAge']))  $filterLines[] = ['Age Range', $f['selectedAge']];

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
                        $sheet->mergeCells("B{$row}:{$lastCol}{$row}");
                        $row++;
                    }
                }

                $row++; // spacer

                // ── 3-level merged table header ───────────────────────────
                $headerStartRow = $row;

                // Row 1 of header
                $sheet->setCellValue("A{$row}", 'No.');
                $sheet->mergeCells("A{$row}:A" . ($row + 2));
                $sheet->setCellValue("B{$row}", 'HH No. (1)');
                $sheet->mergeCells("B{$row}:B" . ($row + 2));
                $sheet->setCellValue("C{$row}", 'Name of WRA (FN,MI,LN) (2)');
                $sheet->mergeCells("C{$row}:C" . ($row + 2));
                $sheet->setCellValue("D{$row}", 'Address (3)');
                $sheet->mergeCells("D{$row}:D" . ($row + 2));
                $sheet->setCellValue("E{$row}", 'Age in Years (4)');
                $sheet->mergeCells("E{$row}:G{$row}");
                $sheet->setCellValue("H{$row}", 'Birthday (MM/DD/YY) (5)');
                $sheet->mergeCells("H{$row}:H" . ($row + 2));
                $sheet->setCellValue("I{$row}", 'SE Status (6)');
                $sheet->mergeCells("I{$row}:I" . ($row + 2));
                $sheet->setCellValue("J{$row}", 'Do you plan to have more children? (Place a check) (7)');
                $sheet->mergeCells("J{$row}:L{$row}");
                $sheet->setCellValue("M{$row}", 'If col. 7b & 7c is (✓), are you currently using any FP method (8)');
                $sheet->mergeCells("M{$row}:O{$row}");
                $sheet->setCellValue("P{$row}", 'If col 7b or 7c is / and using col 8b or 8c, would you like to shift to modern method? (9)');
                $sheet->mergeCells("P{$row}:Q{$row}");
                $sheet->setCellValue("R{$row}", 'WRA with MFP Unmet Need (10)');
                $sheet->mergeCells("R{$row}:R" . ($row + 2));
                $sheet->setCellValue("S{$row}", 'Based on TCL on FP, did WRA accept any modern FP method? (11)');
                $sheet->mergeCells("S{$row}:U{$row}");
                $row++;

                // Row 2 of header
                $sheet->setCellValue("E{$row}", '10-14');
                $sheet->mergeCells("E{$row}:E" . ($row + 1));
                $sheet->setCellValue("F{$row}", '15-19');
                $sheet->mergeCells("F{$row}:F" . ($row + 1));
                $sheet->setCellValue("G{$row}", '20-49');
                $sheet->mergeCells("G{$row}:G" . ($row + 1));
                $sheet->setCellValue("J{$row}", 'If Yes, when?');
                $sheet->mergeCells("J{$row}:K{$row}");
                $sheet->setCellValue("L{$row}", 'No');
                $sheet->mergeCells("L{$row}:L" . ($row + 1));
                $sheet->setCellValue("M{$row}", 'If Yes, what type?');
                $sheet->mergeCells("M{$row}:N{$row}");
                $sheet->setCellValue("O{$row}", 'Not using any FP method (place a /) (8c)');
                $sheet->mergeCells("O{$row}:O" . ($row + 1));
                $sheet->setCellValue("P{$row}", 'Yes (9a)');
                $sheet->mergeCells("P{$row}:P" . ($row + 1));
                $sheet->setCellValue("Q{$row}", 'No (9b)');
                $sheet->mergeCells("Q{$row}:Q" . ($row + 1));
                $sheet->setCellValue("S{$row}", 'No (11a) (Put a /)');
                $sheet->mergeCells("S{$row}:S" . ($row + 1));
                $sheet->setCellValue("T{$row}", 'Yes (11b)');
                $sheet->mergeCells("T{$row}:U{$row}");
                $row++;

                // Row 3 of header
                $sheet->setCellValue("J{$row}", 'Now (7a)');
                $sheet->setCellValue("K{$row}", 'Spacing (7b)');
                $sheet->setCellValue("M{$row}", 'modern (8a)');
                $sheet->setCellValue("N{$row}", 'traditional (8b)');
                $sheet->setCellValue("T{$row}", 'specify modern FP method');
                $sheet->setCellValue("U{$row}", 'Date when FP method accepted');
                $row++;

                // Style the entire header block
                $sheet->getStyle("A{$headerStartRow}:{$lastCol}" . ($row - 1))->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e8e8e8']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true
                    ],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // ── Data rows ─────────────────────────────────────────────
                $dataStart = $row;
                foreach ($this->rows as $i => $r) {
                    $age = $r['age'] ?? 0;

                    $sheet->setCellValue("A{$row}", $i + 1);
                    $sheet->setCellValue("B{$row}", $r['house_hold_number'] ?? '');
                    $sheet->setCellValue("C{$row}", $r['name_of_wra'] ?? '');
                    $sheet->setCellValue("D{$row}", $r['address'] ?? '');
                    $sheet->setCellValue("E{$row}", ($age >= 10 && $age <= 14) ? '/' : '');
                    $sheet->setCellValue("F{$row}", ($age >= 15 && $age <= 19) ? '/' : '');
                    $sheet->setCellValue("G{$row}", ($age >= 20 && $age <= 49) ? '/' : '');
                    $sheet->setCellValue("H{$row}", $r['date_of_birth'] ?? '');
                    $sheet->setCellValue("I{$row}", ($r['SE_status'] ?? '') === 'Yes' ? 'NHTS' : 'Non-NHTS');
                    $sheet->setCellValue("J{$row}", ($r['plan_to_have_more_children_yes'] ?? '') === 'now' ? '/' : '');
                    $sheet->setCellValue("K{$row}", ($r['plan_to_have_more_children_yes'] ?? '') === 'spacing' ? '/' : '');
                    $sheet->setCellValue("L{$row}", ($r['plan_to_have_more_children_no'] ?? '') === 'limiting' ? '/' : '');
                    $sheet->setCellValue("M{$row}", $r['modern_FP'] ?? '');
                    $sheet->setCellValue("N{$row}", $r['traditional_FP'] ?? '');
                    $sheet->setCellValue("O{$row}", ($r['currently_using_any_FP_method_no'] ?? '') === 'yes' ? '/' : '');
                    $sheet->setCellValue("P{$row}", ($r['shift_to_modern_method'] ?? '') === 'Yes' ? '/' : '');
                    $sheet->setCellValue("Q{$row}", ($r['shift_to_modern_method'] ?? '') === 'No' ? '/' : '');
                    $sheet->setCellValue("R{$row}", ($r['wra_with_MFP_unmet_need'] ?? '') !== 'no' ? '/' : '');
                    $sheet->setCellValue("S{$row}", ($r['wra_accepte_any_modern_FP_method'] ?? '') === 'no' ? '/' : '');
                    $sheet->setCellValue("T{$row}", $r['selected_modern_FP_method'] ?? '');
                    $sheet->setCellValue("U{$row}", $r['date_when_FP_method_accepted'] ?? '');

                    if ($i % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB('f9f9f9');
                    }
                    $row++;
                }

                // Borders on data
                if ($row > $dataStart) {
                    $sheet->getStyle("A{$dataStart}:{$lastCol}" . ($row - 1))->applyFromArray([
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font'      => ['size' => 9],
                    ]);
                    // Left-align name and address columns
                    $sheet->getStyle("C{$dataStart}:D" . ($row - 1))
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
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
}
