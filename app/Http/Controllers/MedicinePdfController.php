<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
}