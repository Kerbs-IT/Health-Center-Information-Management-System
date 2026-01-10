import Swal from "sweetalert2";

// View Pregnancy Checkup Handler
document.addEventListener("click", async function(e) {
    const viewBtn = e.target.closest('.viewPregnancyCheckupBtn');
    
    if (!viewBtn) return; // Not our button, ignore
    
    // Prevent default Bootstrap modal behavior temporarily
    e.preventDefault();
    e.stopPropagation();
    
    const checkupId = viewBtn.dataset.checkupId;
    
    // Validate checkup ID exists
    if (!checkupId || checkupId === 'undefined' || checkupId === 'null') {
        console.error('Invalid checkup ID:', checkupId);
        showErrorNotification('Unable to load checkup: Invalid ID');
        return;
    }
    
    // console.log('Loading checkup ID:', checkupId);
    
    // Show loading state
    showLoadingModal();
    
    try {
        const response = await fetch(
            `/prenatal/view-pregnancy-checkup-info/${checkupId}`,
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        // Validate response data
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response data format');
        }
        
        // Clear all modal fields first
        clearCheckupModalData();
        
        // Populate pregnancy checkup info
        if (data.pregnancy_checkup_info && typeof data.pregnancy_checkup_info === 'object') {
            populateCheckupInfo(data.pregnancy_checkup_info);
        } else {
            // console.warn('No pregnancy_checkup_info in response');
        }
        
        // Populate health worker info
        if (data.healthWorker && typeof data.healthWorker === 'object') {
            populateHealthWorkerInfo(data.healthWorker);
        } else {
            console.warn('No healthWorker info in response');
        }
        
        // Hide loading and show modal
        hideLoadingModal();
        openCheckupModal();
        
    } catch (error) {
        console.error('Error fetching checkup data:', error);
        hideLoadingModal();
        showErrorNotification(`Failed to load checkup data: ${error.message}`);
    }
});

/**
 * Safely set element content
 */
function safeSetContent(elementId, value, defaultValue = 'N/A') {
    const element = document.getElementById(elementId);
    
    if (!element) {
        console.warn(`Element not found: ${elementId}`);
        return false;
    }
    
    // Handle null/undefined values
    if (value === null || value === undefined || value === '') {
        element.innerHTML = defaultValue;
        return true;
    }
    
    // Escape HTML to prevent XSS
    const safeValue = String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    
    element.innerHTML = safeValue;
    return true;
}

/**
 * Format time from 24hr to 12hr format
 */
function formatTime(timeString) {
    if (!timeString || typeof timeString !== 'string') {
        return 'N/A';
    }
    
    try {
        const parts = timeString.split(':');
        if (parts.length < 2) {
            return 'N/A';
        }
        
        let [hours, minutes] = parts;
        hours = parseInt(hours, 10);
        minutes = parseInt(minutes, 10);
        
        if (isNaN(hours) || isNaN(minutes)) {
            return 'N/A';
        }
        
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        minutes = minutes.toString().padStart(2, '0');
        
        return `${hours}:${minutes} ${ampm}`;
    } catch (error) {
        console.error('Error formatting time:', error);
        return 'N/A';
    }
}

/**
 * Populate checkup information
 */
function populateCheckupInfo(checkupInfo) {
    if (!checkupInfo || typeof checkupInfo !== 'object') {
        console.warn('Invalid checkup info provided');
        return;
    }
    
    Object.entries(checkupInfo).forEach(([key, value]) => {
        try {
            // Special handling for time fields
            if (key === 'check_up_time') {
                const formattedTime = formatTime(value);
                safeSetContent(key, formattedTime);
                return;
            }
            
            // Special handling for patient name (populate multiple fields)
            if (key === 'patient_name') {
                safeSetContent('patient_name', value);
                safeSetContent('checkup_patient_name', value);
                return;
            }
            
            // Special handling for date fields
            if (key.includes('date') && value) {
                try {
                    const date = new Date(value);
                    if (!isNaN(date.getTime())) {
                        const formatted = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                        safeSetContent(key, formatted);
                        return;
                    }
                } catch (dateError) {
                    console.warn(`Error formatting date for ${key}:`, dateError);
                }
            }
            
            // Default: set content as-is
            safeSetContent(key, value);
            
        } catch (error) {
            console.error(`Error setting field ${key}:`, error);
        }
    });
}

/**
 * Populate health worker information
 */
