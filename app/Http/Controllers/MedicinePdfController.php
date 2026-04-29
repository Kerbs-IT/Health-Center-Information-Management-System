<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicinePdfController extends Controller
{
    public function downloadPdf()
    {
        $medicines = Medicine::with('category')
            ->orderBy('medicine_name', 'asc')
            ->get();

        $pdf = Pdf::loadView('reports.medicines-pdf', [
            'medicines' => $medicines
        ]);

        // Configure PDF settings
        $pdf->setPaper('A4', 'landscape');

        // Optional: Set additional options
        $pdf->setOptions([
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'chroot' => public_path(),
        ]);

        $filename = 'Medicine_Inventory_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function downloadCsv()
    {
        $medicines = Medicine::with(['category' => fn($q) => $q->withTrashed()])
            ->with(['batches' => fn($q) => $q->orderBy('expiry_date', 'asc')])
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Medicine Inventory');

        // --- Header row ---
        $headers = [
            'A' => ['label' => 'No.',                   'width' => 6],
            'B' => ['label' => 'Medicine Name',          'width' => 30],
            'C' => ['label' => 'Category',               'width' => 30],
            'D' => ['label' => 'Dosage',                 'width' => 15],
            'E' => ['label' => 'Age Range',              'width' => 22],
            'F' => ['label' => 'Total Stock',            'width' => 14],
            'G' => ['label' => 'Stock Status',           'width' => 16],
            'H' => ['label' => 'Expiry Status',          'width' => 16],
            'I' => ['label' => 'Earliest Expiry Date',  'width' => 22],
        ];

        foreach ($headers as $col => $config) {
            $cell = $col . '1';
            $sheet->setCellValue($cell, $config['label']);
            $sheet->getColumnDimension($col)->setWidth($config['width']);
        }

        // Style the header row — bold, white text, green background
        $headerRange = 'A1:I1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF198754'], // Bootstrap success green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(20);

        // --- Data rows ---
        foreach ($medicines as $index => $medicine) {
            $row  = $index + 2;
            $fifo = $medicine->batches->first();

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $medicine->medicine_name);
            $sheet->setCellValue('C' . $row, $medicine->category->category_name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $medicine->dosage);
            $sheet->setCellValue('E' . $row, formatAgeRangeForCsv($medicine->min_age_months, $medicine->max_age_months));
            $sheet->setCellValue('F' . $row, $medicine->stock);
            $sheet->setCellValue('G' . $row, $medicine->stock_status);
            $sheet->setCellValue('H' . $row, $fifo?->expiry_status ?? 'No Batches');
            $sheet->setCellValue('I' . $row, $fifo?->expiry_date?->format('M d, Y') ?? 'N/A');

            // Alternate row shading
            if ($index % 2 === 0) {
                $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF8F9FA'],
                    ],
                ]);
            }
        }

        // Center-align No. and numeric columns
        $lastRow = $medicines->count() + 1;
        $sheet->getStyle('A1:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F1:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- Stream response ---
        $filename = 'medicine-inventory-' . date('Y-m-d') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

}


function formatAgeRangeForCsv($minMonths, $maxMonths): string
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