import Swal from "sweetalert2";
const viewCaseBtn = document.querySelectorAll(".viewCaseBtn");

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".senior-citizen-view-icon");

    if (!viewBtn) return;
    const id = viewBtn.dataset.bsCaseId;
    // console.log("caseId", id);

    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }
    try {
        // Validate ID
        if (!id || isNaN(Number(id))) {
            console.error("Invalid ID:", id);
            alert("Unable to load case details: Invalid ID");
            return;
        }

        const response = await fetch(
            `/senior-citizen/case-details/${Number(id)}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Validate response data
        if (!data || typeof data !== "object") {
            throw new Error("Invalid response data format");
        }

        // Safely populate case record fields
        if (
            data.seniorCaseRecord &&
            typeof data.seniorCaseRecord === "object"
        ) {
            Object.entries(data.seniorCaseRecord).forEach(([key, value]) => {
                try {
                    // console.log("Processing key:", key);
                    const element = document.getElementById(`view_${key}`);

                    if (element) {
                        // Escape HTML to prevent XSS
                        const safeValue =
                            value !== null && value !== undefined
                                ? String(value)
                                      .replace(/&/g, "&amp;")
                                      .replace(/</g, "&lt;")
                                      .replace(/>/g, "&gt;")
                                      .replace(/"/g, "&quot;")
                                      .replace(/'/g, "&#039;")
                                : "N/A";

                        element.innerHTML = safeValue;
                    } else {
                        // console.warn(`Element not found: view_${key}`);
                    }
                } catch (fieldError) {
                    // console.error(`Error setting field ${key}:`, fieldError);
                }
            });
        } else {
            console.warn("No seniorCaseRecord in response");
        }

        // Safely populate maintenance medication table
        const tableBody = document.getElementById("viewCaseBody");

        if (!tableBody) {
            console.error("Table body element not found: viewCaseBody");
            throw new Error("Table element not found");
        }

        // Clear table
        tableBody.innerHTML = "";

        // Check if maintenance medications exist
        if (
            data.seniorCaseRecord?.senior_citizen_maintenance_med &&
            Array.isArray(data.seniorCaseRecord.senior_citizen_maintenance_med)
        ) {
            const medications =
                data.seniorCaseRecord.senior_citizen_maintenance_med;

            if (medications.length === 0) {
                // Show "No records" message
                tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted">No maintenance medications found</td>
                </tr>
            `;
            } else {
                medications.forEach((record, index) => {
                    try {
                        // console.log("Processing medication record:", record);

                        // Safely get values with defaults
                        const medication =
                            record.maintenance_medication || "N/A";
                        const dosage = record.dosage_n_frequency || "N/A";
                        const quantity = record.quantity || "N/A";
                        const startDate = record.start_date || "N/A";
                        const endDate = record.end_date || "N/A";

                        // Create table row
                        const row = document.createElement("tr");
                        row.innerHTML = `
                        <td>${escapeHtml(medication)}</td>
                        <td>${escapeHtml(dosage)}</td>
                        <td>${escapeHtml(quantity)}</td>
                        <td>${escapeHtml(startDate)}</td>
                        <td>${escapeHtml(endDate)}</td>
                    `;

                        tableBody.appendChild(row);
                    } catch (rowError) {
                        console.error(
                            `Error adding medication row ${index}:`,
                            rowError
                        );
                    }
                });
            }
        } else {
            console.warn("No maintenance medications in response");
            tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">No maintenance medications found</td>
            </tr>
        `;
        }
    } catch (error) {
        console.error("Error fetching senior case details:", error);
        alert(`Failed to load case details: ${error.message}`);

        // Optionally clear/reset the UI on error
        const tableBody = document.getElementById("viewCaseBody");
        if (tableBody) {
            tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load data. Please try again.
                </td>
            </tr>
        `;
        }
    }

    // Helper function to escape HTML (prevents XSS)
    function escapeHtml(text) {
        if (text === null || text === undefined) {
            return "N/A";
        }

        const div = document.createElement("div");
        div.textContent = String(text);
        return div.innerHTML;
    }
});

