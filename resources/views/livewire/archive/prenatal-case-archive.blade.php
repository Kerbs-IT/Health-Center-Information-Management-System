<div>
    <div class="tables d-flex flex-column p-md-3 p-1">
        <div class="add-btn mb-3 d-flex justify-content-between">
            <div>
                @php
                $medicalRecordId = $this->caseId;

                // More reliable: Check the route name or a component property
                $currentRoute = request()->route()->getName();
                $currentUrl = url()->current();

                // Debug - check what we're getting
                // dd(['route' => $currentRoute, 'url' => $currentUrl]);

                // Determine back URL based on route or URL
                if (str_contains($currentRoute ?? '', 'vaccination') || str_contains($currentUrl, 'vaccination')) {
                $backUrl = url("/patient-record/vaccination/case/{$medicalRecordId}");
                } elseif (str_contains($currentRoute ?? '', 'prenatal') || str_contains($currentUrl, 'prenatal')) {
                $backUrl = url("/patient-record/prenatal/view-case/{$medicalRecordId}");
                } elseif (str_contains($currentRoute ?? '', 'family-planning') || str_contains($currentUrl, 'family-planning')) {
                $backUrl = url("/patient-record/family-planning/case/{$medicalRecordId}");
                } elseif (str_contains($currentRoute ?? '', 'tb-dots') || str_contains($currentUrl, 'tb-dots')) {
                $backUrl = url("/patient-record/tb-dots/case/{$medicalRecordId}");
                } elseif (str_contains($currentRoute ?? '', 'senior-citizen') || str_contains($currentUrl, 'senior-citizen')) {
                $backUrl = url("/patient-record/senior-citizen/case/{$medicalRecordId}");
                } else {
                // Since you're on prenatal archive, default to prenatal instead of vaccination
                $backUrl = url("/patient-record/prenatal/view-case/{$medicalRecordId}");
                }

                // Add query parameters if they exist
                $queryParams = request()->only(['search', 'entries', 'sortField', 'sortDirection']);
                if (!empty($queryParams)) {
                $backUrl .= '?' . http_build_query($queryParams);
                }
                @endphp

                <a href="{{ $backUrl }}" class="btn btn-danger px-4 fs-5 mb-3">
                    Back
                </a>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle"></i> Showing archived records. Click the restore button to activate a record.
        </div>

        <table class="w-100 table overflow-y-scroll">
            <thead class="table-header">
                <tr>
                    <th>Case No.</th>
                    <th>Type of Record</th>
                    <th>Nurse</th>
                    <th style="cursor:pointer;" wire:click="sortBy('created_at')">
                        Date
                        @if ($sortField === 'created_at')
                        {{ $sortDirection === 'asc' ? '▼' : '▲' }}
                        @endif
                    </th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allRecords as $record)
                <tr class="px-">
                    <td>{{ $record['id'] }}</td>
                    <td>{{ $record['type_of_record'] }}</td>
                    <td>Nurse Joy</td>
                    <td>{{ optional($record['created_at'])->format('M j, Y') }}</td>
                    <td><span class="badge bg-secondary">{{ $record['status'] }}</span></td>
                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button onclick="confirmActivate({{ $record['id'] }}, '{{ $record['record_type'] }}')" class="text-success fs-2 fw-bold">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="alert alert-warning mb-0">
                            <i class="fa-solid fa-inbox"></i> No archived records found.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $allRecords->links() }}
    </div>

    <script>
        function confirmActivate(recordId, recordType) {
            Swal.fire({
                title: 'Restore Record?',
                text: "This record will be moved to active status.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.restoreRecord(recordId, recordType);
                }
            });
        }

        window.addEventListener('patientActivated', event => {
            Swal.fire({
                icon: 'success',
                title: 'Restored!',
                text: 'Record has been restored successfully.',
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
</div>