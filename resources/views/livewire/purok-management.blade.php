<div>
    {{-- ─── Header Buttons ─────────────────────────────────────────────── --}}
    <div class="header-text d-flex justify-content-between align-items-center">
        <a href="{{ route('health.worker') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to Health workers
        </a>
        <div class="end-button d-flex flex-column flex-md-row gap-2">
            <button type="button" class="btn btn-success text-nowrap"
                wire:click="openAdd">
                Add Purok
            </button>
            <a href="{{ route('puroks.archived') }}" class="btn btn-danger">
                <i class="fa-solid fa-box-archive"></i> View Archived
            </a>
        </div>
    </div>

    {{-- ─── Table ───────────────────────────────────────────────────────── --}}
    <div class="records table-responsive mt-4 w-100">
        <table class="table table-bordered w-100">
            <thead class="table-header">
                <tr>
                    <th>#</th>
                    <th>Purok Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $purok)
                <tr>
                    <td>{{ $data->firstItem() + $loop->index }}</td>
                    <td>{{ $purok->brgy_unit }}</td>
                    <td>
                        <button class="btn btn-success btn-sm"
                            wire:click="openEdit({{ $purok->id }})">
                            Edit
                        </button>
                        <button class="btn btn-danger btn-sm"
                            onclick="confirmArchive({{ $purok->id }})">
                            Archive
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No puroks found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-end mt-3 flex-wrap gap-2">
            <small class="text-muted">
                Page {{ $data->currentPage() }} of {{ $data->lastPage() }}
            </small>
            {{ $data->links() }}
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ADD MODAL                                                           --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="addPurokModal" tabindex="-1" aria-labelledby="addPurokModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addPurokModalLabel">Add Purok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addName" class="form-label fw-semibold">
                            Purok Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            id="addName"
                            class="form-control @error('addName') is-invalid @enderror"
                            wire:model.live="addName"
                            placeholder="Enter purok name">
                        @error('addName')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-success"
                        wire:click="saveAdd"
                        wire:loading.attr="disabled">
                        <span wire:loading wire:target="saveAdd"
                            class="spinner-border spinner-border-sm me-1"></span>
                        Save
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- EDIT MODAL                                                          --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="editPurokModal" tabindex="-1" aria-labelledby="editPurokModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="editPurokModalLabel">Edit Purok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label fw-semibold">
                            Purok Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            id="editName"
                            class="form-control @error('editName') is-invalid @enderror"
                            wire:model.live="editName"
                            placeholder="Enter purok name">
                        @error('editName')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-success"
                        wire:click="saveEdit"
                        wire:loading.attr="disabled">
                        <span wire:loading wire:target="saveEdit"
                            class="spinner-border spinner-border-sm me-1"></span>
                        Save Changes
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- SCRIPTS                                                             --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <script>
        function confirmArchive(purokId) {
            Swal.fire({
                title: 'Archive Purok?',
                text: "This purok will be moved to archived status.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.archivePurok(purokId);
                }
            });
        }

        window.addEventListener('openAddModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('addPurokModal'));
            modal.show();
        });

        window.addEventListener('closeAddModal', () => {
            const modalEl = document.getElementById('addPurokModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });

        window.addEventListener('purokInUse', () => {
            Swal.fire({
                icon: 'error',
                title: 'Cannot Archive!',
                text: 'This purok still has assigned health worker. Please reassign them first before archiving.',
            });
        });

        window.addEventListener('purokAdded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Added!',
                text: 'Purok has been added successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        });

        window.addEventListener('openEditModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('editPurokModal'));
            modal.show();
        });

        window.addEventListener('closeEditModal', () => {
            const modalEl = document.getElementById('editPurokModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        });

        window.addEventListener('purokUpdated', () => {
            Swal.fire({
                icon: 'success',
                title: 'Updated!',
                text: 'Purok has been updated successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        });

        window.addEventListener('purokArchived', () => {
            Swal.fire({
                icon: 'success',
                title: 'Archived!',
                text: 'Purok has been archived successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
</div>