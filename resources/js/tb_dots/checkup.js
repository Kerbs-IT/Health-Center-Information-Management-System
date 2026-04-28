import Swal from "sweetalert2";
import { vitalSignInputMask } from "../vitalSign";

// ============================================================================
// IS_FINAL TOGGLE — ADD MODAL
// ============================================================================

document.addEventListener("DOMContentLoaded", function () {
    // Add modal toggle
    const addToggle = document.getElementById("add_is_final_toggle");
    if (addToggle) {
        addToggle.addEventListener("change", function () {
            const warning = document.getElementById("add_is_final_warning");
            const hiddenInput = document.getElementById("add_is_final_hidden");

            if (warning) warning.classList.toggle("d-none", !this.checked);
            if (hiddenInput) hiddenInput.value = this.checked ? "1" : "0";
        });
    }

    // Edit modal toggle
    const editToggle = document.getElementById("edit_is_final_toggle");
    if (editToggle) {
        editToggle.addEventListener("change", function () {
            applyEditFinalToggleState(this.checked, false);
        });
    }
});

// ============================================================================
// IS_FINAL TOGGLE — EDIT MODAL HELPER
// ============================================================================

function applyEditFinalToggleState(isFinal, lockToggle = false) {
    const toggle = document.getElementById("edit_is_final_toggle");
    const hiddenInput = document.getElementById("edit_is_final_hidden");
    const warning = document.getElementById("edit_is_final_warning");

    if (!toggle || !hiddenInput) return;

    toggle.checked = isFinal;
    hiddenInput.value = isFinal ? "1" : "0";

    if (warning) warning.classList.toggle("d-none", !isFinal);

    // Lock everything if the case is already final (any record in the case)
    if (lockToggle) {
        toggle.disabled = true;

        const form = document.getElementById("edit-checkup-form");
        if (form) {
            form.querySelectorAll("input, select, textarea").forEach((el) => {
                el.disabled = true;
            });
        }

        const saveBtn = document.getElementById("edit-checkup-save-btn");
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

// Reset EDIT modal when it closes
document
    .getElementById("edit_tb_dots_checkup_Modal")
    ?.addEventListener("hidden.bs.modal", function () {
        const form = document.getElementById("edit-checkup-form");
        if (form) {
            form.querySelectorAll("input, select, textarea").forEach((el) => {
                el.disabled = false;
                el.removeAttribute("readonly");
            });
        }

        const saveBtn = document.getElementById("edit-checkup-save-btn");
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = "Save Changes";
        }

        applyEditFinalToggleState(false, false);

        const isFinalError = document.getElementById("edit_is_final_error");
        if (isFinalError) isFinalError.textContent = "";
    });

// Reset ADD modal when it closes
document
    .getElementById("tbDotsAddCheckUpModal")
    ?.addEventListener("hidden.bs.modal", function () {
        const addToggle = document.getElementById("add_is_final_toggle");
        const addHidden = document.getElementById("add_is_final_hidden");
        const addWarning = document.getElementById("add_is_final_warning");
        const addError = document.getElementById("add_is_final_error");

        if (addToggle) addToggle.checked = false;
        if (addHidden) addHidden.value = "0";
        if (addWarning) addWarning.classList.add("d-none");
        if (addError) addError.textContent = "";
    });

// ============================================================================
// ADD CHECKUP BUTTON — reset form
// ============================================================================

const saveBtn = document.getElementById("add-check-up-save-btn");
const addCheckup = document.getElementById("add-check-up-record-btn");

if (addCheckup) {
    addCheckup.addEventListener("click", () => {
        const errors = document.querySelectorAll(".error-text");
        if (errors) {
            errors.forEach((error) => (error.innerHTML = ""));
        }
        const form = document.getElementById("add-check-up-form");
        form.reset();

        // Also reset the toggle manually since form.reset() won't update hidden input
        const addToggle = document.getElementById("add_is_final_toggle");
        const addHidden = document.getElementById("add_is_final_hidden");
        const addWarning = document.getElementById("add_is_final_warning");
        if (addToggle) addToggle.checked = false;
        if (addHidden) addHidden.value = "0";
        if (addWarning) addWarning.classList.add("d-none");

        const checkup_blood_pressure = document.getElementById(
            "add_checkup_blood_pressure",
        );
        const checkup_temperature = document.getElementById(
            "add_checkup_temperature",
        );
        const checkup_respiratory_rate = document.getElementById(
            "add_checkup_respiratory_rate",
        );
        const checkup_pulse_rate = document.getElementById(
            "add_checkup_pulse_rate",
        );
        const checkup_height = document.getElementById("add_checkup_height");
        const checkup_weight = document.getElementById("add_checkup_weight");

        if (
            checkup_blood_pressure &&
            checkup_temperature &&
            checkup_height &&
            checkup_weight &&
            checkup_respiratory_rate &&
            checkup_pulse_rate
        ) {
            vitalSignInputMask(
                checkup_blood_pressure,
                checkup_temperature,
                checkup_pulse_rate,
                checkup_respiratory_rate,
                checkup_height,
                checkup_weight,
            );
        }
    });
}

// ============================================================================
// ADD CHECKUP SAVE
// ============================================================================

if (saveBtn) {
    saveBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const id = saveBtn.dataset.medicalId;
        const originalText = saveBtn.innerHTML;

        saveBtn.disabled = true;
        saveBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

        try {
            const form = document.getElementById("add-check-up-form");
            const formData = new FormData(form);

            const response = await fetch(
                `/patient-record/add/check-up/tb-dots/${id}`,
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

                    // is_final specific error
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
                }

                Swal.fire({
                    title: "TB-Dots Check-Up Record",
                    html: Object.values(data.errors ?? {})
                        .flat()
                        .map((e) => `<div>${e}</div>`)
                        .join(""),
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            } else {
                errorElements.forEach((el) => (el.textContent = ""));

                if (typeof Livewire !== "undefined") {
                    Livewire.dispatch("tbRefreshTable");
                }

                Swal.fire({
                    title: "TB-Dots Check-Up Record",
                    text: capitalizeEachWord(data.message),
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                }).then((result) => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;

                    if (result.isConfirmed) {
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("tbDotsAddCheckUpModal"),
                        );
                        if (modal) modal.hide();
                    }
                });
            }
        } catch (error) {
            console.error("Error saving checkup:", error);

            Swal.fire({
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

// ============================================================================
// VIEW CHECKUP
// ============================================================================

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".tb-dots-view-check-up");
    if (!viewBtn) return;
    const id = viewBtn.dataset.caseId ?? null;

    const response = await fetch(`/patient-record/view-check-up/tb-dots/${id}`);
    const data = await response.json();

    if (!response.ok) return;

    Object.entries(data.checkUpInfo).forEach(([key, value]) => {
        if (key === "date_of_comeback") {
            const el = document.getElementById(`view_${key}`);
            if (el) {
                el.innerHTML = new Date(value).toISOString().split("T")[0];
            }
        }
        const el = document.getElementById(`view_checkup_${key}`);
        if (el) el.innerHTML = value ?? "N/A";
    });
});

// ============================================================================
// EDIT CHECKUP — load data
// ============================================================================

const editSaveBtn = document.getElementById("edit-checkup-save-btn");

document.addEventListener("click", async (e) => {
    const editCheckUpBtn = e.target.closest(".tb-dots-edit-check-up");
    if (!editCheckUpBtn) return;

    const id = editCheckUpBtn.dataset.caseId ?? null;
    if (editSaveBtn) editSaveBtn.dataset.caseId = id;

    document
        .querySelectorAll(".error-text")
        .forEach((el) => (el.innerHTML = ""));

    const response = await fetch(`/patient-record/view-check-up/tb-dots/${id}`);
    const data = await response.json();

    if (!response.ok) return;

    // Populate fields
    Object.entries(data.checkUpInfo).forEach(([key, value]) => {
        const el = document.getElementById(`edit_checkup_${key}`);
        if (el) el.value = value ?? "";

        if (key === "date_of_comeback" && value != null) {
            const dateEl = document.getElementById(`edit_${key}`);
            if (dateEl) {
                dateEl.value = new Date(value).toISOString().split("T")[0];
            }
        }
    });

    // Apply final toggle state — ready for when controller returns these flags
    const caseIsFinal = !!data.case_is_final;
    const thisRecordIsFinal = !!data.this_record_is_final;
    applyEditFinalToggleState(thisRecordIsFinal, caseIsFinal);

    // Vital sign masks
    const checkup_blood_pressure = document.getElementById(
        "edit_checkup_blood_pressure",
    );
    const checkup_temperature = document.getElementById(
        "edit_checkup_temperature",
    );
    const checkup_respiratory_rate = document.getElementById(
        "edit_checkup_respiratory_rate",
    );
    const checkup_pulse_rate = document.getElementById(
        "edit_checkup_pulse_rate",
    );
    const checkup_height = document.getElementById("edit_checkup_height");
    const checkup_weight = document.getElementById("edit_checkup_weight");

    if (
        checkup_blood_pressure &&
        checkup_temperature &&
        checkup_height &&
        checkup_weight &&
        checkup_respiratory_rate &&
        checkup_pulse_rate
    ) {
        vitalSignInputMask(
            checkup_blood_pressure,
            checkup_temperature,
            checkup_pulse_rate,
            checkup_respiratory_rate,
            checkup_height,
            checkup_weight,
        );
    }
});

// ============================================================================
// EDIT CHECKUP SAVE
// ============================================================================

if (editSaveBtn) {
    editSaveBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const id = editSaveBtn.dataset.caseId;
        const originalText = editSaveBtn.innerHTML;

        editSaveBtn.disabled = true;
        editSaveBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

        try {
            const form = document.getElementById("edit-checkup-form");
            const formData = new FormData(form);

            const response = await fetch(
                `/patient-record/tb-dots/update-checkup/${id}`,
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

                    // is_final specific error
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
                }

                Swal.fire({
                    title: "Update TB-Dots Check-Up Record",
                    html: Object.values(data.errors ?? {})
                        .flat()
                        .map((e) => `<div>${e}</div>`)
                        .join(""),
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                editSaveBtn.disabled = false;
                editSaveBtn.innerHTML = originalText;
            } else {
                errorElements.forEach((el) => (el.textContent = ""));

                Swal.fire({
                    title: "Update TB-Dots Check-Up Record",
                    text: capitalizeEachWord(data.message),
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                }).then((result) => {
                    editSaveBtn.disabled = false;
                    editSaveBtn.innerHTML = originalText;

                    if (result.isConfirmed) {
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById(
                                "edit_tb_dots_checkup_Modal",
                            ),
                        );
                        if (modal) modal.hide();
                    }
                });
                // Dispatch refresh so Livewire re-checks hasFinalRecord
                if (typeof Livewire !== "undefined") {
                    Livewire.dispatch("tbRefreshTable");
                }
            }
        } catch (error) {
            console.error("Error updating checkup:", error);

            Swal.fire({
                title: "Error",
                text: `Failed to update record: ${error.message}`,
                icon: "error",
                confirmButtonColor: "#3085d6",
            });

            editSaveBtn.disabled = false;
            editSaveBtn.innerHTML = originalText;
        }
    });
}

// ============================================================================
// ARCHIVE CHECKUP
// ============================================================================

document.addEventListener("click", async (e) => {
    const archiveBtn = e.target.closest(".tb-check-up-delete-btn");
    if (!archiveBtn) return;
    const id = archiveBtn.dataset.caseId;

    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "The TB-Dots Check-up Record will be moved to archived status.",
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
            `/patient-record/tb-dots/checkup/delete/${id}`,
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
            Livewire.dispatch("tbRefreshTable");
        }

        const row = archiveBtn.closest("tr");
        if (row) row.remove();

        Swal.fire({
            title: "Archived!",
            text: "The TB-Dots Check-up Record has been archived.",
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

// ============================================================================
// HELPERS
// ============================================================================

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
