<div>
    <div class="filters d-flex justify-content-lg-between  justify-content-end flex-column flex-md-row flex-wrap gap-3 mb-2 mb-md-0">
        <div class="mb-md-3 mb-0  flex-fill xl:w-[25%]">
            <label>Show Entries</label>
            <select class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <!-- Search -->
        <div class=" flex-fill xl:w-[25%]">
            <small>Search</small>
            <input type="text" class="form-control bg-light border-1 border-black " placeholder="Search here..." wire:model.live.debounce.1000ms="search">
        </div>
        <!-- date range -->
        <div class="date-range-filter  flex-fill xl:w-[25%]">
            <label class="filter-label fw-bold w-100" for="dateRange">Date Range:</label>
            <input type="text" id="dateRange" class="filter-select border-1 border-black form-control" style="min-width: 250px;" />

        </div>

        <div class="button-con d-flex align-items-center mt-1 justify-content-end">
            <button wire:click="exportPdf" type="button" class="btn btn-success d-flex  justify-content-center align-items-center gap-2 px-3 py-2" style="height: auto;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px" viewBox="0 0 512 512">
                    <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" fill="white" />
                </svg>
                <p class="mb-0" style="font-size: 0.875rem;">Download</p>
            </button>
        </div>
    </div>
    <div class="tables table-responsive">
        <table class="w-100 table table-hover ">
            <thead class="table-header">
                <tr>
                    <th class="text-nowrap">No.</th>

                    <!-- Full Name -->
                    <th style="cursor:pointer;" wire:click="sortBy('full_name')">
                        Full Name
                        @if ($sortField === 'full_name')
                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                        @endif
                    </th>

                    <!-- Age -->
                    <th style="cursor:pointer;" wire:click="sortBy('age')">
                        Age
                        @if ($sortField === 'age')
                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                        @endif
                    </th>

                    <!-- Sex -->
                    <th style="cursor:pointer;" wire:click="sortBy('sex')">
                        Sex
                        @if ($sortField === 'sex')
                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                        @endif
                    </th>

                    <th>Contact No.</th>

                    <!-- NEW: Status Column Header -->
                    <th>Type of Patient</th>

                    <!-- Date Registered -->
                    <th style="cursor:pointer;" wire:click="sortBy('created_at')">
                        Date Registered
                        @if ($sortField === 'created_at')
                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                        @endif
                    </th>

                    <th>Action</th>
                </tr>
            </thead>

            <!-- data of patient -->
            <tbody>
                @forelse($records as $record)
                @forelse($record->medical_record_case as $case)
                <tr class="">
                    <td>{{ $loop->parent->iteration }}</td>
                    <td class="text-nowrap">{{ $record->full_name ?? 'N/A' }}</td>
                    <td class="text-nowrap">{{ $record->age_display ?? 'N/A' }}</td>
                    <td>{{ $record->sex ?? 'N/A' }}</td>
                    <td>{{ $record->contact_number ?? 'N/A' }}</td>

                    <!-- add a helper here  -->
                    <!-- str::title is for making it capitalize and str_replace is for replacing - ex. senior-citizen -->
                    <td>{{Str::title(str_replace("-"," ",$case->type_of_case ?? 'N/A'))}}</td>
                    <!-- Show the case creation date instead of patient creation date -->
                    <td>{{ $case->created_at ? $case->created_at->format('M j, Y') : 'N/A' }}</td>

                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <a href="/patient-record/{{$case->type_of_case}}/view-records?patient_id={{ $record->id }}"
                                class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-folder-open"></i> View Record
                            </a>

                            <a href="#"
                                class="delete-record-icon"
                                data-bs-patient-id="{{ $record->id }}"
                                data-record-type="{{ $case->type_of_case }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 448 512">
                                    <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" fill='red' />
                                </svg>
                            </a>

                        </div>
                    </td>
                </tr>
                @empty
                <!-- If patient has no medical record cases, still show the patient row -->
                <tr class="{{ isset($record->vaccination_status_info['class']) ? $record->vaccination_status_info['class'] : '' }}">
                    <td>{{ $record->id ?? 'N/A' }}</td>
                    <td class="text-nowrap">{{ $record->full_name ?? 'N/A' }}</td>
                    <td class="text-nowrap">{{ $record->age_display ?? 'N/A' }}</td>
                    <td>{{ $record->sex ?? 'N/A' }}</td>
                    <td>{{ $record->contact_number ?? 'N/A' }}</td>
                    <td>N/A</td>
                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <span class="text-muted">No cases</span>
                        </div>
                    </td>
                </tr>
                @endforelse
                @empty
                <tr>
                    <td colspan="7" class="text-center bg-light">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!-- for pagination -->
        {{ $records ->links() }}
    </div>
</div>