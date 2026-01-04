<div class="container-fluid py-4">



    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="left-con d-flex align-items-center gap-2">
            <h3 class="mb-0">Patient Accounts</h3>
            <span class="badge bg-warning text-dark fs-6">
                {{ $unboundCount }} Unbound
            </span>
        </div>

        <div class="right-side-con">

            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                Add an Account
            </button>
        </div>

    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Search & Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Search by name, email, or username..."
                        wire:model.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="filterStatus">
                        <option value="all">All Accounts</option>
                        <option value="unbound">Unbound Only</option>
                        <option value="bound">Bound Only</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-secondary w-100" wire:click="$refresh">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>DOB</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $user->contact_number }}</td>
                        <td>
                            @if($user->patient_record_id)
                            <span class="badge bg-success">Bound</span>
                            @else
                            <span class="badge bg-warning text-dark">Unbound</span>
                            @endif
                        </td>
                        <td>
                            @if($user->patient_record_id)
                            <button
                                class="btn btn-sm btn-danger"
                                @click="
                                        Swal.fire({
                                            title: 'Are you sure?',
                                            text: 'You want to unbind this account?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#d33',
                                            cancelButtonColor: '#3085d6',
                                            confirmButtonText: 'Yes, unbind it!'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.unbind({{ $user->id }})
                                            }
                                        })
                                    ">
                                Unbind
                            </button>
                            @else
                            <button
                                class="btn btn-sm btn-success"
                                wire:click="openBindModal({{ $user->id }})">
                                Bind
                            </button>
                            @endif
                            <button type="button" class="btn btn-sm btn-info text-white edit-user-profile" data-bs-toggle="modal" data-bs-target="#edit-user-profile" data-id="{{$user->id}}">Edit</button>
                            <button type="button" class="btn btn-sm btn-danger text-white delete-user"  data-id="{{$user->id}}">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No patient accounts found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Bind Modal --}}
    @if($showModal)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bind Account to Patient Record</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">

                    {{-- User Info --}}
                    <div class="alert alert-info">
                        <h6 class="mb-2">Account Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong> {{ $selectedUser->full_name }}<br>
                                <strong>DOB:</strong> {{ $selectedUser->date_of_birth ? $selectedUser->date_of_birth->format('M d, Y') : 'N/A' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong> {{ $selectedUser->email }}<br>
                                <strong>Contact:</strong> {{ $selectedUser->contact_number }}
                            </div>
                        </div>
                    </div>

                    {{-- Search Records --}}
                    <label class="form-label">Search Patient Record</label>
                    <div class="input-group mb-3">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Search by name or patient ID..."
                            wire:model.defer="recordSearch">
                        <button class="btn btn-primary" wire:click="searchRecords">
                            Search
                        </button>
                    </div>

                    {{-- Records List --}}
                    @if(count($patientRecords) > 0)
                    <div class="list-group" style="max-height: 400px; overflow-y: auto;">
                        @foreach($patientRecords as $record)
                        <div
                            class="list-group-item list-group-item-action {{ $selectedRecordId == $record->id ? 'active' : '' }}"
                            wire:click="$set('selectedRecordId', {{ $record->id }})"
                            style="cursor: pointer;">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $record->full_name }}</h6>
                                    <small>
                                        Patient ID: <strong>{{ $record->id }}</strong><br>
                                        DOB: {{ $record->date_of_birth ? $record->date_of_birth->format('M d, Y') : 'N/A' }}<br>
                                        Contact: {{ $record->contact_number ?? 'N/A' }}
                                    </small>
                                </div>
                                @if($selectedRecordId == $record->id)
                                <span class="badge bg-success">Selected</span>
                                @endif
                            </div>

                            {{-- Match Indicators --}}
                            <div class="mt-2">
                                @if(strtolower($selectedUser->first_name) == strtolower($record->first_name) &&
                                strtolower($selectedUser->last_name) == strtolower($record->last_name))
                                <span class="badge bg-success">Name Match</span>
                                @endif
                                @if($selectedUser->date_of_birth && $record->date_of_birth &&
                                $selectedUser->date_of_birth->format('Y-m-d') == $record->date_of_birth->format('Y-m-d'))
                                <span class="badge bg-success">DOB Match</span>
                                @endif
                                @if($selectedUser->contact_number && $record->contact_number &&
                                preg_replace('/[^0-9]/', '', $selectedUser->contact_number) == preg_replace('/[^0-9]/', '', $record->contact_number))
                                <span class="badge bg-success">Contact Match</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-warning">
                        No patient records found. Try different search terms.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                    <button
                        class="btn btn-primary"
                        wire:click="bind"
                        @if(!$selectedRecordId) disabled @endif>
                        Bind Account
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif



    {{-- Loading --}}
    <div wire:loading class="position-fixed top-50 start-50">
        <div class="spinner-border text-primary"></div>
    </div>

</div>