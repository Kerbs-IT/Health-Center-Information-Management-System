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
            <input type="text" class="form-control bg-light" placeholder="Search here..." wire:model.live.debounce.1000ms="search">
        </div>
        <div class="mb-md-3 mb-0  flex-fill xl:w-[25%]">
            <small>Filter</small>
            <select name="filter_option" id="" class="form-select bg-light">
                <option value="" disabled selected>Filter by Age</option>
                <option value="">0-10 weeks</option>
            </select>
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
        <table class="w-100 table table-hover">
            <thead class="table-header">
                <tr>
                    <th>Patient No.</th>

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
                    <th>Vaccination Status</th>

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
                @foreach($vaccinationRecord as $record)
                <tr class="{{ isset($record->vaccination_status_info['class']) ? $record->vaccination_status_info['class'] : '' }}">
                    <td>{{ $record->patient->id ?? 'N/A' }}</td>
                    <td>{{ $record->patient->full_name ?? 'N/A' }}</td>
                    <td>{{ $record->patient->age ?? 'none' }}</td>
                    <td>{{ $record->patient->sex ?? 'none' }}</td>
                    <td>{{ $record->patient->contact_number ?? '' }}</td>

                    <!-- Status Column -->
                    <td>
                        @if(isset($record->vaccination_status_info) && is_array($record->vaccination_status_info))
                        <span class="{{ $record->vaccination_status_info['badge_class'] ?? 'badge bg-secondary' }}">
                            {{ $record->vaccination_status_info['badge'] ?? 'No Status' }}
                        </span>
                        @if(isset($record->vaccination_status_info['due_vaccines']) && is_array($record->vaccination_status_info['due_vaccines']) && count($record->vaccination_status_info['due_vaccines']) > 0)
                        <div class="text-muted small mt-1">
                            @foreach($record->vaccination_status_info['due_vaccines'] as $vaccine)
                            <div>{{ is_string($vaccine) ? $vaccine : '' }}</div>
                            @endforeach
                        </div>
                        @endif
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td>{{ $record->patient->created_at ? $record->patient->created_at->format('M j, Y') : '' }}</td>

                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <a href="/patient-record/vaccination/view-details/{{ $record->patient->id ?? '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512">
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                </svg>
                            </a>



                            <a href="/patient-record/vaccination/edit-details/{{ $record->patient->id ?? '' }}" class="text-success fs-2 fw-bold ">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="" class="delete-record-icon-vaccination" data-bs-patient-id="{{ $record->patient->id ?? '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 448 512">
                                    <path d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" fill='red' />
                                </svg>
                            </a>

                            <a href="/patient-record/vaccination/case/{{ $record->id ?? '' }}" class=" text-warning  fs-2 fw-bold ">
                                <i class="fa-solid fa-folder"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach

                @if($vaccinationRecord->isEmpty())
                <tr>
                    <td colspan="8" class="text-center bg-light">No records found.</td>
                </tr>
                @endif
            </tbody>
        </table>
        <!-- for pagination -->
        {{ $vaccinationRecord ->links() }}
    </div>
</div>