<?php

namespace App\Http\Controllers;

use App\Exports\MedicineInventoryExport;
use App\Models\Medicine;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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

        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'defaultFont'         => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'     => false,
            'chroot'              => public_path(),
        ]);

        return $pdf->download('Medicine_Inventory_' . date('Y-m-d_His') . '.pdf');
    }

    public function downloadCsv()
    {
        $filename = 'medicine-inventory-' . date('Y-m-d') . '.xlsx';

        return Excel::download(new MedicineInventoryExport(), $filename);
    }
}