<div>
    @php
    $typeOfCase = $type_of_case;

    $routeMap = [
    'prenatal' => 'records.prenatal',
    'family-planning' => 'record.family.planning',
    'tb-dots' => 'record.tb-dots',
    'senior-citizen' => 'record.senior.citizen',
    'vaccination' => 'record.vaccination'
    ];

    // If showing all types, back to "All Records", otherwise back to specific type
    if ($showAllTypes) {
    $backRoute = 'record.all'; // Your "All Records" route
    $pageTitle = 'All Archived Records';
    } else {
    $backRoute = $routeMap[$typeOfCase] ?? 'record.all';
    $pageTitle = 'Archived ' . ucfirst(str_replace('-', ' ', $typeOfCase ?? '')) . ' Records';
    }

    $backUrl = route($backRoute) . '?' . http_build_query(
    request()->only(['patient_id', 'search', 'entries', 'sortField', 'sortDirection'])
    );
    @endphp

    <div class="alert alert-info mb-3">
        <i class="fa-solid fa-info-circle"></i>
        @if($showAllTypes)
        Showing all archived records from all patient types.
        @else
        Showing archived {{ $typeOfCase ?? '' }} records only.
        @endif
        Click the restore button to activate a record.
    </div>

    <a href="{{ $backUrl }}" class="btn btn-danger px-4 fs-5 mb-3">
        Back
    </a>

    <div class="filters d-flex justify-content-lg-between justify-content-end flex-column flex-md-row flex-wrap gap-3 mb-2 mb-md-0">
        <div class="mb-md-3 mb-0 flex-fill xl:w-[25%]">
            <label>Show Entries</label>
            <select class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="flex-fill xl:w-[25%]">
            <small>Search</small>
            <input type="text" class="form-control bg-light border-1 border-black" placeholder="Search here..." wire:model.live.debounce.1000ms="search">
        </div>
        <div class="date-range-filter flex-fill xl:w-[25%]">
            <label class="filter-label fw-bold w-100" for="dateRange">Date Range:</label>
            <input type="text" id="dateRange" class="filter-select border-1 border-black form-control" style="min-width: 250px;" />
        </div>
    </div>

    <div class="tables table-responsive">
        <table class="w-100 table table-hover">
            <thead class="table-header">
                <tr>
                    <th class="text-nowrap">Patient No.</th>
                    <th style="cursor:pointer;" wire:click="sortBy('full_name')">
                        Full Name
                        @if ($sortField === 'full_name')
                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                        @endif
                    </th>
                    <th style="cursor:pointer;" wire:click="sortBy('age')">
                        Age
                        @if ($sortField === 'age')
                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                        @endif
                    </th>
                    <th style="cursor:pointer;" wire:click="sortBy('sex')">
                        Sex
                        @if ($sortField === 'sex')
                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                        @endif
                    </th>
                    <th>Contact No.</th>

                    <!-- Show "Type of Patient" column ONLY when viewing all types -->
                    @if($showAllTypes)
                    <th>Type of Patient</th>
                    @else
                    <th>Follow-Up Status</th>
                    @endif

                    <th style="cursor:pointer;" wire:click="sortBy('created_at')">
                        Date Registered
                        @if ($sortField === 'created_at')
                        {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                        @endif
                    </th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($archivedRecords as $record)
                <tr>
                    <td>{{ $record->patient->id ?? 'N/A' }}</td>
                    <td class="text-nowrap">{{ $record->patient->full_name ?? 'N/A' }}</td>
                    <td class="text-nowrap">{{ $record->patient->age_display ?? 'N/A' }}</td>
                    <td>{{ $record->patient->sex ?? 'N/A' }}</td>
                    <td>{{ $record->patient->contact_number ?? 'N/A' }}</td>

                    <!-- Show type when viewing all, show status when viewing specific type -->
                    @if($showAllTypes)
                    <td>
                        <span class="badge bg-primary">{{ ucfirst(str_replace('-', ' ', $record->type_of_case)) }}</span>
                    </td>
                    @else
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
                    @endif

                    <td>{{ $record->patient->created_at ? $record->patient->created_at->format('M j, Y') : '' }}</td>
                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button onclick="confirmActivate({{ $record->patient->id }})" class="text-success fs-2 fw-bold" title="Restore Patient">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach

                @if($archivedRecords->isEmpty())
                <tr>
                    <td colspan="{{ $showAllTypes ? '8' : '8' }}" class="text-center bg-light">No archived records found.</td>
                </tr>
                @endif
            </tbody>
        </table>
        {{ $archivedRecords->links() }}
    </div>
</div>

<script>
    function confirmActivate(patientId) {
        Swal.fire({
            title: 'Activate Patient?',
            text: "This patient will be moved to active status.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, activate it!'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.activatePatient(patientId);
            }
        });
    }

    window.addEventListener('patientActivated', event => {
        Swal.fire({
            icon: 'success',
            title: 'Activated!',
            text: 'Patient Record has been restored successfully.',
            timer: 2000,
            showConfirmButton: false
        });
    });

    window.addEventListener('activationError', event => {
        Swal.fire({
            icon: 'error',
            title: 'Cannot Restore',
            text: event.detail.message,
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    });
</script>