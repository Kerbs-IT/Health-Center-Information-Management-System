<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineRequestLog;
use Barryvdh\DomPDF\Facade\Pdf;

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
}