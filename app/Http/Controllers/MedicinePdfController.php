<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf;

class MedicinePdfController extends Controller
{
    public function downloadPdf()
    {

        $medicines = Medicine::with('category')
            ->orderBy('medicine_name', 'asc')
            ->get();

        $pdf = SnappyPdf::loadView('reports.medicines-pdf', [
            'medicines' => $medicines
        ]);

        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'Landscape');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);

        $filename = 'Medicine_Inventory_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }
}