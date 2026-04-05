<div class="tables d-flex flex-column p-md-3 p-0">
    <div class="add-btn mb-3 d-flex justify-content-between">
        @php
        $backUrl = route('record.general.consultation') . '?' . http_build_query(request()->only(['patient_id', 'search', 'entries', 'sortField', 'sortDirection']));
        @endphp

        <a href="{{ $backUrl }}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>

        <div class="right-side-btn">
            <button type="button" class="btn btn-success px-3 py-2"
                data-bs-toggle="modal"
                data-bs-target="#gcModal"
                id="add-gc-case-record-btn"
                data-health-worker-id="{{ $medicalRecordCase->gc_medical_record->health_worker_id ?? '' }}">
                Add Record
            </button>
            <a href="{{ route('gc.case.record.archive') }}?medical_record_id={{ $medicalRecordCase->id }}" class="btn btn-danger">
                <i class="fa-solid fa-box-archive"></i> Archive
            </a>
        </div>
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
                @forelse($gc_case_record as $index => $record)
                <tr>
                    <td>{{ $gc_case_record->firstItem() + $loop->index }}</td>

                    <td>{{ \Illuminate\Support\Str::limit($record->symptoms, 40) }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($record->diagnosis, 40) }}</td>
                    <td>{{ $record->status }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->date_of_consultation)->format('M j, Y') }}</td>
                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">

                            {{-- View --}}
                            <button type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#viewGcRecordModal"
                                class="view-gc-case-info"
                                data-bs-case-id="{{ $record->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512">
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                </svg>
                            </button>

                            {{-- Edit --}}
                            <button type="button"
                                class="fs-3 text-success fw-bold gc-case-edit-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#editGcModal"
                                data-bs-case-id="{{ $record->id }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>

                            {{-- Archive --}}
                            <button type="button"
                                class="fs-3 archive-gc-record-icon text-danger fw-bold"
                                data-bs-case-id="{{ $record->id }}">
                                <i class="fa-solid fa-box-archive text-danger"></i>
                            </button>

                            {{-- PDF Export --}}
                            <button type="button" wire:click="exportPdf({{ $record->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" style="width:30px;height:30px;fill:green" viewBox="0 0 512 512">
                                    <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                </svg>
                            </button>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center bg-light">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $gc_case_record->links() }}
    </div>
</div>