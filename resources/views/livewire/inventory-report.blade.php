
<div class="min-vh-100 p-lg-5 p-md-3 p-2">
    <div class="shadow p-2 bg-light">
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
    <div class="modal fade" id="medicineModal" tabindex="-1" aria-labelledby="medicineModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="medicineModalLabel"><i class="bi bi-capsule me-2"></i>All Medicines</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap w-100">
                        <label class="form-label mb-0 text-muted small fw-semibold">Date Range:</label>
                        <input type="text" id="medicinesDatePicker"
                            class="form-control form-control-lg"
                            style="width: auto; min-width: 380px; cursor: pointer; background: white;"
                            readonly>
                    </div>

                    <center>
                    <div wire:loading wire:target="medicinesPage, medicinesPerPage"
                        class="text-center py-5"
                        style="min-height: 300px;">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Loading medicines...</p>
                    </div>
                    </center>
                    <div wire:loading.class="d-none" wire:target="medicinesPage, medicinesPerPage">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 text-muted small">Show:</label>
                                <select wire:model.live="medicinesPerPage" class="form-select form-select-sm" style="width:auto">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <small class="text-muted">
                                Showing {{ $allMedicines->firstItem() ?? 0 }}–{{ $allMedicines->lastItem() ?? 0 }}
                                of {{ $allMedicines->total() }} records
                            </small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Medicine Name</th>
                                        <th>Category</th>
                                        <th>Dosage</th>
                                        <th>Stock</th>
                                        <th>Stock Status</th>
                                        <th>Current Batch Expiry</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allMedicines as $index => $medicine)
                                    @php
                                        $fifo      = $medicine->batches->first();
                                        $lastBatch = $medicine->allBatches->last();
                                        $available   = $medicine->available_stock;
                                        $stockStatus = $available <= 0 ? 'Out of Stock'
                                                    : ($available <= 10 ? 'Low Stock' : 'In Stock');
                                        $stockClass  = $available <= 0 ? 'bg-danger'
                                                    : ($available <= 10 ? 'bg-warning text-dark' : 'bg-success');
                                    @endphp
                                    <tr>
                                        <td>{{ $allMedicines->firstItem() + $index }}</td>
                                        <td>{{ $medicine->medicine_name }}</td>
                                        <td>{{ $medicine->category?->category_name ?? 'N/A' }}</td>
                                        <td>{{ $medicine->dosage }}</td>
                                        <td>{{ $available }}</td>
                                        <td><span class="badge {{ $stockClass }}">{{ $stockStatus }}</span></td>
                                        <td>
                                            @if($fifo)
                                                {{ $fifo->expiry_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($allMedicines->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item {{ $allMedicines->onFirstPage() ? 'disabled' : '' }}">
                                            <button class="page-link" wire:click="$set('medicinesPage', {{ $allMedicines->currentPage() - 1 }})" {{ $allMedicines->onFirstPage() ? 'disabled' : '' }}>&laquo;</button>
                                        </li>
                                        @foreach(range(max(1, $allMedicines->currentPage() - 2), min($allMedicines->lastPage(), $allMedicines->currentPage() + 2)) as $page)
                                            <li class="page-item {{ $page == $allMedicines->currentPage() ? 'active' : '' }}">
                                                <button class="page-link" wire:click="$set('medicinesPage', {{ $page }})">{{ $page }}</button>
                                            </li>
                                        @endforeach
                                        <li class="page-item {{ !$allMedicines->hasMorePages() ? 'disabled' : '' }}">
                                            <button class="page-link" wire:click="$set('medicinesPage', {{ $allMedicines->currentPage() + 1 }})" {{ !$allMedicines->hasMorePages() ? 'disabled' : '' }}>&raquo;</button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        @endif

                        <div class="mt-3 p-3 bg-light rounded">
                            <strong>Total Medicines: {{ $allMedicines->total() }}</strong>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <a href="{{ route('download.medicine.report', ['start' => $medicinesStartDate, 'end' => $medicinesEndDate]) }}"
                        class="btn btn-success btn-sm me-2 ms-5">
                            <i class="bi bi-download"></i> Download PDF
                        </a>
                        <a href="{{ route('download.medicine.report.excel', ['start' => $medicinesStartDate, 'end' => $medicinesEndDate]) }}"
                        class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-csv"></i> Download CSV
                        </a>
                    </div>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: All Request -->
    <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="requestModalLabel"><i class="bi bi-capsule me-2"></i>All Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <label class="form-label mb-0 text-muted small fw-semibold">Date Range:</label>
                        <input type="text" id="requestsDatePicker"
                            class="form-control form-control-sm"
                            style="width: auto; min-width: 380px; cursor: pointer; background: white;"
                            readonly>
                    </div>
                    <center>
                    <div wire:loading wire:target="requestsPage, requestsPerPage"
                        class="text-center py-5"
                        style="min-height: 300px;">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Loading requests...</p>
                    </div>
                    </center>

                    <div wire:loading.class="d-none" wire:target="requestsPage, requestsPerPage">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 text-muted small">Show:</label>
                                <select wire:model.live="requestsPerPage" class="form-select form-select-sm" style="width:auto">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <small class="text-muted">
                                Showing {{ $allRequests->firstItem() ?? 0 }}–{{ $allRequests->lastItem() ?? 0 }}
                                of {{ $allRequests->total() }} records
                            </small>
                        </div>

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
                                    @foreach ($allRequests as $index => $request)
                                    <tr>
                                        <td>{{ $allRequests->firstItem() + $index }}</td>
                                        <td>{{ $request->requester_name }}</td>
                                        <td>{{ $request->medicine?->medicine_name ?? 'N/A' }}</td>
                                        <td>{{ $request->medicine?->dosage ?? 'N/A' }}</td>
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

                        @if($allRequests->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item {{ $allRequests->onFirstPage() ? 'disabled' : '' }}">
                                            <button class="page-link" wire:click="$set('requestsPage', {{ $allRequests->currentPage() - 1 }})" {{ $allRequests->onFirstPage() ? 'disabled' : '' }}>&laquo;</button>
                                        </li>
                                        @foreach(range(max(1, $allRequests->currentPage() - 2), min($allRequests->lastPage(), $allRequests->currentPage() + 2)) as $page)
                                            <li class="page-item {{ $page == $allRequests->currentPage() ? 'active' : '' }}">
                                                <button class="page-link" wire:click="$set('requestsPage', {{ $page }})">{{ $page }}</button>
                                            </li>
                                        @endforeach
                                        <li class="page-item {{ !$allRequests->hasMorePages() ? 'disabled' : '' }}">
                                            <button class="page-link" wire:click="$set('requestsPage', {{ $allRequests->currentPage() + 1 }})" {{ !$allRequests->hasMorePages() ? 'disabled' : '' }}>&raquo;</button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        @endif

                        <div class="mt-3 p-3 bg-light rounded">
                            <strong>Total Requests: {{ $allRequests->total() }}</strong>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                    <a href="{{ route('download.request.report', ['start' => $requestsStartDate, 'end' => $requestsEndDate]) }}"  class="btn btn-success btn-sm me-2 ms-5">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    <a href="{{ route('download.request.report.excel', ['start' => $requestsStartDate, 'end' => $requestsEndDate]) }}" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-file-csv"></i> Download CSV
                    </a>
                    </div>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: All Medicine Distributed -->
    <div class="modal fade" id="distributedModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-right me-2"></i>All Total Medicine Distributed
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <label class="form-label mb-0 text-muted small fw-semibold">Date Range:</label>
                        <input type="text" id="distributedDatePicker"
                            class="form-control form-control-sm"
                            style="width: auto; min-width: 380px; cursor: pointer; background: white;"
                            readonly>
                    </div>
                    {{-- Loading Spinner — triggers on property changes --}}
                    <center>
                    <div wire:loading wire:target="distributedPage, distributedPerPage"
                        class="text-center py-5"
                        style="min-height: 300px;">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Loading distributed records...</p>
                    </div>
                    </center>

                    {{-- Table --}}
                    <div wire:loading.class wire:target="distributedPage, distributedPerPage">

                        {{-- Per page + total controls --}}
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 text-muted small">Show:</label>
                                <select wire:model.live="distributedPerPage"
                                        class="form-select form-select-sm" style="width:auto">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <small class="text-muted">
                                Showing {{ $distributed->firstItem() ?? 0 }}–{{ $distributed->lastItem() ?? 0 }}
                                of {{ $distributed->total() }} records
                            </small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Patient Name</th>
                                        <th>Medicine</th>
                                        <th>Dosage</th>
                                        <th>Quantity</th>
                                        <th>Date Distributed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($distributed as $index => $dist)
                                    <tr>
                                        <td>{{ $distributed->firstItem() + $index }}</td>
                                        <td>{{ $dist->patient_name }}</td>
                                        <td>{{ $dist->medicine_name }}</td>
                                        <td>{{ $dist->dosage ?? 'N/A' }}</td>
                                        <td>{{ $dist->quantity }}</td>
                                        <td>{{ \Carbon\Carbon::parse($dist->performed_at)->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                            <p class="text-muted mb-0">No distributed records found</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($distributed->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item {{ $distributed->onFirstPage() ? 'disabled' : '' }}">
                                            <button class="page-link"
                                                    wire:click="$set('distributedPage', {{ $distributed->currentPage() - 1 }})"
                                                    {{ $distributed->onFirstPage() ? 'disabled' : '' }}>
                                                &laquo;
                                            </button>
                                        </li>

                                        @foreach(range(max(1, $distributed->currentPage() - 2), min($distributed->lastPage(), $distributed->currentPage() + 2)) as $page)
                                            <li class="page-item {{ $page == $distributed->currentPage() ? 'active' : '' }}">
                                                <button class="page-link"
                                                        wire:click="$set('distributedPage', {{ $page }})">
                                                    {{ $page }}
                                                </button>
                                            </li>
                                        @endforeach

                                        <li class="page-item {{ !$distributed->hasMorePages() ? 'disabled' : '' }}">
                                            <button class="page-link"
                                                    wire:click="$set('distributedPage', {{ $distributed->currentPage() + 1 }})"
                                                    {{ !$distributed->hasMorePages() ? 'disabled' : '' }}>
                                                &raquo;
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        @endif

                        <div class="mt-3 p-3 bg-light rounded">
                            <strong>Total Distributed: {{ $this->totalMedicineDispense() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <a href="{{ route('download.distributed.report', ['start' => $distributedStartDate, 'end' => $distributedEndDate]) }}" class="btn btn-success btn-sm me-2 ms-5">
                            <i class="bi bi-download"></i> Download PDF
                        </a>
                        <a href="{{ route('download.distributed.report.excel', ['start' => $distributedStartDate, 'end' => $distributedEndDate]) }}" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-csv"></i> Download CSV
                        </a>
                    </div>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: All Low Stock -->
    <div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="lowStockModalLabel"><i class="bi bi-capsule me-2"></i> Total Low Stock</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <label class="form-label mb-0 text-muted small fw-semibold">Date Range:</label>
                        <input type="text" id="lowStockDatePicker"
                            class="form-control form-control-sm"
                            style="width: auto; min-width: 380px; cursor: pointer; background: white;"
                            readonly>
                    </div>
                    <center>
                    <div wire:loading wire:target="lowStockPage, lowStockPerPage"
                        class="text-center py-5"
                        style="min-height: 300px;">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Loading low stock medicines...</p>
                    </div>
                    </center>
                    <div wire:loading.class="d-none" wire:target="lowStockPage, lowStockPerPage">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 text-muted small">Show:</label>
                                <select wire:model.live="lowStockPerPage" class="form-select form-select-sm" style="width:auto">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <small class="text-muted">
                                Showing {{ $lowStockData->firstItem() ?? 0 }}–{{ $lowStockData->lastItem() ?? 0 }}
                                of {{ $lowStockData->total() }} records
                            </small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Medicine Name</th>
                                        <th>Dosage</th>
                                        <th>Available Stock</th>
                                        <!-- <th># of Batches</th> -->
                                        <th>Current Batch Expiry</th>
                                        <th>Expiry Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lowStockData as $index => $medicine)
                                    <tr>
                                        <td>{{ $lowStockData->firstItem() + $index }}</td>
                                        <td>{{ $medicine->medicine_name }}</td>
                                        <td>{{ $medicine->dosage }}</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $medicine->available_stock }}</span>
                                        </td>
                                        <td>
                                            @if($medicine->fifo_expiry_date)
                                                {{ \Carbon\Carbon::parse($medicine->fifo_expiry_date)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                @if ($medicine->expiry_status === 'Valid') bg-success bg-opacity-25 text-success
                                                @elseif ($medicine->expiry_status === 'Expiring Soon') bg-warning bg-opacity-25 text-yellow-800
                                                @elseif ($medicine->expiry_status === 'Expired') bg-danger bg-opacity-25 text-danger
                                                @else bg-secondary bg-opacity-25 text-secondary
                                                @endif">
                                                {{ $medicine->expiry_status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($lowStockData->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item {{ $lowStockData->onFirstPage() ? 'disabled' : '' }}">
                                            <button class="page-link" wire:click="$set('lowStockPage', {{ $lowStockData->currentPage() - 1 }})" {{ $lowStockData->onFirstPage() ? 'disabled' : '' }}>&laquo;</button>
                                        </li>
                                        @foreach(range(max(1, $lowStockData->currentPage() - 2), min($lowStockData->lastPage(), $lowStockData->currentPage() + 2)) as $page)
                                            <li class="page-item {{ $page == $lowStockData->currentPage() ? 'active' : '' }}">
                                                <button class="page-link" wire:click="$set('lowStockPage', {{ $page }})">{{ $page }}</button>
                                            </li>
                                        @endforeach
                                        <li class="page-item {{ !$lowStockData->hasMorePages() ? 'disabled' : '' }}">
                                            <button class="page-link" wire:click="$set('lowStockPage', {{ $lowStockData->currentPage() + 1 }})" {{ !$lowStockData->hasMorePages() ? 'disabled' : '' }}>&raquo;</button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        @endif

                        <div class="mt-3 p-3 bg-light rounded">
                            <strong>Total Low Stock Medicine: {{ $lowStockData->total() }}</strong>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <a href="{{ route('download.lowstock.report', ['start' => $lowStockStartDate, 'end' => $lowStockEndDate]) }}" class="btn btn-success btn-sm me-2 ms-5">
                            <i class="bi bi-download"></i> Download PDF
                        </a>
                        <a href="{{ route('download.lowstock.report.excel', ['start' => $lowStockStartDate, 'end' => $lowStockEndDate]) }}" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-csv"></i> Download CSV
                        </a>
                    </div>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 5: All Expiring Soon-->
    <div class="modal fade" id="expiringSoonModal" tabindex="-1" aria-labelledby="expiringSoonModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-green-600 text-white w-100">
                    <h5 class="modal-title" id="expiringSoonModalLabel">
                        <i class="bi bi-calendar-x me-2"></i> Total Expiring Soon
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                        <label class="form-label mb-0 text-muted small fw-semibold">Date Range:</label>
                        <input type="text" id="expiringSoonDatePicker"
                            class="form-control form-control-sm"
                            style="width: auto; min-width: 380px; cursor: pointer; background: white;"
                            readonly>
                    </div>
                    <center>
                    <div wire:loading wire:target="expiringSoonPage, expiringSoonPerPage"
                        class="text-center py-5"
                        style="min-height: 300px;">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Loading expiring batches...</p>
                    </div>
                    </center>
                    <div wire:loading.class="d-none" wire:target="expiringSoonPage, expiringSoonPerPage">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 text-muted small">Show:</label>
                                <select wire:model.live="expiringSoonPerPage" class="form-select form-select-sm" style="width:auto">
                                    <option value="15">15</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <small class="text-muted">
                                Showing {{ $expiringSoonData->firstItem() ?? 0 }}–{{ $expiringSoonData->lastItem() ?? 0 }}
                                of {{ $expiringSoonData->total() }} batches
                            </small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Medicine Name</th>
                                        <th>Dosage</th>
                                        <th>Batch No.</th>
                                        <th>Available Stock</th>
                                        <th>Stock Status</th>
                                        <th>Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expiringSoonData as $index => $batch)
                                    <tr>
                                        <td>{{ $expiringSoonData->firstItem() + $index }}</td>
                                        <td>{{ $batch->medicine?->medicine_name ?? 'N/A' }}</td>
                                        <td>{{ $batch->medicine?->dosage ?? 'N/A' }}</td>
                                        <td>
                                            <span class="text-dark">{{ $batch->batch_number ?? '—' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $batch->available_stock }}</span>
                                        </td>
                                        <td>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                                @if ($batch->computed_stock_status === 'In Stock') bg-success bg-opacity-25 text-success
                                                @elseif ($batch->computed_stock_status === 'Low Stock') bg-warning bg-opacity-25 text-yellow-800
                                                @elseif ($batch->computed_stock_status === 'Out of Stock') bg-danger bg-opacity-25 text-danger
                                                @endif">
                                                {{ $batch->computed_stock_status }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($batch->expiry_date)->format('M d, Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($expiringSoonData->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item {{ $expiringSoonData->onFirstPage() ? 'disabled' : '' }}">
                                            <button class="page-link"
                                                wire:click="$set('expiringSoonPage', {{ $expiringSoonData->currentPage() - 1 }})"
                                                {{ $expiringSoonData->onFirstPage() ? 'disabled' : '' }}>&laquo;</button>
                                        </li>
                                        @foreach(range(max(1, $expiringSoonData->currentPage() - 2), min($expiringSoonData->lastPage(), $expiringSoonData->currentPage() + 2)) as $page)
                                            <li class="page-item {{ $page == $expiringSoonData->currentPage() ? 'active' : '' }}">
                                                <button class="page-link" wire:click="$set('expiringSoonPage', {{ $page }})">{{ $page }}</button>
                                            </li>
                                        @endforeach
                                        <li class="page-item {{ !$expiringSoonData->hasMorePages() ? 'disabled' : '' }}">
                                            <button class="page-link"
                                                wire:click="$set('expiringSoonPage', {{ $expiringSoonData->currentPage() + 1 }})"
                                                {{ !$expiringSoonData->hasMorePages() ? 'disabled' : '' }}>&raquo;</button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        @endif

                        <div class="mt-3 p-3 bg-light rounded">
                            <strong>Total Expiring Soon Batches: {{ $expiringSoonData->total() }}</strong>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <a href="{{ route('download.expSoon.report', ['start' => $expiringSoonStartDate, 'end' => $expiringSoonEndDate]) }}" class="btn btn-success btn-sm me-2 ms-5">
                            <i class="bi bi-download"></i> Download PDF
                        </a>
                        <a href="{{ route('download.expSoon.report.excel', ['start' => $expiringSoonStartDate, 'end' => $expiringSoonEndDate]) }}" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-csv"></i> Download CSV
                        </a>
                    </div>
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
