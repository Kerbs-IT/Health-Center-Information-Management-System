import Swal from "sweetalert2";

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function syncNoRecordRow(tableBody, noRowId) {
    const noRow = document.getElementById(noRowId);
    const hasRows = tableBody
        ? tableBody.querySelectorAll(".senior-citizen-maintenance-record")
              .length > 0
        : false;
    if (noRow) noRow.style.display = hasRows ? "none" : "";
}

window.syncAddEndDateMin = function (dateVal) {
    const endDate = document.getElementById("add_maintenance_end_date");
    if (!endDate) return;
    endDate.min = dateVal;
    if (endDate.value && endDate.value < dateVal) {
        endDate.value = "";
    }
};

// ---------------------------------------------------------------------------
// Clear errors when modal opens
// ---------------------------------------------------------------------------

const addRecordBtn = document.getElementById("add_record_btn");
if (addRecordBtn) {
    addRecordBtn.addEventListener("click", () => {
        document
            .querySelectorAll(".error-text")
            .forEach((el) => (el.innerHTML = ""));
    });
}

// ---------------------------------------------------------------------------
// Add medicine row
// ---------------------------------------------------------------------------

const addBTN = document.getElementById("add-record-btn");
const addTableBody = document.getElementById("add-record-body");

if (addBTN && addTableBody) {
    addBTN.addEventListener("click", () => {
        const medicine = document.getElementById("add_maintenance_medication");
        const dosage_n_frequency = document.getElementById(
            "add_dosage_n_frequency",
        );
        const quantity = document.getElementById("add_maintenance_quantity");
        const start_date = document.getElementById(
            "add_maintenance_start_date",
        );
        const end_date = document.getElementById("add_maintenance_end_date");

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

        // Use insertRow to avoid innerHTML += breaking DOM refs
        const row = addTableBody.insertRow(-1);
        row.classList.add("senior-citizen-maintenance-record");
        row.innerHTML = `
            <td>${medicine.value}</td>
            <td>${dosage_n_frequency.value}</td>
            <td>${qtyVal}</td>
            <td>${start_date.value}</td>
            <td>${end_date.value}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm medicine-remove">Remove</button>
            </td>
            <input type="hidden" name="medicines[]"             value="${medicine.value}">
            <input type="hidden" name="dosage_n_frequencies[]"  value="${dosage_n_frequency.value}">
            <input type="hidden" name="maintenance_quantity[]"  value="${qtyVal}">
            <input type="hidden" name="start_date[]"            value="${start_date.value}">
            <input type="hidden" name="end_date[]"              value="${end_date.value}">
        `;

        medicine.value = "";
        dosage_n_frequency.value = "";
        quantity.value = "";
        start_date.value = "";
        end_date.value = "";
        end_date.min = "";

        syncNoRecordRow(addTableBody, "add-no-medication-row");
    });
}

// ---------------------------------------------------------------------------
// Remove medicine row
// ---------------------------------------------------------------------------

if (addTableBody) {
    addTableBody.addEventListener("click", (e) => {
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
            syncNoRecordRow(addTableBody, "add-no-medication-row");
        }
    });
}

// Show "No record" on initial load
syncNoRecordRow(addTableBody, "add-no-medication-row");

// ---------------------------------------------------------------------------
// IS_FINAL TOGGLE — ADD MODAL
// ---------------------------------------------------------------------------

function applyAddFinalToggleState(isFinal) {
    const hiddenInput = document.getElementById("add_is_final_hidden");
    const warning = document.getElementById("add_is_final_warning");
    const dateWrapper = document.getElementById("add_comeback_wrapper");
    const dateInput = document.getElementById("add_date_of_comeback");
    const requiredStar = document.getElementById("add_comeback_required_star");

    if (!hiddenInput) return;

    hiddenInput.value = isFinal ? "1" : "0";

    if (warning) warning.classList.toggle("d-none", !isFinal);

    if (dateWrapper) {
        dateWrapper.style.opacity = isFinal ? "0.45" : "1";
        dateWrapper.style.pointerEvents = isFinal ? "none" : "auto";
    }

    if (dateInput) dateInput.disabled = isFinal;

    if (requiredStar) requiredStar.style.display = isFinal ? "none" : "inline";
}

document.addEventListener("DOMContentLoaded", function () {
    const addToggle = document.getElementById("add_is_final_toggle");
    if (addToggle) {
        addToggle.addEventListener("change", function () {
            applyAddFinalToggleState(this.checked);
        });
    }
});

// Reset ADD modal when it closes
document
    .getElementById("vaccinationModal")
    ?.addEventListener("hidden.bs.modal", function () {
        const addToggle = document.getElementById("add_is_final_toggle");
        if (addToggle) addToggle.checked = false;
        applyAddFinalToggleState(false);
        const addError = document.getElementById("add_is_final_error");
        if (addError) addError.textContent = "";
    });

// ---------------------------------------------------------------------------
// Save new case record
// ---------------------------------------------------------------------------

const saveBtn = document.getElementById("add-new-record-save-btn");

if (saveBtn) {
    saveBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const id = saveBtn.dataset.bsMedicalId;
        const originalText = saveBtn.innerHTML;

        saveBtn.disabled = true;
        saveBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

        try {
            const form = document.getElementById("add-new-record-form");
            const formData = new FormData(form);

            const response = await fetch(
                `/patient-case/senior-citizen/new-case/${id}`,
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                        Accept: "application/json",
                    },
                    body: formData,
                },
            );

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
                        const isFinalError =
                            document.getElementById("add_is_final_error");
                        if (isFinalError) {
                            isFinalError.textContent = Array.isArray(
                                data.errors.is_final,
                            )
                                ? data.errors.is_final[0]
                                : data.errors.is_final;
                        }
                    }

                    await Swal.fire({
                        title: "Add new Medicine Maintenance Record",
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

                if (typeof Livewire !== "undefined") {
                    Livewire.dispatch("seniorCitizenRefreshTable");
                }

                await Swal.fire({
                    title: "Add new Medicine Maintenance Record",
                    text: capitalizeEachWord(data.message),
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error("Error saving record:", error);

            await Swal.fire({
                title: "Error",
                text: `Failed to save record: ${error.message}`,
                icon: "error",
                confirmButtonColor: "#3085d6",
            });

            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    });
}

// ---------------------------------------------------------------------------
// Utilities
// ---------------------------------------------------------------------------

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
