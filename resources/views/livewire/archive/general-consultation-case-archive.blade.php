<div class="tables d-flex flex-column p-md-3 p-0">
    <div class="add-btn mb-3 d-flex justify-content-between">
        @php
        $backUrl = url("/patient-record/general-consultation/case/{$this->medical_record_id}");
        $backUrl .= '?' . http_build_query(
        request()->only(['search', 'entries', 'sortField', 'sortDirection'])
        );
        @endphp

        <a href="{{ $backUrl }}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
    </div>

    <div class="alert alert-info">
        <i class="fa-solid fa-info-circle"></i> Showing archived records. Click the restore button to activate a record.
    </div>

    <div class="table-responsive">
        <table class="w-100 table">
            <thead class="table-header">
                <tr>
                    <th>#</th>
                    <th>Symptoms</th>
                    <th>Diagnosis</th>
                    <th>Status</th>
                    <th style="cursor:pointer;" wire:click="sortBy('date_of_consultation')">
                        Date of Consultation
                        @if ($sortField === 'date_of_consultation')
                        {{ $sortDirection === 'asc' ? '▼' : '▲' }}
                        @endif
                    </th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>{{ $records->firstItem() + $loop->index }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($record->symptoms, 40) }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($record->diagnosis, 40) }}</td>
                    <td><span class="badge bg-secondary">{{ $record->status }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($record->date_of_consultation)->format('M j, Y') }}</td>
                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button onclick="confirmActivate({{ $record->id }})" class="text-success fs-2 fw-bold">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center bg-light">No archived records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $records->links() }}
    </div>

    <script>
        function confirmActivate(recordId) {
            Swal.fire({
                title: 'Restore Record?',
                text: "This record will be moved back to active status.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.activateGcRecord(recordId);
                }
            });
        }

        window.addEventListener('patientActivated', event => {
            Swal.fire({
                icon: 'success',
                title: 'Restored!',
                text: 'Consultation record has been restored successfully.',
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