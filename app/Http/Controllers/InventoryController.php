<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineRequestLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MedicineListExport;
use App\Exports\RequestListExport;
use App\Exports\DistributedListExport;
use App\Exports\LowStockExport;
use App\Exports\ExpiringSoonExport;
use App\Models\MedicineBatch;
class InventoryController extends Controller
{
    // ─── PDF Downloads ─────────────────────────────────────────────────

    public function downloadMedicineReport()
    {
        $medicines = Medicine::with([
            'category',
            'batches'    => fn($q) => $q->orderBy('expiry_date', 'asc'),
            'allBatches' => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])
        ->orderBy('medicine_name')
        ->get()
        ->map(function ($medicine) {
            $medicine->category_name = $medicine->category?->category_name ?? 'N/A';
            return $medicine;
        });

        $data = [
            'medicines'     => $medicines,
            'total'         => $medicines->count(),
            'generatedDate' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.medicine-list-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
        ]);

        return $pdf->download('medicine-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadRequestReport()
    {
        $requests = MedicineRequest::with(['medicine', 'patients', 'user'])
            ->select('id', 'patients_id', 'user_id', 'medicine_id', 'quantity_requested', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($request) {
                $request->requester_name = $request->requester_name;
                $request->medicine_name  = $request->medicine ? $request->medicine->medicine_name : 'N/A';
                $request->dosage         = $request->medicine ? $request->medicine->dosage : 'N/A';
                return $request;
            });

        $data = [
            'requests'      => $requests,
            'total'         => MedicineRequest::count(),
            'generatedDate' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.request-list-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
        ]);

        return $pdf->download('requests-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadDistributedReport()
    {
        $distributed = MedicineRequestLog::where('action', 'approved')
            ->select('medicine_request_id', 'patient_name', 'medicine_name', 'dosage', 'quantity', 'performed_at')
            ->orderByDesc('performed_at')
            ->get();

        $data = [
            'distributed'   => $distributed,
            'total'         => MedicineRequestLog::where('action', 'approved')->count(),
            'generatedDate' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.distribute-list-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
        ]);

        return $pdf->download('distributed-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadLowStockReport()
    {
        $report = new \App\Livewire\InventoryReport();
        $medicines = $report->getLowStockCollection();

        $data = [
            'medicines'     => $medicines,
            'total'         => $medicines->count(),
            'generatedDate' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.low-stock-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
        ]);

        return $pdf->download('low-stock-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadExpiringSoonReport()
    {
        $batches = MedicineBatch::with([
            'medicine' => fn($q) => $q->with([
                'batches' => fn($q2) => $q2->where('expiry_date', '>', now()),
            ]),
        ])
        ->where('expiry_status', 'Expiring Soon')
        ->orderBy('expiry_date', 'asc')
        ->get()
        ->map(function ($batch) {
            $available = $batch->medicine?->batches
                ->sum(fn($b) => max(0, $b->quantity - $b->reserved_quantity)) ?? 0;

            $batch->available_stock = $available;

            $batch->computed_stock_status = $available <= 0
                ? 'Out of Stock'
                : ($available <= 10 ? 'Low Stock' : 'In Stock');

            return $batch;
        });

        $data = [
            'batches'       => $batches,
            'total'         => $batches->count(),
            'generatedDate' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.expiring-soon-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => false,
        ]);

        return $pdf->download('expiring-soon-list-' . now()->format('Y-m-d') . '.pdf');
    }

    // ─── Excel Downloads ────────────────────────────────────────────────

    public function downloadMedicineReportExcel()
    {
        return Excel::download(
            new MedicineListExport(),
            'medicine-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function downloadRequestReportExcel()
    {
        return Excel::download(
            new RequestListExport(),
            'requests-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function downloadDistributedReportExcel()
    {
        return Excel::download(
            new DistributedListExport(),
            'distributed-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function downloadLowStockReportExcel()
    {
        return Excel::download(
            new LowStockExport(),
            'low-stock-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function downloadExpiringSoonReportExcel()
    {
        return Excel::download(
            new ExpiringSoonExport(),
            'expiring-soon-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}