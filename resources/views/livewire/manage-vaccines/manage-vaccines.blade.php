<div>
    {{-- ============================================================
         Header Row: Title + Add Button
    ============================================================ --}}

    {{-- ============================================================
         Filters Row
    ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-3 flex-wrap w-100">
        <div class="items d-flex align-items-center gap-3 flex-wrap w-50">


            {{-- Search --}}
            <div class="input-group border-1 rounded" style="max-width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fa-solid fa-search text-muted small"></i>
                </span>
                <input
                    type="text"
                    class="form-control border-start-0 ps-0 "
                    placeholder="Search vaccine or acronym..."
                    wire:model.live.debounce.300ms="search">
            </div>

            {{-- Status Filter --}}
            <select class="form-select border-1" style="max-width: 160px;" wire:model.live="filterStatus">
                <option value="Active">Active</option>
                <option value="Archived">Archived</option>
                <option value="all">All</option>
            </select>
        </div>
        <div class="items">
            <button
                class="btn text-white d-flex align-items-center gap-2 px-3"
                style="background-color: #0b8433;"
                onclick="openAddModal()">
                <i class="fa-solid fa-plus"></i>
                <span>Add Vaccine</span>
            </button>
        </div>
    </div>

    {{-- ============================================================
         Table
    ============================================================ --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-header">
                    <tr>
                        <th class="text-white fw-semibold py-3 ps-4" style="width: 40px;">#</th>
                        <th class="text-white fw-semibold py-3">Vaccine Name</th>
                        <th class="text-white fw-semibold py-3">Acronym</th>
                        <th class="text-white fw-semibold py-3 text-center">Max Doses</th>
                        <th class="text-white fw-semibold py-3 text-center">Status</th>
                        <th class="text-white fw-semibold py-3 text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vaccines as $index => $vaccine)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $vaccines->firstItem() + $index }}</td>
                        <td class="fw-medium">{{ $vaccine->type_of_vaccine }}</td>
                        <td>
                            <span class="badge rounded-pill px-3 py-1 fw-semibold"
                                style="background-color: #e8f5e9; color: #0b8433; font-size: 0.78rem;">
                                {{ $vaccine->vaccine_acronym }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-semibold text-dark">{{ $vaccine->max_doses }}</span>
                            <span class="text-muted small"> dose{{ $vaccine->max_doses > 1 ? 's' : '' }}</span>
                        </td>
                        <td class="text-center">
                            @if($vaccine->status === 'Active')
                            <span class="badge rounded-pill px-3 py-1"
                                style="background-color: #e8f5e9; color: #0b8433;">
                                <i class="fa-solid fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>
                                Active
                            </span>
                            @else
                            <span class="badge rounded-pill px-3 py-1 bg-danger-subtle text-danger">
                                <i class="fa-solid fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i>
                                Archived
                            </span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Edit --}}
                                <button
                                    class="btn btn-sm btn-outline-success px-2 py-1"
                                    title="Edit"
                                    onclick="openEditModal({{ $vaccine->id }}, '{{ addslashes($vaccine->type_of_vaccine) }}', '{{ addslashes($vaccine->vaccine_acronym) }}', {{ $vaccine->max_doses }})">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                {{-- Archive / Restore --}}
                                @if($vaccine->status === 'Active')
                                <button
                                    class="btn btn-sm btn-outline-danger px-2 py-1"
                                    title="Archive"
                                    onclick="confirmArchive({{ $vaccine->id }}, '{{ addslashes($vaccine->type_of_vaccine) }}')">
                                    <i class="fa-solid fa-box-archive"></i>
                                </button>
                                @else
                                <button
                                    class="btn btn-sm px-2 py-1"
                                    title="Restore"
                                    style="color: #0b8433; border-color: #0b8433;"
                                    onclick="confirmRestore({{ $vaccine->id }}, '{{ addslashes($vaccine->type_of_vaccine) }}')">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-syringe fa-2x mb-2 d-block opacity-25"></i>
                            No vaccines found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($vaccines->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                Showing {{ $vaccines->firstItem() }}–{{ $vaccines->lastItem() }} of {{ $vaccines->total() }} vaccines
            </span>
            {{ $vaccines->links() }}
        </div>
        @endif
    </div>
</div>

