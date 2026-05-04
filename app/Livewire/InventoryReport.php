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
use App\Models\MedicineBatch;
class InventoryReport extends Component
{
    public $startDate;
    public $endDate;
    public $barChartStartDate;
    public $barChartEndDate;
    public $pieChartStartDate;
    public $pieChartEndDate;

    public $distributedPage = 1;
    public $distributedPerPage = 15;
    public $medicinesPage = 1;
    public $medicinesPerPage = 15;
    public $requestsPage = 1;
    public $requestsPerPage = 15;
    public $lowStockPage = 1;
    public $lowStockPerPage = 15;
    public $expiringSoonPage = 1;
    public $expiringSoonPerPage = 15;

    public $medicinesStartDate;
    public $medicinesEndDate;
    public $requestsStartDate;
    public $requestsEndDate;
    public $distributedStartDate;
    public $distributedEndDate;
    public $lowStockStartDate;
    public $lowStockEndDate;
    public $expiringSoonStartDate;
    public $expiringSoonEndDate;
    public function mount()
    {
        // Default to current year
        $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        $this->barChartStartDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->barChartEndDate = Carbon::now()->endOfYear()->format('Y-m-d');

        $this->pieChartStartDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->pieChartEndDate = Carbon::now()->endOfYear()->format('Y-m-d');
        $this->medicinesStartDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->medicinesEndDate   = Carbon::now()->endOfYear()->format('Y-m-d');
        $this->requestsStartDate  = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->requestsEndDate    = Carbon::now()->endOfYear()->format('Y-m-d');
        $this->distributedStartDate  = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->distributedEndDate    = Carbon::now()->endOfYear()->format('Y-m-d');
        $this->lowStockStartDate     = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->lowStockEndDate       = Carbon::now()->endOfYear()->format('Y-m-d');
        $this->expiringSoonStartDate = Carbon::now()->startOfYear()->format('Y-m-d');
        $this->expiringSoonEndDate   = Carbon::now()->endOfYear()->format('Y-m-d');
    }
    public function updateMedicinesDateRange($start, $end)
    {
        $this->medicinesStartDate = $start;
        $this->medicinesEndDate   = $end;
        $this->medicinesPage      = 1;
    }

    public function updateRequestsDateRange($start, $end)
    {
        $this->requestsStartDate = $start;
        $this->requestsEndDate   = $end;
        $this->requestsPage      = 1;
    }
    public function updateDistributedDateRange($start, $end)
    {
        $this->distributedStartDate = $start;
        $this->distributedEndDate   = $end;
        $this->distributedPage      = 1;
    }

    public function updateLowStockDateRange($start, $end)
    {
        $this->lowStockStartDate = $start;
        $this->lowStockEndDate   = $end;
        $this->lowStockPage      = 1;
    }

