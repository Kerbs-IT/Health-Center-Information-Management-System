import Swal from "sweetalert2";

const addMedicationBTN = document.getElementById("medication_add_btn");
const tableBody = document.getElementById("medication_table_body");

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

/**
 * Re-check whether the "No record added." row should be visible.
 * Must query fresh every time because innerHTML += recreates DOM nodes.
 */
function syncNoRecordRow() {
    const noRow = document.getElementById("no-medication-row");
    const hasRows = tableBody
        ? tableBody.querySelectorAll(".senior-citizen-maintenance-record")
              .length > 0
        : false;
    if (noRow) noRow.style.display = hasRows ? "none" : "";
}

/** Call via oninput="syncEndDateMin(this.value)" on the start date input. */
window.syncEndDateMin = function (dateVal) {
    const endDate = document.getElementById("maintenance_end_date");
    if (!endDate) return;
    endDate.min = dateVal;
    if (endDate.value && endDate.value < dateVal) {
        endDate.value = "";
    }
};

// ---------------------------------------------------------------------------
// Add medication row
// ---------------------------------------------------------------------------

if (addMedicationBTN) {
    addMedicationBTN.addEventListener("click", () => {
        const medicine = document.getElementById("maintenance_medication");
        const dosage_n_frequency =
            document.getElementById("dosage_n_frequency");
        const quantity = document.getElementById("maintenance_quantity");
        const start_date = document.getElementById("maintenance_start_date");
        const end_date = document.getElementById("maintenance_end_date");

        // Reset all borders first
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

        if (errors.length === 0) {
            // Use insertRow so we never touch innerHTML and never lose the no-record row ref
            const row = tableBody.insertRow(-1);
            row.classList.add("senior-citizen-maintenance-record");

            row.innerHTML = `
                <td>${medicine.value}</td>
                <td>${dosage_n_frequency.value}</td>
                <td>${qtyVal}</td>
                <td>${start_date.value}</td>
                <td>${end_date.value}</td>
                <td class="align-middle text-center">
                    <div class="delete-icon maintenance-remove-icon d-flex align-items-center justify-self-center w-100 h-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" width="20" height="20" viewBox="0 0 448 512">
                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/>
                        </svg>
                    </div>
                </td>
                <input type="hidden" name="medicines[]"              value="${medicine.value}">
                <input type="hidden" name="dosage_n_frequencies[]"   value="${dosage_n_frequency.value}">
                <input type="hidden" name="maintenance_quantity[]"   value="${qtyVal}">
                <input type="hidden" name="start_date[]"             value="${start_date.value}">
                <input type="hidden" name="end_date[]"               value="${end_date.value}">
            `;

            // Clear values and constraints
            medicine.value = "";
            dosage_n_frequency.value = "";
            quantity.value = "";
            start_date.value = "";
            end_date.value = "";
            end_date.min = "";

            syncNoRecordRow();
        } else {
            Swal.fire({
                title: "Missing Information",
                html: errors.map((e) => `<div>${e}</div>`).join(""),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    });
}

// ---------------------------------------------------------------------------
// Remove medication row
// ---------------------------------------------------------------------------

if (tableBody) {
    tableBody.addEventListener("click", (e) => {
        const row = e.target.closest(".senior-citizen-maintenance-record");
        if (row && e.target.closest(".maintenance-remove-icon")) {
            row.remove();
            syncNoRecordRow();
        }
    });
}

// Show "No record added." on initial load
syncNoRecordRow();

// ---------------------------------------------------------------------------
// Save record
// ---------------------------------------------------------------------------

const seniorCitizenBtn = document.getElementById(
    "senior_citizen_save_record_btn",
);

if (seniorCitizenBtn) {
    seniorCitizenBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const originalHTML = seniorCitizenBtn.innerHTML;
        seniorCitizenBtn.disabled = true;
        seniorCitizenBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Submitting...
        `;

        const restoreBtn = () => {
            seniorCitizenBtn.disabled = false;
            seniorCitizenBtn.innerHTML = originalHTML;
        };

        try {
            const handledBySelect = document.getElementById("handled_by");
            const handledByBackup =
                document.getElementById("handled_by_backup");
            if (handledBySelect && handledByBackup) {
                handledByBackup.value = handledBySelect.value;
            }

            const form = document.getElementById("add-patient-form");
            const formData = new FormData(form);

            const response = await fetch(
                "/patient-record/add/senior-citizen-record",
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

            if (response.ok) {
                errorElements.forEach((el) => (el.textContent = ""));

                await Swal.fire({
                    title: "Senior Citizen Patient",
                    text: "Patient is Successfully added.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (
                            typeof window.clearPatientRecordSelection ===
                            "function"
                        ) {
                            window.clearPatientRecordSelection();
                        }
                        form.reset();
                        // Remove all medication rows then restore the no-record row
                        tableBody
                            .querySelectorAll(
                                ".senior-citizen-maintenance-record",
                            )
                            .forEach((r) => r.remove());
                        syncNoRecordRow();
                        window.currentStep = 1;
                        window.showStep(window.currentStep);
                    }
                });
            } else {
                errorElements.forEach((el) => (el.textContent = ""));

                if (data.errors) {
                    Object.entries(data.errors).forEach(([key, value]) => {
                        const el = document.getElementById(`${key}_error`);
                        if (el)
                            el.textContent = Array.isArray(value)
                                ? value[0]
                                : value;
                    });

                    const message = Object.values(data.errors)
                        .flat()
                        .join("\n");

                    await Swal.fire({
                        title: "Senior Citizen Patient",
                        text: capitalizeEachWord(message),
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK",
                    });
                } else {
                    await Swal.fire({
                        title: "Senior Citizen Patient",
                        text: "An unexpected error occurred.",
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK",
                    });
                }
            }
        } catch (err) {
            console.error("Submission error:", err);
            await Swal.fire({
                title: "Error",
                text: "A network or server error occurred. Please try again.",
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        } finally {
            restoreBtn();
        }
    });
}

// ---------------------------------------------------------------------------
// Utilities
// ---------------------------------------------------------------------------

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