function populateHealthWorkerInfo(healthWorker) {
    if (!healthWorker || typeof healthWorker !== 'object') {
        console.warn('Invalid health worker info provided');
        safeSetContent('health_worker_name', null);
        return;
    }
    
    const fullName = healthWorker.full_name || 
                     `${healthWorker.first_name || ''} ${healthWorker.last_name || ''}`.trim() ||
                     'N/A';
    
    safeSetContent('health_worker_name', fullName);
}

/**
 * Clear all modal data
 */
function clearCheckupModalData() {
    // List of all field IDs in your modal
    const fieldIds = [
        'check_up_time',
        'patient_name',
        'checkup_patient_name',
        'health_worker_name',
        'blood_pressure',
        'weight',
        'height',
        'temperature',
        'pulse_rate',
        'respiratory_rate',
        'nutritional_status',
        'laboratory_tests_done',
        'hemoglobin_count',
        'urinalysis',
        'complete_blood_count',
        'stool_examination',
        'acetic_acid_wash_test',
        'tetanus_toxoid_vaccination',
        'date_of_visit',
        'age_of_gestation',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'remarks',
        'next_visit_date'
        // Add any other field IDs your modal uses
    ];
    
    fieldIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                element.value = '';
            } else {
                element.innerHTML = '';
            }
        }
    });
}

/**
 * Open the checkup modal
 */
function openCheckupModal() {
    const modalElement = document.getElementById('pregnancyCheckUpModal');
    
    if (!modalElement) {
        console.error('Modal element not found: pregnancyCheckUpModal');
        showErrorNotification('Unable to open modal');
        return;
    }
    
    try {
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    } catch (error) {
        console.error('Error opening modal:', error);
        showErrorNotification('Error opening modal');
    }
}

/**
 * Show loading state (optional)
 */
function showLoadingModal() {
    const modalElement = document.getElementById('pregnancyCheckUpModal');
    if (modalElement) {
        const modalBody = modalElement.querySelector('.modal-body');
        if (modalBody) {
            modalBody.style.opacity = '0.5';
            modalBody.style.pointerEvents = 'none';
        }
    }
    // Or show a spinner overlay
}

/**
 * Hide loading state
 */
function hideLoadingModal() {
    const modalElement = document.getElementById('pregnancyCheckUpModal');
    if (modalElement) {
        const modalBody = modalElement.querySelector('.modal-body');
        if (modalBody) {
            modalBody.style.opacity = '1';
            modalBody.style.pointerEvents = 'auto';
        }
    }
}

/**
 * Show error notification (customize based on your notification system)
 */
function showErrorNotification(message) {
    // Option 1: Simple alert
    alert(message);
    
    // Option 2: Toast notification (if you have a toast library)
    // toast.error(message);
    
    // Option 3: Custom notification div
    // const notification = document.createElement('div');
    // notification.className = 'alert alert-danger';
    // notification.textContent = message;
    // document.body.appendChild(notification);
    // setTimeout(() => notification.remove(), 5000);
}

// Clean up modal when closed
document.getElementById('pregnancyCheckUpModal')?.addEventListener('hidden.bs.modal', function() {
    clearCheckupModalData();
});

// edit btn
// ============================================================================
// EDIT BUTTON - Event Delegation (Works with Livewire)
// ============================================================================

let medicalId = 0;

