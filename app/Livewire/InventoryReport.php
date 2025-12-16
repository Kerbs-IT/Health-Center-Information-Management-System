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
        return Medicine::where('status', 'Low Stock')->count();
    }

    public function totalExpSoon(){
        return Medicine::where('status', 'Expiring Soon')->count();
    }

    // NEW: Medicine Categories Bar Chart Data (replaces getBarChartData)
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
        $inStock = Medicine::where('status', 'In Stock')->count();
        $lowStock = Medicine::where('status', 'Low Stock')->count();
        $outOfStock = Medicine::where('status', 'Out of Stock')->count();

        return [
            'labels' => ['In Stock', 'Low Stock', 'Out of Stock'],
            'data' => [$inStock, $lowStock, $outOfStock]
        ];
    }

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

    // DAILY LINE CHART DATA: Daily Medicine Dispensed (Last 30 Days)
    public function getDailyDispensingData(){
        $dailyData = MedicineRequestLog::selectRaw('DATE(performed_at) as date, SUM(quantity) as total')
            ->where('action', 'approved')
            ->where('performed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $values = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->format('M d');

            $found = $dailyData->firstWhere('date', $date);
            $values[] = $found ? $found->total : 0;
        }

        return [
            'labels' => $dates,
            'data' => $values
        ];
    }

    // DAILY LINE CHART DATA: Daily Request Count (Last 30 Days)
    public function getDailyRequestsData(){
        $dailyData = MedicineRequest::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $values = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->format('M d');

            $found = $dailyData->firstWhere('date', $date);
            $values[] = $found ? $found->total : 0;
        }

        return [
            'labels' => $dates,
            'data' => $values
        ];
    }

    // DAILY LINE CHART DATA: Daily Response Time (Last 30 Days)
    public function getDailyResponseTimeData(){
        $dailyData = DB::table('medicine_requests as mr')
            ->join('medicine_request_logs as mrl', 'mr.id', '=', 'mrl.medicine_request_id')
            ->selectRaw('
                DATE(mr.created_at) as date,
                AVG(TIMESTAMPDIFF(MINUTE, mr.created_at, mrl.performed_at)) as avg_minutes
            ')
            ->where('mr.created_at', '>=', now()->subDays(30))
            ->where('mrl.action', 'approved')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $values = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->format('M d');

            $found = $dailyData->firstWhere('date', $date);
            $values[] = $found ? round($found->avg_minutes) : 0;
        }

        return [
            'labels' => $dates,
            'data' => $values
        ];
    }

    // WEEKLY LINE CHART DATA: Weekly Medicine Dispensed (Last 12 Weeks)
    public function getWeeklyDispensingData(){
        $weeklyData = MedicineRequestLog::selectRaw('
                YEAR(performed_at) as year,
                WEEK(performed_at) as week,
                SUM(quantity) as total
            ')
            ->where('action', 'approved')
            ->where('performed_at', '>=', now()->subWeeks(12))
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subWeeks($i);
            $year = $date->year;
            $week = $date->week;

            $labels[] = 'Week ' . $week;

            $found = $weeklyData->where('year', $year)->where('week', $week)->first();
            $values[] = $found ? $found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }

    // WEEKLY LINE CHART DATA: Weekly Request Volume (Last 12 Weeks)
    public function getWeeklyRequestsData(){
        $weeklyData = MedicineRequest::selectRaw('
                YEAR(created_at) as year,
                WEEK(created_at) as week,
                COUNT(*) as total
            ')
            ->where('created_at', '>=', now()->subWeeks(12))
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subWeeks($i);
            $year = $date->year;
            $week = $date->week;

            $labels[] = 'Week ' . $week;

            $found = $weeklyData->where('year', $year)->where('week', $week)->first();
            $values[] = $found ? $found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }

    // WEEKLY LINE CHART DATA: Weekly Approval Rate (Last 12 Weeks)
    public function getWeeklyApprovalRateData(){
        $weeklyData = MedicineRequest::selectRaw('
                YEAR(created_at) as year,
                WEEK(created_at) as week,
                COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed
            ')
            ->where('created_at', '>=', now()->subWeeks(12))
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subWeeks($i);
            $year = $date->year;
            $week = $date->week;

            $labels[] = 'Week ' . $week;

            $found = $weeklyData->where('year', $year)->where('week', $week)->first();
            if ($found && $found->total > 0) {
                $rate = ($found->completed / $found->total) * 100;
                $values[] = round($rate, 1);
            } else {
                $values[] = 0;
            }
        }

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }

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

                // Table data
                'inventoryTable' => $this->getInventoryTableData(),
                'topDispensedTable' => $this->getTopDispensedTable(),
                'requestStatusTable' => $this->getRequestStatusTable(),

                'generatedDate' => now()->format('F d, Y h:i A')
            ];

            $pdf = SnappyPdf::loadView('reports.inventory-report-pdf', $data);

            $pdf->setOption('enable-javascript', true);
            $pdf->setOption('javascript-delay', 2000);
            $pdf->setOption('no-stop-slow-scripts', true);
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('margin-top', 10);
            $pdf->setOption('margin-right', 10);
            $pdf->setOption('margin-bottom', 10);
            $pdf->setOption('margin-left', 10);

            $filename = 'inventory-report-' . now()->format('Y-m-d') . '.pdf';

            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, $filename);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate report: ' . $e->getMessage());
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