const saveBtn = document.getElementById("edit-save-btn");
// edit case id
const editBtn = document.querySelectorAll(".editCaseBtn");
const editTableBody = document.getElementById("edit-tbody");

let currentEditCaseId = null; // Store the ID for save operation

document.addEventListener("click", async function (e) {
    const editBtn = e.target.closest(".editCaseBtn");

    // Exit if not our button
    if (!editBtn) return;

    e.preventDefault();
    e.stopPropagation();

    const id = editBtn.dataset.bsCaseId;

    // Validate ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to load case details: Invalid ID");
        return;
    }

    // Validate ID is a number
    if (isNaN(Number(id))) {
        console.error("Case ID must be a number:", id);
        alert("Invalid case ID format");
        return;
    }

    // console.log("Loading case for edit, ID:", id);

    // Store ID for save button
    currentEditCaseId = id;
    const saveBtn = document.getElementById("edit-save-btn");
    if (saveBtn) {
        saveBtn.dataset.medicalId = id;
    } else {
        console.warn("Save button not found: edit-save-btn");
    }

    try {
        const response = await fetch(
            `/senior-citizen/case-details/${Number(id)}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Validate response data
        if (!data || typeof data !== "object") {
            throw new Error("Invalid response data format");
        }

        // Safely populate edit form fields
        if (
            data.seniorCaseRecord &&
            typeof data.seniorCaseRecord === "object"
        ) {
            Object.entries(data.seniorCaseRecord).forEach(([key, value]) => {
                try {
                    // Skip nested objects/arrays
                    if (typeof value === "object" && value !== null) {
                        // console.log(`Skipping object/array field: ${key}`);
                        return;
                    }

                    const element = document.getElementById(`edit_${key}`);

                    if (element) {
                        // Set input value
                        if (
                            element.tagName === "INPUT" ||
                            element.tagName === "TEXTAREA" ||
                            element.tagName === "SELECT"
                        ) {
                            element.value = value ?? "";
                        } else {
                            console.warn(
                                `Element edit_${key} is not an input field`
                            );
                        }
                        if (key == 'date_of_comeback' && value != null) {
                            const date = new Date(value);
                            document.getElementById(`edit_${key}`).value = date
                                .toISOString()
                                .split("T")[0];
                        }
                    } else {
                        // console.warn(`Element not found: edit_${key}`);
                    }
                } catch (fieldError) {
                    console.error(`Error setting field ${key}:`, fieldError);
                }
            });
        } else {
            console.warn("No seniorCaseRecord in response");
        }

        // Safely populate maintenance medication table
        const editTableBody = document.getElementById("edit-tbody");

        if (!editTableBody) {
            console.error("Table body element not found: edit-tbody");
            throw new Error("Table element not found");
        }

        // Clear table
        editTableBody.innerHTML = "";

        // Check if maintenance medications exist
        if (
            data.seniorCaseRecord?.senior_citizen_maintenance_med &&
            Array.isArray(data.seniorCaseRecord.senior_citizen_maintenance_med)
        ) {
            const medications =
                data.seniorCaseRecord.senior_citizen_maintenance_med;

            if (medications.length === 0) {
                // Show "No records" message
                editTableBody.innerHTML = `
                    <tr class = "empty-medication">
                        <td colspan="6" class="text-center text-muted py-3">
                            <i class="fas fa-info-circle"></i>
                            No maintenance medications. Click "Add Medicine" to add new records.
                        </td>
                    </tr>
                `;
            } else {
                medications.forEach((record, index) => {
                    try {
                        // console.log("Processing medication record:", record);

                        // Safely get values with defaults
                        const medication = record.maintenance_medication || "";
                        const dosage = record.dosage_n_frequency || "";
                        const quantity = record.quantity || "";
                        const startDate = record.start_date || "";
                        const endDate = record.end_date || "";

                        // Create table row
                        const row = document.createElement("tr");
                        row.className = "senior-citizen-maintenance";
                        row.innerHTML = `
                            <td>${escapeHtml(medication) || "N/A"}</td>
                            <td>${escapeHtml(dosage) || "N/A"}</td>
                            <td>${escapeHtml(quantity) || "N/A"}</td>
                            <td>${escapeHtml(startDate) || "N/A"}</td>
                            <td>${escapeHtml(endDate) || "N/A"}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm medicine-remove">Remove</button>
                            </td>
                            <input type="hidden" name="medicines[]" value="${escapeAttribute(
                                medication
                            )}">
                            <input type="hidden" name="dosage_n_frequencies[]" value="${escapeAttribute(
                                dosage
                            )}">
                            <input type="hidden" name="maintenance_quantity[]" value="${escapeAttribute(
                                quantity
                            )}">
                            <input type="hidden" name="start_date[]" value="${escapeAttribute(
                                startDate
                            )}">
                            <input type="hidden" name="end_date[]" value="${escapeAttribute(
                                endDate
                            )}">
                        `;

                        editTableBody.appendChild(row);
                    } catch (rowError) {
                        console.error(
                            `Error adding medication row ${index}:`,
                            rowError
                        );
                    }
                });
            }
        } else {
            console.warn("No maintenance medications in response");
            editTableBody.innerHTML = `
                <tr class='empty-medication'>
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="fas fa-info-circle"></i>
                        No maintenance medications. Click "Add Medicine" to add new records.
                    </td>
                </tr>
            `;
        }
    } catch (error) {
        console.error("Error fetching case details for edit:", error);
        alert(`Failed to load case details: ${error.message}`);

        // Clear/reset the UI on error
        const editTableBody = document.getElementById("edit-tbody");
        if (editTableBody) {
            editTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        Failed to load data. Please try again.
                    </td>
                </tr>
            `;
        }
    }
});

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
 * Escape HTML to prevent XSS attacks (for display in table cells)
 */
