<div class="container-fluid py-5">
    <div class="card">
        <!-- <div class="card-header">
            <h3 class="card-title">Patient List</h3>
        </div> -->
        <div class="card-body">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text"
                        wire:model.live.debounce.500ms="search"
                        class="form-control border-2"
                        placeholder="Search patients...">
                </div>
                <div class="col-md-6">
                    <select wire:model.live="statusFilter" class="form-select border-2 p-2">
                        <option value="all">All Patients</option>
                        <option value="active">Active Only</option>
                        <option value="archived">Archived Only</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>sex</th>
                            <th>Contact Number</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                        <tr>
                            <td>{{ $patient->id }}</td>
                            <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                            <td>{{ $patient->sex }}</td>
                            <td>{{ $patient->contact_number }}</td>
                            <td>
                                @if($patient->status === 'Active')
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Archived</span>
                                @endif
                            </td>
                            <td>
                                @if($patient->status === 'active')
                                <button
                                    onclick="confirmArchive({{ $patient->id }})"
                                    class="btn btn-sm btn-danger">
                                    <i class="fas fa-archive"></i> Archive
                                </button>
                                @else
                                <button
                                    onclick="confirmActivate({{ $patient->id }})"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-check-circle"></i> Activate
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No patients found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $patients->links() }}
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

    // Listen for Livewire events
    window.addEventListener('patientArchived', event => {
        Swal.fire({
            icon: 'success',
            title: 'Archived!',
            text: 'Patient has been archived successfully.',
            timer: 2000,
            showConfirmButton: false
        });
    });

    window.addEventListener('patientActivated', event => {
        Swal.fire({
            icon: 'success',
            title: 'Activated!',
            text: 'Patient has been activated successfully.',
            timer: 2000,
            showConfirmButton: false
        });
    });
</script>