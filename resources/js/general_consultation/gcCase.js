import { fetchHealthworkers } from "../patient/healthWorkerList";
import { vitalSignInputMask } from "../vitalSign";
import Swal from "sweetalert2";

// ============================================================================
// VIEW CASE RECORD
// ============================================================================
document.addEventListener("click", async (e) => {
    const viewIcon = e.target.closest(".view-gc-case-info");
    if (!viewIcon) return;

    const caseId = viewIcon.dataset.bsCaseId;
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        return;
    }

    try {
        const response = await fetch(`/gc-case/record/${caseId}`);
        const data = await response.json();

        document.getElementById("view-patient-name").innerHTML =
            data.patient_name ?? "N/A";
        document.getElementById("view-date-of-consultation").innerHTML = data
            .gcCase.date_of_consultation
            ? new Date(data.gcCase.date_of_consultation).toLocaleDateString(
                  "en-US",
                  { month: "short", day: "numeric", year: "numeric" },
              )
            : "N/A";
        document.getElementById("view-symptoms").innerHTML =
            data.gcCase.symptoms ?? "N/A";
        document.getElementById("view-blood-pressure").innerHTML =
            data.gcCase.blood_pressure ?? "N/A";
        document.getElementById("view-temperature").innerHTML = data.gcCase
            .temperature
            ? `${data.gcCase.temperature} °C`
            : "N/A";
        document.getElementById("view-pulse-rate").innerHTML =
            data.gcCase.pulse_rate ?? "N/A";
        document.getElementById("view-respiratory-rate").innerHTML =
            data.gcCase.respiratory_rate ?? "N/A";
        document.getElementById("view-height").innerHTML = data.gcCase.height
            ? `${data.gcCase.height} cm`
            : "N/A";
        document.getElementById("view-weight").innerHTML = data.gcCase.weight
            ? `${data.gcCase.weight} kg`
            : "N/A";
        document.getElementById("view-diagnosis").innerHTML =
            data.gcCase.diagnosis ?? "N/A";
        document.getElementById("view-treatment-plan").innerHTML =
            data.gcCase.treatment_plan ?? "N/A";
    } catch (error) {
        console.error("Error viewing case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to view record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// ============================================================================
// ADD CASE RECORD
// ============================================================================
const addCaseBtn = document.getElementById("add-gc-case-record-btn");
const addCaseForm = document.getElementById("add-gc-case-form");

if (addCaseBtn) {
    addCaseBtn.addEventListener("click", (e) => {
        e.preventDefault();

        // reset form and errors
        addCaseForm.reset();
        document
            .querySelectorAll(".add_gc_case_record_errors")
            .forEach((el) => (el.innerHTML = ""));

        // set today's date
        const dateCon = document.getElementById("add_date_of_consultation");
        if (dateCon) {
            dateCon.value = new Date().toISOString().split("T")[0];
        }

        // populate health worker dropdown
        const selectedHealthWorkerId = addCaseBtn.dataset.healthWorkerId;
        const hiddenHandledBy = document.getElementById(
            "hidden_add_handled_by",
        );
        const addHealthWorkerDropDown = document.getElementById(
            "dissabled_add_handled_by",
        );

        if (hiddenHandledBy) {
            hiddenHandledBy.value = selectedHealthWorkerId;
        }

        if (addHealthWorkerDropDown) {
            addHealthWorkerDropDown.disabled = true;
            addHealthWorkerDropDown.innerHTML =
                '<option value="" selected disabled>Select the Health Worker</option>';
            fetchHealthworkers().then((result) => {
                result.healthWorkers.forEach((worker) => {
                    addHealthWorkerDropDown.innerHTML += `<option value="${worker.id}" ${selectedHealthWorkerId == worker.id ? "selected" : ""}>${worker.staff.full_name}</option>`;
                });
            });
        }
    });
}

const addGcCaseSaveBtn = document.getElementById("add_gc_case_save_btn");

if (addGcCaseSaveBtn) {
    addGcCaseSaveBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        // disable button and show spinner
        const originalHTML = addGcCaseSaveBtn.innerHTML;
        addGcCaseSaveBtn.disabled = true;
        addGcCaseSaveBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Saving...
        `;

        const restoreBtn = () => {
            addGcCaseSaveBtn.disabled = false;
            addGcCaseSaveBtn.innerHTML = originalHTML;
        };

        try {
            const caseId = addGcCaseSaveBtn.dataset.bsCaseId;
            const formData = new FormData(addCaseForm);

            const response = await fetch(`/add-gc-case/${caseId}`, {
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

            if (!response.ok) {
                // clear errors
                document
                    .querySelectorAll(".add_gc_case_record_errors")
                    .forEach((el) => (el.innerHTML = ""));

                // populate inline errors
                if (data.errors && typeof data.errors === "object") {
                    Object.entries(data.errors).forEach(([key, value]) => {
                        const el = document.getElementById(`${key}_error`);
                        if (el)
                            el.innerHTML = Array.isArray(value)
                                ? value.join(", ")
                                : value;
                    });
                }

                // sweetalert error
                const errorMessage =
                    typeof data.errors === "object"
                        ? Object.values(data.errors).flat().join("<br>")
                        : (data.errors ?? "An unexpected error occurred.");

                Swal.fire({
                    title: "Add Consultation Record",
                    html: capitalizeEachWord(errorMessage),
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                return;
            }

            // success
            document
                .querySelectorAll(".add_gc_case_record_errors")
                .forEach((el) => (el.innerHTML = ""));
            Livewire.dispatch("refreshTable");

            Swal.fire({
                title: "Add Consultation Record",
                text: data.message,
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("gcModal"),
                    );
                    modal.hide();
                    addCaseForm.reset();
                }
            });
        } catch (error) {
            console.error("Submission error:", error);
        } finally {
            restoreBtn();
        }
    });
}

// ============================================================================
// EDIT CASE RECORD — populate modal on edit btn click
// ============================================================================
document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".gc-case-edit-btn");
    if (!editBtn) return;

    const caseId = editBtn.dataset.bsCaseId;
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        return;
    }

    // reset errors
    document
        .querySelectorAll(".error-text")
        .forEach((el) => (el.innerHTML = ""));

    try {
        const response = await fetch(`/gc-case/record/${caseId}`);
        const data = await response.json();
        const gc = data.gcCase;

        // set hidden case record id
        document.getElementById("gc_case_record_id").value = caseId;

        // patient name
        document.getElementById("edit_patient_name").value =
            data.patient_name ?? "";

        // date of consultation
        document.getElementById("edit_date_of_consultation").value =
            gc.date_of_consultation ?? "";

        // symptoms, diagnosis, treatment
        document.getElementById("edit_symptoms").value = gc.symptoms ?? "";
        document.getElementById("edit_diagnosis").value = gc.diagnosis ?? "";
        document.getElementById("edit_treatment_plan").value =
            gc.treatment_plan ?? "";

        // vitals
        document.getElementById("edit_blood_pressure").value =
            gc.blood_pressure ?? "";
        document.getElementById("edit_temperature").value =
            gc.temperature ?? "";
        document.getElementById("edit_pulse_rate").value = gc.pulse_rate ?? "";
        document.getElementById("edit_respiratory_rate").value =
            gc.respiratory_rate ?? "";
        document.getElementById("edit_height").value = gc.height ?? "";
        document.getElementById("edit_weight").value = gc.weight ?? "";

        // apply input mask to edit vital fields
        vitalSignInputMask(
            document.getElementById("edit_blood_pressure"),
            document.getElementById("edit_temperature"),
            document.getElementById("edit_pulse_rate"),
            document.getElementById("edit_respiratory_rate"),
            document.getElementById("edit_height"),
            document.getElementById("edit_weight"),
        );

        // populate health worker dropdown
        const healthWorkerDropdown =
            document.getElementById("update_handled_by");
        if (healthWorkerDropdown) {
            healthWorkerDropdown.innerHTML =
                '<option value="" selected disabled>Select the Health Worker</option>';
            fetchHealthworkers().then((result) => {
                result.healthWorkers.forEach((worker) => {
                    healthWorkerDropdown.innerHTML += `<option value="${worker.id}" ${gc.health_worker_id == worker.id ? "selected" : ""}>${worker.staff.full_name}</option>`;
                });
            });
        }
    } catch (error) {
        console.error("Error loading edit case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to load record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// ============================================================================
// UPDATE CASE RECORD — submit edited form
// ============================================================================
const updateGcCaseSaveBtn = document.getElementById("update_gc_case_save_btn");

if (updateGcCaseSaveBtn) {
    updateGcCaseSaveBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        // disable button and show spinner
        const originalHTML = updateGcCaseSaveBtn.innerHTML;
        updateGcCaseSaveBtn.disabled = true;
        updateGcCaseSaveBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Saving...
        `;

        const restoreBtn = () => {
            updateGcCaseSaveBtn.disabled = false;
            updateGcCaseSaveBtn.innerHTML = originalHTML;
        };

        try {
            const caseId = document.getElementById("gc_case_record_id").value;
            const formData = new FormData(
                document.getElementById("edit-gc-case-form"),
            );

            const response = await fetch(`/update-gc-case/${caseId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    "X-HTTP-Method-Override": "PUT",
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                // clear previous errors
                document
                    .querySelectorAll(".error-text")
                    .forEach((el) => (el.innerHTML = ""));

                // populate inline errors
                if (data.errors && typeof data.errors === "object") {
                    Object.entries(data.errors).forEach(([key, value]) => {
                        const errorId = key.startsWith("update_")
                            ? `${key}_error`
                            : `update_${key}_error`;
                        const el = document.getElementById(errorId);
                        if (el) {
                            el.innerHTML = Array.isArray(value)
                                ? value.join(", ")
                                : value;
                        }
                    });
                }

                // sweetalert error
                const errorMessage =
                    typeof data.errors === "object"
                        ? Object.values(data.errors).flat().join("<br>")
                        : (data.errors ?? "An unexpected error occurred.");

                Swal.fire({
                    title: "Update Consultation Record",
                    html: capitalizeEachWord(errorMessage),
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                restoreBtn();
                return;
            }

            // success
            document
                .querySelectorAll(".error-text")
                .forEach((el) => (el.innerHTML = ""));

            // Refresh Livewire table
            Livewire.dispatch("refreshTable");

            Swal.fire({
                title: "Update Consultation Record",
                text: data.message,
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    // populate viewUpdateGcRecordModal
                    document.getElementById(
                        "update-view-patient-name",
                    ).innerHTML =
                        document.getElementById("edit_patient_name").value;
                    document.getElementById(
                        "update-view-date-of-consultation",
                    ).innerHTML = new Date(
                        document.getElementById("edit_date_of_consultation")
                            .value,
                    ).toLocaleDateString("en-US", {
                        month: "short",
                        day: "numeric",
                        year: "numeric",
                    });
                    document.getElementById("update-view-symptoms").innerHTML =
                        document.getElementById("edit_symptoms").value;
                    document.getElementById(
                        "update-view-blood-pressure",
                    ).innerHTML =
                        document.getElementById("edit_blood_pressure").value ||
                        "N/A";
                    document.getElementById(
                        "update-view-temperature",
                    ).innerHTML = document.getElementById("edit_temperature")
                        .value
                        ? `${document.getElementById("edit_temperature").value} °C`
                        : "N/A";
                    document.getElementById(
                        "update-view-pulse-rate",
                    ).innerHTML =
                        document.getElementById("edit_pulse_rate").value ||
                        "N/A";
                    document.getElementById(
                        "update-view-respiratory-rate",
                    ).innerHTML =
                        document.getElementById("edit_respiratory_rate")
                            .value || "N/A";
                    document.getElementById("update-view-height").innerHTML =
                        document.getElementById("edit_height").value
                            ? `${document.getElementById("edit_height").value} cm`
                            : "N/A";
                    document.getElementById("update-view-weight").innerHTML =
                        document.getElementById("edit_weight").value
                            ? `${document.getElementById("edit_weight").value} kg`
                            : "N/A";
                    document.getElementById("update-view-diagnosis").innerHTML =
                        document.getElementById("edit_diagnosis").value;
                    document.getElementById(
                        "update-view-treatment-plan",
                    ).innerHTML = document.getElementById(
                        "edit_treatment_plan",
                    ).value;

                    // close edit modal, open view update modal
                    bootstrap.Modal.getInstance(
                        document.getElementById("editGcModal"),
                    ).hide();
                    new bootstrap.Modal(
                        document.getElementById("viewUpdateGcRecordModal"),
                    ).show();
                }
            });
        } catch (error) {
            console.error("Update error:", error);
            Swal.fire({
                title: "Error",
                text: `An unexpected error occurred: ${error.message}`,
                icon: "error",
                confirmButtonColor: "#3085d6",
            });
        } finally {
            restoreBtn();
        }
    });
}




// ============================================================================
// ARCHIVE CASE RECORD
// ============================================================================
document.addEventListener("click", async (e) => {
    const archiveBtn = e.target.closest(".archive-gc-record-icon");
    if (!archiveBtn) return;

    const caseId = archiveBtn.dataset.bsCaseId;
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "This consultation record will be moved to archived status.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, archive it!",
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch("archiveRecord", { recordId: parseInt(caseId) });

            Swal.fire({
                icon: "success",
                title: "Archived!",
                text: "Consultation record has been archived successfully.",
                timer: 1500,
                showConfirmButton: false,
            });
        }
    });
});

// ============================================================================
// HELPER
// ============================================================================
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
