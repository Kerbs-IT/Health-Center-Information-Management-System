<!-- Swap Area Modal - Vanilla JavaScript Version (No jQuery) -->
<div class="modal fade" id="swapAreaModal" tabindex="-1" aria-labelledby="swapAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="swapAreaModalLabel">Swap Assigned Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <form id="swapAreaForm" class="p-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="swap_health_worker_id" name="health_worker_id">

                    <!-- Current Health Worker Info -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Health Worker</label>
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded">
                            <img id="swap_worker_image" src="" alt="Worker" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0" id="swap_worker_name">-</h6>
                                <small class="text-muted">Current Area: <span id="swap_current_area">-</span></small>
                            </div>
                        </div>
                    </div>

                    <!-- New Area Selection -->
                    <div class="mb-3">
                        <label for="new_area_id" class="form-label fw-bold">New Assigned Area <span class="text-danger">*</span></label>
                        <select class="form-select" id="new_area_id" name="new_area_id" required>
                            <option value="">Select New Area</option>
                            <option value="1">Karlaville Park Homes</option>
                            <option value="2">Purok 1</option>
                            <option value="3">Purok 2</option>
                            <option value="4">Purok 3</option>
                            <option value="5">Purok 4</option>
                            <option value="6">Purok 5</option>
                            <option value="7">Purok 6</option>
                            <option value="8">Beverly Homes 1</option>
                            <option value="9">Beverly Homes 2</option>
                            <option value="10">Green Forbes City</option>
                            <option value="11">Gawad Kalinga</option>
                            <option value="12">Kaia Homes Phase 2</option>
                            <option value="13">Heneral DOS</option>
                            <option value="14">SUGAR LAND</option>
                        </select>
                    </div>

                    <!-- Preview Section (hidden initially) -->
                    <div id="swapPreview" class="alert alert-info d-none">
                        <h6 class="alert-heading">Preview Impact</h6>
                        <div id="previewContent"></div>
                    </div>

                    <!-- Warning Message -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action will update all patient records associated with this health worker's area.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="previewSwapBtn">Preview Changes</button>
                <button type="button" class="btn btn-success d-none" id="confirmSwapBtn">Confirm Swap</button>
            </div>
        </div>
    </div>
</div>

