<div>
    <main class="container-fluid p-4">

        {{-- Filters --}}
        <h2 class="mb-4 fs-1 text-cen1ter">Medicine Logs</h2>
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
            {{-- inside your filters d-flex div, after the Action filter --}}
            <div>
                <label class="form-label">Date Range</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="fa-solid fa-calendar-days text-muted"></i>
                    </span>
                    <input type="text"
                        id="logsDateRangePicker"
                        class="form-control"
                        placeholder="All dates"
                        readonly
                        style="min-width:300px; cursor:pointer;">
                    {{-- Clear button — only visible once a range is selected --}}
                    @if($startDate || $endDate)
                        <button class="btn btn-danger"
                                style="z-index: 9997;"
                                type="button"
                                wire:click="clearLogsDateRange"
                                title="Clear date filter">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                </div>
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
                                @elseif($log->action === 'cancelled')
                                    <span class="badge bg-danger text-white">
                                        <i class="fa-solid fa-rotate-left"></i> Cancelled
                                    </span>
                                @elseif($log->action === 'rejected')
                                    <span class="badge bg-danger">
                                        <i class="fa-solid fa-times"></i> Rejected
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                @endif
                            </td>
                            {{-- Batches Used column --}}
                            <td class="text-center">
                                @php
                                    $batches         = is_array($log->batches_used) ? $log->batches_used : [];
                                    $reservedBatches = is_array($log->medicineRequest?->batches_snapshot)
                                                        ? $log->medicineRequest->batches_snapshot
                                                        : [];
                                @endphp

                                @if($log->action === 'approved' && count($reservedBatches) > 0)
                                    <button class="btn btn-sm btn-outline-success"
                                            type="button"
                                            tabindex="0"
                                            data-bs-toggle="popover"
                                            data-batches="{{ json_encode($reservedBatches) }}"
                                            data-popover-title="Batches Reserved (FIFO)"
                                            data-qty-label="reserved">
                                        <i class="fa-solid fa-lock"></i>
                                        {{ count($reservedBatches) }} Reserved
                                    </button>

                                @elseif($log->action === 'cancelled' && count($reservedBatches) > 0)
                                    <button class="btn btn-sm btn-outline-danger"
                                            type="button"
                                            tabindex="0"
                                            data-bs-toggle="popover"
                                            data-batches="{{ json_encode($reservedBatches) }}"
                                            data-popover-title="Batches Released (Cancelled)"
                                            data-qty-label="released">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        {{ count($reservedBatches) }} Released
                                    </button>

                                @elseif(in_array($log->action, ['approved', 'cancelled']))
                                    {{-- snapshot missing edge case --}}
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        <i class="fa-solid fa-circle-info"></i> No Snapshot
                                    </span>

                                @elseif($log->action === 'rejected')
                                    <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle p-2">
                                        <i class="fa-solid fa-ban"></i> Not Applicable
                                    </span>

                                @elseif(count($batches) > 0)
                                    <button class="btn btn-sm btn-outline-primary"
                                            type="button"
                                            tabindex="0"
                                            data-bs-toggle="popover"
                                            data-batches="{{ json_encode($batches) }}"
                                            data-popover-title="Batches Used (FIFO)"
                                            data-qty-label="taken">
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
        function initPopovers() {
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                const existing = bootstrap.Popover.getInstance(el);
                if (existing) existing.dispose();

                // Build content from the data-batches JSON attribute
                const batches = JSON.parse(el.dataset.batches || '[]');
                const content = batches.map(b => `
                    <div class="mb-1">
                        <small>
                            <strong>${b.batch_number ?? 'N/A'}</strong><br>
                            Expiry: ${b.expiry_date ?? 'N/A'}<br>
                            Qty taken: ${b.qty_taken ?? 0}
                        </small>
                    </div>
                `).join('');

                new bootstrap.Popover(el, {
                    html: true,
                    trigger: 'focus',
                    title: 'Batches Used',
                    content: content || '—',
                });
            });
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', initPopovers);

        // Livewire v3 re-render hook
        document.addEventListener('livewire:updated', initPopovers);
    </script>
    @endpush
</div>