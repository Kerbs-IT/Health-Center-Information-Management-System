<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineRequestLog;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    public function downloadMedicineReport()
    {
        $medicines = Medicine::with('category')
            ->select('medicine_name', 'category_id', 'dosage', 'stock', 'stock_status', 'expiry_date')
            ->orderBy('medicine_name')
            ->get()
            ->map(function($medicine){
                $medicine->category_name = $medicine->category ? $medicine->category->category_name : 'N/A';
                return $medicine;
            });

        $data = [
            'medicines' => $medicines,
            'total' => Medicine::count(),
            'generatedDate' => now()->format('F d, Y h:i A')
        ];

        $pdf = Pdf::loadView('reports.medicine-list-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
        ]);

        return $pdf->download('medicine-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadRequestReport()
    {
        $requests = MedicineRequest::with(['medicine', 'patients', 'user'])
            ->select('id', 'patients_id', 'user_id', 'medicine_id', 'quantity_requested', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($request) {
                $request->requester_name = $request->requester_name;
                $request->medicine_name = $request->medicine
                    ? $request->medicine->medicine_name
                    : 'N/A';
                $request->dosage = $request->medicine ? $request->medicine->dosage : 'N/A';
                return $request;
            });

        $data = [
            'requests' => $requests,
            'total' => MedicineRequest::count(),
            'generatedDate' => now()->format('F d, Y h:i A')
        ];

        $pdf = Pdf::loadView('reports.request-list-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
        ]);

        return $pdf->download('requests-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadDistributedReport()
    {
        $distributed = MedicineRequestLog::where('action', 'approved')
            ->select('medicine_request_id', 'patient_name', 'medicine_name','dosage', 'quantity', 'performed_at')
            ->orderByDesc('performed_at')
            ->get();

        $data = [
            'distributed'       => $distributed,
            'total'             => MedicineRequestLog::where('action', 'approved')->count(),
            'generatedDate'     => now()->format('F d, Y h:i A')
        ];

        $pdf = Pdf::loadView('reports.distribute-list-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
        ]);

        return $pdf->download('distributed-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadLowStockReport()
    {
        $medicines = Medicine::where('stock_status', 'Low Stock')
            ->select('medicine_name', 'dosage', 'stock', 'expiry_date', 'expiry_status')
            ->orderBy('stock', 'asc')
            ->get();

        $data = [
            'medicines' => $medicines,
            'total' => $medicines->count(),
            'generatedDate' => now()->format('F d, Y h:i A')
        ];

        $pdf = Pdf::loadView('reports.low-stock-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
        ]);

        return $pdf->download('low-stock-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadExpiringSoonReport()
    {
        $medicines = Medicine::where('expiry_status', 'Expiring Soon')
            ->select('medicine_name', 'dosage', 'stock', 'stock_status', 'expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->get();

        $data = [
            'medicines' => $medicines,
            'total' => $medicines->count(),
            'generatedDate' => now()->format('F d, Y h:i A')
        ];

        $pdf = Pdf::loadView('reports.expiring-soon-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
        ]);

        return $pdf->download('expiring-soon-list-' . now()->format('Y-m-d') . '.pdf');
    }





    // ─── Reusable helper ───────────────────────────────────────────────
    private function styleSheet($sheet, array $headers, string $lastCol, int $totalRows): void
    {
        // Set column widths & headers
        foreach ($headers as $col => $config) {
            $sheet->setCellValue($col . '1', $config['label']);
            $sheet->getColumnDimension($col)->setWidth($config['width']);
        }

        // Header style — bold, white text, green background
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4CAF50'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Alternate row shading
        for ($i = 2; $i <= $totalRows + 1; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF9F9F9'],
                    ],
                ]);
            }
        }
    }

    private function streamXlsx(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ─── 1. Medicine List ───────────────────────────────────────────────
    public function downloadMedicineReportExcel()
    {
        $medicines = Medicine::with('category')
            ->select('medicine_name', 'category_id', 'dosage', 'stock', 'stock_status', 'expiry_date')
            ->orderBy('medicine_name')
            ->get()
            ->map(fn($m) => tap($m, fn($m) => $m->category_name = $m->category?->category_name ?? 'N/A'));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Medicine List');

        $headers = [
            'A' => ['label' => 'No.',           'width' => 6],
            'B' => ['label' => 'Medicine Name', 'width' => 28],
            'C' => ['label' => 'Category',      'width' => 20],
            'D' => ['label' => 'Dosage',        'width' => 15],
            'E' => ['label' => 'Stock',         'width' => 10],
            'F' => ['label' => 'Stock Status',  'width' => 16],
            'G' => ['label' => 'Expiry Date',   'width' => 18],
        ];

        $this->styleSheet($sheet, $headers, 'G', $medicines->count());

        foreach ($medicines as $i => $m) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $m->medicine_name);
            $sheet->setCellValue('C' . $row, $m->category_name);
            $sheet->setCellValue('D' . $row, $m->dosage);
            $sheet->setCellValue('E' . $row, $m->stock);
            $sheet->setCellValue('F' . $row, $m->stock_status);
            $sheet->setCellValue('G' . $row, \Carbon\Carbon::parse($m->expiry_date)->format('M d, Y'));
        }

        $sheet->getStyle('A1:A' . ($medicines->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E1:G' . ($medicines->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $this->streamXlsx($spreadsheet, 'medicine-list-' . now()->format('Y-m-d') . '.xlsx');
    }

    // ─── 2. Request List ────────────────────────────────────────────────
    public function downloadRequestReportExcel()
    {
        $requests = MedicineRequest::with(['medicine', 'patients'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                $r->requester_name = $r->requester_name;
                $r->medicine_name  = $r->medicine?->medicine_name ?? 'N/A';
                $r->dosage         = $r->medicine?->dosage ?? 'N/A';
                return $r;
            });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Requests');

        $headers = [
            'A' => ['label' => 'No.',            'width' => 6],
            'B' => ['label' => 'Patient Name',   'width' => 25],
            'C' => ['label' => 'Medicine',       'width' => 25],
            'D' => ['label' => 'Dosage',         'width' => 15],
            'E' => ['label' => 'Quantity',       'width' => 12],
            'F' => ['label' => 'Status',         'width' => 14],
            'G' => ['label' => 'Date Requested', 'width' => 22],
        ];

        $this->styleSheet($sheet, $headers, 'G', $requests->count());

        foreach ($requests as $i => $r) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $r->requester_name);
            $sheet->setCellValue('C' . $row, $r->medicine_name);
            $sheet->setCellValue('D' . $row, $r->dosage);
            $sheet->setCellValue('E' . $row, $r->quantity_requested);
            $sheet->setCellValue('F' . $row, ucfirst($r->status));
            $sheet->setCellValue('G' . $row, \Carbon\Carbon::parse($r->created_at)->format('M d, Y h:i A'));
        }

        $sheet->getStyle('A1:A' . ($requests->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E1:F' . ($requests->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $this->streamXlsx($spreadsheet, 'requests-list-' . now()->format('Y-m-d') . '.xlsx');
    }

    // ─── 3. Distributed List ────────────────────────────────────────────
    public function downloadDistributedReportExcel()
    {
        $distributed = MedicineRequestLog::where('action', 'approved')
            ->select('patient_name', 'medicine_name', 'dosage', 'quantity', 'performed_at')
            ->orderByDesc('performed_at')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Distributed');

        $headers = [
            'A' => ['label' => 'No.',              'width' => 6],
            'B' => ['label' => 'Patient Name',     'width' => 25],
            'C' => ['label' => 'Medicine',         'width' => 25],
            'D' => ['label' => 'Dosage',           'width' => 15],
            'E' => ['label' => 'Quantity',         'width' => 12],
            'F' => ['label' => 'Date Distributed', 'width' => 24],
        ];

        $this->styleSheet($sheet, $headers, 'F', $distributed->count());

        foreach ($distributed as $i => $d) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $d->patient_name);
            $sheet->setCellValue('C' . $row, $d->medicine_name);
            $sheet->setCellValue('D' . $row, $d->dosage);
            $sheet->setCellValue('E' . $row, $d->quantity);
            $sheet->setCellValue('F' . $row, \Carbon\Carbon::parse($d->performed_at)->format('M d, Y h:i A'));
        }

        $sheet->getStyle('A1:A' . ($distributed->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E1:F' . ($distributed->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $this->streamXlsx($spreadsheet, 'distributed-list-' . now()->format('Y-m-d') . '.xlsx');
    }

    // ─── 4. Low Stock ───────────────────────────────────────────────────
    public function downloadLowStockReportExcel()
    {
        $medicines = Medicine::where('stock_status', 'Low Stock')
            ->select('medicine_name', 'dosage', 'stock', 'expiry_date', 'expiry_status')
            ->orderBy('stock', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Low Stock');

        $headers = [
            'A' => ['label' => 'No.',           'width' => 6],
            'B' => ['label' => 'Medicine Name', 'width' => 28],
            'C' => ['label' => 'Dosage',        'width' => 15],
            'D' => ['label' => 'Stock',         'width' => 10],
            'E' => ['label' => 'Expiry Date',   'width' => 18],
            'F' => ['label' => 'Expiry Status', 'width' => 16],
        ];

        $this->styleSheet($sheet, $headers, 'F', $medicines->count());

        foreach ($medicines as $i => $m) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $m->medicine_name);
            $sheet->setCellValue('C' . $row, $m->dosage);
            $sheet->setCellValue('D' . $row, $m->stock);
            $sheet->setCellValue('E' . $row, \Carbon\Carbon::parse($m->expiry_date)->format('M d, Y'));
            $sheet->setCellValue('F' . $row, $m->expiry_status);
        }

        $sheet->getStyle('A1:A' . ($medicines->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D1:F' . ($medicines->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $this->streamXlsx($spreadsheet, 'low-stock-list-' . now()->format('Y-m-d') . '.xlsx');
    }

    // ─── 5. Expiring Soon ───────────────────────────────────────────────
    public function downloadExpiringSoonReportExcel()
    {
        $medicines = Medicine::where('expiry_status', 'Expiring Soon')
            ->select('medicine_name', 'dosage', 'stock', 'stock_status', 'expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Expiring Soon');

        $headers = [
            'A' => ['label' => 'No.',           'width' => 6],
            'B' => ['label' => 'Medicine Name', 'width' => 28],
            'C' => ['label' => 'Dosage',        'width' => 15],
            'D' => ['label' => 'Stock',         'width' => 10],
            'E' => ['label' => 'Stock Status',  'width' => 16],
            'F' => ['label' => 'Expiry Date',   'width' => 18],
        ];

        $this->styleSheet($sheet, $headers, 'F', $medicines->count());

        foreach ($medicines as $i => $m) {
            $row = $i + 2;
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $m->medicine_name);
            $sheet->setCellValue('C' . $row, $m->dosage);
            $sheet->setCellValue('D' . $row, $m->stock);
            $sheet->setCellValue('E' . $row, $m->stock_status);
            $sheet->setCellValue('F' . $row, \Carbon\Carbon::parse($m->expiry_date)->format('M d, Y'));
        }

        $sheet->getStyle('A1:A' . ($medicines->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D1:F' . ($medicines->count() + 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return $this->streamXlsx($spreadsheet, 'expiring-soon-list-' . now()->format('Y-m-d') . '.xlsx');
    }
}