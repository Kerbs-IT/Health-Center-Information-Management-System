
<div class="min-vh-100 p-lg-5 p-md-3 p-2">
    <div class="shadow p-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-xl-5 gap-lg-3 gap-md-2 gap-1 mb-8 mx-2 mt-2 p-1">
            <!-- Total Medicines Card -->
            <button data-bs-toggle="modal" data-bs-target="#medicineModal">
                <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-blue-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-green-500 p-3 rounded-lg">
                            <i class="bi bi-capsule text-3xl text-white"></i>
                        </div>
                        <h1 class="font-bolder text-blue-700">{{ $this->totalMedicineCount() }}</h1>
                    </div>
                    <h5 class="text-sm font-bold text-blue-900 uppercase tracking-wide">Total Medicines</h5>
                </div>
            </button>

            <!-- Total Requests Card -->
             <button data-bs-toggle="modal" data-bs-target="#requestModal">
                <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-cyan-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-green-500 p-3 rounded-lg">
                            <i class="bi bi-clipboard-check text-3xl text-white"></i>
                        </div>
                        <h1 class="font-bold text-cyan-700">{{ $this->totalRequests() }}</h1>
                    </div>
                    <h5 class="text-sm font-bold text-cyan-900 uppercase tracking-wide">Total Requests</h5>
                </div>
             </button>

            <!-- Total Medicine Given Card -->
             <button data-bs-toggle="modal" data-bs-target="#distributedModal">
                <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-purple-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-green-500 p-3 rounded-lg">
                            <i class="bi bi-box-arrow-right text-3xl text-white"></i>
                        </div>
                        <h1 class="font-bold text-purple-700">{{ $this->totalMedicineDispense() }}</h1>
                    </div>
                    <h5 class="text-sm font-bold text-purple-900 uppercase tracking-wide">Total Distributed</h5>
                </div>
            </button>

            <!-- Total Low Stock Card -->
            <button data-bs-toggle="modal" data-bs-target="#lowStockModal">
                <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-orange-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-green-500 p-3 rounded-lg">
                            <i class="bi bi-exclamation-triangle text-3xl text-white"></i>
                        </div>
                        <h1 class="font-bold text-orange-700">{{ $this->totalLowStock() }}</h1>
                    </div>
                    <h5 class="text-sm font-bold text-orange-900 uppercase tracking-wide">Low Stock Alert</h5>
                </div>
            </button>

            <!-- Expiring Soon Card -->
             <button data-bs-toggle="modal" data-bs-target="#expiringSoonModal">
                <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-red-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <div class="bg-green-500 p-3 rounded-lg">
                            <i class="bi bi-calendar-x text-3xl text-white"></i>
                        </div>
                        <h1 class="font-bold text-red-700">{{ $this->totalExpSoon() }}</h1>
                    </div>
                    <h5 class="text-sm font-bold text-red-900 uppercase tracking-wide">Expiring Soon</h5>
                </div>
             </button>
        </div>

        <!-- ANALYTICS SECTION -->
        <div class="mt-3 px-3 chart-section">
            <div class="flex justify-between px-3 pb-3">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Visual Analytics</h2>
                <button class="btn btn-success text-nowrap" wire:click="generateReport">Generate Report</button>
            </div>


            <div class="w-100 mb-3">
                <!-- LINE CHART -->
                <div class="bg-white p-6 rounded-xl shadow px-3 pb-3">
                    <h3 class="text-center">Consumption Trends</h3>
                    <div class="d-flex justify-content-between flex-wrap flex-md-row flex-column">
                        <div class="md:w-[50%] w-[100%]">
                            <select id="lineChartSelector" class="form-select w-auto mb-3 md:w-[50%] w-[100%]">
                                <option value="given">Medicine Given</option>
                                <option value="request_trend">Request Trend</option>
                                <option value="top_medicines">Top 5 Most Dispensed Medicines</option>
                            </select>
                        </div>
                        <div class="md:w-[50%] w-[100] flex items-center  gap-3  justify-content-start flex-wrap">
                            <label class="font-semibold text-gray-700">Date Range:</label>
                            <input type="text" id="dateRangePicker" class="form-control  md:w-[50%] w-[100%]" readonly style="background-color: white; cursor: pointer; font-size: clamp(0.9rem, 1.1vw, 1rem) !important;">
                        </div>
                    </div>
                    <div class="w-100 h-[350px]" wire:ignore>
                        <canvas id="lineChart" class="w-100"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRID FOR 2 CHARTS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-3">
                <!-- MEDICINE CATEGORIES BAR CHART -->
                <div class="bg-white p-6 rounded-xl shadow p-2">
                    <h3 class="text-center">Medicine Categories</h3>
                    <div class="md:w-[50%] w-[100] flex items-center  gap-3  justify-content-start flex-wrap flex-lg-row">
                        <label class="font-semibold text-gray-700">Date Range:</label>
                        <input type="text" id="barChartDatePicker" class="form-control md:w-[50%] w-[100%]" readonly style="background-color: white; cursor: pointer; font-size: clamp(0.9rem, 1.1vw, 1rem) !important;">
                    </div>
                    <div class="w-100" wire:ignore>
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <!-- STOCK DISTRIBUTION PIE CHART -->
                <div class="bg-white p-6 rounded-xl shadow d-flex justify-center flex-column p-2 flex-xl-nowrap flex-wrap">
                    <h3 class="text-center">Stock Level Distribution</h3>
                    <div class="md:w-[50%] w-[100] flex items-center  gap-3  justify-content-start flex-wrap">
                        <label class="font-semibold text-gray-700">Date Range:</label>
                        <input type="text" id="pieChartDatePicker" class="form-control md:w-[50%] w-[100%]" readonly style="background-color: white; cursor: pointer; font-size: clamp(0.9rem, 1.1vw, 1rem) !important;">
                    </div>
                    <div class="w-80 xl:min-h-[290px] align-self-center" wire:ignore>
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS (keeping all existing modals) -->
    <!-- MODAL 1: All Medicines -->
    <div wire.ignore.self class="modal fade" id="medicineModal" tabindex="-1" aria-labelledby="medicineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="medicineModalLabel"><i class="bi bi-capsule me-2"></i>All Medicines</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Category</th>
                                    <th>Dosage</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->getAllMedicinesData() as $index => $medicine )
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{$medicine->medicine_name}}</td>
                                    <td>{{ $medicine->category_name }}</td>
                                    <td>{{ $medicine->dosage }}</td>
                                    <td>{{$medicine->stock}}</td>
                                   <td>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if ($medicine->stock_status === 'In Stock') bg-success bg-opacity-25 text-success
                                        @elseif ($medicine->stock_status === 'Low Stock') bg-warning bg-opacity-25 text-yellow-800
                                        @elseif ($medicine->stock_status === 'Out of Stock') bg-danger bg-opacity-25 text-danger
                                        @endif">
                                        {{ $medicine->stock_status }}
                                    </span>
                                    </td>
                                    <td>{{ $medicine->expiry_date }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Total Medicines: {{ $this->totalMedicineCount() }}</strong>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <a href="{{ route('download.medicine.report') }}" class="btn btn-success btn-sm me-2 ms-5" >
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: All Request -->
    <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="requestModalLabel"><i class="bi bi-capsule me-2"></i>All Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Medicine</th>
                                    <th>Dosage</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Date Requested</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->getAllRequestsData() as $index => $request )
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $request->requester_name }}</td>
                                    <td>{{ $request->medicine_name }}</td>
                                    <td>{{ $request->dosage }}</td>
                                    <td>{{ $request->quantity_requested }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->status == 'completed' ? 'success' : ($request->status == 'pending' ? 'warning' : ($request->status == 'approved' ? 'info' : 'danger')) }}">
                                            {{ $request->status }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Total Requests: {{ $this->totalRequests() }}</strong>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <a href="{{ route('download.request.report') }}" class="btn btn-success btn-sm me-2 ms-5">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: All Medicine Distributed -->
    <div class="modal fade" id="distributedModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="requestModalLabel"><i class="bi bi-capsule me-2"></i>All Total Medicine Distributed </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Patient Name</th>
                                    <th>Medicine</th>
                                    <th>Quantity</th>
                                    <th>Date Distributed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->getAllDistributedData() as $index => $dist )
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{$dist->patient_name}}</td>
                                    <td>{{ $dist->medicine_name }}</td>
                                    <td>{{ $dist->quantity }}</td>
                                    <td>{{ \Carbon\Carbon::parse($dist->performed_at)->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Total Distributed Medicine: {{ $this->totalMedicineDispense() }}</strong>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <a href="{{ route('download.distributed.report') }}" class="btn btn-success btn-sm me-2 ms-5">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: All Low Stock -->
    <div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="lowStockModalLabel"><i class="bi bi-capsule me-2"></i> Total Low Stock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Stock</th>
                                    <th>Expiry Date</th>
                                    <th>Expiry Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->getLowStockData() as $index => $medicine )
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $medicine->medicine_name }}</td>
                                    <td>{{ $medicine->dosage }}</td>
                                    <td><span class="badge bg-warning text-dark">{{ $medicine->stock }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($medicine->expiry_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if ($medicine->expiry_status === 'Valid') bg-success bg-opacity-25 text-success
                                            @elseif ($medicine->expiry_status === 'Expiring Soon') bg-warning bg-opacity-25 text-yellow-800
                                            @elseif ($medicine->expiry_status === 'Expired') bg-danger bg-opacity-25 text-danger
                                            @endif">
                                            {{ $medicine->expiry_status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Total Low Stock Medicine: {{ $this->totalLowStock() }}</strong>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <a href="{{ route('download.lowstock.report') }}" class="btn btn-success btn-sm me-2 ms-5">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 5: All Expiring Soon-->
    <div class="modal fade" id="expiringSoonModal" tabindex="-1" aria-labelledby="expiringSoonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="expiringSoonModalLabel"><i class="bi bi-capsule me-2"></i> Total Expiring Soon</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Dosage</th>
                                    <th>Stock</th>
                                    <th>Stock Status</th>
                                    <th>Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->getExpiringSoonData() as $index => $medicine )
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $medicine->medicine_name }}</td>
                                    <td>{{ $medicine->dosage }}</td>
                                    <td><span class="badge bg-warning text-dark">{{ $medicine->stock }}</span></td>
                                    <td>
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if ($medicine->stock_status === 'In Stock') bg-success bg-opacity-25 text-success
                                            @elseif ($medicine->stock_status === 'Low Stock') bg-warning bg-opacity-25 text-yellow-800
                                            @elseif ($medicine->stock_status === 'Out of Stock') bg-danger bg-opacity-25 text-danger
                                            @endif">
                                            {{ $medicine->stock_status }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($medicine->expiry_date)->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Total Expiring Soon Medicine: {{ $this->totalExpSoon() }}</strong>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <a href="{{ route('download.expSoon.report') }}" class="btn btn-success btn-sm me-2 ms-5">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

</div>


@push('data')
<script>
    // Make chart data available to JavaScript
    window.chartData = {
        categoriesData: @json($categoriesData),
        pieChartData: @json($pieChartData),
        dateRangeGivenData: @json($dateRangeGivenData),
        dateRangeRequestData: @json($dateRangeRequestData),
        topMedicinesData: @json($topMedicinesData)
    };
</script>

@endpush
@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('download-report', (params) => {
            // Build query string
            const queryParams = new URLSearchParams(params[0]).toString();

            // Create temporary link and trigger download
            const downloadUrl = "{{ route('download.inventory.report') }}" + '?' + queryParams;
            window.location.href = downloadUrl;
        });
    });
</script>
@endpush