document.addEventListener("click", async function(e) {
    const editBtn = e.target.closest('.editPregnancyCheckupBtn');
    if (!editBtn) return;
    
    const checkupId = editBtn.dataset.checkupId;
    
    // Validate checkup ID
    if (!checkupId || checkupId === 'undefined' || checkupId === 'null') {
        console.error('Invalid checkup ID:', checkupId);
        alert('Unable to load checkup: Invalid ID');
        return;
    }
    
    // console.log('Loading checkup ID for edit:', checkupId);

    try {
        const response = await fetch(
            `/prenatal/view-pregnancy-checkup-info/${checkupId}`,
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        // Validate response data
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response data');
        }
        
        // Check if pregnancy_checkup_info exists
        if (data.pregnancy_checkup_info && typeof data.pregnancy_checkup_info === 'object') {
            Object.entries(data.pregnancy_checkup_info).forEach(([key, value]) => {
                try {
                    if (key === "check_up_time") {
                        const element = document.getElementById(`edit_${key}`);
                        if (element) {
                            element.value = value || '';
                        } else {
                            console.warn(`Element not found: edit_${key}`);
                        }
                    } 
                    else if (key === "patient_name") {
                        const nameElement = document.getElementById("edit_patient_name");
                        const hiddenElement = document.getElementById("edit_check_up_full_name");
                        
                        if (nameElement) {
                            nameElement.value = value || '';
                        } else {
                            console.warn('Element not found: edit_patient_name');
                        }
                        
                        if (hiddenElement) {
                            hiddenElement.value = value || '';
                            // console.log("name", value);
                            // console.log("hidden value:", hiddenElement.value);
                        } else {
                            console.warn('Element not found: edit_check_up_full_name');
                        }
                    } 
                    else if (value === "Yes" || value === "No") {
                        const element = document.getElementById(
                            `edit_${key}_${value}`
                        );
                        if (element) {
                            element.checked = true;
                        } else {
                            console.warn(
                                `Element not found: edit_${key}_${value}`
                            );
                        }
                    } else if (key == "date_of_comeback") {
                        const element = document.getElementById(`edit_${key}`);
                        if (element && value) {
                            const date = value.split("T")[0]; // Gets "2025-12-24"
                            element.value = date;
                        }
                    } else {
                        const element = document.getElementById(`edit_${key}`);
                        if (element) {
                            element.value = value ?? "";
                        } else {
                            // console.warn(`Element not found: edit_${key}`);
                        }
                    }
                } catch (fieldError) {
                    console.error(`Error setting field ${key}:`, fieldError);
                }
            });
        } else {
            console.warn('No pregnancy_checkup_info in response');
        }
        
        // Safely set health worker data
        if (data.healthWorker && typeof data.healthWorker === 'object') {
            const handledByElement = document.getElementById("edit_check_up_handled_by");
            if (handledByElement) {
                handledByElement.value = data.healthWorker.full_name ?? "";
            } else {
                console.warn('Element not found: edit_check_up_handled_by');
            }
            
            const workerIdElement = document.getElementById("edit_health_worker_id");
            if (workerIdElement) {
                workerIdElement.value = data.healthWorker.user_id || '';
            } else {
                // console.warn('Element not found: edit_health_worker_id');
            }
        } else {
            console.warn('No healthWorker info in response');
        }
        
        // Store the ID for update
        medicalId = checkupId;
        
    } catch (error) {
        console.error('Error fetching checkup data:', error);
        alert(`Failed to load checkup data: ${error.message}`);
    }
});

const updateBTN = document.getElementById("edit-check-up-save-btn");

updateBTN.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("edit-check-up-form");
    const formData = new FormData(form);

    // for (const [key, value] of formData.entries()) {
    //     console.log(key, value);
    // }

    const response = await fetch(`/update/prenatal-check-up/${medicalId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

    const data = await response.json();

    const errorElements = document.querySelectorAll(".error-text");
    if (!response.ok) {
        // reset the error element text first
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        // if there's an validation error load the error text
        Object.entries(data.errors).forEach(([key, value]) => {
            if (document.getElementById(`${key}_error`) && value !=null) {
                document.getElementById(`${key}_error`).innerHTML = value;
            }
        });

        let message = "";

        if (data.errors) {
            if (typeof data.errors == "object") {
                message = Object.values(data.errors).flat().join("\n");
            } else {
                message = data.errors;
            }
        } else {
            message = "An unexpected error occurred.";
        }

        Swal.fire({
            title: "Prenatal Patient",
            text: capitalizeEachWord(message), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        // THIS IS THE BEST SOLUTION FOR UPDATING THE RECORD
        Livewire.dispatch("prenatalRefreshTable");
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        Swal.fire({
            title: "Prenatal check-Up Info",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

// remove icon

// ============================================================================
// ARCHIVE BUTTON - Event Delegation (Works with Livewire)
// ============================================================================

document.addEventListener("click", async function(e) {
    const archiveBtn = e.target.closest('.pregnancy-checkup-archieve-btn');
    if (!archiveBtn) return;
    
    const caseId = archiveBtn.dataset.caseId;
    
    // Validate case ID
    if (!caseId || caseId === 'undefined' || caseId === 'null') {
        console.error('Invalid case ID:', caseId);
        alert('Unable to archive: Invalid ID');
        return;
    }
    
    try {
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "The Prenatal Check-up Record will be moved to archived status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
        });
        
        if (!result.isConfirmed) return;
        
        // console.log('Archiving case ID:', caseId);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('CSRF token not found. Please refresh the page.');
        }
        
        const response = await fetch(
            `/prenatal/check-up/delete/${caseId}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken.content,
                    "Accept": "application/json",
                },
            }
        );
        
        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }
        
        // Success - refresh table
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch("prenatalRefreshTable");
        }
        
        // Remove the row from DOM
        const row = archiveBtn.closest("tr");
        if (row) {
            row.remove();
        }
        
        // Show success message
        Swal.fire({
            title: "Archived!",
            text: "The prenatal check-up record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
        
    } catch (error) {
        console.error('Error archiving checkup:', error);
        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});