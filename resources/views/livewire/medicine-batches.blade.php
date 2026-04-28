<div>
    <main class="d-flex flex-column container-fluid bg-light">
        <div class="m-md-3 m-1 p-md-3 p-2 shadow min-vh-100">

            {{-- ── Header ─────────────────────────────────────────── --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('medicines') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
                <div>
                    <h2 class="mb-0 fs-2">Batch Management</h2>
                    <p class="text-muted mb-0 fs-5">
                        <span class="fw-semibold text-dark">{{ $medicine->medicine_name }}</span>
                        — {{ $medicine->dosage }}
                        <span class="badge bg-secondary ms-2">{{ $medicine->category->category_name ?? 'No Category' }}</span>
                    </p>
                </div>
            </div>

            {{-- ── Flash ──────────────────────────────────────────── --}}
            @if (session()->has('batch_success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('batch_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ── Medicine summary cards ──────────────────────────── --}}
            <div class="row g-3 mb-4 row-cols-1 row-cols-sm-3">
                <div class="col">
                    <div class="card text-center bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-purple-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                        <div class="card-body py-3">
                            <div class="fs-5 fw-bold">{{ $medicine->stock }}</div>
                            <div class="text-muted small">Total Stock</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-purple-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                        <div class="card-body py-3">
                            <div class="fs-5 fw-bold">
                                {{ $medicine->stock_status }}
                            </div>
                            <div class="text-muted small">Stock Status</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-purple-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                        <div class="card-body py-3">
                            <div class="fs-5 fw-bold
                                @if($medicine->expiry_status === 'Valid') text-success
                                @elseif($medicine->expiry_status === 'Expiring Soon') text-warning
                                @else text-danger @endif">
                                {{ $medicine->expiry_status }}
                            </div>
                            <div class="text-muted small">Expiry Status</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Add Batch Form ──────────────────────────────────── --}}
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white fw-semibold">
                    <i class="fa-solid fa-plus me-1"></i> Add New Batch (Restock)
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Batch Number</label>
                            <input type="text" class="form-control" wire:model="newBatchNumber"
                                   placeholder="e.g., LOT-2025-001">
                            @error('newBatchNumber') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" wire:model="newBatchQty"
                                   min="1" placeholder="0">
                            @error('newBatchQty') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Manufactured Date</label>
                            <input type="date" class="form-control" wire:model="newBatchManufactured">
                            @error('newBatchManufactured') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model="newBatchExpiry"
                                   min="{{ now()->addDay()->toDateString() }}">
                            @error('newBatchExpiry') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" wire:click="addBatch">
                            <i class="fa-solid fa-plus me-1"></i>Add Batch
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Batch Table ─────────────────────────────────────── --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="fa-solid fa-arrow-down-short-wide me-1 text-warning"></i>
                    FIFO Queue — oldest expiry consumed first
                </h5>
                <button class="btn btn-sm btn-outline-secondary" wire:click="toggleArchived">
                    <i class="fa-solid fa-{{ $showArchived ? 'list' : 'archive' }} me-1"></i>
                    {{ $showArchived ? 'Show Active Batches' : 'Show Archived Batches' }}
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-warning">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Batch Number</th>
                            <th class="text-center">Manufactured</th>
                            <th class="text-center">Expiry Date</th>
                            <!-- <th class="text-center">Price / Unit</th> -->
                            <!-- <th class="text-center">Total Cost</th> -->
                            <th class="text-center">Remaining</th>
                            <th class="text-center">Initial Qty</th>

                            <th class="text-center">Expiry Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $index => $batch)
                        <tr class="{{ $batch->quantity == 0 ? 'text-muted' : '' }}">
                            <td class="text-center">
                                @if($index === 0 && $batch->quantity > 0 && !$showArchived)
                                    <span class="badge bg-warning text-dark" title="Next to be consumed">NEXT</span>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td>{{ $batch->batch_number ?? '—' }}</td>
                            <td class="text-center">
                                {{ $batch->manufactured_date
                                    ? $batch->manufactured_date->format('M d, Y')
                                    : '—' }}
                            </td>
                            <td class="text-center">{{ $batch->expiry_date->format('M d, Y') }}</td>
                            <td class="text-center">
                                <span>
                                    {{ $batch->quantity }}
                                </span>
                            </td>
                            <td class="text-center">{{ $batch->initial_quantity }}</td>
                            <td class="text-center">
                                @php
                                    $statusClass = match($batch->expiry_status) {
                                        'Valid'         => 'bg-success',
                                        'Expiring Soon' => 'bg-warning text-dark',
                                        'Expired'       => 'bg-danger',
                                        default         => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $batch->expiry_status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($showArchived)
                                        <button class="btn btn-sm btn-info text-white"
                                                wire:click="restoreBatch({{ $batch->id }})"
                                                title="Restore">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-primary"
                                                wire:click="editBatch({{ $batch->id }})"
                                                title="Edit Batch">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                                wire:click="confirmArchiveBatch({{ $batch->id }})"
                                                title="Archive Batch">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fs-1 d-block mb-2"></i>
                                {{ $showArchived ? 'No archived batches.' : 'No batches yet. Add the first batch above.' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- EDIT BATCH MODAL                                            --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div wire:ignore.self class="modal fade" id="editBatchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Batch</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="cancelEdit"></button>
                </div>
                <form wire:submit.prevent="updateBatch">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Batch Number</label>
                                <input type="text" class="form-control" wire:model="editBatchNumber">
                                @error('editBatchNumber') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" wire:model="editBatchQty" min="0">
                                @error('editBatchQty') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price / Unit (₱) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" wire:model="editBatchPrice" min="0" step="0.01">
                                </div>
                                @error('editBatchPrice') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Manufactured Date</label>
                                <input type="date" class="form-control" wire:model="editBatchManufactured">
                                @error('editBatchManufactured') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="editBatchExpiry">
                                @error('editBatchExpiry') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Batch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- ARCHIVE CONFIRMATION MODAL                                  --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div wire:ignore.self class="modal fade" id="archiveBatchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation me-2"></i>Archive Batch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to archive this batch? Its remaining stock will be deducted from the medicine total.</p>
                    <p class="text-muted small">You can restore it later from the <strong>Archived Batches</strong> view.</p>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger"
                            wire:click="archiveBatch"
                            data-bs-dismiss="modal">
                        <i class="fa-solid fa-archive me-1"></i>Archive
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-edit-batch-modal', () => {
                new bootstrap.Modal(document.getElementById('editBatchModal')).show();
            });
            Livewire.on('close-edit-batch-modal', () => {
                const el  = document.getElementById('editBatchModal');
                const mod = bootstrap.Modal.getInstance(el);
                if (mod) mod.hide();
            });
            Livewire.on('show-archive-batch-confirmation', () => {
                new bootstrap.Modal(document.getElementById('archiveBatchModal')).show();
            });
        });
    </script>
    @endpush
</div>