
<div class=>
 <div>
    <main class="d-flex flex-column container-fluid bg-light">
        <h2 class="mb-2 fs-1 text-center">Request Medicine</h2>

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

        <div class="m-1 p-lg-5 p-md-3 p-2 shadow">

            {{-- Top bar --}}
            <div class="d-flex gap-3 align-items-end flex-wrap flex-column flex-sm-row mb-4">
                <div class="flex-fill">
                    <label class="form-label">Search medicine</label>
                    <input wire:model.live.debounce.300ms="search" type="search"
                           class="form-control" placeholder="Search by medicine name...">
                </div>
                <button class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#requestMedicineModal" wire:click="resetForm">
                    <i class="fa-solid fa-plus pe-1"></i>Request Medicine
                </button>
            </div>

            {{-- ── ACTIVE REQUESTS ── --}}
            <h5 class="fw-bold mb-3">
                <i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Active Requests
                <span class="badge bg-warning text-dark ms-2">{{ $activeRequests->total() }}</span>
            </h5>

            @if($activeRequests->total() > 0)
                <div class="table-responsive mb-2">
                    <table class="table table-hover">
                        <thead class="table-header">
                            <tr class="text-nowrap">
                                <th class="text-center">Requested For</th>
                                <th class="text-center">Medicine Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Date Requested</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $user     = auth()->user();
                                $childIds = \App\Models\patients::where('guardian_user_id', $user->id)
                                    ->pluck('id')->toArray();
                            @endphp
                            @foreach ($activeRequests as $request)
                                <tr>
                                    <td class="text-center">
                                        @if($request->patients_id && in_array($request->patients_id, $childIds))
                                            <span class="badge bg-primary">
                                                <i class="fa-solid fa-child me-1"></i>
                                                {{ $request->patients->full_name ?? 'Child' }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fa-solid fa-user me-1"></i>Myself
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $request->medicine->medicine_name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $request->quantity_requested }}</td>
                                    <td class="text-center">
                                        @if($request->status === 'pending')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fa-solid fa-hourglass-half me-1"></i>Pending
                                            </span>
                                            <br><small class="text-muted">Awaiting approval</small>
                                        @elseif($request->status === 'ready_to_pickup')
                                            <span class="badge bg-primary text-white">
                                                <i class="fa-solid fa-bell me-1"></i>Ready to Pick Up
                                            </span>
                                            <br><small class="">
                                                <i class="fa-solid fa-location-pin me-1"></i>Please visit the health center!
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        {{ $request->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $request->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            @if($request->status === 'pending')
                                                <button wire:click="editRequest({{ $request->id }})"
                                                        class="btn btn-sm btn-primary">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button wire:click="confirmRequestMedicineDelete({{ $request->id }})"
                                                        class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            @else
                                                {{-- ready_to_pickup: view only --}}
                                                <button wire:click="viewDetails({{ $request->id }})"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        data-bs-toggle="modal" data-bs-target="#viewDetailsModal">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $activeRequests->links() }}
            @else
                <div class="text-center py-4 mb-3 bg-white rounded border">
                    <i class="fa-solid fa-check-circle fs-2 text-success mb-2 d-block"></i>
                    <p class="text-muted mb-0">No active requests — you're all caught up!</p>
                </div>
            @endif

            <hr class="my-4">

            {{-- ── HISTORY ── --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-rectangle-list me-2 text-secondary"></i>Request History
                    <span class="badge bg-secondary ms-2">{{ $historyRequests->total() }}</span>
                </h5>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <label class="form-label mb-0 text-muted small">Filter:</label>
                    <select wire:model.live="statusFilter" class="form-select form-select-sm" style="width:auto">
                        <option value="">All History</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <label class="form-label mb-0 text-muted small">Show:</label>
                    <select wire:model.live="perPage" class="form-select form-select-sm" style="width:auto">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-header">
                        <tr class="text-nowrap">
                            <th class="text-center">Requested For</th>
                            <th class="text-center">Medicine Name</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Date Requested</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historyRequests as $request)
                            <tr>
                                <td class="text-center">
                                    @if($request->patients_id && in_array($request->patients_id, $childIds))
                                        <span class="badge bg-primary">
                                            <i class="fa-solid fa-child me-1"></i>
                                            {{ $request->patients->full_name ?? 'Child' }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-user me-1"></i>Myself
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $request->medicine->medicine_name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $request->quantity_requested }}</td>
                                <td class="text-center">
                                    @if($request->status === 'completed')
                                        <span class="badge bg-success">
                                            <i class="fa-solid fa-check me-1"></i>Completed
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fa-solid fa-times me-1"></i>Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ $request->created_at->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $request->created_at->format('h:i A') }}</small>
                                </td>
                                <td class="text-center">
                                    <button wire:click="viewDetails({{ $request->id }})"
                                            class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal" data-bs-target="#viewDetailsModal">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fa-solid fa-inbox fs-2 text-muted mb-2 d-block"></i>
                                    <p class="text-muted mb-0">No history yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $historyRequests->links() }}

        </div>
    </main>

    {{-- All your existing modals (requestMedicineModal, editMedicineModal, viewDetailsModal)
         remain exactly the same — paste them here unchanged --}}
    {{-- Request Medicine Modal --}}
    <div class="modal fade" id="requestMedicineModal" tabindex="-1" aria-labelledby="requestMedicineLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow">
                {{-- Modal Header --}}
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="requestMedicineLabel">
                        Request Modal
                </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>

                <div class="modal-body">
                    <form wire:submit.prevent="submitRequest">
                        @csrf

                        {{-- ── Who is this request for? ── --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Who is this request for? <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="requestFor"
                                        id="requestForSelf" value="self">
                                    <label class="form-check-label" for="requestForSelf">
                                        Myself
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="requestFor"
                                        id="requestForChild" value="child">
                                    <label class="form-check-label" for="requestForChild">
                                        Family Member(s)
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- ── Child selector (only shown when "My Child" is selected) ── --}}
                        @if($requestFor === 'child')
                            <div class="mb-3 p-3 border rounded bg-light">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i>Search &amp; Select Family Member
                                    <span class="text-danger">*</span>
                                </label>

                                {{-- Search box --}}
                                <input wire:model.live.debounce.300ms="childSearch"
                                    type="search"
                                    class="form-control mb-2"
                                    placeholder="Search family member by name...">

                                {{-- Child list --}}
                                @if($children && count($children) > 0)
                                    <div class="list-group" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($children as $child)
                                            <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 cursor-pointer
                                                        {{ $selectedChildId == $child->id ? 'active' : '' }}">
                                                <input type="radio"
                                                    wire:model.live="selectedChildId"
                                                    value="{{ $child->id }}"
                                                    class="form-check-input flex-shrink-0 mt-0">
                                                <div>
                                                    <div class="fw-semibold">{{ $child->full_name }}</div>
                                                    <small class="{{ $selectedChildId == $child->id ? 'text-white-50' : 'text-muted' }}">
                                                        Age: {{ $child->age_display ?? $child->age }}
                                                        &bull; {{ ucfirst($child->sex ?? 'N/A') }}
                                                    </small>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="fa-solid fa-child-reaching fs-3 d-block mb-1"></i>
                                        <small>
                                            @if($childSearch)
                                                No Family Member(s) found matching "{{ $childSearch }}"
                                            @else
                                                No Family Member(s) linked to your account
                                            @endif
                                        </small>
                                    </div>
                                @endif

                                @error('selectedChildId')
                                    <div class="text-danger small mt-2">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        @endif

                        {{-- ── Medicine Selection ── --}}
                        <div class="mb-3">
                            <label class="form-label">Medicine <span class="text-danger">*</span></label>
                            <select wire:model="selectedMedicineId" class="form-select @error('selectedMedicineId') is-invalid @enderror">
                                <option value="">Select medicine</option>
                                @foreach($medicines as $medicine)
                                    <option value="{{ $medicine->medicine_id }}">
                                        {{ $medicine->medicine_name }} - {{ $medicine->dosage }}
                                        (Available: {{ $medicine->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedMedicineId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ── Quantity ── --}}
                        <div class="mb-3">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input wire:model="quantity"
                                type="number"
                                class="form-control @error('quantity') is-invalid @enderror"
                                placeholder="Enter quantity" min="1" max="99999" step="1"
                                oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0, 5)"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ── Reason ── --}}
                        <div class="mb-3">
                            <label class="form-label">Reason for Request</label>
                            <textarea wire:model="reason"
                                    class="form-control @error('reason') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Describe the reason..."></textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <small>Please visit the health center to collect your medicine after your request is approved.</small>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" wire:click="resetForm">
                        Cancel
                    </button>
                    <button type="button" wire:click="submitRequest" class="btn btn-success" id="submitBtn">Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
        {{-- Edit Medicine Modal --}}
    <div class="modal fade" id="editMedicineModal" tabindex="-1" aria-labelledby="editMedicineLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="editMedicineLabel">Edit Request Medicine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>

                <div class="modal-body">
                    <form wire:submit.prevent="updateRequest">
                        @csrf
                        {{-- Medicine Selection --}}
                        <div class="mb-3">
                            <label class="form-label">Medicine <span class="text-danger">*</span></label>
                            <select wire:model="selectedMedicineId" class="form-select @error('selectedMedicineId') is-invalid @enderror">
                                <option value="">Select medicine</option>
                                @foreach($medicines as $medicine)
                                    <option value="{{ $medicine->medicine_id }}">
                                        {{ $medicine->medicine_name }} - {{ $medicine->dosage }}
                                        (Available: {{ $medicine->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedMedicineId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Quantity --}}
                        <div class="mb-3">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input wire:model="quantity"
                                   type="number"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   min="1"
                                   placeholder="Enter quantity">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Reason --}}
                        <div class="mb-3">
                            <label class="form-label">Reason for Request <span class="text-danger">*</span></label>
                            <textarea wire:model="reason"
                                      class="form-control @error('reason') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Describe the reason..."></textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <small>Please visit the health center to collect your medicine after your request is approved.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" wire:click="resetForm">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-success" wire:click="updateRequest" id="submitBtn">Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- View Details Modal - Add this before @push('scripts') --}}
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow">
                {{-- Modal Header --}}
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="viewDetailsLabel">
                        <i class="fa-solid fa-info-circle me-2"></i>Request Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body">
                    @if($viewRequest)
                        <div class="row g-3">
                            {{-- Request Information --}}
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-3 text-success">
                                    <i class="fa-solid fa-file-medical me-2"></i>Request Information
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small">Requested For</label>
                                <p class="fw-bold">                                @php
                                    $user    = auth()->user();
                                    $childIds = \App\Models\patients::where('guardian_user_id', $user->id)->pluck('id')->toArray();
                                @endphp
                                @if($request->patients_id && in_array($request->patients_id, $childIds))
                                    <span class="badge bg-primary">
                                        <i class="fa-solid fa-child me-1"></i>{{ $request->patients->full_name ?? 'Child' }}
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fa-solid fa-user me-1"></i>Myself
                                    </span>
                                @endif</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small">Status</label>
                                <p>
                                    @if($viewRequest->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($viewRequest->status === 'approved')
                                        <span class="badge bg-info">Approved</span>
                                    @elseif($viewRequest->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($viewRequest->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </p>
                            </div>

                            {{-- Medicine Details --}}
                            <div class="col-12 mt-4">
                                <h6 class="border-bottom pb-2 mb-3 text-success">
                                    <i class="fa-solid fa-pills me-2"></i>Medicine Details
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small">Medicine Name</label>
                                <p class="fw-bold">{{ $viewRequest->medicine->medicine_name ?? 'N/A' }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small">Dosage</label>
                                <p class="fw-bold">{{ $viewRequest->medicine->dosage ?? 'N/A' }}</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small">Quantity Requested</label>
                                <p class="fw-bold">{{ $viewRequest->quantity_requested }}</p>
                            </div>

                            @if($viewRequest->quantity_approved)
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Quantity Approved</label>
                                <p class="fw-bold text-success">{{ $viewRequest->quantity_approved }}</p>
                            </div>
                            @endif

                            {{-- Request Reason --}}
                            <div class="col-12 mt-3">
                                <label class="form-label text-muted small">Reason for Request</label>
                                <div class="p-3 bg-light rounded">
                                    <p class="mb-0">{{ $viewRequest->reason }}</p>
                                </div>
                            </div>

                            {{-- Admin Response --}}
                            @if($viewRequest->admin_notes)
                            <div class="col-12 mt-3">
                                <label class="form-label text-muted small">Admin Notes</label>
                                <div class="p-3 bg-light rounded border-start border-4 border-info">
                                    <p class="mb-0">{{ $viewRequest->admin_notes }}</p>
                                </div>
                            </div>
                            @endif

                            {{-- Timestamps --}}
                            <div class="col-12 mt-4">
                                <h6 class="border-bottom pb-2 mb-3 text-success">
                                    <i class="fa-solid fa-clock me-2"></i>Timeline
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted small">Date Requested</label>
                                <p>{{ $viewRequest->requested_at->format('M d, Y h:i A') }}</p>
                            </div>

                            @if($viewRequest->processed_at)
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Date Processed</label>
                                <p>{{ $viewRequest->processed_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif

                            @if($viewRequest->completed_at)
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Date Completed</label>
                                <p>{{ $viewRequest->completed_at->format('M d, Y h:i A') }}</p>
                            </div>
                            @endif

                            @if($viewRequest->status === 'approved' && !$viewRequest->completed_at)
                            <div class="col-12 mt-3">
                                <div class="alert alert-info mb-0">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    <small>Please visit the health center to collect your medicine.</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa-solid fa-exclamation-triangle fs-1 text-warning mb-3"></i>
                            <p class="text-muted">No details available</p>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Scripts --}}
    @push('scripts')
    <script>
        // Close modal after successful submission
        document.addEventListener('livewire:init', () => {
            Livewire.on('close-modal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('requestMedicineModal'));
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>
    @endpush

</div>

