<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\MedicineRequestLog;
use App\Models\MedicineRequest;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf;

class InventoryReport extends Component
{
    public function totalMedicineCount(){
        return Medicine::count();
    }

    // NEW: Total Requests (replaces totalVaccineCount)
    public function totalRequests(){
        return MedicineRequest::count();
    }

    public function totalMedicineDispense(){
        // Only count approved medicines
        return MedicineRequestLog::where('action', 'approved')->sum('quantity');
    }

    public function totalLowStock(){
        return Medicine::where('stock_status', 'Low Stock')->count();
    }

    public function totalExpSoon(){
        return Medicine::where('expiry_status', 'Expiring Soon')->count();
    }

    //
    public function getMedicineCategoriesData(){
        $categories = DB::table('medicines')
            ->join('categories', 'medicines.category_id', '=', 'categories.category_id')
            ->select('categories.category_name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.category_id', 'categories.category_name')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $categories->pluck('category_name')->toArray(),
            'data' => $categories->pluck('count')->toArray()
        ];
    }

    // PIE CHART DATA: Stock Level Distribution
    public function getPieChartData(){
        $inStock = Medicine::where('stock_status', 'In Stock')->count();
        $lowStock = Medicine::where('stock_status', 'Low Stock')->count();
        $outOfStock = Medicine::where('stock_status', 'Out of Stock')->count();

        return [
            'labels' => ['In Stock', 'Low Stock', 'Out of Stock'],
            'data' => [$inStock, $lowStock, $outOfStock]
        ];
    }

// In InventoryReport.php, update these two methods:

    // LINE CHART DATA: Monthly Medicine Given
    public function getMonthlyGivenData(){
        $monthlyData = MedicineRequestLog::selectRaw('MONTH(performed_at) as month, SUM(quantity) as total')
            ->where('action', 'approved')
            ->whereYear('performed_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months with 0
        $completeData = [];
        for ($i = 1; $i <= 12; $i++) {
            $completeData[] = $monthlyData[$i] ?? 0;
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'fullLabels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'data' => $completeData
        ];
    }

    // LINE CHART DATA: Medicine Request Trend
    public function getRequestTrendData(){
        $monthlyRequests = MedicineRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months with 0
        $completeData = [];
        for ($i = 1; $i <= 12; $i++) {
            $completeData[] = $monthlyRequests[$i] ?? 0;
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'fullLabels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'data' => $completeData
        ];
    }

    // LINE CHART DATA: Top 5 Most Dispensed Medicines
    public function getTopMedicinesData(){
        $topMedicines = MedicineRequestLog::select('medicine_name', DB::raw('SUM(quantity) as total'))
            ->where('action', 'approved')
            ->groupBy('medicine_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Get monthly data for each top medicine
        $datasets = [];
        foreach ($topMedicines as $medicine) {
            $monthlyData = MedicineRequestLog::selectRaw('MONTH(performed_at) as month, SUM(quantity) as total')
                ->where('medicine_name', $medicine->medicine_name)
                ->where('action', 'approved')
                ->whereYear('performed_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            // Fill in missing months with 0
            $completeData = [];
            for ($i = 1; $i <= 12; $i++) {
                $completeData[] = $monthlyData[$i] ?? 0;
            }

            $datasets[] = [
                'label' => $medicine->medicine_name,
                'data' => $completeData
            ];
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'datasets' => $datasets
        ];
    }


    // Get MedicineData
    public function getAllMedicinesData(){
        return Medicine::with('category')->select('medicine_name', 'category_id', 'dosage', 'stock', 'stock_status','expiry_date')
        ->orderBy('medicine_name')
        ->get()
        ->map(function($medicine){
            $medicine->category_name = $medicine->category ? $medicine->category->category_name : 'N/A';
            return $medicine;
        });
    }

    // Get All Requests Medicine Data
    public function getAllRequestsData(){
        return MedicineRequest::with(['medicine', 'patients.user'])
            ->select('id', 'patients_id', 'medicine_id', 'quantity_requested', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($request) {
                $request->full_name = $request->patients && $request->patients->user
                    ? $request->patients->user->name
                    : 'N/A';
                $request->medicine_name = $request->medicine
                    ? $request->medicine->medicine_name
                    : 'N/A';
                $request->dosage = $request->medicine ? $request->medicine->dosage : 'N/A';
                return $request;
            });
    }

    // Get All distributed Medicine Data
    public function getAllDistributedData(){
        return MedicineRequestLog::where('action', 'approved')
            ->select('medicine_request_id', 'patient_name', 'medicine_name', 'quantity', 'performed_at')
            ->orderByDesc('performed_at')
            ->get();
    }
    // Get All Low Stock Medicine Data
    public function getLowStockData(){
        return Medicine::where('stock_status', 'Low Stock')
            ->select('medicine_name', 'dosage', 'stock', 'expiry_date', 'expiry_status')
            ->orderBy('stock', 'asc')
            ->get();
    }
    // Get All Expiring Medicine
    public function getExpiringSoonData(){
        return Medicine::where('expiry_status', 'Expiring Soon')
        ->select('medicine_name', 'dosage', 'stock', 'stock_status', 'expiry_date')
        ->orderBy('expiry_date', 'asc')
        ->get();
    }

       // PDF Download Method






    // Get table data for PDF
    public function getInventoryTableData(){
        return Medicine::with('category')
            ->select('medicine_name', 'category_id', 'dosage', 'stock', 'status', 'expiry_date')
            ->orderBy('medicine_name')
            ->get()
            ->map(function($medicine) {
                $medicine->category_name = $medicine->category ? $medicine->category->category_name : 'N/A';
                return $medicine;
            });
    }

    public function getTopDispensedTable(){
        return MedicineRequestLog::select('medicine_name', DB::raw('SUM(quantity) as total_dispensed'))
            ->where('action', 'approved')
            ->groupBy('medicine_name')
            ->orderByDesc('total_dispensed')
            ->limit(10)
            ->get();
    }

    public function getRequestStatusTable(){
        return MedicineRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
    }

    // Generate PDF Report
// Replace your generateReport() method in InventoryReport.php with this:

    public function generateReport()
    {
        try {
            $data = [
                'totalMedicines' => $this->totalMedicineCount(),
                'totalRequests' => $this->totalRequests(),
                'totalDispensed' => $this->totalMedicineDispense(),
                'lowStock' => $this->totalLowStock(),
                'expiringSoon' => $this->totalExpSoon(),
                'categoriesData' => $this->getMedicineCategoriesData(),
                'pieChartData' => $this->getPieChartData(),
                'monthlyGivenData' => $this->getMonthlyGivenData(),
                'requestTrendData' => $this->getRequestTrendData(),
                'topDispensedTable' => $this->getTopDispensedTable(),
                'requestStatusTable' => $this->getRequestStatusTable(),
                'generatedDate' => now()->format('F d, Y h:i A')
            ];

            $pdf = SnappyPdf::loadView('reports.inventory-report-pdf', $data)
                ->setOption('margin-top', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10)
                ->setOption('enable-local-file-access', true);

            $filename = 'inventory-report-' . now()->format('Y-m-d-His') . '.pdf';

            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $filename);

        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to generate report: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.inventory-report', [
            'categoriesData' => $this->getMedicineCategoriesData(),
            'pieChartData' => $this->getPieChartData(),
            'monthlyGivenData' => $this->getMonthlyGivenData(),
            'requestTrendData' => $this->getRequestTrendData(),
            'topMedicinesData' => $this->getTopMedicinesData(),
        ])->layout('livewire.layouts.base');
    }
}