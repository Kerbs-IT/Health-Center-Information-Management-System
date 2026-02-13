<div class="tables d-flex flex-column p-md-3 p-0">
    <div class="add-btn mb-3 d-flex justify-content-between">
        @php
        $medicalRecordId = $this->medical_record_id;

        // Determine the back URL from current URL
        $currentUrl = url()->current();

        if (str_contains($currentUrl, 'vaccination')) {
        $backUrl = url("/patient-record/vaccination/case/{$medicalRecordId}");
        } elseif (str_contains($currentUrl, 'prenatal')) {
        $backUrl = url("/patient-record/prenatal/case/{$medicalRecordId}");
        } elseif (str_contains($currentUrl, 'family-planning')) {
        $backUrl = url("/patient-record/family-planning/case/{$medicalRecordId}");
        } elseif (str_contains($currentUrl, 'tb-dots')) {
        $backUrl = url("/patient-record/tb-dots/case/{$medicalRecordId}");
        } elseif (str_contains($currentUrl, 'senior-citizen')) {
        $backUrl = url("/patient-record/senior-citizen/case/{$medicalRecordId}");
        } else {
        $backUrl = url("/patient-record/vaccination/case/{$medicalRecordId}");
        }

        // Add query parameters if they exist
        $backUrl .= '?' . http_build_query(
        request()->only(['search', 'entries', 'sortField', 'sortDirection'])
        );
        @endphp

        <a href="{{ $backUrl }}" class="btn btn-danger px-4 fs-5 mb-3">
            Back
        </a>


    </div>
    <div class="alert alert-info">
        <i class="fa-solid fa-info-circle"></i> Showing archived records. Click the restore button to activate a record.
    </div>
    <div class="table-responsive">
        <table class="w-100 table ">
            <thead class="table-header">
                <tr>
                    <th>Case No.</th>
                    <th>Vaccine Type/s</th>
                    <th>Dosage</th>
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
            <!-- data of patient -->
            <tbody>
                @forelse($records as $record)
                <tr class="px-">
                    <!-- <div>{{$record}}</div> -->

                    <td>{{$record->id}}</td>
                    <td>{{$record->vaccine_type}}</td>
                    <td>{{$record->dose_number}}{{$record->dose_number == 1 ? 'st':'th'}} Dose</td>
                    <td>{{ \Carbon\Carbon::parse($record->date_of_vaccination)->format('M j, Y') }}</td>
                    <td><span class="badge bg-secondary">{{ $record['status'] }}</span></td>

                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button onclick="confirmActivate({{ $record->id }})" class=" text-success  fs-2 fw-bold ">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="">
                    <td colspan="12" class="text-center bg-light">No records found.</td>
                </tr>
                @endforelse

            </tbody>

        </table>
    </div>

    <script>
        function confirmActivate(recordId) {
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
                    @this.activateVaccinationRecord(recordId);
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
                title: 'Cannot Activate',
                text: event.detail.message,
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        });
    </script>