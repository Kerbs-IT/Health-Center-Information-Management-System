<div class="main-content card shadow d-flex flex-column p-md-3 p-2 w-100  ">
    <div class="banner">
        <h5>Vaccination Patient</h5>
    </div>
    <!-- Filters Section -->
    <div class="row g-3 mb-md-4 mb-2">
        <!-- Show Entries -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-1">
            <label class="form-label">Show</label>
            <select wire:model.live="entries" class="form-select rounded bg-light">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

        <!-- Search -->
        <div class="col-12 col-sm-6 col-md-8 col-lg-6 col-xl-2">
            <label class="form-label">Search</label>
            <input type="text"
                wire:model.live.debounce.500ms="search"
                class="form-control rounded bg-light"
                placeholder="Search child or mother name...">
        </div>

        <!-- Age Range Filter -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <label class="form-label">Filter (Age)</label>
            <select wire:model.live="ageRange" class="form-select bg-light rounded">
                <option value="">All Ages</option>
                <option value="0-4" selected>0-59 months (0-4 yrs)</option>
                <option value="5-9">5-9 years old</option>
                <option value="10-14">10-14 years old</option>
                <option value="15-49">15-49 years old</option>
            </select>
        </div>

        <!-- Month Filter -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <label class="form-label">Month</label>
            <select wire:model.live="filterMonth" class="form-select bg-light rounded">
                <option value="">All Months</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
        </div>

        <!-- Year Filter -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-xl-1">
            <label class="form-label">Year</label>
            <select wire:model.live="filterYear" class="form-select bg-light rounded">
                <option value="">All Years</option>
                @foreach($years as $year)
                <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <!-- Barangay Filter (only for nurses) -->
        @if(Auth::user()->role == 'nurse' || Auth::user()->role == 'staff')
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <label class="form-label">Purok</label>
            <select wire:model.live="selectedBrgy" class="form-select bg-light rounded">
                <option value="">{{ $isHealthWorker ? 'All My Puroks' : 'All Puroks' }}</option>
                @if($isHealthWorker)
                @foreach($availablePuroks as $purokValue)
                <option value="{{ $purokValue }}">{{ $purokValue }}</option>
                @endforeach
                @else
                @foreach($brgys as $brgy)
                <option value="{{ $brgy->brgy_unit }}">{{ $brgy->brgy_unit }}</option>
                @endforeach
                @endif
            </select>
            @if($isHealthWorker)
            <small class="text-muted">Showing only your assigned areas</small>
            @endif
        </div>
        @endif

        <!-- Reset + Download Buttons -->
        <!-- Reset + Download Buttons -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <label class="form-label">&nbsp;</label>
            <div class="d-flex flex-wrap gap-2">
                <!-- Reset -->
                <button wire:click="resetFilters" type="button"
                    class="btn btn-secondary text-nowrap" style="font-size: 0.875rem;">
                    Reset
                </button>

                {{-- PDF --}}
                <button wire:click="exportPdf" type="button"
                    class="btn btn-danger d-flex align-items-center gap-1">
                    <i class="fas fa-file-pdf"></i>
                    <span style="font-size: 0.875rem;">PDF</span>
                </button>

                {{-- Excel --}}
                <button wire:click="exportExcel" type="button"
                    wire:loading.attr="disabled"
                    class="btn btn-success d-flex align-items-center gap-1">
                    <span wire:loading.remove wire:target="exportExcel">
                        <i class="fas fa-file-excel"></i>
                    </span>
                    <span wire:loading wire:target="exportExcel">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                    <span style="font-size: 0.875rem;">Excel</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="text-center my-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="mb-3 text-center">
        <h2>MASTER LIST OF {{ $this->selectedRange }}</h2>
    </div>
    <div class="mb-3 d-flex justify-content-between flex-wrap">
        <h4 class="flex-fill text-center">Name of Barangay: <span class="fw-light text-decoration-underline">Hugo Perez,Proper</span></h4>
        @php
        $nurse = App\Models\User::where('role','nurse')->first();
        $nurseName = $nurse->full_name ?? 'Gladys';
        @endphp
        <h4 class="flex-fill text-center">Name of Midwife: <span class="fw-light text-decoration-underline">{{$nurseName}}</span></h4>
    </div>

    <div class="table-con" wire:key="table-container-{{ $refreshKey }}">
        <table>
            <thead class="table-header">
                <tr>
                    <th class="need-space">Name of Child</th>
                    <th class="need-space">Address</th>
                    <th>Sex</th>
                    <th>Age</th>
                    <th class="need-space">Date of Birth</th>
                    <th style="font-size: 15px;">SE status 1 Months 4 months</th>
                    <th>BCG</th>
                    <th>HEPA w/in 24 hrs</th>
                    <th>PENTA 1</th>
                    <th>PENTA 2</th>
                    <th>PENTA 3</th>
                    <th>OPV 1</th>
                    <th>OPV 2</th>
                    <th>OPV 3</th>
                    <th>PCV 1</th>
                    <th>PCV 2</th>
                    <th>PCV 3</th>
                    <th>IPV 1</th>
                    <th>IPV 2</th>
                    <th>MCV 1</th>
                    <th>MCV 2</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vaccinationMasterlist as $masterlist)
                <tr wire:key="vaccination-{{ $masterlist->id }}-{{ $refreshKey }}">
                    <td class="need-space">{{ optional($masterlist)->name_of_child ?? '' }}</td>
                    <td class="need-space">{{ optional($masterlist)->Address ?? '' }}</td>
                    <td>{{ optional($masterlist)->sex ?? '' }}</td>
                    <td>{{ $masterlist->age_display }}</td>
                    <td class="need-space">{{ optional($masterlist)->date_of_birth?->format('Y-m-d') ?? '' }}</td>
                    <td style="font-size: 15px;">{{ optional($masterlist)->SE_status ?? '' }}</td>

                    {{-- FIX Bug 2: vaccine date columns cast to Carbon — must use ?->format() --}}
                    <td>{{ optional($masterlist)->BCG?->format('Y-m-d') ?? '' }}</td>

                    {{-- FIX Bug 3: space-column + date format --}}
                    <td>{{ optional($masterlist)->{'Hepatitis B'}?->format('Y-m-d') ?? '' }}</td>

                    <td>{{ optional($masterlist)->PENTA_1?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->PENTA_2?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->PENTA_3?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->OPV_1?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->OPV_2?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->OPV_3?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->PCV_1?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->PCV_2?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->PCV_3?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->IPV_1?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->IPV_2?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->MCV_1?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->MCV_2?->format('Y-m-d') ?? '' }}</td>
                    <td>{{ optional($masterlist)->remarks ?? '' }}</td>
                    <td>
                        <button class="fs-2 text-success vaccination-masterlist-edit-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editvaccinationMasterListModal"
                            data-masterlist-id="{{ $masterlist->id }}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr wire:key="empty-state-{{ $refreshKey }}">
                    <td colspan="24" class="text-center">
                        <i class="fas fa-search mb-2" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mb-1">No records found</p>
                        @if($search || $selectedBrgy || $filterMonth || $filterYear)
                        <small class="text-muted">Try adjusting your filters or search term</small>
                        @else
                        <small class="text-muted">No data available</small>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $vaccinationMasterlist->links() }}

    @if(Auth::user()->role == 'staff')
    <div class="mb-3">
        <h2>Name of BHM: <span>{{ Auth::user()->staff->full_name }}</span></h2>
    </div>
    @endif
</div>