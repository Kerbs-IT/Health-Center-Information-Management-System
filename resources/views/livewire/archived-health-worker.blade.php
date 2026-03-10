<div>
    <div class="alert alert-info mb-3">
        <i class="fa-solid fa-info-circle"></i>
        Showing archived health workers. Click the restore button to reactivate a staff member.
        <strong>Note:</strong> Staff can only be restored if their assigned area is not currently occupied.
    </div>

    <a href="{{ route('health.worker') }}" class="btn btn-primary px-4 fs-5 mb-3">
        <i class="fa-solid fa-arrow-left"></i> Back to Active
    </a>

    <div class="filters d-flex justify-content-lg-between justify-content-end flex-column flex-md-row flex-wrap gap-3 mb-2 mb-md-0">
        <div class="mb-md-3 mb-0 flex-fill xl:w-[25%]">
            <label>Show Entries</label>
            <select class="form-select" wire:model.live="entries">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="flex-fill xl:w-[25%]">
            <small>Search</small>
            <input type="text" class="form-control bg-light border-1 border-black" placeholder="Search name or contact..." wire:model.live.debounce.500ms="search">
        </div>
    </div>

    <div class="records table-responsive mt-4">
        <table class="table px-3 table-hover">
            <thead class="table-header">
                <th>No</th>
                <th>Name</th>
                <th class="text-center text-nowrap">Contact Info</th>
                <th class="text-center text-nowrap">Designated Area</th>
                <th class="text-center text-nowrap">Archived Date</th>
                <th class="text-center text-nowrap">Action</th>
            </thead>
            <tbody>
                @forelse($archivedWorkers as $index => $worker)
                <tr class="align-middle">
                    <td>{{ $archivedWorkers->firstItem() + $index }}</td>
                    <td>
                        <div class="d-flex gap-2 align-items-center">
                            <h5 class="text-nowrap">{{ $worker->staff->full_name }}</h5>
                        </div>
                    </td>
                    <td class="h-100">
                        <div class="d-flex align-items-center h-100 justify-content-center">
                            <p class="d-block mb-0">{{ $worker->staff->contact_number ?? 'none' }}</p>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center w-100 h-100 justify-content-center">
                            <p class="mb-0">{{ $worker->staff->assigned_area->brgy_unit ?? 'none' }}</p>
                        </div>
                    </td>
                    <td class="text-center text-nowrap">
                        {{ $worker->updated_at->format('M j, Y') }}
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <button
                                onclick="confirmRestore({{ $worker->id }})"
                                class="text-success fs-2 fw-bold border-0 bg-transparent"
                                title="Restore Health Worker">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center bg-light">No archived health workers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $archivedWorkers->links() }}
    </div>
</div>

<script>
    function confirmRestore(userId) {
        Swal.fire({
            title: 'Restore Health Worker?',
            text: "This staff member will be moved to active status.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, restore!'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.restoreHealthWorker(userId);
            }
        });
    }

    window.addEventListener('healthWorkerRestored', event => {
        Swal.fire({
            icon: 'success',
            title: 'Restored!',
            text: event.detail.message || 'Health worker has been restored successfully.',
            timer: 3000,
            showConfirmButton: true
        });
    });

    window.addEventListener('restorationError', event => {
        Swal.fire({
            icon: 'error',
            title: 'Cannot Restore Staff',
            html: `<p class="text-start">${event.detail.message}</p>`,
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK',
            width: '600px'
        });
    });
</script>