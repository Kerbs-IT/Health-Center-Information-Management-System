<div>
    <main class="container-fluid bg-light p-4">
        <h2 class="mb-4 fs-1 text-center">Medicine Request Logs</h2>

        {{-- Filters --}}
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
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    class="form-control"
                    placeholder="Search patient, medicine, or performer..."
                >
            </div>
        </div>

        {{-- Logs Table --}}
        <div class="table-responsive shadow bg-white p-3">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Patient</th>
                        <th class="text-center">Medicine</th>
                        <th class="text-center">Dosage</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Action</th>
                        <th class="text-center">Performed By</th>
                        <th class="text-center">Performed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $index => $log)
                        <tr>
                            <td class="text-center">
                                {{ $logs->firstItem() + $index }}
                            </td>
                            <td class="text-center">
                                {{ $log->patient_name }}
                            </td>
                            <td class="text-center">
                                <strong>{{ $log->medicine_name }}</strong>
                            </td>
                            <td class="text-center">
                                {{ $log->dosage ?? '—' }}
                            </td>
                            <td class="text-center">
                                {{ $log->quantity }}
                            </td>
                            <td class="text-center">
                                @if($log->action === 'approved')
                                    <span class="badge bg-success">
                                        <i class="fa-solid fa-check"></i> Approved
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fa-solid fa-times"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $log->performed_by_name }}
                            </td>
                            <td class="text-center">
                                {{ $log->performed_at->format('F d, Y') }} <br>
                                <small class="text-muted">
                                    {{ $log->performed_at->format('h:i A') }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fa-solid fa-clipboard-list fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">No medicine logs found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </main>
</div>