    public function updateExpiringSoonDateRange($start, $end)
    {
        $this->expiringSoonStartDate = $start;
        $this->expiringSoonEndDate   = $end;
        $this->expiringSoonPage      = 1;
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


    public function totalMedicineDispense()
    {
        return MedicineRequestLog::where('action', 'dispensed')->sum('quantity');
    }
    public function totalLowStock(){
        return Medicine::where('stock_status', 'Low Stock')->count();
    }

    public function totalExpSoon()
    {
        return MedicineBatch::where('expiry_status', 'Expiring Soon')->count();
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
                ->where('medicine_request_logs.action', 'dispensed')
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
            ->where('medicine_request_logs.action', 'dispensed')
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
        $start = Carbon::parse($this->pieChartStartDate)->startOfDay();
        $end = Carbon::parse($this->pieChartEndDate)->endOfDay();
        $daysDiff = $start->diffInDays($end);

        // Get ALL medicines that were either:
        // 1. Dispensed during this period, OR
        // 2. Created/Updated during this period

        $medicineNames = collect();

        // Get medicines dispensed in this date range
        if ($daysDiff == 0) {
            $dispensedMedicines = MedicineRequestLog::where('action', 'dispensed')
                ->whereDate('performed_at', $start)
                ->distinct()
                ->pluck('medicine_name');
        } else {
            $dispensedMedicines = MedicineRequestLog::where('action', 'dispensed')
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

        // Merge both lists (remove duplicates)
        $medicineNames = $dispensedMedicines->merge($modifiedMedicines)->unique();

        // If no medicines found, return empty data
        if ($medicineNames->isEmpty()) {
            return [
                'labels' => ['In Stock', 'Low Stock', 'Out of Stock'],
                'data' => [0, 0, 0]
            ];
        }

        // Get current stock status for those medicines
        $inStock = Medicine::whereIn('medicine_name', $medicineNames)
            ->where('stock_status', 'In Stock')
            ->count();

        $lowStock = Medicine::whereIn('medicine_name', $medicineNames)
            ->where('stock_status', 'Low Stock')
            ->count();

        $outOfStock = Medicine::whereIn('medicine_name', $medicineNames)
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
                ->where('action', 'dispensed')
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
                ->where('action', 'dispensed')
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
                ->where('action', 'dispensed')
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
                ->where('action', 'dispensed')
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
    $start = Carbon::parse($this->startDate)->startOfDay();
    $end = Carbon::parse($this->endDate)->endOfDay();
    $daysDiff = $start->diffInDays($end);
    $isSingleDay = $start->toDateString() === $end->toDateString();


    // Get top 5 medicines for the selected date range
    if ($isSingleDay) {
        // For single day: use whereDate
        $topMedicines = MedicineRequestLog::select('medicine_name', DB::raw('SUM(quantity) as total'))
            ->where('action', 'dispensed')
            ->whereDate('performed_at', $start)
            ->groupBy('medicine_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    } else {
        // For date range: use whereBetween
        $topMedicines = MedicineRequestLog::select('medicine_name', DB::raw('SUM(quantity) as total'))
            ->where('action', 'dispensed')
            ->whereBetween('performed_at', [$start, $end])
            ->groupBy('medicine_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

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

    // Handle single day selection (Today/Yesterday)
    if ($isSingleDay) {
        // Generate 24-hour labels in 12-hour format
        for ($i = 0; $i < 24; $i++) {
            $hour = Carbon::createFromTime($i, 0, 0);
            $labels[] = $hour->format('g A');  // "12 AM", "1 AM", "2 PM", etc.
            $fullLabels[] = $start->copy()->setTime($i, 0)->format('M d, Y g:00 A');  // "Jan 12, 2026 1:00 AM"
        }

        foreach ($topMedicines as $medicine) {
            $records = MedicineRequestLog::selectRaw('HOUR(performed_at) as hour, SUM(quantity) as total')
                ->where('medicine_name', $medicine->medicine_name)
                ->where('action', 'dispensed')
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
                ->where('action', 'dispensed')
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
                ->where('action', 'dispensed')
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
                ->where('action', 'dispensed')
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

    public function getAllMedicinesData()
    {
        return Medicine::with([
            'category',
            'batches'    => fn($q) => $q->orderBy('expiry_date', 'asc'),
            'allBatches' => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])
        ->whereBetween('created_at', [
            Carbon::parse($this->medicinesStartDate)->startOfDay(),
            Carbon::parse($this->medicinesEndDate)->endOfDay(),
        ])
        ->orderBy('medicine_name')
        ->paginate($this->medicinesPerPage, ['*'], 'medicinesPage', $this->medicinesPage);
    }

    public function getAllRequestsData()
    {
        return MedicineRequest::with(['medicine', 'patients', 'user'])
            ->select('id', 'patients_id', 'user_id', 'medicine_id', 'quantity_requested', 'status', 'created_at')
            ->whereBetween('created_at', [
                Carbon::parse($this->requestsStartDate)->startOfDay(),
                Carbon::parse($this->requestsEndDate)->endOfDay(),
            ])
            ->orderByDesc('created_at')
            ->paginate($this->requestsPerPage, ['*'], 'requestsPage', $this->requestsPage);
    }

    public function getLowStockData()
    {
        return Medicine::with([
            // valid batches (not expired, not soft-deleted) ordered earliest first
            'batches'    => fn($q) => $q->where('expiry_date', '>', now())->orderBy('expiry_date', 'asc'),
            // ALL non-archived batches for last-expiry & batch count
            'allBatches' => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])
        ->where('stock_status', 'Low Stock')
        ->whereBetween('updated_at', [
            Carbon::parse($this->lowStockStartDate)->startOfDay(),
            Carbon::parse($this->lowStockEndDate)->endOfDay(),
        ])
        ->orderBy('stock', 'asc')
        ->paginate($this->lowStockPerPage, ['*'], 'lowStockPage', $this->lowStockPage)
        ->through(function ($medicine) {
            // Available stock = sum of free units across valid (non-expired) batches
            $medicine->available_stock = $medicine->batches
                ->sum(fn($b) => max(0, $b->quantity - $b->reserved_quantity));

            // Current-use batch = earliest valid batch (FIFO)
            $fifoBatch = $medicine->batches->first();
            $medicine->fifo_expiry_date = $fifoBatch?->expiry_date;

            // Expiry status driven by the LAST batch's expiry date
            $lastBatch = $medicine->allBatches->last();
            if ($lastBatch) {
                $daysUntilExpiry = now()->diffInDays($lastBatch->expiry_date, false);
                $medicine->expiry_status = $daysUntilExpiry < 0
                    ? 'Expired'
                    : ($daysUntilExpiry <= 30 ? 'Expiring Soon' : 'Valid');
            } else {
                $medicine->expiry_status = 'N/A';
            }

            // Total number of non-archived batches
            $medicine->batch_count = $medicine->allBatches->count();

            return $medicine;
        });
}

    public function getLowStockCollection()
    {
        return Medicine::with([
            'batches' => fn($q) => $q->where('expiry_date', '>', now())->orderBy('expiry_date', 'asc'),
            'allBatches' => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])
        ->where('stock_status', 'Low Stock')
        ->orderBy('stock', 'asc')
        ->get()
        ->map(function ($medicine) {
            $medicine->available_stock_count = $medicine->batches->sum(fn($b) => $b->quantity - $b->reserved_quantity);
            $fifoBatch = $medicine->batches->first();
            $medicine->fifo_expiry_date = $fifoBatch?->expiry_date;
            $lastBatch = $medicine->allBatches->last();
            if ($lastBatch) {
                $daysUntilExpiry = now()->diffInDays($lastBatch->expiry_date, false);
                $medicine->last_batch_expiry_status = $daysUntilExpiry < 0
                    ? 'Expired'
                    : ($daysUntilExpiry <= 30 ? 'Expiring Soon' : 'Valid');
            } else {
                $medicine->last_batch_expiry_status = 'N/A';
            }
            $medicine->batch_count = $medicine->allBatches->count();
            return $medicine;
        });
    }
    public function getLowStockCollectionByDate($start, $end)
    {
        return Medicine::with([
            'batches'    => fn($q) => $q->where('expiry_date', '>', now())->orderBy('expiry_date', 'asc'),
            'allBatches' => fn($q) => $q->orderBy('expiry_date', 'asc'),
        ])
        ->where('stock_status', 'Low Stock')
        ->whereBetween('updated_at', [
            Carbon::parse($start)->startOfDay(),
            Carbon::parse($end)->endOfDay(),
        ])
        ->orderBy('stock', 'asc')
        ->get()
        ->map(function ($medicine) {
            $medicine->available_stock_count = $medicine->batches->sum(fn($b) => $b->quantity - $b->reserved_quantity);
            $fifo = $medicine->batches->first();
            $medicine->fifo_expiry_date = $fifo?->expiry_date;
            $last = $medicine->allBatches->last();
            if ($last) {
                $days = now()->diffInDays($last->expiry_date, false);
                $medicine->expiry_status = $days < 0 ? 'Expired' : ($days <= 30 ? 'Expiring Soon' : 'Valid');
            } else {
                $medicine->expiry_status = 'N/A';
            }
            $medicine->available_stock = $medicine->available_stock_count;
            return $medicine;
        });
    }
    public function getExpiringSoonCollection()
    {
        return Medicine::with([
            'batches' => fn($q) => $q->where('expiry_date', '>', now())->orderBy('expiry_date', 'asc'),
            'allBatches' => fn($q) => $q->where('expiry_status', 'Expiring Soon')->orderBy('expiry_date', 'asc'),
        ])
        ->where('expiry_status', 'Expiring Soon')
        ->orderBy('expiry_date', 'asc')
        ->get()
        ->map(function ($medicine) {
            $medicine->available_stock_count = $medicine->batches->sum(fn($b) => $b->quantity - $b->reserved_quantity);
            $avail = $medicine->available_stock_count;
            $medicine->computed_stock_status = $avail <= 0 ? 'Out of Stock'
                : ($avail <= 10 ? 'Low Stock' : 'In Stock');
            $fifoBatch = $medicine->batches->first();
            $medicine->fifo_expiry_date = $fifoBatch?->expiry_date;
            $medicine->expiring_batch_count = $medicine->allBatches->count();
            return $medicine;
        });
    }

    public function getExpiringSoonData()
    {
        return MedicineBatch::with([
            'medicine' => fn($q) => $q->with([
                // All valid batches of that medicine — for available stock
                'batches' => fn($q2) => $q2->where('expiry_date', '>', now()),
            ]),
        ])
        ->where('expiry_status', 'Expiring Soon')
        ->orderBy('expiry_date', 'asc')
        ->whereBetween('expiry_date', [
            Carbon::parse($this->expiringSoonStartDate)->startOfDay(),
            Carbon::parse($this->expiringSoonEndDate)->endOfDay(),
        ])
        ->paginate($this->expiringSoonPerPage, ['*'], 'expiringSoonPage', $this->expiringSoonPage)
        ->through(function ($batch) {
            // Available stock = free units across ALL valid batches of this medicine
            $available = $batch->medicine?->batches
                ->sum(fn($b) => max(0, $b->quantity - $b->reserved_quantity)) ?? 0;

            $batch->available_stock = $available;

            $batch->computed_stock_status = $available <= 0
                ? 'Out of Stock'
                : ($available <= 10 ? 'Low Stock' : 'In Stock');

            return $batch;
        });
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
            ->where('action', 'dispensed')
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
            // Get all the data
            $data = [
                'totalMedicines' => $this->totalMedicineCount(),
                'totalRequests' => $this->totalRequests(),
                'totalDispensed' => $this->totalMedicineDispense(),
                'lowStock' => $this->totalLowStock(),
                'expiringSoon' => $this->totalExpSoon(),
                'categoriesData' => $this->getMedicineCategoriesData(),
                'pieChartData' => $this->getPieChartData(),
                'monthlyGivenData' => $this->getDateRangeGivenData(),
                'requestTrendData' => $this->getDateRangeRequestData(),
                'topDispensedTable' => $this->getTopDispensedTable()->toArray(),
                'generatedDate' => now()->format('F d, Y h:i A'),
                'startDate' => Carbon::parse($this->startDate)->format('F d, Y'),
                'endDate' => Carbon::parse($this->endDate)->format('F d, Y'),
                'barChartStartDate' => Carbon::parse($this->barChartStartDate)->format('F d, Y'),
                'barChartEndDate' => Carbon::parse($this->barChartEndDate)->format('F d, Y'),
                'pieChartStartDate' => Carbon::parse($this->pieChartStartDate)->format('F d, Y'),
                'pieChartEndDate' => Carbon::parse($this->pieChartEndDate)->format('F d, Y'),
            ];

            // Dispatch event to JavaScript to generate PDF
            $this->dispatch('generate-pdf', $data);

        } catch (\Exception $e) {
            \Log::error('Report Generation Error: ' . $e->getMessage());
            session()->flash('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }


    public function updatedDistributedPage()
{
    // reset to top when page changes — nothing needed, render() handles it
}

public function updatedDistributedPerPage()
{
    $this->distributedPage = 1; // reset to page 1 when per page changes
}
    public function render()
    {
        $distributed = MedicineRequestLog::where('action', 'dispensed')
        ->whereBetween('performed_at', [
            Carbon::parse($this->distributedStartDate)->startOfDay(),
            Carbon::parse($this->distributedEndDate)->endOfDay(),
        ])
        ->select('patient_name', 'medicine_name', 'dosage', 'quantity', 'performed_at')
        ->orderByDesc('performed_at')
        ->paginate($this->distributedPerPage, ['*'], 'distributedPage', $this->distributedPage);

        return view('livewire.inventory-report', [
            'categoriesData'       => $this->getMedicineCategoriesData(),
            'pieChartData'         => $this->getPieChartData(),
            'dateRangeGivenData'   => $this->getDateRangeGivenData(),
            'dateRangeRequestData' => $this->getDateRangeRequestData(),
            'topMedicinesData'     => $this->getTopMedicinesDateRangeData(),
            'distributed'          => $distributed,
            'allMedicines'         => $this->getAllMedicinesData(),
            'allRequests'          => $this->getAllRequestsData(),
            'lowStockData'         => $this->getLowStockData(),
            'expiringSoonData'     => $this->getExpiringSoonData(),
            'distributedStartDate'  => $this->distributedStartDate,
            'distributedEndDate'    => $this->distributedEndDate,
            'lowStockStartDate'     => $this->lowStockStartDate,
            'lowStockEndDate'       => $this->lowStockEndDate,
            'expiringSoonStartDate' => $this->expiringSoonStartDate,
            'expiringSoonEndDate'   => $this->expiringSoonEndDate,
        ])->layout('livewire.layouts.base', ['page' => 'INVENTORY REPORT']);
    }
}