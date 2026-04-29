import Swal from "sweetalert2";

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function escapeHtml(text) {
    if (text === null || text === undefined || text === "") return "";
    const div = document.createElement("div");
    div.textContent = String(text);
    return div.innerHTML;
}

function escapeAttribute(text) {
    if (text === null || text === undefined) return "";
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

function syncEditNoRecordRow() {
    const editTableBody = document.getElementById("edit-tbody");
    const noRow = document.getElementById("edit-no-medication-row");
    if (!editTableBody || !noRow) return;
    const hasRows =
        editTableBody.querySelectorAll(".senior-citizen-maintenance").length >
        0;
    noRow.style.display = hasRows ? "none" : "";
}

window.syncEditEndDateMin = function (dateVal) {
    const endDate = document.getElementById("edit_maintenance_end_date");
    if (!endDate) return;
    endDate.min = dateVal;
    if (endDate.value && endDate.value < dateVal) {
        endDate.value = "";
    }
};

// ---------------------------------------------------------------------------
// IS_FINAL TOGGLE — EDIT MODAL
// ---------------------------------------------------------------------------

function applyEditFinalToggleState(isFinal, lockToggle = false) {
    const toggle = document.getElementById("edit_is_final_toggle");
    const hiddenInput = document.getElementById("edit_is_final_hidden");
    const warning = document.getElementById("edit_is_final_warning");
    const dateWrapper = document.getElementById("edit_comeback_wrapper");
    const dateInput = document.getElementById("edit_date_of_comeback");

    if (!toggle || !hiddenInput) return;

    toggle.checked = isFinal;
    hiddenInput.value = isFinal ? "1" : "0";

    if (warning) warning.classList.toggle("d-none", !isFinal);

    if (dateWrapper) {
        dateWrapper.style.opacity = isFinal ? "0.45" : "1";
        dateWrapper.style.pointerEvents = isFinal ? "none" : "auto";
    }

    if (dateInput) {
        if (isFinal) {
            dateInput.setAttribute("readonly", "readonly");
        } else {
            dateInput.removeAttribute("readonly");
        }
    }

    if (lockToggle) {
        toggle.disabled = true;

        const form = document.getElementById("edit-senior-citizen-form");
        if (form) {
            form.querySelectorAll("input, select, textarea").forEach((el) => {
                if (el.id === "edit_date_of_comeback") {
                    el.setAttribute("readonly", "readonly");
                    return;
                }
                el.disabled = true;
            });
        }

        const saveBtn = document.getElementById("edit-save-btn");
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                </svg>
                Record Locked
            `;
        }
    } else {
        toggle.disabled = false;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const editToggle = document.getElementById("edit_is_final_toggle");
    if (editToggle) {
        editToggle.addEventListener("change", function () {
            applyEditFinalToggleState(this.checked, false);
        });
    }
});

// Reset EDIT modal when it closes
document
    .getElementById("editSeniorCitizenModal")
    ?.addEventListener("hidden.bs.modal", function () {
        const form = document.getElementById("edit-senior-citizen-form");
        if (form) {
            form.querySelectorAll("input, select, textarea").forEach((el) => {
                el.disabled = false;
                el.removeAttribute("readonly");
            });
        }

        const saveBtn = document.getElementById("edit-save-btn");
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = "Save Record";
        }

        applyEditFinalToggleState(false, false);

        const isFinalError = document.getElementById("edit_is_final_error");
        if (isFinalError) isFinalError.textContent = "";
    });

// ---------------------------------------------------------------------------
// VIEW case
// ---------------------------------------------------------------------------

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".senior-citizen-view-icon");
    if (!viewBtn) return;

    const id = viewBtn.dataset.bsCaseId;
    if (!id || id === "undefined" || id === "null" || isNaN(Number(id))) {
        console.error("Invalid case ID:", id);
        alert("Unable to load case details: Invalid ID");
        return;
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
            },
        );

        if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();
        if (!data || typeof data !== "object")
            throw new Error("Invalid response data format");

        if (
            data.seniorCaseRecord &&
            typeof data.seniorCaseRecord === "object"
        ) {
            Object.entries(data.seniorCaseRecord).forEach(([key, value]) => {
                try {
                    const element = document.getElementById(`view_${key}`);
                    if (!element) return;
                    if (key === "date_of_comeback") {
                        element.innerHTML = new Date(value)
                            .toISOString()
                            .split("T")[0];
                    } else {
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
                    }
                } catch (fieldError) {
                    // silent
                }
            });
        }

        const tableBody = document.getElementById("viewCaseBody");
        if (!tableBody) throw new Error("Table element not found");

        tableBody.innerHTML = "";

        const medications =
            data.seniorCaseRecord?.senior_citizen_maintenance_med;
        if (Array.isArray(medications) && medications.length > 0) {
            medications.forEach((record, index) => {
                try {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${escapeHtml(record.maintenance_medication || "N/A")}</td>
                        <td>${escapeHtml(record.dosage_n_frequency || "N/A")}</td>
                        <td>${escapeHtml(record.quantity || "N/A")}</td>
                        <td>${escapeHtml(record.start_date || "N/A")}</td>
                        <td>${escapeHtml(record.end_date || "N/A")}</td>
                    `;
                    tableBody.appendChild(row);
                } catch (rowError) {
                    console.error(
                        `Error adding medication row ${index}:`,
                        rowError,
                    );
                }
            });
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted">No maintenance medications found</td>
                </tr>
            `;
        }
    } catch (error) {
        console.error("Error fetching senior case details:", error);
        alert(`Failed to load case details: ${error.message}`);

        const tableBody = document.getElementById("viewCaseBody");
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        Failed to load data. Please try again.
                    </td>
                </tr>
            `;
        }
    }
});