<style>
    .swap-icon-con {
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
        cursor: pointer;
    }

    .swap-icon-con:hover {
        background-color: rgba(23, 162, 184, 0.1);
    }

    #swapPreview {
        border-left: 4px solid #0dcaf0;
    }

    .swap-info-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .patient-count-badge {
        background: #0dcaf0;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentWorkerId = null;
        let swapModal = null;

        // Initialize Bootstrap modal
        const swapModalElement = document.getElementById('swapAreaModal');
        if (swapModalElement) {
            swapModal = new bootstrap.Modal(swapModalElement);
        }

        // Open swap modal when clicking swap icon
        document.addEventListener('click', function(e) {
            if (e.target.closest('.swap-icon')) {
                e.preventDefault();
                const swapIcon = e.target.closest('.swap-icon');
                const workerId = swapIcon.getAttribute('data-id');
                currentWorkerId = workerId;

                // Reset form and preview
                document.getElementById('swapAreaForm').reset();
                document.getElementById('swapPreview').classList.add('d-none');
                document.getElementById('confirmSwapBtn').classList.add('d-none');
                document.getElementById('previewSwapBtn').classList.remove('d-none');

                // Load health worker data
                loadHealthWorkerData(workerId);

                // Show modal
                if (swapModal) {
                    swapModal.show();
                }
            }
        });

        // Load health worker data
        function loadHealthWorkerData(workerId) {
            fetch(`/health-workers/swap/${workerId}/data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const workerData = data.data;
                        document.getElementById('swap_health_worker_id').value = workerData.health_worker.user_id;
                        document.getElementById('swap_worker_name').textContent = workerData.health_worker.full_name;
                        document.getElementById('swap_worker_image').src = '/' + workerData.health_worker.profile_image;
                        document.getElementById('swap_current_area').textContent = workerData.current_area_name;

                        // Set current area as selected in dropdown
                        document.getElementById('new_area_id').value = workerData.current_area_id;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error', 'Failed to load health worker data');
                });
        }

        // Preview swap button click
        document.getElementById('previewSwapBtn').addEventListener('click', function() {
            const newAreaId = document.getElementById('new_area_id').value;

            if (!newAreaId) {
                showAlert('warning', 'Select Area', 'Please select a new area');
                return;
            }

            // Check if same area
            const currentArea = document.getElementById('swap_current_area').textContent;
            const newAreaSelect = document.getElementById('new_area_id');
            const newArea = newAreaSelect.options[newAreaSelect.selectedIndex].text;

            if (currentArea === newArea) {
                showAlert('warning', 'Same Area', 'Please select a different area');
                return;
            }

            // Show loading
            const button = this;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';

            // Prepare form data
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('health_worker_id', currentWorkerId);
            formData.append('new_area_id', newAreaId);

            fetch('/health-workers/swap/preview', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayPreview(data.data);
                        document.getElementById('previewSwapBtn').classList.add('d-none');
                        document.getElementById('confirmSwapBtn').classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Error', 'Failed to preview swap');
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'Preview Changes';
                });
        });

        // Display preview
        function displayPreview(data) {
            let previewHtml = '';

            if (data.is_swap) {
                // Swap scenario
                previewHtml = `
                <div class="swap-info-card">
                    <strong>${data.current_worker.name}</strong><br>
                    <small>Current: ${data.current_worker.area} <span class="patient-count-badge">${data.current_worker.patient_count} patients</span></small><br>
                    <small>Will move to: ${data.target_worker.area}</small>
                </div>
                <div class="text-center my-2">
                    <i class="fas fa-exchange-alt fa-2x text-primary"></i>
                </div>
                <div class="swap-info-card">
                    <strong>${data.target_worker.name}</strong><br>
                    <small>Current: ${data.target_worker.area} <span class="patient-count-badge">${data.target_worker.patient_count} patients</span></small><br>
                    <small>Will move to: ${data.current_worker.area}</small>
                </div>
                <p class="mt-3 mb-0"><strong>Result:</strong> ${data.message}</p>
            `;
            } else {
                // Reassignment scenario
                previewHtml = `
                <div class="swap-info-card">
                    <strong>${data.current_worker.name}</strong><br>
                    <small>Moving from: ${data.current_worker.area} <span class="patient-count-badge">${data.current_worker.patient_count} patients</span></small><br>
                    <small>Moving to: ${data.new_area.name} <span class="patient-count-badge">${data.new_area.patient_count} patients</span></small>
                </div>
                <p class="mt-3 mb-0"><strong>Result:</strong> ${data.message}</p>
            `;
            }

            document.getElementById('previewContent').innerHTML = previewHtml;
            document.getElementById('swapPreview').classList.remove('d-none');
        }

        // Confirm swap button click
        document.getElementById('confirmSwapBtn').addEventListener('click', function() {
            const button = this;

            // Using SweetAlert2 if available, otherwise use confirm
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will update all patient records for the affected areas.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, swap areas',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performSwap(button);
                    }
                });
            } else {
                if (confirm('Are you sure? This will update all patient records for the affected areas.')) {
                    performSwap(button);
                }
            }
        });

        // Perform swap
        function performSwap(button) {
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Swapping...';

            const formData = new FormData(document.getElementById('swapAreaForm'));

            fetch('/health-workers/swap', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (swapModal) {
                            swapModal.hide();
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMsg = 'Failed to swap areas';
                    showAlert('error', 'Error', errorMsg);
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'Confirm Swap';
                });
        }

        // Reset preview when area changes
        document.getElementById('new_area_id').addEventListener('change', function() {
            document.getElementById('swapPreview').classList.add('d-none');
            document.getElementById('confirmSwapBtn').classList.add('d-none');
            document.getElementById('previewSwapBtn').classList.remove('d-none');
        });

        // Helper function to show alerts
        function showAlert(icon, title, text) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: text
                });
            } else {
                alert(title + ': ' + text);
            }
        }
    });
</script>