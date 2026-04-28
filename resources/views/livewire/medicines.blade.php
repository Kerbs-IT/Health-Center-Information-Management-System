<div>
    <main class="d-flex flex-column container-fluid">
        <div class="m-md-3 m-1 p-md-3 p-2 shadow min-vh-100 bg-light">
            <h2 class="mb-5 fs-1 text-center">Medicine Inventory</h2>

            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="medicine-inventory d-flex gap-3 align-items-none align-items-sm-end flex-wrap flex-column flex-sm-row">
                <div class="flex-fill">
                    <label class="form-label">Show</label>
                    <select class="form-select w-[80%]" wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="75">75</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="flex-fill">
                    <label class="form-label">Search</label>
                    <input type="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search medicine or category...">
                </div>
                <div class="flex-fill">
                    <label class="form-label">Category Filter</label>
                    <select class="form-select" wire:model.live="categoryFilter">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-fill">
                    <label class="form-label">Age Range Filter</label>
                    <select class="form-select" wire:model.live="ageFilter">
                        <option value="">All Ages</option>
                        <option value="0-9months">0–9 months</option>
                        <option value="10-24months">10–24 months (1–2 years)</option>
                        <option value="2-5years">2–5 years</option>
                        <option value="6-12years">6–12 years</option>
                        <option value="13-17years">13–17 years</option>
                        <option value="adult">Adult (18+ years)</option>
                    </select>
                </div>
                <button class="btn btn-secondary" wire:click="toggleArchived">
                    <i class="fa-solid fa-{{ $showArchived ? 'list' : 'archive' }} pe-1"></i>
                    {{ $showArchived ? 'Show Active' : 'Show Archived' }}
                </button>
                {{-- existing PDF button --}}
                <a href="{{ route('medicines.download-pdf') }}" class="btn btn-danger" target="_blank">
                    <i class="fa-solid fa-file-pdf pe-1"></i>Download PDF
                </a>

                {{-- NEW CSV button --}}
                <a href="{{ route('medicines.download-csv') }}" target="_blank" class="btn btn-success" style="background-color: #217346; border-color: #217346;">
                    <i class="fa-solid fa-file-csv pe-1"></i>Download CSV
                </a>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                    <i class="fa-solid fa-plus pe-1"></i>Add Medicine
                </button>
            </div>

            @php
            function sortIcon($sortField, $currentField, $direction) {
                if ($sortField !== $currentField) return '<i class="bi bi-chevron-expand"></i>';
                return $direction === 'asc'
                    ? '<i class="fa-solid fa-chevron-up"></i>'
                    : '<i class="fa-solid fa-chevron-down"></i>';
            }

            function formatAgeRange($minMonths, $maxMonths) {
                if (is_null($minMonths) && is_null($maxMonths)) return 'All ages';
                $fmt = function($m) {
                    if ($m < 12) return $m . ' months';
                    $y  = floor($m / 12);
                    $mo = $m % 12;
                    $s  = $y . ' ' . ($y == 1 ? 'year' : 'years');
                    if ($mo > 0) $s .= ' ' . $mo . ' months';
                    return $s;
                };
                if (is_null($minMonths)) return 'Up to ' . $fmt($maxMonths);
                if (is_null($maxMonths)) return $fmt($minMonths) . '+';
                return $fmt($minMonths) . ' - ' . $fmt($maxMonths);
            }
            @endphp

            <div class="table-responsive mt-5">
                <table class="table table-hover">
                    <thead class="table-header">
                {{-- thead --}}
                <tr>
                    <th class="text-center" wire:click="sortBy('medicine_name')">
                        <button class="text-nowrap">Medicine Name {!! sortIcon($sortField, 'medicine_name', $sortDirection) !!}</button>
                    </th>
                    <th class="text-center" wire:click="sortBy('category_name')">
                        <button class="text-nowrap">Category {!! sortIcon($sortField, 'category_name', $sortDirection) !!}</button>
                    </th>
                    <th class="text-center" wire:click="sortBy('dosage')">
                        <button class="text-nowrap">Dosage {!! sortIcon($sortField, 'dosage', $sortDirection) !!}</button>
                    </th>
                    <th class="text-center text-nowrap">Age Range</th>
                    <th class="text-center" wire:click="sortBy('stock')">
                        <button class="text-nowrap">Total Stock {!! sortIcon($sortField, 'stock', $sortDirection) !!}</button>
                    </th>
                    <!-- <th class="text-center">Price</th> -->
                    <th class="text-center">Expiry Status</th>
                    <th class="text-center">Actions</th>
                </tr>
                    </thead>
                    <tbody>
                    @forelse($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine->medicine_name ?? '' }}</td>
                            <td>
                                @if($medicine->category)
                                    {{ $medicine->category->category_name }}
                                    @if($medicine->category->trashed())
                                        <span class="badge bg-secondary ms-1" title="Archived category">
                                            <i class="fa-solid fa-archive"></i> Archived
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">No Category</span>
                                @endif
                            </td>
                            <td>{{ $medicine->dosage ?? '' }}</td>
                            <td class="text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-primary bg-opacity-25 text-blue-800">
                                    {{ formatAgeRange($medicine->min_age_months, $medicine->max_age_months) }}
                                </span>
                            </td>
                            {{-- tbody row, after dosage <td> --}}
                            <td class="text-center">
                                @php $fifo = $medicine->fifo_batch; @endphp
                                {{ $medicine->stock }}
                                <!-- <small class="d-block text-muted">{{ $medicine->stock_status }}</small> -->
                            </td>

                            <td class="text-center">
                                @if($fifo)
                                    @php
                                        $statusClass = match($fifo->expiry_status) {
                                            'Valid'         => 'bg-success',
                                            'Expiring Soon' => 'bg-warning text-dark',
                                            'Expired'       => 'bg-danger',
                                            default         => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $fifo->expiry_status }}</span>
                                    <!-- <small class="d-block text-muted">{{ $fifo->expiry_date->format('M d, Y') }}</small> -->
                                @else
                                    <span class="badge bg-secondary">No Batches</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($showArchived)
                                        <button class="btn btn-info text-white" wire:click="restoreMedicine({{ $medicine->medicine_id }})">
                                            <i class="fa-solid fa-rotate-left fa-lg"></i>
                                        </button>
                                    @else
                                        {{-- Batches: navigate to dedicated batch management page --}}
                                        <a href="{{ route('medicines.batches', $medicine->medicine_id) }}"
                                           class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded text-decoration-none"
                                           title="Manage Batches">
                                            <i class="fas fa-boxes fs-4"></i>
                                        </a>
                                        {{-- Edit --}}
                                        <button class="btn bg-primary text-white" wire:click="editMedicineData({{ $medicine->medicine_id }})">
                                            <i class="fa-solid fa-pen-to-square fa-lg"></i>
                                        </button>
                                        {{-- Archive --}}
                                        <button class="btn p-0" wire:click="confirmMedicineArchive({{ $medicine->medicine_id }})">
                                            <i class="fa-solid fa-trash text-danger fs-3"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fa-solid fa-inbox fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted">No medicine found</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $medicines->links() }}
        </div>
    </main>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- ADD MEDICINE MODAL                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div wire:ignore.self class="modal fade" id="addMedicineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-capsules me-2"></i>Add New Medicine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="storeMedicineData">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="medicine_name">
                                @error('medicine_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Dosage <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="dosage" placeholder="e.g., 500mg, 10ml">
                                @error('dosage') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" wire:model="stock" min="1" max="99999" step="1"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,5)"
                                       onkeypress="return event.charCode>=48&&event.charCode<=57">
                                @error('stock') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" min="{{ now()->toDateString() }}" wire:model="expiry_date">
                                @error('expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        {{-- Batch Info --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Batch Number</label>
                                <input type="text" class="form-control" wire:model="batch_number"
                                    placeholder="e.g., LOT-2025-001 (auto-generated if blank)">
                                @error('batch_number') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manufactured Date</label>
                                <input type="date" class="form-control" wire:model="manufactured_date"
                                    max="{{ now()->toDateString() }}">
                                @error('manufactured_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        {{-- Age Range --}}
                        <div class="border-top pt-3 mt-2">
                            <h6 class="mb-3"><i class="fa-solid fa-user-group me-2"></i>Age Range (Optional)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimum Age</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" wire:model.live="min_age_value" min="0" placeholder="0">
                                        <select class="form-select" style="max-width:120px" wire:model.live="min_age_unit">
                                            <option value="years">Years</option>
                                            <option value="months">Months</option>
                                        </select>
                                    </div>
                                    <small class="text-muted">Leave empty if no minimum age</small>
                                    @error('min_age_value') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Maximum Age</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" wire:model.live="max_age_value" min="0" placeholder="18">
                                        <select class="form-select" style="max-width:120px" wire:model.live="max_age_unit">
                                            <option value="years">Years</option>
                                            <option value="months">Months</option>
                                        </select>
                                    </div>
                                    <small class="text-muted">Leave empty for no upper limit</small>
                                    @error('max_age_value') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="alert alert-info py-2">
                                <small><strong>Quick Guide:</strong> Use months for infants/toddlers (0–24 months), years for older children and adults (2+ years)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- EDIT MEDICINE MODAL                                         --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div wire:ignore.self class="modal fade" id="editMedicineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-capsules me-2"></i>Edit Medicine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="updateMedicineData">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Medicine Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="medicine_name">
                                @error('medicine_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Dosage <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="dosage">
                                @error('dosage') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" wire:model="stock">
                                @error('stock') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" min="{{ now()->toDateString() }}" wire:model="expiry_date">
                                @error('expiry_date') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="border-top pt-3 mt-2">
                            <h6 class="mb-3"><i class="fa-solid fa-user-group me-2"></i>Age Range (Optional)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimum Age</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" wire:model.live="min_age_value" min="0">
                                        <select class="form-select" style="max-width:120px" wire:model.live="min_age_unit">
                                            <option value="months">Months</option>
                                            <option value="years">Years</option>
                                        </select>
                                    </div>
                                    @error('min_age_value') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Maximum Age</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" wire:model.live="max_age_value" min="0">
                                        <select class="form-select" style="max-width:120px" wire:model.live="max_age_unit">
                                            <option value="months">Months</option>
                                            <option value="years">Years</option>
                                        </select>
                                    </div>
                                    @error('max_age_value') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-editMedicine-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('editMedicineModal'));
                modal.show();
            });
            Livewire.on('close-editMedicine-modal', () => {
                const el = document.getElementById('editMedicineModal');
                const modal = bootstrap.Modal.getInstance(el);
                if (modal) modal.hide();
            });
        });
    </script>
    @endpush
</div>