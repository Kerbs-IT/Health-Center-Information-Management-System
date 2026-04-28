<div>
    <main class="container-fluid p-4">

        {{-- Filters --}}
        <h2 class="mb-4 fs-1 text-cen1ter">Medicine Request Logs</h2>
        <div class="d-flex gap-3 flex-wrap mb-4">
            <div>
                <label class="form-label">Show</label>
                <select wire:model.live="perPage" class="form-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="flex-fill">
                <label class="form-label">Search</label>
                <input type="search" wire:model.live.debounce.300ms="search" class="form-control"
                       placeholder="Search patient, medicine, or performer...">
            </div>
            <div>
                <label class="form-label">Action</label>
                <select wire:model.live="filterAction" class="form-select">
                    <option value="">All Actions</option>
                    <option value="approved">Approved</option>
                    <option value="dispensed">Dispensed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>

        {{-- Logs Table --}}
        <div class="table-responsive shadow bg-white p-3">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Patient</th>
                        <th class="text-center">Medicine</th>
                        <th class="text-center">Dosage</th>
                        <th class="text-center">Qty</th>
                        <!-- <th class="text-center">Unit Price</th>
                        <th class="text-center">Total Price</th> -->
                        <th class="text-center">Action</th>
                        <th class="text-center">Batches Used</th>
                        <th class="text-center">Performed By</th>
                        <th class="text-center">Performed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-center">{{ $log->patient_name }}</td>
                            <td class="text-center"><strong>{{ $log->medicine_name }}</strong></td>
                            <td class="text-center">{{ $log->dosage ?? '—' }}</td>
                            <td class="text-center">{{ $log->quantity }}</td>
                            <td class="text-center">
                                @if($log->action === 'dispensed')
                                    <span class="badge bg-primary">
                                        <i class="fa-solid fa-hand-holding-medical"></i> Dispensed
                                    </span>
                                @elseif($log->action === 'approved')
                                    <span class="badge bg-success">
                                        <i class="fa-solid fa-check"></i> Approved
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fa-solid fa-times"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            {{-- Batches Used column --}}
                            <td class="text-center">
                                @php $batches = is_array($log->batches_used) ? $log->batches_used : []; @endphp
                                @if(count($batches) > 0)
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                            data-bs-toggle="popover"
                                            data-bs-trigger="focus"
                                            data-bs-html="true"
                                            title="Batches Used (FIFO)"
                                            data-bs-content="
                                                @foreach($batches as $b)
                                                    <div class='mb-1'>
                                                        <small>
                                                            <strong>{{ $b['batch_number'] ?? 'N/A' }}</strong><br>
                                                            Expiry: {{ $b['expiry_date'] ?? 'N/A' }}<br>
                                                            Qty taken: {{ $b['qty_taken'] ?? 0 }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            ">
                                        <i class="fa-solid fa-layer-group"></i>
                                        {{ count($batches) }} batch(es)
                                    </button>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $log->performed_by_name }}</td>
                            <td class="text-center">
                                {{ $log->performed_at->format('F d, Y') }}<br>
                                <small class="text-muted">{{ $log->performed_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fa-solid fa-clipboard-list fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">No medicine logs found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $logs->links() }}</div>
        </div>
    </main>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Bootstrap popovers for batch detail buttons
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                new bootstrap.Popover(el);
            });
        });
        // Re-init popovers after Livewire re-renders
        document.addEventListener('livewire:update', () => {
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                const existing = bootstrap.Popover.getInstance(el);
                if (existing) existing.dispose();
                new bootstrap.Popover(el);
            });
        });
    </script>
    @endpush
</div>