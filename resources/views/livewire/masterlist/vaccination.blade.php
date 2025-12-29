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
            <input
                type="text"
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
        @if((Auth::user()->role) == 'nurse')
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <label class="form-label">Barangay</label>
            <select wire:model.live="selectedBrgy" class="form-select bg-light rounded">
                <option value="">All Barangay</option>
                @foreach($brgys as $brgy)
                <option value="{{ $brgy->brgy_unit }}">{{ $brgy->brgy_unit }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <!-- Reset Filters Button -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-xl-1">
            <label class="form-label">&nbsp;</label>
            <button
                wire:click="resetFilters"
                type="button"
                class="btn btn-secondary w-100 text-nowrap"  style="font-size: 0.875rem;">
                Reset Filters
            </button>
        </div>

        <!-- Download Button -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2 col-xl-1">
            <label class="form-label">&nbsp;</label>
            <button
                type="button"
                class="btn btn-success d-flex justify-content-center align-items-center gap-2 w-100"
                wire:click="exportPdf()">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px" viewBox="0 0 512 512">
                    <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" fill="white" />
                </svg>
                <span class="d-none d-sm-inline">Download</span>
            </button>
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
        <h4 class="flex-fill text-center">Name of Barangay: <span class="fw-light text-decoration-underline">{{$this->selectedBrgy == ''?'All Barangays':$this->selectedBrgy }}</span></h4>
        <h4 class="flex-fill text-center">Name of Midwife: <span class="fw-light text-decoration-underline">Nurse Joy</span></h4>
    </div>
    <div class="table-con">
        <table>
            <thead class="table-header ">
                <tr>
                    <th class="need-space">Name of Child</th>
                    <th class="need-space">Address</th>
                    <th>sex</th>
                    <th>Age</th>
                    <th class="need-space">Date of Birth</th>
                    <th class="" style="font-size: 15px;">SE status 1 Months 4 months</th>
                    <th>BCG</th>
                    <th>NEPA w/in 24 hrs</th>
                    <th>PENTA 1</th>
                    <th>PENTA 2</th>
                    <th>PENTA 3</th>
                    <th>OPV 1</th>
                    <th>OPV 2</th>
                    <th>OPV3</th>
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
                <tr>

                    <td class="need-space">{{optional($masterlist)->name_of_child??''}}</td>
                    <td class="need-space">{{optional($masterlist)-> Address ?? ''}}</td>
                    <td>{{optional($masterlist)-> sex ?? ''}}</td>
                    <td>{{optional($masterlist)-> age ?? ''}}</td>
                    <td class="need-space">{{optional($masterlist)-> date_of_birth?->format('Y-m-d') ?? ''}}</td>
                    <td class="" style="font-size: 15px;">{{optional($masterlist)->SE_status??''}}</td>
                    <td>{{optional($masterlist)->BCG??''}}</td>
                    <td>{{optional($masterlist)->{'Hepatitis B'}??''}}</td>
                    <td>{{optional($masterlist)-> PENTA_1??''}}</td>
                    <td>{{optional($masterlist)-> PENTA_2??''}}</td>
                    <td>{{optional($masterlist)->PENTA_3??''}}</td>
                    <td>{{optional($masterlist)->OPV_1}}</td>
                    <td>{{optional($masterlist)->OPV_2}}</td>
                    <td>{{optional($masterlist)->OPV_3}}</td>
                    <td>{{optional($masterlist)->PCV_1}}</td>
                    <td>{{optional($masterlist)->PCV_2}}</td>
                    <td>{{optional($masterlist)->PCV_3}}</td>
                    <td>{{optional($masterlist)->IPV_1}}</td>
                    <td>{{optional($masterlist)->IPV_2}}</td>
                    <td>{{optional($masterlist)->MCV_1??''}}</td>
                    <td>{{optional($masterlist)->MCV_2}}</td>
                    <td>{{optional($masterlist)->remarks}}</td>
                    <td>
                        <button class="btn btn-success vaccination-masterlist-edit-btn" data-bs-toggle="modal" data-bs-target="#editvaccinationMasterListModal" data-masterlist-id="{{$masterlist->id}}">Edit</button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="24" class="text-center ">
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
                {{-- Fill remaining rows to maintain consistent table height --}}
                @php
                $currentCount = $vaccinationMasterlist->count();
                $emptyRowsNeeded = $entries - $currentCount;
                @endphp

                @if($currentCount > 0 && $emptyRowsNeeded > 0)
                @for($i = 0; $i < $emptyRowsNeeded; $i++)
                    <tr>
                    <td class="need-space">&nbsp;</td>
                    <td class="need-space">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    </tr>
                    @endfor
                    @endif
            </tbody>
        </table>
    </div>
    @if(Auth::user() -> role == 'staff')
    <div class="mb-3">
        <h2>Name of BHM:<span>{{Auth::user() -> staff -> full_name}}</span></h2>
    </div>
    @endif
</div>