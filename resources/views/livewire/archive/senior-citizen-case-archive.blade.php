<div>
    <div class="tables d-flex flex-column p-md-3 p-0">
        <div class="add-btn mb-3 d-flex justify-content-between">
            <div>
                @php
                $medicalRecordId = $caseId;
                $section = 'senior-citizen';

                // Simple mapping for back URL
                $backUrls = [
                'vaccination' => "/patient-record/vaccination/case/{$medicalRecordId}",
                'prenatal' => "/patient-record/prenatal/view-case/{$medicalRecordId}",
                'family-planning' => "/patient-record/family-planning/case/{$medicalRecordId}",
                'tb-dots' => "/patient-record/tb-dots/case/{$medicalRecordId}",
                'senior-citizen' => "/patient-record/senior-citizen/view-case/{$medicalRecordId}",
                ];

                $backUrl = url($backUrls[$section] ?? $backUrls['senior-citizen']);

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

        <div class="table-responsive">
            <table class="w-100 table">
                <thead class="table-header text-nowrap">
                    <tr>
                        <th>Case No.</th>
                        <th>Type of Record</th>
                        <th style="cursor:pointer;" wire:click="sortBy('created_at')">
                            Date
                            @if ($sortField === 'created_at')
                            {{ $sortDirection === 'asc' ? '▼' : '▲' }}
                            @endif
                        </th>
                        <th>Date of comeback</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($seniorCaseRecords as $record)
                    <tr class="px-1">
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->type_of_record }}</td>
                        <td>{{ $record->created_at->format('M d Y') }}</td>
                        <td>{{ $record->date_of_comeback?$record->date_of_comeback->format('M d Y'): 'N/A' }}</td>
                        <td><span class="badge bg-secondary">{{ $record->status }}</span></td>
                        <td>
                            <div class="actions d-flex gap-2 justify-content-center align-items-center">

                                <button onclick="confirmActivate({{ $record->id }})" class="text-success fs-2 fw-bold" title="Restore Record">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="alert alert-warning mb-0">
                                <i class="fa-solid fa-inbox"></i> No archived records found.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $seniorCaseRecords->links() }}
    </div>

    <script>
        function confirmActivate(recordId) {
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
                    @this.restoreRecord(recordId);
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