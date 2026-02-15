<div>
    <div class="container-fluid py-5">
        <div class="card">
            <div class="card-body">

                {{-- ─── Filters Row 1: Search | Status | Purok ─────────────────── --}}
                <div class="row mb-3 gap-md-0 gap-2 align-items-end">
                    {{-- Entries per page --}}
                    <div class="col-md-2">
                        <label class="fw-bold w-100">Show:</label>
                        <select wire:model.live="perPage" class="form-select border-2">
                            <option value="10">10</option>
                            <option value="15" selected>15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    {{-- Search --}}
                    <div class="col-md-4">
                        <label class="fw-bold w-100">Search:</label>
                        <input type="text"
                            wire:model.live.debounce.500ms="search"
                            class="form-control border-2"
                            placeholder="Search patients...">
                    </div>

                    {{-- Status --}}
                    <div class="col-md-2">
                        <label class="fw-bold w-100">Status:</label>
                        <select wire:model.live="statusFilter" class="form-select border-2">
                            <option value="all">All</option>
                            <option value="Active">Active</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>

                    {{-- Purok --}}
                    <div class="col-md-2">
                        <label class="fw-bold w-100">Purok:</label>
                        @if($isStaff)
                        {{-- Staff: locked to assigned area, no dropdown --}}
                        <input type="text"
                            class="form-control border-2 bg-light"
                            value="{{ $assignedPurok }}"
                            disabled>
                        @else
                        <select wire:model.live="purokFilter" class="form-select border-2">
                            <option value="all">All Puroks</option>
                            @foreach($puroks as $purok)
                            <option value="{{ $purok }}">{{ $purok }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>

                    {{-- Type of Patient --}}
                    <div class="col-md-2">
                        <label class="fw-bold w-100">Type of Patient:</label>
                        <select wire:model.live="typeFilter" class="form-select border-2">
                            <option value="all">All Types</option>
                            @foreach($caseTypes as $type)
                            <option value="{{ $type }}">{{ ucwords(str_replace('-', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- ─── Filters Row 2: Date Range | Download ───────────────────── --}}
                <div class="row mb-3 gap-md-0 gap-2 align-items-end">

                    {{-- Date Range (existing datepicker — dispatches dateRangeChanged to Livewire) --}}
                    <div class="date-range-filter col-md-3">
                        <label class="filter-label fw-bold w-100" for="dateRange">Date Range:</label>
                        <input type="text" id="dateRange"
                            class="filter-select border-1 border-black form-control"
                           />
                    </div>

                    {{-- Download PDF --}}
                    <div class="col-md-2 d-flex align-items-end">
                        <button wire:click="downloadPdf"
                            wire:loading.attr="disabled"
                            class="btn btn-success w-100">
                            <span wire:loading.remove wire:target="downloadPdf">
                                <i class="fas fa-file-pdf me-1"></i> Download PDF
                            </span>
                            <span wire:loading wire:target="downloadPdf">
                                <i class="fas fa-spinner fa-spin me-1"></i> Generating...
                            </span>
                        </button>
                    </div>

                </div>

                {{-- ─── Entry count summary ─────────────────────────────────────── --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">
                        Showing
                        <strong>{{ $patients->firstItem() ?? 0 }}</strong>
                        –
                        <strong>{{ $patients->lastItem() ?? 0 }}</strong>
                        of
                        <strong>{{ $patients->total() }}</strong>
                        entr{{ $patients->total() == 1 ? 'y' : 'ies' }}
                    </small>
                </div>

                {{-- ─── Table ───────────────────────────────────────────────────── --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Contact Number</th>
                                <th>Type of Patient</th>
                                <th>Purok</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patients as $index => $patient)
                            <tr>
                                <td>{{ $patients->firstItem() + $loop->index }}</td>
                                <td>{{ $patient->full_name }}</td>
                                <td>{{ $patient->sex }}</td>
                                <td>{{ $patient->ageDisplay }}</td>
                                <td>{{ $patient->contact_number }}</td>
                                <td>
                                    @if($patient->type_of_case)
                                    <span class="badge
                                        @switch($patient->type_of_case)
                                            @case('vaccination')     bg-primary   @break
                                            @case('prenatal')        bg-light-pink      @break
                                            @case('family-planning') bg-info      @break
                                            @case('senior-citizen')  bg-secondary @break
                                            @case('tb-dots')         bg-warning text-dark @break
                                            @default bg-dark
                                        @endswitch
                                    ">
                                        {{ ucwords(str_replace('-', ' ', $patient->type_of_case)) }}
                                    </span>
                                    @else
                                    <span class="text-muted fst-italic">—</span>
                                    @endif
                                </td>
                                <td>{{ $patient->purok ?? '—' }}</td>
                                <td>
                                    @if($patient->status === 'Active')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Archived</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap justify-content-center">
                                        {{-- Edit --}}
                                        <button wire:click="openEditModal({{ $patient->id }})"
                                            class="btn btn-sm btn-warning text-nowrap">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        {{-- Archive / Activate --}}
                                        @if($patient->status === 'Active')
                                        <button onclick="confirmArchive({{ $patient->id }})"
                                            class="btn btn-sm btn-danger text-nowrap">
                                            <i class="fas fa-archive"></i> Archive
                                        </button>
                                        @else
                                        <button onclick="confirmActivate({{ $patient->id }})"
                                            class="btn btn-sm btn-success text-nowrap">
                                            <i class="fas fa-check-circle"></i> Activate
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No patients found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ─── Pagination ───────────────────────────────────────────────── --}}
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <small class="text-muted">
                        Page {{ $patients->currentPage() }} of {{ $patients->lastPage() }}
                    </small>
                    {{ $patients->links() }}
                </div>

            </div>
        </div>
    </div>

    {{-- ─── Edit Patient Modal ──────────────────────────────────────────────── --}}
    <div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="editPatientModalLabel">
                        <i class="fas fa-user-edit me-2"></i> Edit Patient Record
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- ─── Personal Info ──────────────────────────────── --}}
                    <h6 class="fw-bold text-muted mb-3 border-bottom pb-1">Personal Information</h6>
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="edit_first_name"
                                class="form-control @error('edit_first_name') is-invalid @enderror"
                                placeholder="First name">
                            @error('edit_first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Middle Name</label>
                            <input type="text" wire:model="edit_middle_initial"
                                class="form-control"
                                placeholder="Middle Name">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="edit_last_name"
                                class="form-control @error('edit_last_name') is-invalid @enderror"
                                placeholder="Last name">
                            @error('edit_last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-1">
                            <label class="form-label fw-bold">Suffix</label>
                            <input type="text" wire:model="edit_suffix"
                                class="form-control"
                                placeholder="Jr.">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" wire:model="edit_date_of_birth"
                                class="form-control @error('edit_date_of_birth') is-invalid @enderror">
                            @error('edit_date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Sex <span class="text-danger">*</span></label>
                            <select wire:model="edit_sex"
                                class="form-select @error('edit_sex') is-invalid @enderror">
                                <option value="">-- Select --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            @error('edit_sex')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Civil Status</label>
                            <select wire:model="edit_civil_status" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Nationality</label>
                            <input type="text" wire:model="edit_nationality"
                                class="form-control"
                                placeholder="e.g. Filipino">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Place of Birth</label>
                            <input type="text" wire:model="edit_place_of_birth"
                                class="form-control"
                                placeholder="e.g. Trece Martires, Cavite">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
                            <input type="number" wire:model="edit_contact_number"
                                class="form-control @error('edit_contact_number') is-invalid @enderror"
                                placeholder="09XXXXXXXXX"
                                max="999999999999">
                            @error('edit_contact_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- ─── Address ──────────────────────────────────────── --}}
                    <h6 class="fw-bold text-muted mt-4 mb-3 border-bottom pb-1">Address</h6>
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Street / Block & Lot</label>
                            <input type="text" wire:model="edit_street"
                                class="form-control"
                                placeholder="e.g. Blk 1 Lot 2, Rizal St">
                            <small class="text-muted">Format: <em>house/blk lot, street name</em></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Purok / Brgy Subdivision <span class="text-danger">*</span></label>
                            @if($isStaff)
                            <input type="text" class="form-control bg-light"
                                value="{{ $assignedPurok }}" disabled>
                            @else
                            <select wire:model="edit_brgy"
                                class="form-select @error('edit_brgy') is-invalid @enderror">
                                <option value="">-- Select --</option>
                                @foreach($puroks as $purok)
                                <option value="{{ $purok }}">{{ $purok }}</option>
                                @endforeach
                            </select>
                            @error('edit_brgy')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @endif
                        </div>

                    </div>

                    {{-- Warning note --}}
                    <div class="alert alert-info mt-3 mb-0 py-2 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Saving will automatically update this patient's name and address across all related records
                        (vaccination, prenatal, family planning, TB-DOTS, senior citizen) and linked user account if any.
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button"
                        wire:click="updatePatient"
                        wire:loading.attr="disabled"
                        class="btn btn-success fw-bold">
                        <span wire:loading.remove wire:target="updatePatient">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </span>
                        <span wire:loading wire:target="updatePatient">
                            <i class="fas fa-spinner fa-spin me-1"></i> Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmArchive(patientId) {
            Swal.fire({
                title: 'Archive Patient?',
                text: "This patient will be moved to archived status.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.archivePatient(patientId);
                }
            });
        }

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

        window.addEventListener('patientArchived', () => {
            Swal.fire({
                icon: 'success',
                title: 'Archived!',
                text: 'Patient Record has been archived successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        });

        window.addEventListener('patientActivated', () => {
            Swal.fire({
                icon: 'success',
                title: 'Activated!',
                text: 'Patient Record has been activated successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // ── Edit modal open/close ─────────────────────────────────────────────
        window.addEventListener('openEditModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('editPatientModal'));
            modal.show();
        });

        window.addEventListener('closeEditModal', () => {
            const modalEl = document.getElementById('editPatientModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });

        window.addEventListener('patientUpdated', () => {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Patient record has been updated successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
</div>