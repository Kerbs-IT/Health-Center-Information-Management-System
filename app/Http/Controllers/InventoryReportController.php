<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineRequestLog;
use App\Models\MedicineRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryReportController extends Controller
{
    public function showReportView(Request $request)
    {
        // Get date ranges from request or use defaults
        $startDate = $request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfYear()->format('Y-m-d'));
        $barChartStartDate = $request->input('bar_start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $barChartEndDate = $request->input('bar_end_date', Carbon::now()->endOfYear()->format('Y-m-d'));
        $pieChartStartDate = $request->input('pie_start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $pieChartEndDate = $request->input('pie_end_date', Carbon::now()->endOfYear()->format('Y-m-d'));

        // Prepare data
        $data = [
            'totalMedicines' => Medicine::count(),
            'totalRequests' => MedicineRequest::count(),
            'totalDispensed' => MedicineRequestLog::where('action', 'approved')->sum('quantity'),
            'lowStock' => Medicine::where('stock_status', 'Low Stock')->count(),
            'expiringSoon' => Medicine::where('expiry_status', 'Expiring Soon')->count(),

            'categoriesData' => $this->getMedicineCategoriesData($barChartStartDate, $barChartEndDate),
            'pieChartData' => $this->getPieChartData($pieChartStartDate, $pieChartEndDate),
            'dateRangeGivenData' => $this->getDateRangeGivenData($startDate, $endDate),
            'requestTrendData' => $this->getDateRangeRequestData($startDate, $endDate),
            'topMedicinesData' => $this->getTopMedicinesDateRangeData($startDate, $endDate),

            'generatedDate' => now()->format('F d, Y h:i A'),
            'startDate' => Carbon::parse($startDate)->format('F d, Y'),
            'endDate' => Carbon::parse($endDate)->format('F d, Y'),
            'barChartStartDate' => Carbon::parse($barChartStartDate)->format('F d, Y'),
            'barChartEndDate' => Carbon::parse($barChartEndDate)->format('F d, Y'),
            'pieChartStartDate' => Carbon::parse($pieChartStartDate)->format('F d, Y'),
            'pieChartEndDate' => Carbon::parse($pieChartEndDate)->format('F d, Y'),
        ];

        return view('reports.inventory-pdf', $data);
    }
public function downloadReport(Request $request)
{
    // Get date ranges from request or use defaults
    $startDate = $request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->endOfYear()->format('Y-m-d'));
    $barChartStartDate = $request->input('bar_start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
    $barChartEndDate = $request->input('bar_end_date', Carbon::now()->endOfYear()->format('Y-m-d'));
    $pieChartStartDate = $request->input('pie_start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
    $pieChartEndDate = $request->input('pie_end_date', Carbon::now()->endOfYear()->format('Y-m-d'));

    // Prepare data
    $data = [
        'totalMedicines' => Medicine::count(),
        'totalRequests' => MedicineRequest::count(),
        'totalDispensed' => MedicineRequestLog::where('action', 'approved')->sum('quantity'),
        'lowStock' => Medicine::where('stock_status', 'Low Stock')->count(),
        'expiringSoon' => Medicine::where('expiry_status', 'Expiring Soon')->count(),

        'categoriesData' => $this->getMedicineCategoriesData($barChartStartDate, $barChartEndDate),
        'pieChartData' => $this->getPieChartData($pieChartStartDate, $pieChartEndDate),
        'dateRangeGivenData' => $this->getDateRangeGivenData($startDate, $endDate),
        'requestTrendData' => $this->getDateRangeRequestData($startDate, $endDate),
        'topMedicinesData' => $this->getTopMedicinesDateRangeData($startDate, $endDate),

        'generatedDate' => now()->format('F d, Y h:i A'),
        'startDate' => Carbon::parse($startDate)->format('F d, Y'),
        'endDate' => Carbon::parse($endDate)->format('F d, Y'),
        'barChartStartDate' => Carbon::parse($barChartStartDate)->format('F d, Y'),
        'barChartEndDate' => Carbon::parse($barChartEndDate)->format('F d, Y'),
        'pieChartStartDate' => Carbon::parse($pieChartStartDate)->format('F d, Y'),
        'pieChartEndDate' => Carbon::parse($pieChartEndDate)->format('F d, Y'),
    ];

    // Generate PDF
    $pdf = Pdf::loadView('reports.inventory-pdf', $data)
        ->setPaper('a4', 'portrait')
        ->setOption('enable-javascript', true)
        ->setOption('javascript-delay', 2000)
        ->setOption('enable-smart-shrinking', true)
        ->setOption('no-stop-slow-scripts', true);

    // Download PDF
    $filename = 'Inventory_Report_' . now()->format('Y-m-d_His') . '.pdf';
    return $pdf->download($filename);
}

    private function getMedicineCategoriesData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $daysDiff = $start->diffInDays($end);

        // For single day, show hourly breakdown by category
        if ($daysDiff == 0) {
            $hourlyData = DB::table('medicine_request_logs')
                ->join('medicines', 'medicine_request_logs.medicine_name', '=', 'medicines.medicine_name')
                ->join('categories', 'medicines.category_id', '=', 'categories.category_id')
                ->where('medicine_request_logs.action', 'approved')
                ->whereDate('medicine_request_logs.performed_at', $start)
                ->selectRaw('categories.category_name, HOUR(medicine_request_logs.performed_at) as hour, SUM(medicine_request_logs.quantity) as count')
                ->groupBy('categories.category_id', 'categories.category_name', 'hour')
                ->get();

            $categories = $hourlyData->groupBy('category_name')->map(function($items) {
                return $items->sum('count');
            });

            return [
                'labels' => $categories->keys()->toArray(),
                'data' => $categories->values()->toArray()
            ];
        }

        // For multiple days
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

    private function getPieChartData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        $daysDiff = $start->diffInDays($end);

        $medicineNames = collect();

        // Get medicines dispensed in this date range
        if ($daysDiff == 0) {
            $dispensedMedicines = MedicineRequestLog::where('action', 'approved')
                ->whereDate('performed_at', $start)
                ->distinct()
                ->pluck('medicine_name');
        } else {
            $dispensedMedicines = MedicineRequestLog::where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->distinct()
                ->pluck('medicine_name');
        }

        // Get medicines created/updated in this date range
        if ($daysDiff == 0) {
            $modifiedMedicines = Medicine::whereDate('updated_at', $start)
                ->orWhereDate('created_at', $start)
                ->pluck('medicine_name');
        } else {
            $modifiedMedicines = Medicine::whereBetween('updated_at', [$start, $end])
                ->orWhereBetween('created_at', [$start, $end])
                ->pluck('medicine_name');
        }

        $medicineNames = $dispensedMedicines->merge($modifiedMedicines)->unique();

        if ($medicineNames->isEmpty()) {
            return [
                'labels' => ['In Stock', 'Low Stock', 'Out of Stock'],
                'data' => [0, 0, 0]
            ];
        }

        $inStock = Medicine::whereIn('medicine_name', $medicineNames)
            ->where('stock_status', 'In Stock')->count();
        $lowStock = Medicine::whereIn('medicine_name', $medicineNames)
            ->where('stock_status', 'Low Stock')->count();
        $outOfStock = Medicine::whereIn('medicine_name', $medicineNames)
            ->where('stock_status', 'Out of Stock')->count();

        return [
            'labels' => ['In Stock', 'Low Stock', 'Out of Stock'],
            'data' => [$inStock, $lowStock, $outOfStock]
        ];
    }

    private function getDateRangeGivenData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $daysDiff = $start->diffInDays($end);

        // Single day - hourly breakdown
        if ($daysDiff == 0) {
            $records = MedicineRequestLog::selectRaw('HOUR(performed_at) as hour, SUM(quantity) as total')
                ->where('action', 'approved')
                ->whereDate('performed_at', $start)
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('total', 'hour')
                ->toArray();

            $labels = [];
            $data = [];
            $currentHour = $start->copy()->startOfDay();

            for ($i = 0; $i < 24; $i++) {
                $labels[] = $currentHour->format('g A');
                $data[] = $records[$i] ?? 0;
                $currentHour->addHour();
            }

            return ['labels' => $labels, 'data' => $data];
        }

        // Over 1 year - monthly breakdown
        elseif ($daysDiff >= 364) {
            $records = MedicineRequestLog::selectRaw('DATE_FORMAT(performed_at, "%Y-%m") as date, SUM(quantity) as total')
                ->where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $labels = [];
            $data = [];
            $current = $start->copy()->startOfMonth();

            while ($current <= $end) {
                $dateKey = $current->format('Y-m');
                $labels[] = $current->format('M Y');
                $data[] = $records[$dateKey] ?? 0;
                $current->addMonth();
            }

            return ['labels' => $labels, 'data' => $data];
        }

        // 2-12 months - weekly breakdown
        elseif ($daysDiff > 60) {
            $records = MedicineRequestLog::selectRaw('YEARWEEK(performed_at, 1) as week, SUM(quantity) as total')
                ->where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('total', 'week')
                ->toArray();

            $labels = [];
            $data = [];
            $current = $start->copy()->startOfWeek();

            while ($current <= $end) {
                $weekKey = $current->format('oW');
                $labels[] = $current->format('M d');
                $data[] = $records[$weekKey] ?? 0;
                $current->addWeek();
            }

            return ['labels' => $labels, 'data' => $data];
        }

        // Under 2 months - daily breakdown
        else {
            $records = MedicineRequestLog::selectRaw('DATE(performed_at) as date, SUM(quantity) as total')
                ->where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $period = CarbonPeriod::create($start, $end);
            $labels = [];
            $data = [];

            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $labels[] = $date->format('M d');
                $data[] = $records[$dateKey] ?? 0;
            }

            return ['labels' => $labels, 'data' => $data];
        }
    }

    private function getDateRangeRequestData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $daysDiff = $start->diffInDays($end);

        // Single day - hourly breakdown
        if ($daysDiff == 0) {
            $records = MedicineRequest::selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
                ->whereDate('created_at', $start)
                ->groupBy('hour')
                ->orderBy('hour')
                ->pluck('total', 'hour')
                ->toArray();

            $labels = [];
            $data = [];
            $currentHour = $start->copy()->startOfDay();

            for ($i = 0; $i < 24; $i++) {
                $labels[] = $currentHour->format('g A');
                $data[] = $records[$i] ?? 0;
                $currentHour->addHour();
            }

            return ['labels' => $labels, 'data' => $data];
        }

        // Over 1 year - monthly breakdown
        elseif ($daysDiff >= 364) {
            $records = MedicineRequest::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as date, COUNT(*) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $labels = [];
            $data = [];
            $current = $start->copy()->startOfMonth();

            while ($current <= $end) {
                $dateKey = $current->format('Y-m');
                $labels[] = $current->format('M Y');
                $data[] = $records[$dateKey] ?? 0;
                $current->addMonth();
            }

            return ['labels' => $labels, 'data' => $data];
        }

        // 2-12 months - weekly breakdown
        elseif ($daysDiff > 60) {
            $records = MedicineRequest::selectRaw('YEARWEEK(created_at, 1) as week, COUNT(*) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('week')
                ->orderBy('week')
                ->pluck('total', 'week')
                ->toArray();

            $labels = [];
            $data = [];
            $current = $start->copy()->startOfWeek();

            while ($current <= $end) {
                $weekKey = $current->format('oW');
                $labels[] = $current->format('M d');
                $data[] = $records[$weekKey] ?? 0;
                $current->addWeek();
            }

            return ['labels' => $labels, 'data' => $data];
        }

        // Under 2 months - daily breakdown
        else {
            $records = MedicineRequest::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $period = CarbonPeriod::create($start, $end);
            $labels = [];
            $data = [];

            foreach ($period as $date) {
                $dateKey = $date->format('Y-m-d');
                $labels[] = $date->format('M d');
                $data[] = $records[$dateKey] ?? 0;
            }

            return ['labels' => $labels, 'data' => $data];
        }
    }

    private function getTopMedicinesDateRangeData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        $daysDiff = $start->diffInDays($end);
        $isSingleDay = $start->toDateString() === $end->toDateString();

        // Get top 5 medicines for the selected date range
        if ($isSingleDay) {
            $topMedicines = MedicineRequestLog::select('medicine_name', DB::raw('SUM(quantity) as total'))
                ->where('action', 'approved')
                ->whereDate('performed_at', $start)
                ->groupBy('medicine_name')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        } else {
            $topMedicines = MedicineRequestLog::select('medicine_name', DB::raw('SUM(quantity) as total'))
                ->where('action', 'approved')
                ->whereBetween('performed_at', [$start, $end])
                ->groupBy('medicine_name')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        $datasets = [];
        $labels = [];

        // If no medicines dispensed, return empty
        if ($topMedicines->isEmpty()) {
            if ($daysDiff == 0) {
                $currentHour = $start->copy()->startOfDay();
                for ($i = 0; $i < 24; $i++) {
                    $labels[] = $currentHour->format('g A');
                    $currentHour->addHour();
                }
            } elseif ($daysDiff >= 364) {
                $current = $start->copy()->startOfMonth();
                while ($current <= $end) {
                    $labels[] = $current->format('M Y');
                    $current->addMonth();
                }
            } elseif ($daysDiff > 60) {
                $current = $start->copy()->startOfWeek();
                while ($current <= $end) {
                    $labels[] = $current->format('M d');
                    $current->addWeek();
                }
            } else {
                $period = CarbonPeriod::create($start, $end);
                foreach ($period as $date) {
                    $labels[] = $date->format('M d');
                }
            }

            return ['labels' => $labels, 'datasets' => []];
        }

        // Single day - hourly breakdown
        if ($isSingleDay) {
            for ($i = 0; $i < 24; $i++) {
                $hour = Carbon::createFromTime($i, 0, 0);
                $labels[] = $hour->format('g A');
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
        }

        // Over 1 year - monthly breakdown
        elseif ($daysDiff >= 364) {
            $current = $start->copy()->startOfMonth();
            while ($current <= $end) {
                $labels[] = $current->format('M Y');
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
        }

        // 2-12 months - weekly breakdown
        elseif ($daysDiff > 60) {
            $current = $start->copy()->startOfWeek();
            while ($current <= $end) {
                $labels[] = $current->format('M d');
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
        }

        // Under 2 months - daily breakdown
        else {
            $period = CarbonPeriod::create($start, $end);
            foreach ($period as $date) {
                $labels[] = $date->format('M d');
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
            'datasets' => $datasets
        ];
    }
}