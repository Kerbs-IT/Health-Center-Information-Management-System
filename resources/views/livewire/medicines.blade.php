<div>
    <main class="d-flex flex-column container-fluid bg-light ">
        <div class="m-3 p-3 shadow min-vh-100">
            <h2 class="mb-5 fs-1 text-center">Manage Medicine</h2>
            <div class="medicine-inventory d-flex gap-3 align-items-none align-items-sm-end flex-wrap flex-column flex-sm-row">
                <div class="flex-fill">
                    <label for="" class="form-label">Show</label>
                    <select type="text" class="form-select w-50" name="show" wire:model.live="perPage">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="75">75</option>
                        <option value="100">100</option>

                    </select>
                </div>
                <div class="flex-fill">
                    <label for="search" class="form-label">Search</label>
                    <input type="search" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Search ....">
                </div>
                <div class="flex-fill">
                    <label for="" class="form-label">Filter</label>
                    <select name="" class="form-select" id="" wire:model.live="filterCategory">
                        <option value="">--Filter by category--</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMedicineModal"><i class="fa-solid fa-plus pe-1"></i>Add Medicine</button>
            </div>
            @php
            function sortIcon($sortField, $currentField, $direction)
            {
                if ($sortField !== $currentField) {
                    return '
                               <i class="bi bi-chevron-expand"></i>
                            ';
                }

                if ($direction === 'asc') {
                    return '<i class="fa-solid fa-chevron-up"></i>';
                }

                if ($direction === 'desc') {
                    return '<i class="fa-solid fa-chevron-down"></i>';
                }

                return '';
            }
            @endphp


            <div class="table-responsive mt-5">
                <table class="table table-hover" id="medicineTable">
                    <thead class="table-header">
                        <tr>
                            <th class="text-center" scope="col"><button wire:click="sortBy('medicine_id')" class="sort-btn">No. {!! sortIcon($sortField, 'medicine_id', $sortDirection) !!}</button></th>
                            <th class="text-center" scope="col"><button wire:click="sortBy('medicine_name')">Medicine Name {!! sortIcon($sortField, 'medicine_name', $sortDirection) !!}</button></th>
                            <th class="text-center" scope="col"><button wire:click="sortBy('category_name')">Category {!! sortIcon($sortField, 'category_name', $sortDirection) !!}</button></th>
                            <th class="text-center" scope="col"><button wire:click="sortBy('dosage')">Dosage {!! sortIcon($sortField, 'dosage', $sortDirection) !!}</button></th>
                            <th class="text-center" scope="col"><button wire:click="sortBy('stock')">Stock {!! sortIcon($sortField, 'stock', $sortDirection) !!}</button></th>
                            <th class="text-center" scope="col"><button wire:click="sortBy('status')">Status {!! sortIcon($sortField, 'status', $sortDirection) !!}</button></th>
                            <th class="text-center" scope="col"><button wire:click="sortBy('expiry_date')">Expiry Date {!! sortIcon($sortField, 'expiry_date', $sortDirection) !!}</button></th>
                            <th class="text-center" scope="col"><button wire:click="sortBy('created_at')">Date {!! sortIcon($sortField, 'created_at', $sortDirection) !!}</button></th>
                            <th class="text-center" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine->medicine_id }}</td>
                            <td>{{ $medicine->medicine_name }}</td>
                            <td>{{ $medicine->category->category_name }}</td>
                            <td>{{ $medicine->dosage }}</td>
                            <td>{{ $medicine->stock }}</td>
                            <td>
                                <span
                                    class="
                                        px-3 py-1 rounded-full text-xs font-semibold
                                    @if ($medicine->status === 'In Stock') bg-green-100 text-green-700
                                        @elseif ($medicine->status === 'Low Stock') bg-yellow-100 text-yellow-700
                                        @elseif ($medicine->status === 'Out of Stock') bg-red-100 text-red-700
                                        @elseif ($medicine->status === 'Expiring Soon') bg-orange-100 text-orange-700
                                        @endif
                                    ">
                                    {{ $medicine->status }}
                                </span>
                            </td>
                            <td>{{ $medicine->expiry_date }}</td>
                            <td>{{ $medicine->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn bg-primary text-white" wire:click="editMedicineData({{ $medicine->medicine_id }})"><i class="fa-solid fa-pen-to-square me-1"></i>Edit</button>
                                    <button class="btn p-0"><i class="fa-solid fa-trash text-danger fs-3"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        <!-- Dynamic td -->
                    </tbody>
                </table>
                {{ $medicines->links() }}
            </div>
        </div>
    </main>
        <!-- Add Medicine Modal -->
    <div wire:ignore.self class="modal fade" id="addMedicineModal" tabindex="-1" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="addMedicineModalLabel">
                <i class="fa-solid fa-capsules me-2"></i> Add New Medicine
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" id="addMedicineForm" method="POST" wire:submit.prevent="storeMedicineData">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Medicine Name</label>
                        <input type="text" class="form-control" name="medicine_name" wire:model="medicine_name">
                        @error('medicine_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" id="category_id" wire:model="category_id">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                            @endforeach

                        </select>
                        @error('category_id')
                             <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosage</label>
                        <input type="text" class="form-control" name="dosage" wire:model="dosage">
                        @error('dosage')
                             <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" wire:model="stock">
                        @error('stock')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" name="expiry_date" wire:model="expiry_date">
                        @error('expiry_date')
                          <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit"  class="btn btn-success">Save Medicine</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Edit Medicine Modal -->
    <div wire:ignore.self class="modal fade" id="editMedicineModal" tabindex="-1" aria-labelledby="EditMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="EditMedicineModalLabel">
                <i class="fa-solid fa-capsules me-2"></i> Add New Medicine
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" id="addMedicineForm" method="POST" wire:submit.prevent="updateMedicineData">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Medicine Name</label>
                        <input type="text" class="form-control" name="medicine_name" wire:model="medicine_name">
                        @error('medicine_name')
                          <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" id="category_id" wire:model="category_id">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                          <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosage</label>
                        <input type="text" class="form-control" name="dosage" wire:model="dosage">
                        @error('dosage')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="stock" wire:model="stock">
                        @error('stock')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" name="expiry_date" wire:model="expiry_date">
                        @error('expiry_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit"  class="btn btn-success">Save Medicine</button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>
