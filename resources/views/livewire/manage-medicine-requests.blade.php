<div>
    <main class="d-flex flex-column container-fluid bg-light">
        <h2 class="mb-5 fs-1 text-center">Manage Medicine Requests</h2>

        {{-- Flash messages --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="m-3 p-lg-5 p-md-3 p-2 shadow min-h-[70vh]">
            {{-- Filters --}}
            <div class="medicine-inventory d-flex gap-3 align-items-none align-items-sm-end flex-wrap flex-column flex-sm-row">
                <div class="flex-fill">
                    <label class="form-label">Show</label>
                    <select wire:model.live="perPage" class="form-select w-50">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="flex-fill">
                    <label class="form-label">Search</label>
                    <input wire:model.live.debounce.300ms="search" type="search" class="form-control"
                           placeholder="Search patient or medicine...">
                </div>
                <div class="flex-fill">
                    <label class="form-label">Filter Status</label>
                        <select wire:model.live="filterStatus" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="ready_to_pickup">Ready to Pick Up</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="">All Status</option>
                        </select>
                </div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#walkInModal"
                        wire:click="resetWalkInForm">
                    <i class="fa-solid fa-user-plus me-1"></i>Add Walk-In
                </button>
            </div>

            {{-- Statistics Cards --}}
            <div class="row mt-4 mb-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5">
                <div class="col mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <h3 class="text-warning">{{ $this->getPendingCount() }}</h3>
                            <p class="mb-0 small">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <h3 class="text-primary">{{ $this->getReadyCount() }}</h3>
                            <p class="mb-0 small">Ready to Pick Up</p>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <h3 class="text-success">{{ $this->getCompletedCount() }}</h3>
                            <p class="mb-0 small">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card border-danger h-100">
                        <div class="card-body text-center">
                            <h3 class="text-danger">{{ $this->getRejectedCount() }}</h3>
                            <p class="mb-0 small">Rejected</p>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card border-secondary h-100">
                        <div class="card-body text-center">
                            <h3 class="text-secondary">{{ $this->getTotalCount() }}</h3>
                            <p class="mb-0 small">Total</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Requests Table --}}
            <div class="table-responsive mt-4">
                <table class="table table-hover">
                    <thead class="table-header">
                        <tr>
                            <th class="text-center text-nowrap">Patient Name</th>
                            <th class="text-center text-nowrap">Medicine</th>
                            <th class="text-center text-nowrap">Quantity</th>
                            <th class="text-center text-nowrap">Reason</th>
                            <th class="text-center text-nowrap">Status</th>
                            <th class="text-center text-nowrap">Date Requested</th>
                            <th class="text-center text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td class="text-center">{{ $request->requester_name }}</td>
                            <td class="text-center">
                                @if($request->medicine)
                                    <strong>{{ $request->medicine->medicine_name }}</strong>
                                    @if($request->medicine->trashed())
                                        <span class="badge bg-secondary ms-1"><i class="fa-solid fa-archive"></i> Archived</span>
                                    @endif
                                    <br><small class="text-muted">{{ $request->medicine->dosage }}</small>
                                @else
                                    <span class="text-muted fst-italic">Medicine not found</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $request->quantity_requested }}</td>
                            <td class="text-center"><small>{{ Str::limit($request->reason, 50) }}</small></td>
                            <td class="text-center">
                                @php
                                    $statusMap = [
                                        'pending'         => ['bg-warning text-dark',  'Pending'],
                                        'approved'        => ['bg-info text-white',     'Approved'],
                                        'ready_to_pickup' => ['bg-primary text-white',  'Ready to Pick Up'],
                                        'completed'       => ['bg-success text-white',  'Completed'],
                                        'rejected'        => ['bg-danger text-white',   'Rejected'],
                                        'cancelled'       => ['bg-secondary text-white','Cancelled'],
                                    ];
                                    [$badgeClass, $badgeLabel] = $statusMap[$request->status] ?? ['bg-secondary', ucfirst($request->status)];
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            </td>
                            <td class="text-center">
                                {{ $request->created_at->format('F d Y') }}<br>
                                <small class="text-muted">{{ $request->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    {{-- PENDING: Approve / Reject --}}
                                    @if($request->status === 'pending')
                                        @if($request->medicine && !$request->medicine->trashed())
                                            <button onclick="confirmApprove({{ $request->id }})"
                                                    class="btn btn-sm btn-success text-nowrap">
                                                <i class="fa-solid fa-check me-1"></i>Approve
                                            </button>
                                        @else
                                            <button disabled class="btn btn-sm btn-secondary text-nowrap" title="Medicine is archived">
                                                <i class="fa-solid fa-ban me-1"></i>Archived
                                            </button>
                                        @endif
                                        <button onclick="confirmReject({{ $request->id }})"
                                                class="btn btn-sm btn-danger text-nowrap">
                                            <i class="fa-solid fa-times me-1"></i>Reject
                                        </button>

                                    @elseif($request->status === 'ready_to_pickup')
                                        @if($request->medicine && $request->medicine->trashed())
                                            {{-- Medicine archived: cancel only, no dispense --}}
                                            <span class="badge bg-secondary text-nowrap mb-1">
                                                <i class="fa-solid fa-archive me-1"></i>Medicine Archived
                                            </span>
                                            <button onclick="confirmCancel({{ $request->id }})"
                                                    class="btn btn-sm btn-danger text-nowrap">
                                                <i class="fa-solid fa-rotate-left me-1"></i>Cancel
                                            </button>
                                        @else
                                            <button onclick="confirmDispense({{ $request->id }})"
                                                    class="btn btn-sm btn-primary text-white text-nowrap">
                                                <i class="fa-solid fa-hand-holding-medical me-1"></i>Dispense
                                            </button>
                                            <button onclick="confirmCancel({{ $request->id }})"
                                                    class="btn btn-sm btn-outline-danger text-nowrap">
                                                <i class="fa-solid fa-rotate-left me-1"></i>Cancel
                                            </button>
                                        @endif
                                    @endif

                                    {{-- View details always available --}}
                                    <button wire:click="viewDetails({{ $request->id }})"
                                            class="btn btn-sm btn-primary text-nowrap"
                                            data-bs-toggle="modal" data-bs-target="#viewDetailsModal">
                                        <i class="fa-solid fa-eye fa-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fa-solid fa-inbox fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted">No medicine requests found</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $requests->links() }}
        </div>
    </main>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- WALK-IN MODAL                                               --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="walkInModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i>Walk-In Medicine Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            wire:click="resetWalkInForm"></button>
                </div>
                <div class="modal-body">
                    {{-- User Search --}}
                    <div class="mb-3">
                        <label class="form-label">Search User/Patient <span class="text-danger">*</span></label>
                        <div wire:ignore>
                            <select id="userSelect" class="form-control user-search @error('walkInUserId') is-invalid @enderror"
                                    style="width:100%">
                                <option value="">Select user/patient</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->full_name }}
                                        @if($user->patient) ✓ Has Patient Record @endif
                                        @if($user->patient_type) - {{ $user->patient_type }} @endif
                                    </option>
                                    @foreach($user->patients as $child)
                                        <option value="child:{{ $child->id }}">
                                            {{ $child->full_name }} — Family Member of {{ $user->full_name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        @error('walkInUserId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Medicine --}}
                    <div class="mb-3">
                        <label class="form-label">Medicine <span class="text-danger">*</span></label>
                        <select wire:model="walkInMedicineId" class="form-select @error('walkInMedicineId') is-invalid @enderror">
                            <option value="">Select medicine</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->medicine_id }}">
                                    {{ $medicine->medicine_name }} - {{ $medicine->dosage }}
                                    (Stock: {{ $medicine->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('walkInMedicineId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Quantity --}}
                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input wire:model="walkInQuantity" type="number" min="1" max="99"
                               class="form-control @error('walkInQuantity') is-invalid @enderror"
                               placeholder="Enter quantity"
                               oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,2)"
                               onkeypress="return event.charCode>=48&&event.charCode<=57">
                        @error('walkInQuantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Reason --}}
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea wire:model="walkInReason" class="form-control @error('walkInReason') is-invalid @enderror"
                                  rows="3" placeholder="Enter reason for medicine request..."></textarea>
                        @error('walkInReason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="alert alert-success">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <small>Walk-in requests are dispensed immediately using FIFO batch order. Stock is deducted on submission.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="resetWalkInForm">Cancel</button>
                    <button type="button" wire:click="createWalkIn" class="btn btn-success">
                        <i class="fa-solid fa-check me-1"></i>Dispense Medicine
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- VIEW DETAILS MODAL                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-info-circle me-2"></i>Request Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($viewRequest)
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3 text-success">
                                    <i class="fa-solid fa-file-medical me-2"></i>Request Information
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small">Request ID</label>
                                <p class="fw-bold">#{{ $viewRequest->id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Patient Name</label>
                                <p class="fw-bold">{{ $viewRequest->requester_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Status</label>
                                <p>
                                    @php
                                        $statusMap = [
                                            'pending'        => ['bg-warning text-dark', 'Pending'],
                                            'approved'       => ['bg-info text-white',    'Approved'],
                                            'ready_to_pickup'=> ['bg-primary text-white', 'Ready to Pick Up'],
                                            'completed'      => ['bg-success text-white', 'Completed'],
                                            'rejected'       => ['bg-danger text-white',  'Rejected'],
                                        ];
                                        [$bc, $bl] = $statusMap[$viewRequest->status] ?? ['bg-secondary', ucfirst($viewRequest->status)];
                                    @endphp
                                    <span class="badge {{ $bc }}">{{ $bl }}</span>
                                </p>
                            </div>

                            {{-- Medicine Details --}}
                            <div class="col-12 mt-2">
                                <h6 class="border-bottom pb-2 mb-3 text-success">
                                    <i class="fa-solid fa-pills me-2"></i>Medicine Details
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Medicine Name</label>
                                <p class="fw-bold">
                                    {{ $viewRequest->medicine->medicine_name ?? 'N/A' }}
                                    @if($viewRequest->medicine && $viewRequest->medicine->trashed())
                                        <span class="badge bg-secondary ms-1"><i class="fa-solid fa-archive"></i> Archived</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Dosage</label>
                                <p class="fw-bold">{{ $viewRequest->medicine->dosage ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Price per Unit</label>
                                <p class="fw-bold">
                                    ₱{{ $viewRequest->medicine ? number_format($viewRequest->medicine->price, 2) : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Quantity Requested</label>
                                <p class="fw-bold text-primary">{{ $viewRequest->quantity_requested }}</p>
                            </div>
                            @if($viewRequest->medicine)
                            <div class="col-md-4">
                                <label class="form-label text-muted small">Estimated Total</label>
                                <p class="fw-bold text-success">
                                    ₱{{ number_format($viewRequest->medicine->price * $viewRequest->quantity_requested, 2) }}
                                </p>
                            </div>
                            @endif

                            {{-- Reason --}}
                            <div class="col-12">
                                <label class="form-label text-muted small">Reason for Request</label>
                                <div class="p-3 bg-light rounded">
                                    <p class="mb-0">{{ $viewRequest->reason ?? '—' }}</p>
                                </div>
                            </div>

                            {{-- Timeline --}}
                            <div class="col-12 mt-2">
                                <h6 class="border-bottom pb-2 mb-3 text-success">
                                    <i class="fa-solid fa-clock me-2"></i>Timeline
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Date Requested</label>
                                <p>{{ $viewRequest->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @if($viewRequest->approved_at)
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Approved At</label>
                                <p>{{ $viewRequest->approved_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif
                            @if($viewRequest->ready_at)
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Marked Ready At</label>
                                <p>{{ $viewRequest->ready_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif
                            @if($viewRequest->dispensed_at)
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Dispensed At</label>
                                <p>{{ $viewRequest->dispensed_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif

                            {{-- Status guidance banner --}}
                            <div class="col-12">
                                @if($viewRequest->status === 'pending')
                                    <div class="alert alert-warning mb-0">
                                        <i class="fa-solid fa-clock me-2"></i>
                                        <small>This request is awaiting approval.</small>
                                    </div>
                                @elseif($viewRequest->status === 'approved')
                                    <div class="alert alert-info mb-0">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        <small>Approved. Click <strong>Mark Ready</strong> once the medicine is prepared for collection.</small>
                                    </div>
                                @elseif($viewRequest->status === 'ready_to_pickup')
                                    <div class="alert alert-primary mb-0">
                                        <i class="fa-solid fa-bell me-2"></i>
                                        <small>Patient has been notified. Click <strong>Dispense</strong> when the patient collects the medicine.</small>
                                    </div>
                                @elseif($viewRequest->status === 'completed')
                                    <div class="alert alert-success mb-0">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        <small>Medicine has been dispensed successfully.</small>
                                    </div>
                                @elseif($viewRequest->status === 'rejected')
                                    <div class="alert alert-danger mb-0">
                                        <i class="fa-solid fa-times-circle me-2"></i>
                                        <small>This request has been rejected.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa-solid fa-exclamation-triangle fs-1 text-warning mb-3"></i>
                            <p class="text-muted">No details available</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    @if($viewRequest && $viewRequest->status === 'pending')
                        @if($viewRequest->medicine && !$viewRequest->medicine->trashed())
                            <button type="button" wire:click="approve({{ $viewRequest->id }})"
                                    class="btn btn-success" data-bs-dismiss="modal">
                                <i class="fa-solid fa-check me-1"></i>Approve
                            </button>
                        @endif
                        <button type="button" wire:click="reject({{ $viewRequest->id }})"
                                class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times me-1"></i>Reject
                        </button>
                    @elseif($viewRequest && $viewRequest->status === 'approved')
                        <button type="button" wire:click="markReadyToPickup({{ $viewRequest->id }})"
                                class="btn btn-info text-white" data-bs-dismiss="modal">
                            <i class="fa-solid fa-bell me-1"></i>Mark Ready
                        </button>
                    @elseif($viewRequest && $viewRequest->status === 'ready_to_pickup')
                        @if($viewRequest->medicine && $viewRequest->medicine->trashed())
                            <span class="badge bg-secondary me-auto">
                                <i class="fa-solid fa-archive me-1"></i>Medicine Archived — Cannot Dispense
                            </span>
                        @else
                            <button type="button" wire:click="dispense({{ $viewRequest->id }})"
                                    class="btn btn-primary" data-bs-dismiss="modal">
                                <i class="fa-solid fa-hand-holding-medical me-1"></i>Dispense
                            </button>
                        @endif
                        <button type="button" wire:click="cancelReadyRequest({{ $viewRequest->id }})"
                                wire:confirm="Cancel this request? Reserved stock will be restored."
                                class="btn btn-outline-danger" data-bs-dismiss="modal">
                            <i class="fa-solid fa-rotate-left me-1"></i>Cancel
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // ── Approve ──────────────────────────────────────────────────
    function confirmApprove(id) {
        Swal.fire({
            title: 'Approve Request?',
            text: 'Stock will be reserved and the patient will be notified to pick up.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Yes, Approve',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('approve', id);
            }
        });
    }

    // ── Reject ───────────────────────────────────────────────────
    function confirmReject(id) {
        Swal.fire({
            title: 'Reject Request?',
            text: 'This action will reject the medicine request.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-times me-1"></i> Yes, Reject',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('reject', id);
            }
        });
    }

    // ── Dispense ─────────────────────────────────────────────────
    function confirmDispense(id) {
        Swal.fire({
            title: 'Dispense Medicine?',
            text: 'Confirm that the patient has arrived and is ready to receive the medicine.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-hand-holding-medical me-1"></i> Yes, Dispense',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('dispense', id);
            }
        });
    }

    // ── Cancel Ready Request ─────────────────────────────────────
    function confirmCancel(id) {
        Swal.fire({
            title: 'Cancel Request?',
            text: 'Reserved stock will be fully restored to inventory.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa-solid fa-rotate-left me-1"></i> Yes, Cancel',
            cancelButtonText: 'Keep',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('cancelReadyRequest', id);
            }
        });
    }

    // ── Show success/error SweetAlerts from Livewire flash ───────
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('flashMessage', ({ type, message }) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,        // 'success' | 'error'
                title: message,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
            });
        });
    });
</script>