// ---------------------------------------------------------------------------
// EDIT case — load data
// ---------------------------------------------------------------------------

let currentEditCaseId = null;

document.addEventListener("click", async function (e) {
    const editBtn = e.target.closest(".editCaseBtn");
    if (!editBtn) return;

    e.preventDefault();
    e.stopPropagation();

    document
        .querySelectorAll(".error-text")
        .forEach((el) => (el.innerHTML = ""));

    const id = editBtn.dataset.bsCaseId;
    if (!id || id === "undefined" || id === "null" || isNaN(Number(id))) {
        console.error("Invalid case ID:", id);
        alert("Unable to load case details: Invalid ID");
        return;
    }

    currentEditCaseId = id;
    const saveBtn = document.getElementById("edit-save-btn");
    if (saveBtn) saveBtn.dataset.medicalId = id;

    try {
        const response = await fetch(
            `/senior-citizen/case-details/${Number(id)}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            },
        );

        if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();
        if (!data || typeof data !== "object")
            throw new Error("Invalid response data format");

        if (
            data.seniorCaseRecord &&
            typeof data.seniorCaseRecord === "object"
        ) {
            Object.entries(data.seniorCaseRecord).forEach(([key, value]) => {
                try {
                    if (typeof value === "object" && value !== null) return;
                    const element = document.getElementById(`edit_${key}`);
                    if (!element) return;
                    if (
                        ["INPUT", "TEXTAREA", "SELECT"].includes(
                            element.tagName,
                        )
                    ) {
                        element.value = value ?? "";
                    }
                    if (key === "date_of_comeback" && value != null) {
                        element.value = new Date(value)
                            .toISOString()
                            .split("T")[0];
                    }
                } catch (fieldError) {
                    console.error(`Error setting field ${key}:`, fieldError);
                }
            });
        }

        // Apply is_final toggle state
        const caseIsFinal = !!data.case_is_final;
        const thisRecordIsFinal = !!data.this_record_is_final;
        applyEditFinalToggleState(thisRecordIsFinal, caseIsFinal);

        const editTableBody = document.getElementById("edit-tbody");
        if (!editTableBody) throw new Error("Table element not found");

        editTableBody.innerHTML = "";

        const medications =
            data.seniorCaseRecord?.senior_citizen_maintenance_med;
        if (Array.isArray(medications) && medications.length > 0) {
            medications.forEach((record, index) => {
                try {
                    const medication = record.maintenance_medication || "";
                    const dosage = record.dosage_n_frequency || "";
                    const quantity = record.quantity || "";
                    const startDate = record.start_date || "";
                    const endDate = record.end_date || "";

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
                        <input type="hidden" name="medicines[]"            value="${escapeAttribute(medication)}">
                        <input type="hidden" name="dosage_n_frequencies[]" value="${escapeAttribute(dosage)}">
                        <input type="hidden" name="maintenance_quantity[]" value="${escapeAttribute(quantity)}">
                        <input type="hidden" name="start_date[]"           value="${escapeAttribute(startDate)}">
                        <input type="hidden" name="end_date[]"             value="${escapeAttribute(endDate)}">
                    `;
                    editTableBody.appendChild(row);
                } catch (rowError) {
                    console.error(
                        `Error adding medication row ${index}:`,
                        rowError,
                    );
                }
            });
        } else {
            editTableBody.innerHTML = `
                <tr id="edit-no-medication-row">
                    <td colspan="6" class="text-center text-muted py-3">No maintenance medications found.</td>
                </tr>
            `;
        }

        syncEditNoRecordRow();
    } catch (error) {
        console.error("Error fetching case details for edit:", error);
        alert(`Failed to load case details: ${error.message}`);

        const editTableBody = document.getElementById("edit-tbody");
        if (editTableBody) {
            editTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-3">
                        Failed to load data. Please try again.
                    </td>
                </tr>
            `;
        }
    }
});

// ---------------------------------------------------------------------------
// EDIT — remove medicine row
// ---------------------------------------------------------------------------

const editTableBody = document.getElementById("edit-tbody");
if (editTableBody) {
    editTableBody.addEventListener("click", (e) => {
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
            syncEditNoRecordRow();
        }
    });
}

// ---------------------------------------------------------------------------
// EDIT — add medicine row
// ---------------------------------------------------------------------------

const addBTN = document.getElementById("edit-add-btn");
if (addBTN) {
    addBTN.addEventListener("click", () => {
        const medicine = document.getElementById("edit_maintenance_medication");
        const dosage_n_frequency = document.getElementById(
            "edit_dosage_n_frequency",
        );
        const quantity = document.getElementById("edit_maintenance_quantity");
        const start_date = document.getElementById(
            "edit_maintenance_start_date",
        );
        const end_date = document.getElementById("edit_maintenance_end_date");

        // Reset borders
        [medicine, dosage_n_frequency, quantity, start_date, end_date].forEach(
            (f) => (f.style.border = ""),
        );

        const qtyVal = parseInt(quantity.value, 10);
        const errors = [];

        if (medicine.value.trim() === "") {
            medicine.style.border = "1px solid red";
            errors.push("Maintenance medication is required.");
        }
        if (dosage_n_frequency.value.trim() === "") {
            dosage_n_frequency.style.border = "1px solid red";
            errors.push("Dosage & frequency is required.");
        }
        if (quantity.value === "" || isNaN(qtyVal) || qtyVal < 0) {
            quantity.style.border = "1px solid red";
            errors.push("Quantity must be 0 or greater.");
        }
        if (start_date.value === "") {
            start_date.style.border = "1px solid red";
            errors.push("Start date is required.");
        }
        if (end_date.value === "" || end_date.value < start_date.value) {
            end_date.style.border = "1px solid red";
            errors.push(
                "End date must be greater than or equal to start date.",
            );
        }

        if (errors.length > 0) {
            Swal.fire({
                title: "Missing Information",
                html: errors.map((e) => `<div>${e}</div>`).join(""),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            return;
        }

        const currentEditTableBody = document.getElementById("edit-tbody");
        if (!currentEditTableBody) return;

        const row = currentEditTableBody.insertRow(-1);
        row.className = "senior-citizen-maintenance";
        row.innerHTML = `
            <td>${medicine.value}</td>
            <td>${dosage_n_frequency.value}</td>
            <td>${qtyVal}</td>
            <td>${start_date.value}</td>
            <td>${end_date.value}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm medicine-remove">Remove</button>
            </td>
            <input type="hidden" name="medicines[]"            value="${medicine.value}">
            <input type="hidden" name="dosage_n_frequencies[]" value="${dosage_n_frequency.value}">
            <input type="hidden" name="maintenance_quantity[]" value="${qtyVal}">
            <input type="hidden" name="start_date[]"           value="${start_date.value}">
            <input type="hidden" name="end_date[]"             value="${end_date.value}">
        `;

        medicine.value = "";
        dosage_n_frequency.value = "";
        quantity.value = "";
        start_date.value = "";
        end_date.value = "";
        end_date.min = "";

        syncEditNoRecordRow();
    });
}

// ---------------------------------------------------------------------------
// EDIT — save record
// ---------------------------------------------------------------------------

const saveBtn = document.getElementById("edit-save-btn");

if (saveBtn) {
    saveBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const id = saveBtn.dataset.medicalId;
        const originalText = saveBtn.innerHTML;

        saveBtn.disabled = true;
        saveBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

        try {
            const form = document.getElementById("edit-senior-citizen-form");
            const formData = new FormData(form);

            const response = await fetch(`/patient-case/senior-citizen/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await response.json();
            const errorElements = document.querySelectorAll(".error-text");

            if (!response.ok) {
                errorElements.forEach((el) => (el.textContent = ""));

                if (data.errors) {
                    Object.entries(data.errors).forEach(([key, value]) => {
                        const el = document.getElementById(`${key}_error`);
                        if (el)
                            el.textContent = Array.isArray(value)
                                ? value[0]
                                : value;
                    });

                    if (data.errors?.is_final) {
                        const isFinalError = document.getElementById(
                            "edit_is_final_error",
                        );
                        if (isFinalError) {
                            isFinalError.textContent = Array.isArray(
                                data.errors.is_final,
                            )
                                ? data.errors.is_final[0]
                                : data.errors.is_final;
                        }
                    }

                    await Swal.fire({
                        title: "Update Senior Citizen Medicine Maintenance Record",
                        html: Object.values(data.errors)
                            .flat()
                            .map((e) => `<div>${e}</div>`)
                            .join(""),
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK",
                    });
                }

                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            } else {
                errorElements.forEach((el) => (el.textContent = ""));

                await Swal.fire({
                    title: "Update Senior Citizen Medicine Maintenance Record",
                    text: capitalizeEachWord(data.message),
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                }).then((result) => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                    if (result.isConfirmed) {
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("editSeniorCitizenModal"),
                        );
                        if (modal) modal.hide();
                    }
                });

                if (typeof Livewire !== "undefined") {
                    Livewire.dispatch("seniorCitizenRefreshTable");
                }
            }
        } catch (error) {
            console.error("Error updating record:", error);

            await Swal.fire({
                title: "Error",
                text: `Failed to update record: ${error.message}`,
                icon: "error",
                confirmButtonColor: "#3085d6",
            });

            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    });
}

// ---------------------------------------------------------------------------
// ARCHIVE
// ---------------------------------------------------------------------------

document.addEventListener("click", async (e) => {
    const archiveBtn = e.target.closest(".senior-citizen-archive-icon");
    if (!archiveBtn) return;

    const id = archiveBtn.dataset.bsCaseId;
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "The Senior Citizen Case Record will be moved to archived status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
            cancelButtonText: "Cancel",
        });

        if (!result.isConfirmed) return;

        const originalHTML = archiveBtn.innerHTML;
        archiveBtn.disabled = true;
        archiveBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken)
            throw new Error("CSRF token not found. Please refresh the page.");

        const response = await fetch(
            `/patient-record/senior-citizen/case/delete/${id}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken.content,
                    Accept: "application/json",
                },
            },
        );

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`,
            );
        }

        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("seniorCitizenRefreshTable");
        }

        const row = archiveBtn.closest("tr");
        if (row) row.remove();

        Swal.fire({
            title: "Archived!",
            text: "The senior citizen case record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
    } catch (error) {
        console.error("Error archiving case:", error);

        archiveBtn.disabled = false;
        archiveBtn.innerHTML = originalHTML;

        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});
