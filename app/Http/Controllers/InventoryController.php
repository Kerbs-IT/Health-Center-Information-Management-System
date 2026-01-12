<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\MedicineRequest;
use App\Models\MedicineRequestLog;
use Barryvdh\Snappy\Facades\SnappyPdf;

class InventoryController extends Controller
{
    public function downloadMedicineReport()
    {
        // Use stored category_name instead of relationship
        $medicines = Medicine::select('medicine_name', 'category_id', 'category_name', 'dosage', 'stock', 'stock_status', 'expiry_date')
            ->orderBy('medicine_name')
            ->get()
            ->map(function($medicine){
                // Use the category_name accessor which handles both stored and relationship values
                $medicine->category_display = $medicine->category_name;
                return $medicine;
            });

        $data = [
            'medicines' => $medicines,
            'total' => Medicine::count(),
            'generatedDate' => now()->format('F d, Y h:i A')
        ];

        $pdf = SnappyPdf::loadView('reports.medicine-list-pdf', $data);
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'Portrait');
        $pdf->setOption('margin-top', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-bottom', '15mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('footer-center', 'Page [page] of [topage]');
        $pdf->setOption('footer-font-size', '9');
        $pdf->setOption('footer-spacing', '5');

        return $pdf->download('medicine-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadRequestReport()
    {
        // Use stored medicine details instead of relationship
        $requests = MedicineRequest::select('id', 'patients_id', 'user_id', 'medicine_id', 'medicine_name', 'medicine_dosage', 'quantity_requested', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($request) {
                // Use the requester_name attribute from the model
                $request->requester_name = $request->requester_name;

                // Use stored medicine details (accessors handle fallback to relationship)
                $request->medicine_display = $request->medicine_name;
                $request->dosage_display = $request->medicine_dosage;

                return $request;
            });

        $data = [
            'requests' => $requests,
            'total' => MedicineRequest::count(),
            'generatedDate' => now()->format('F d, Y h:i A')
        ];

        $pdf = SnappyPdf::loadView('reports.request-list-pdf', $data);
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'Portrait');
        $pdf->setOption('margin-top', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-bottom', '15mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('footer-center', 'Page [page] of [topage]');
        $pdf->setOption('footer-font-size', '9');
        $pdf->setOption('footer-spacing', '5');

        return $pdf->download('requests-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadDistributedReport(){
        // Medicine request logs already store medicine_name, so no changes needed
        $distributed = MedicineRequestLog::where('action', 'approved')
            ->select('medicine_request_id', 'patient_name', 'medicine_name','dosage', 'quantity', 'performed_at')
            ->orderByDesc('performed_at')
            ->get();

        $data = [
            'distributed'       => $distributed,
            'total'             => MedicineRequestLog::where('action', 'approved')->count(),
            'generatedDate'     => now()->format('F d, Y h:i A')
        ];
        $pdf = SnappyPdf::loadView('reports.distribute-list-pdf', $data);
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'Portrait');
        $pdf->setOption('margin-top', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-bottom', '15mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('footer-center', 'Page [page] of [topage]');
        $pdf->setOption('footer-font-size', '9');
        $pdf->setOption('footer-spacing', '5');

        return $pdf->download('distributed-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadLowStockReport()
    {
        // Use stored category_name
        $medicines = Medicine::where('stock_status', 'Low Stock')
            ->select('medicine_name', 'category_name', 'dosage', 'stock', 'expiry_date', 'expiry_status')
            ->orderBy('stock', 'asc')
            ->get()
            ->map(function($medicine){
                $medicine->category_display = $medicine->category_name;
                return $medicine;
            });

        $data = [
            'medicines' => $medicines,
            'total' => $medicines->count(),
            'generatedDate' => now()->format('F d, Y h:i A')
        ];

        $pdf = SnappyPdf::loadView('reports.low-stock-pdf', $data);
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'Portrait');
        $pdf->setOption('margin-top', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-bottom', '15mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('footer-center', 'Page [page] of [topage]');
        $pdf->setOption('footer-font-size', '9');
        $pdf->setOption('footer-spacing', '5');
        return $pdf->download('low-stock-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function downloadExpiringSoonReport()
    {
        // Use stored category_name
        $medicines = Medicine::where('expiry_status', 'Expiring Soon')
            ->select('medicine_name', 'category_name', 'dosage', 'stock', 'stock_status',  'expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->map(function($medicine){
                $medicine->category_display = $medicine->category_name;
                return $medicine;
            });

        $data = [
            'medicines' => $medicines,
            'total' => $medicines->count(),
            'generatedDate' => now()->format('F d, Y h:i A')
        ];

        $pdf = SnappyPdf::loadView('reports.expiring-soon-pdf', $data);
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'Portrait');
        $pdf->setOption('margin-top', '5mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-bottom', '15mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('footer-center', 'Page [page] of [topage]');
        $pdf->setOption('footer-font-size', '9');
        $pdf->setOption('footer-spacing', '5');
        return $pdf->download('expiring-soon-list-' . now()->format('Y-m-d') . '.pdf');
    }
}