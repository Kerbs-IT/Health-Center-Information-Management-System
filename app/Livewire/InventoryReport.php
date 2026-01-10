<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\MedicineRequestLog;
use App\Models\MedicineRequest;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class InventoryReport extends Component
{
    public $startDate;
    public $endDate;
    public $barChartStartDate;
    public $barChartEndDate;
    public $pieChartStartDate;
    public $pieChartEndDate;

    public function mount()
    {
        // Default to current year
        $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        $this->barChartStartDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->barChartEndDate = Carbon::now()->endOfYear()->format('Y-m-d');

        $this->pieChartStartDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->pieChartEndDate = Carbon::now()->endOfYear()->format('Y-m-d');
    }

    public function updateDateRange($startDate, $endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        // Return updated data to JavaScript
        $this->dispatch('dateRangeUpdated',
            dateRangeGivenData: $this->getDateRangeGivenData(),
            dateRangeRequestData: $this->getDateRangeRequestData(),
            topMedicinesData: $this->getTopMedicinesDateRangeData()
        );
    }

    public function updateBarChartDateRange($startDate, $endDate){
        $this->barChartStartDate = $startDate;
        $this->barChartEndDate = $endDate;

        $this->dispatch('barChartUpdated',
            categoriesData: $this->getMedicineCategoriesData()
        );
    }

    public function updatePieChartDateRange($startDate, $endDate){
        $this->pieChartStartDate = $startDate;
        $this->pieChartEndDate = $endDate;

        $this->dispatch('pieChartUpdated',
            pieChartData: $this->getPieChartData()
        );
    }

    public function totalMedicineCount(){
        return Medicine::count();
    }

    public function totalRequests(){
        return MedicineRequest::count();
    }

    public function totalMedicineDispense(){
        return MedicineRequestLog::where('action', 'approved')->sum('quantity');
    }

    public function totalLowStock(){
        return Medicine::where('stock_status', 'Low Stock')->count();
    }

    public function totalExpSoon(){
        return Medicine::where('expiry_status', 'Expiring Soon')->count();
    }

    public function getMedicineCategoriesData(){
        $start = Carbon::parse($this->barChartStartDate);
        $end = Carbon::parse($this->barChartEndDate);
        $daysDiff = $start->diffInDays($end);

        // For single day, show hourly breakdown by category
        if ($daysDiff == 0) {
            // Get all categories that have dispensed medicines today
            $hourlyData = DB::table('medicine_request_logs')
                ->join('medicines', 'medicine_request_logs.medicine_name', '=', 'medicines.medicine_name')
                ->join('categories', 'medicines.category_id', '=', 'categories.category_id')
                ->where('medicine_request_logs.action', 'approved')
                ->whereDate('medicine_request_logs.performed_at', $start)
                ->selectRaw('categories.category_name, HOUR(medicine_request_logs.performed_at) as hour, SUM(medicine_request_logs.quantity) as count')
                ->groupBy('categories.category_id', 'categories.category_name', 'hour')
                ->get();

            // Aggregate by category for the whole day
            $categories = $hourlyData->groupBy('category_name')->map(function($items) {
                return $items->sum('count');
            });

            return [
                'labels' => $categories->keys()->toArray(),
                'data' => $categories->values()->toArray()
            ];
        }

        // For multiple days, use existing aggregate logic
        $categories = DB::table('medicine_request_logs')
            ->join('medicines', 'medicine_request_logs.medicine_name', '=', 'medicines.medicine_name')
            ->join('categories', 'medicines.category_id', '=', 'categories.category_id')
            ->where('medicine_request_logs.action', 'approved')
            ->whereBetween('medicine_request_logs.performed_at', [$start, $end])
            ->select('categories.category_name', DB::raw('SUM(medicine_request_logs.quantity) as count'))
            ->groupBy('categories.category_id', 'categories.category_name')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $categories->pluck('category_name')->toArray(),
            'data' => $categories->pluck('count')->toArray()
        ];
    }

    public function getPieChartData(){
        $start = Carbon::parse($this->pieChartStartDate);
        $end = Carbon::parse($this->pieChartEndDate);
        $daysDiff = $start->diffInDays($end);

        // Get medicines that were active/dispensed in this date range
        if ($daysDiff == 0) {
            // For single day, use whereDate to ensure we capture the entire day
            $medicineIds = MedicineRequestLog::where('action', 'approved')
                ->whereDate('performed_at', $start)
                ->distinct()
                ->pluck('medicine_name');
        } else {
            // For multiple days, use whereBetween
            $medicineIds = MedicineRequestLog::where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->distinct()
                ->pluck('medicine_name');
        }

        // Get current stock status for those medicines
        $inStock = Medicine::whereIn('medicine_name', $medicineIds)
            ->where('stock_status', 'In Stock')
            ->count();

        $lowStock = Medicine::whereIn('medicine_name', $medicineIds)
            ->where('stock_status', 'Low Stock')
            ->count();

        $outOfStock = Medicine::whereIn('medicine_name', $medicineIds)
            ->where('stock_status', 'Out of Stock')
            ->count();

        return [
            'labels' => ['In Stock', 'Low Stock', 'Out of Stock'],
            'data' => [$inStock, $lowStock, $outOfStock]
        ];
    }

    public function getDateRangeGivenData(){
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $daysDiff = $start->diffInDays($end);

        // Determine grouping based on date range
        if ($daysDiff == 0) {
            $records = MedicineRequestLog::selectRaw('HOUR(performed_at) as hour, SUM(quantity) as total')
                ->where('action', 'approved')
                ->whereDate('performed_at', $start)
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('total', 'hour')
                ->toArray();

            $labels = [];
            $fullLabels = [];
            $data = [];
            $currentHour = $start->copy()->startOfDay();

            for ($i = 0; $i < 24; $i++) {
                $labels[] = $currentHour->format('g A');
                $fullLabels[] = $currentHour->format('g:00 A');
                $data[] = $records[$i] ?? 0;
                $currentHour->addHour();
            }

            return [
                'labels' => $labels,
                'fullLabels' => $fullLabels,
                'data' => $data
            ];
        }elseif ($daysDiff >= 364) {
            // Group by month for ranges over 1 year
            $records = MedicineRequestLog::selectRaw('DATE_FORMAT(performed_at, "%Y-%m") as date, SUM(quantity) as total')
                ->where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $labels = [];
            $fullLabels = [];
            $data = [];
            $current = $start->copy()->startOfMonth();

            while ($current <= $end) {
                $dateKey = $current->format('Y-m');
                $labels[] = $current->format('M Y');
                $fullLabels[] = $current->format('F Y');
                $data[] = $records[$dateKey] ?? 0;
                $current->addMonth();
            }
        } elseif ($daysDiff > 60) {
            // Group by week for ranges 2-12 months
            $records = MedicineRequestLog::selectRaw('YEARWEEK(performed_at, 1) as week, SUM(quantity) as total')
                ->where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('total', 'week')
                ->toArray();

            $labels = [];
            $fullLabels = [];
            $data = [];
            $current = $start->copy()->startOfWeek();

            while ($current <= $end) {
                $weekKey = $current->format('oW');
                $labels[] = $current->format('M d');
                $fullLabels[] = 'Week of ' . $current->format('M d, Y');
                $data[] = $records[$weekKey] ?? 0;
                $current->addWeek();
            }
        } else {
            // Group by day for ranges under 2 months
            $records = MedicineRequestLog::selectRaw('DATE(performed_at) as date, SUM(quantity) as total')
                ->where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $period = CarbonPeriod::create($start, $end);
            $labels = [];
            $fullLabels = [];
            $data = [];

            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $labels[] = $date->format('M d');
                $fullLabels[] = $date->format('F d, Y');
                $data[] = $records[$dateKey] ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'fullLabels' => $fullLabels,
            'data' => $data
        ];
    }

    public function getDateRangeRequestData(){
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $daysDiff = $start->diffInDays($end);

        // Determine grouping based on date range

        if ($daysDiff == 0) {
            $records = MedicineRequest::selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
                ->whereDate('created_at', $start)
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('total', 'hour')
                ->toArray();

            $labels = [];
            $fullLabels = [];
            $data = [];
            $currentHour = $start->copy()->startOfDay();

            for ($i = 0; $i < 24; $i++) {
                $labels[] = $currentHour->format('g A');
                $fullLabels[] = $currentHour->format('g:00 A');
                $data[] = $records[$i] ?? 0;
                $currentHour->addHour();
            }

            return [
                'labels' => $labels,
                'fullLabels' => $fullLabels,
                'data' => $data
            ];
        }elseif ($daysDiff >= 364) {
            // Group by month for ranges over 1 year
            $records = MedicineRequest::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as date, COUNT(*) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $labels = [];
            $fullLabels = [];
            $data = [];
            $current = $start->copy()->startOfMonth();

            while ($current <= $end) {
                $dateKey = $current->format('Y-m');
                $labels[] = $current->format('M Y');
                $fullLabels[] = $current->format('F Y');
                $data[] = $records[$dateKey] ?? 0;
                $current->addMonth();
            }
        } elseif ($daysDiff > 60) {
            // Group by week for ranges 2-12 months
            $records = MedicineRequest::selectRaw('YEARWEEK(created_at, 1) as week, COUNT(*) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('total', 'week')
                ->toArray();

            $labels = [];
            $fullLabels = [];
            $data = [];
            $current = $start->copy()->startOfWeek();

            while ($current <= $end) {
                $weekKey = $current->format('oW');
                $labels[] = $current->format('M d');
                $fullLabels[] = 'Week of ' . $current->format('M d, Y');
                $data[] = $records[$weekKey] ?? 0;
                $current->addWeek();
            }
        } else {
            // Group by day for ranges under 2 months
            $records = MedicineRequest::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $period = CarbonPeriod::create($start, $end);
            $labels = [];
            $fullLabels = [];
            $data = [];

            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $labels[] = $date->format('M d');
                $fullLabels[] = $date->format('F d, Y');
                $data[] = $records[$dateKey] ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'fullLabels' => $fullLabels,
            'data' => $data
        ];
    }

    public function getTopMedicinesDateRangeData(){
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $daysDiff = $start->diffInDays($end);

        // Get top 5 medicines for the selected date range
        $topMedicines = MedicineRequestLog::select('medicine_name', DB::raw('SUM(quantity) as total'))
            ->where('action', 'approved')
            ->whereBetween('performed_at', [$start, $end])
            ->groupBy('medicine_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $datasets = [];
        $labels = [];
        $fullLabels = [];

        // If no medicines dispensed in this period, return empty data
        if ($topMedicines->isEmpty()) {
            // Still generate labels for the time period
            if ($daysDiff == 0) {
                $currentHour = $start->copy()->startOfDay();
                for ($i = 0; $i < 24; $i++) {
                    $labels[] = $currentHour->format('g A');
                    $fullLabels[] = $currentHour->format('g:00 A');
                    $currentHour->addHour();
                }
            } elseif ($daysDiff >= 364) {
                $current = $start->copy()->startOfMonth();
                while ($current <= $end) {
                    $labels[] = $current->format('M Y');
                    $fullLabels[] = $current->format('F Y');
                    $current->addMonth();
                }
            } elseif ($daysDiff > 60) {
                $current = $start->copy()->startOfWeek();
                while ($current <= $end) {
                    $labels[] = $current->format('M d');
                    $fullLabels[] = 'Week of ' . $current->format('M d, Y');
                    $current->addWeek();
                }
            } else {
                $period = CarbonPeriod::create($start, $end);
                foreach ($period as $date) {
                    $labels[] = $date->format('M d');
                    $fullLabels[] = $date->format('F d, Y');
                }
            }

            return [
                'labels' => $labels,
                'fullLabels' => $fullLabels,
                'datasets' => []
            ];
        }

        // Handle single day selection
        if ($daysDiff == 0) {
            $currentHour = $start->copy()->startOfDay();

            for ($i = 0; $i < 24; $i++) {
                $labels[] = $currentHour->format('g A');
                $fullLabels[] = $currentHour->format('g:00 A');
                $currentHour->addHour();
            }

            foreach ($topMedicines as $medicine) {
                $records = MedicineRequestLog::selectRaw('HOUR(performed_at) as hour, SUM(quantity) as total')
                    ->where('medicine_name', $medicine->medicine_name)
                    ->where('action', 'approved')
                    ->whereDate('performed_at', $start)
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->pluck('total', 'hour')
                    ->toArray();

                $data = [];
                for ($i = 0; $i < 24; $i++) {
                    $data[] = $records[$i] ?? 0;
                }

                $datasets[] = [
                    'label' => $medicine->medicine_name,
                    'data' => $data
                ];
            }
        } elseif ($daysDiff >= 364) {
            // Group by month for ranges over 1 year
            $current = $start->copy()->startOfMonth();
            while ($current <= $end) {
                $labels[] = $current->format('M Y');
                $fullLabels[] = $current->format('F Y');
                $current->addMonth();
            }

            foreach ($topMedicines as $medicine) {
                $records = MedicineRequestLog::selectRaw('DATE_FORMAT(performed_at, "%Y-%m") as date, SUM(quantity) as total')
                    ->where('medicine_name', $medicine->medicine_name)
                    ->where('action', 'approved')
                    ->whereBetween('performed_at', [$start, $end])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('total', 'date')
                    ->toArray();

                $data = [];
                $current = $start->copy()->startOfMonth();
                while ($current <= $end) {
                    $dateKey = $current->format('Y-m');
                    $data[] = $records[$dateKey] ?? 0;
                    $current->addMonth();
                }

                $datasets[] = [
                    'label' => $medicine->medicine_name,
                    'data' => $data
                ];
            }
        } elseif ($daysDiff > 60) {
            // Group by week for ranges 2-12 months
            $current = $start->copy()->startOfWeek();
            while ($current <= $end) {
                $labels[] = $current->format('M d');
                $fullLabels[] = 'Week of ' . $current->format('M d, Y');
                $current->addWeek();
            }

            foreach ($topMedicines as $medicine) {
                $records = MedicineRequestLog::selectRaw('YEARWEEK(performed_at, 1) as week, SUM(quantity) as total')
                    ->where('medicine_name', $medicine->medicine_name)
                    ->where('action', 'approved')
                    ->whereBetween('performed_at', [$start, $end])
                    ->groupBy('week')
                    ->orderBy('week')
                    ->pluck('total', 'week')
                    ->toArray();

                $data = [];
                $current = $start->copy()->startOfWeek();
                while ($current <= $end) {
                    $weekKey = $current->format('oW');
                    $data[] = $records[$weekKey] ?? 0;
                    $current->addWeek();
                }

                $datasets[] = [
                    'label' => $medicine->medicine_name,
                    'data' => $data
                ];
            }
        } else {
            // Group by day for ranges under 2 months
            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $labels[] = $date->format('M d');
                $fullLabels[] = $date->format('F d, Y');
            }

            foreach ($topMedicines as $medicine) {
                $records = MedicineRequestLog::selectRaw('DATE(performed_at) as date, SUM(quantity) as total')
                    ->where('medicine_name', $medicine->medicine_name)
                    ->where('action', 'approved')
                    ->whereBetween('performed_at', [$start, $end])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->pluck('total', 'date')
                    ->toArray();

                $data = [];
                foreach (CarbonPeriod::create($start, $end) as $date) {
                    $dateKey = $date->format('Y-m-d');
                    $data[] = $records[$dateKey] ?? 0;
                }

                $datasets[] = [
                    'label' => $medicine->medicine_name,
                    'data' => $data
                ];
            }
        }

        return [
            'labels' => $labels,
            'fullLabels' => $fullLabels,
            'datasets' => $datasets
        ];
    }

    public function getAllMedicinesData(){
        return Medicine::with('category')->select('medicine_name', 'category_id', 'dosage', 'stock', 'stock_status','expiry_date')
        ->orderBy('medicine_name')
        ->get()
        ->map(function($medicine){
            $medicine->category_name = $medicine->category ? $medicine->category->category_name : 'N/A';
            return $medicine;
        });
    }

    public function getAllRequestsData(){
        return MedicineRequest::with(['medicine', 'patients', 'user'])
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
    }

    public function getAllDistributedData(){
        return MedicineRequestLog::where('action', 'approved')
            ->select('medicine_request_id', 'patient_name', 'medicine_name', 'quantity', 'performed_at')
            ->orderByDesc('performed_at')
            ->get();
    }

    public function getLowStockData(){
        return Medicine::where('stock_status', 'Low Stock')
            ->select('medicine_name', 'dosage', 'stock', 'expiry_date', 'expiry_status')
            ->orderBy('stock', 'asc')
            ->get();
    }

    public function getExpiringSoonData(){
        return Medicine::where('expiry_status', 'Expiring Soon')
        ->select('medicine_name', 'dosage', 'stock', 'stock_status', 'expiry_date')
        ->orderBy('expiry_date', 'asc')
        ->get();
    }

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
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);

        return MedicineRequestLog::select('medicine_name', DB::raw('SUM(quantity) as total_dispensed'))
            ->where('action', 'approved')
            ->whereBetween('performed_at', [$start, $end])
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

    public function generateReport()
    {
        try {
            // Get the date-filtered data
            $monthlyGivenData = $this->getDateRangeGivenData();
            $requestTrendData = $this->getDateRangeRequestData();
            $categoriesData = $this->getMedicineCategoriesData();
            $pieChartData = $this->getPieChartData();
            $topDispensedTable = $this->getTopDispensedTable();

            $data = [
                'totalMedicines' => $this->totalMedicineCount(),
                'totalRequests' => $this->totalRequests(),
                'totalDispensed' => $this->totalMedicineDispense(),
                'lowStock' => $this->totalLowStock(),
                'expiringSoon' => $this->totalExpSoon(),
                'categoriesData' => $categoriesData,
                'pieChartData' => $pieChartData,
                'monthlyGivenData' => $monthlyGivenData,
                'requestTrendData' => $requestTrendData,
                'topDispensedTable' => $topDispensedTable,
                'generatedDate' => now()->format('F d, Y h:i A'),
                'startDate' => Carbon::parse($this->startDate)->format('F d, Y'),
                'endDate' => Carbon::parse($this->endDate)->format('F d, Y'),
                'barChartStartDate' => Carbon::parse($this->barChartStartDate)->format('F d, Y'),
                'barChartEndDate' => Carbon::parse($this->barChartEndDate)->format('F d, Y'),
                'pieChartStartDate' => Carbon::parse($this->pieChartStartDate)->format('F d, Y'),
                'pieChartEndDate' => Carbon::parse($this->pieChartEndDate)->format('F d, Y'),
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
            'dateRangeGivenData' => $this->getDateRangeGivenData(),
            'dateRangeRequestData' => $this->getDateRangeRequestData(),
            'topMedicinesData' => $this->getTopMedicinesDateRangeData(),
        ])->layout('livewire.layouts.base');
    }
}