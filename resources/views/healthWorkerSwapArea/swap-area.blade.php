<div class="modal fade" id="swapAreaModal" tabindex="-1" aria-labelledby="swapAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="swapAreaModalLabel">Manage Assigned Areas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <input type="hidden" id="manage_worker_id">

                <!-- Health Worker Info -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Health Worker</label>
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                        <img id="manage_worker_image" src="" alt="Worker"
                            class="rounded-circle" style="width:50px;height:50px;object-fit:cover;">
                        <div>
                            <h6 class="mb-0" id="manage_worker_name">-</h6>
                            <small class="text-muted">Health Worker</small>
                        </div>
                    </div>
                </div>

                <!-- Current Assigned Areas -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Current Assigned Areas</label>
                    <div id="current_areas_list" class="d-flex flex-wrap gap-2 p-2 border rounded min-height-50">
                        <!-- Populated by JS -->
                        <span class="text-muted small" id="no_areas_msg">No areas assigned</span>
                    </div>
                </div>

                <!-- Add New Area -->
                <div class="mb-3">
                    <label for="new_area_id" class="form-label fw-bold">
                        Add Area <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="new_area_id">
                            <option value="">Select an unassigned area...</option>
                        </select>
                        <button type="button" class="btn btn-success text-nowrap" id="addAreaBtn">
                            <i class="fa-solid fa-plus"></i> Add
                        </button>
                    </div>
                    <small class="text-muted">Only areas without a health worker are shown.</small>
                </div>

                <!-- Warning -->
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Note:</strong> Removing an area will unlink all patient records from this health worker for that area.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .area-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #198754;
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
    }

    .area-badge .remove-area-btn {
        cursor: pointer;
        opacity: 0.8;
        background: none;
        border: none;
        color: white;
        padding: 0;
        font-size: 0.8rem;
        line-height: 1;
    }

    .area-badge .remove-area-btn:hover {
        opacity: 1;
    }

    .min-height-50 {
        min-height: 50px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentWorkerId = null;
        const swapModal = new bootstrap.Modal(document.getElementById('swapAreaModal'));

        // ── Open modal ──────────────────────────────────────────────
        document.addEventListener('click', function(e) {
            const icon = e.target.closest('.swap-icon');
            if (!icon) return;

            e.preventDefault();
            currentWorkerId = icon.getAttribute('data-id');

            loadWorkerData(currentWorkerId);
            swapModal.show();
        });

        // ── Load worker info + their areas + available areas ────────
        function loadWorkerData(workerId) {
            document.getElementById('current_areas_list').innerHTML =
                '<span class="text-muted small">Loading...</span>';
            document.getElementById('new_area_id').innerHTML =
                '<option value="">Loading areas...</option>';

            fetch(`/health-workers/areas/${workerId}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    const {
                        worker,
                        assigned_areas,
                        available_areas
                    } = data.data;

                    // Worker info
                    document.getElementById('manage_worker_id').value = worker.user_id;
                    document.getElementById('manage_worker_name').textContent = worker.full_name;
                    document.getElementById('manage_worker_image').src = '/' + worker.profile_image;

                    // Current areas
                    renderCurrentAreas(assigned_areas);

                    // Available areas dropdown
                    renderAvailableAreas(available_areas);
                })
                .catch(() => showAlert('error', 'Error', 'Failed to load health worker data'));
        }

        // ── Render current area badges with remove buttons ──────────
        function renderCurrentAreas(areas) {
            const container = document.getElementById('current_areas_list');
            const noMsg = document.getElementById('no_areas_msg');

            container.innerHTML = '';

            if (!areas.length) {
                container.innerHTML = '<span class="text-muted small" id="no_areas_msg">No areas assigned</span>';
                return;
            }

            areas.forEach(area => {
                const badge = document.createElement('span');
                badge.className = 'area-badge';
                badge.dataset.areaId = area.id;
                badge.innerHTML = `
                ${area.brgy_unit}
                <button class="remove-area-btn" title="Remove area" data-area-id="${area.id}">
                    <i class="fa-solid fa-xmark"></i>
                </button>`;
                container.appendChild(badge);
            });
        }

        // ── Render available areas in the dropdown ──────────────────
        function renderAvailableAreas(areas) {
            const select = document.getElementById('new_area_id');
            select.innerHTML = '<option value="">Select an unassigned area...</option>';

            areas.forEach(area => {
                select.innerHTML += `<option value="${area.id}">${area.brgy_unit}</option>`;
            });
        }

        // ── Add area ────────────────────────────────────────────────
        document.getElementById('addAreaBtn').addEventListener('click', function() {
            const areaId = document.getElementById('new_area_id').value;
            if (!areaId) {
                showAlert('warning', 'Select Area', 'Please select an area to add.');
                return;
            }

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch(`/health-workers/areas/${currentWorkerId}/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        area_id: areaId
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        loadWorkerData(currentWorkerId); // refresh both lists
                        dispatchLivewireRefresh();
                    } else {
                        showAlert('error', 'Error', data.message ?? 'Failed to add area.');
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-plus"></i> Add';
                });
        });

        // ── Remove area (event delegation) ─────────────────────────
        document.getElementById('current_areas_list').addEventListener('click', function(e) {
            const btn = e.target.closest('.remove-area-btn');
            if (!btn) return;

            const areaId = btn.dataset.areaId;

            Swal.fire({
                title: 'Remove this area?',
                text: 'The health worker will be unlinked from this area.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it',
            }).then(result => {
                if (!result.isConfirmed) return;

                fetch(`/health-workers/areas/${currentWorkerId}/remove`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            area_id: areaId
                        }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            loadWorkerData(currentWorkerId);
                            dispatchLivewireRefresh();
                        } else {
                            showAlert('error', 'Error', data.message ?? 'Failed to remove area.');
                        }
                    });
            });
        });

        // ── Helpers ─────────────────────────────────────────────────
        function dispatchLivewireRefresh() {
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('healthWorkerTableRefresh');
            }
        }

        function showAlert(icon, title, text) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon,
                    title,
                    text
                });
            } else {
                alert(`${title}: ${text}`);
            }
        }
    });
</script>