<div class="container-fluid py-4">



    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div class="left-con d-flex align-items-center gap-2">
            <h3 class="mb-0">Patient Accounts</h3>
            <span class="badge bg-warning text-dark fs-6">
                {{ $unboundCount }} Unbound
            </span>
        </div>

        <div class="right-side-con ms-auto mt-ms-0 mt-2">

            <button type="button" class="btn btn-success text-nowrap" data-bs-toggle="modal" id="add-patient-account-modal-btn" data-bs-target="#addModal">
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
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="all">All Accounts</option>
                        <option value="active">Active</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success w-100" wire:click="$refresh">
                        <i class="bi bi-arrow-clockwise"></i> Search
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

                        <th>Name</th>
                        <th>Type of Patient</th>
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

                        <td>{{ $user->full_name }}</td>
                        <td>{{$user->patient_type??'none'}}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $user->contact_number }}</td>
                        <td>
                            @if($user->status == 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger text-white">Archived</span>
                            @endif
                        </td>
                        <td>

                            <button type="button" class="btn btn-sm btn-info text-white edit-user-profile" data-bs-toggle="modal" data-bs-target="#edit-user-profile" data-id="{{$user->id}}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger text-white delete-user" data-id="{{$user->id}}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
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







    {{-- Loading --}}
    <div wire:loading class="position-fixed top-50 start-50">
        <div class="spinner-border text-primary"></div>
    </div>

</div>