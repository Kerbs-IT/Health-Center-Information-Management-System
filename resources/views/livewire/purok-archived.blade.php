<div>
    {{-- ─── Header ──────────────────────────────────────────────────────── --}}
    <div class="header-text d-flex justify-content-start align-items-center">
        <div class="end-button">
            <a href="{{ route('manage.puroks') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Puroks
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
                            onclick="confirmRestore({{ $purok->id }})">
                            <i class="fa-solid fa-rotate-left"></i> Restore
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No archived puroks found.</td>
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
    {{-- SCRIPTS                                                             --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <script>
        function confirmRestore(purokId) {
            Swal.fire({
                title: 'Restore Purok?',
                text: "This purok will be moved back to active status.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.restorePurok(purokId);
                }
            });
        }

        window.addEventListener('purokRestored', () => {
            Swal.fire({
                icon: 'success',
                title: 'Restored!',
                text: 'Purok has been restored successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        });

        
    </script>
</div>