function escapeHtml(text) {
    if (text === null || text === undefined || text === "") {
        return "";
    }

    const div = document.createElement("div");
    div.textContent = String(text);
    return div.innerHTML;
}

/**
 * Escape HTML attributes to prevent XSS attacks (for input values)
 */
function escapeAttribute(text) {
    if (text === null || text === undefined) {
        return "";
    }

    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}

if (editTableBody) {
    // remove maintenance
    editTableBody.addEventListener("click", (e) => {
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
             if (editTableBody.children.length === 0) {
                 editTableBody.innerHTML = `
                        <tr class='empty-medication'>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="fas fa-info-circle"></i>
                                No maintenance medications. Click "Add Medicine" to add new records.
                            </td>
                        </tr>
                    `;
             }
        }
    });
}

// add medicine to the container
const addBTN = document.getElementById("edit-add-btn");
if (addBTN) {
    addBTN.addEventListener("click", (e) => {


        const medicine = document.getElementById("edit_maintenance_medication");
        const dosage_n_frequency = document.getElementById(
            "edit_dosage_n_frequency"
        );
        const quantity = document.getElementById("edit_maintenance_quantity");
        const start_date = document.getElementById(
            "edit_maintenance_start_date"
        );
        const end_date = document.getElementById("edit_maintenance_end_date");

        if (
            medicine.value == "" &&
            dosage_n_frequency.value == "" &&
            quantity.value == "" &&
            start_date.value == "" &&
            end_date.value == ""
        ) {
            Swal.fire({
                title: "Missing Information",
                text: "Information provided is incomplete or invalid.", // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            medicine.style.border =
                medicine.value === "" ? "1px solid red" : "";
            dosage_n_frequency.style.border =
                dosage_n_frequency.value === "" ? "1px solid red" : "";
            quantity.style.border =
                quantity.value === "" ? "1px solid red" : "";
            start_date.style.border =
                start_date.value === "" ? "1px solid red" : "";
            end_date.style.border =
                end_date.value === "" ? "1px solid red" : "";
        } else {
            const editTableBody = document.getElementById("edit-tbody");

            if (!editTableBody) {
                console.error("Table body not found");
                return;
            }

            // ✅ Check if empty message exists and remove it
            const emptyRow = editTableBody.querySelector("tr.empty-medication");
            if (emptyRow) {
                emptyRow.remove();
                // console.log("Empty message removed");
            }
            editTableBody.innerHTML += `
                <tr class="senior-citizen-maintenance-record" >
                    <td>${medicine.value}</td>
                    <td>${dosage_n_frequency.value}</td>
                    <td>${quantity.value}</td>
                    <td>${start_date.value}</td>
                    <td>${end_date.value}</td>
                    <td>
                        <button type=button class="btn btn-danger btn-sm medicine-remove">Remove</button>
                    </td>
                    <input type="hidden" name="medicines[]" value="${medicine.value}">
                    <input type="hidden" name="dosage_n_frequencies[]" value="${dosage_n_frequency.value}">
                    <input type="hidden" name="maintenance_quantity[]" value="${quantity.value}">
                    <input type="hidden" name="start_date[]" value="${start_date.value}">
                    <input type="hidden" name="end_date[]" value="${end_date.value}">
                </tr>
            `;

            // reset the borders
            medicine.style.border =
                medicine.value === "" ? "1px solid red" : "";
            dosage_n_frequency.style.border =
                dosage_n_frequency.value === "" ? "1px solid red" : "";
            quantity.style.border =
                quantity.value === "" ? "1px solid red" : "";
            start_date.style.border =
                start_date.value === "" ? "1px solid red" : "";
            end_date.style.border =
                end_date.value === "" ? "1px solid red" : "";

            medicine.value = "";
            dosage_n_frequency.value = "";
            quantity.value = "";
            start_date.value = "";
            end_date.value = "";
        }
    });
}

// update the records

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = saveBtn.dataset.medicalId; // id of the case
    const form = document.getElementById("edit-senior-citizen-form");
    const formData = new FormData(form);

    const response = await fetch(`/patient-case/senior-citizen/${id}`, {
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
            if (document.getElementById(`${key}_error`)) {
                document.getElementById(`${key}_error`).textContent = value;
            }
        });

        Swal.fire({
            title: "Update Senior Citizen Medicine Maintenance Record",
            text: Object.values(data.errors)
                .map((err) => err) // convert array of errors to text
                .join("\n"), // join with new lines
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        // reset the error element text first
        errorElements.forEach((element) => {
            element.textContent = "";
        });

        Swal.fire({
            title: "Update Senior Citizen Medicine Maintenance Record",
            text: capitalizeEachWord(data.message),
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("editSeniorCitizenModal")
                );
                modal.hide();
            }
        });;
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

// archive functionality
document.addEventListener('click', async (e) => {
    const archiveBtn = e.target.closest(".senior-citizen-archive-icon");
    if (!archiveBtn) return;
    
    const id = archiveBtn.dataset.bsCaseId;
    // console.log("caseId", id);

    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        // ✅ Show confirmation dialog FIRST
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "The Senior Citizen Case Record will be archived.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Archive",
            cancelButtonText: "Cancel"
        });
        
        // ✅ Exit if user cancelled
        if (!result.isConfirmed) return;
        
        // ✅ Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('CSRF token not found. Please refresh the page.');
        }
        
        const response = await fetch(
            `/patient-record/senior-citizen/case/delete/${id}`,
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
            Livewire.dispatch("seniorCitizenRefreshTable"); // ✅ Update dispatch name if needed
        }
        
        // Remove the row from DOM
        const row = archiveBtn.closest("tr");
        if (row) {
            row.remove();
        }
        
        // Show success message
        Swal.fire({
            title: "Archived!",
            text: "The senior citizen case record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
        
    } catch (error) {
        console.error('Error archiving case:', error);
        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});
