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
use Carbon\Carbon;
class InventoryController extends Controller
{
    // ─── PDF Downloads ─────────────────────────────────────────────────

    public function downloadMedicineReport(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        $medicines = Medicine::with([
                    'category',
        'batches'    => fn($q) => $q->orderBy('expiry_date', 'asc'),
        'allBatches' => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])
            ->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay(),
            ])
            ->orderBy('medicine_name')
            ->get()
            ->map(fn($m) => tap($m, fn($m) => $m->category_name = $m->category?->category_name ?? 'N/A'));

        $data = [
            'medicines'     => $medicines,
            'total'         => $medicines->count(),
            'startDate'     => Carbon::parse($start)->format('F d, Y'),
            'endDate'       => Carbon::parse($end)->format('F d, Y'),
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

    public function downloadRequestReport(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        $requests = MedicineRequest::with(['medicine', 'patients', 'user'])
            ->select('id','patients_id','user_id','medicine_id','quantity_requested','status','created_at')
            ->whereBetween('created_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay(),
            ])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => tap($r, function($r) {
                $r->medicine_name = $r->medicine?->medicine_name ?? 'N/A';
                $r->dosage        = $r->medicine?->dosage ?? 'N/A';
            }));

        $data = [
            'requests'      => $requests,
            'total'         => $requests->count(),
            'startDate'     => Carbon::parse($start)->format('F d, Y'),
            'endDate'       => Carbon::parse($end)->format('F d, Y'),
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

    public function downloadDistributedReport(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        $distributed = MedicineRequestLog::where('action', 'dispensed')
            ->whereBetween('performed_at', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay(),
            ])
            ->select('medicine_request_id', 'patient_name', 'medicine_name', 'dosage', 'quantity', 'performed_at')
            ->orderByDesc('performed_at')
            ->get();

        $data = [
            'distributed'   => $distributed,
            'total'         => $distributed->count(),
            'totalQuantity' => $distributed->sum('quantity'),
            'startDate'     => Carbon::parse($start)->format('F d, Y'),
            'endDate'       => Carbon::parse($end)->format('F d, Y'),
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


    public function downloadLowStockReport(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        $report    = new \App\Livewire\InventoryReport();
        $medicines = $report->getLowStockCollectionByDate($start, $end);

        $data = [
            'medicines'     => $medicines,
            'total'         => $medicines->count(),
            'startDate'     => Carbon::parse($start)->format('F d, Y'),
            'endDate'       => Carbon::parse($end)->format('F d, Y'),
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

    public function downloadExpiringSoonReport(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        $batches = MedicineBatch::with([
            'medicine' => fn($q) => $q->with([
                'batches' => fn($q2) => $q2->where('expiry_date', '>', now()),
            ]),
        ])
        ->where('expiry_status', 'Expiring Soon')
        ->whereBetween('expiry_date', [
            Carbon::parse($start)->startOfDay(),
            Carbon::parse($end)->endOfDay(),
        ])
        ->orderBy('expiry_date', 'asc')
        ->get()
        ->map(function ($batch) {
            $available = $batch->medicine?->batches
                ->sum(fn($b) => max(0, $b->quantity - $b->reserved_quantity)) ?? 0;
            $batch->available_stock       = $available;
            $batch->computed_stock_status = $available <= 0 ? 'Out of Stock'
                : ($available <= 10 ? 'Low Stock' : 'In Stock');
            return $batch;
        });

        $data = [
            'batches'       => $batches,
            'total'         => $batches->count(),
            'startDate'     => Carbon::parse($start)->format('F d, Y'),
            'endDate'       => Carbon::parse($end)->format('F d, Y'),
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

    public function downloadMedicineReportExcel(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        return Excel::download(
            new MedicineListExport($start, $end),
            'medicine-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function downloadRequestReportExcel(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        return Excel::download(
            new RequestListExport($start, $end),
            'requests-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function downloadDistributedReportExcel(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        return Excel::download(
            new DistributedListExport($start, $end),
            'distributed-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function downloadLowStockReportExcel(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        return Excel::download(
            new LowStockExport($start, $end),
            'low-stock-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }


    public function downloadExpiringSoonReportExcel(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end   = $request->input('end',   Carbon::now()->endOfYear()->format('Y-m-d'));

        return Excel::download(
            new ExpiringSoonExport($start, $end),
            'expiring-soon-list-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}