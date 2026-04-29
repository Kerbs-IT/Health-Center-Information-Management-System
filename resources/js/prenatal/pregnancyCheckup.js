import Swal from "sweetalert2";
import { vitalSignInputMask } from "../vitalSign";

// ============================================================================
// VIEW PREGNANCY CHECKUP HANDLER
// ============================================================================

document.addEventListener("click", async function (e) {
    const viewBtn = e.target.closest(".viewPregnancyCheckupBtn");
    if (!viewBtn) return;

    e.preventDefault();
    e.stopPropagation();

    const checkupId = viewBtn.dataset.checkupId;

    if (!checkupId || checkupId === "undefined" || checkupId === "null") {
        console.error("Invalid checkup ID:", checkupId);
        showErrorNotification("Unable to load checkup: Invalid ID");
        return;
    }

    showLoadingModal();

    try {
        const response = await fetch(
            `/prenatal/view-pregnancy-checkup-info/${checkupId}`,
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

        clearCheckupModalData();

        if (
            data.pregnancy_checkup_info &&
            typeof data.pregnancy_checkup_info === "object"
        ) {
            populateCheckupInfo(data.pregnancy_checkup_info);
        }

        if (data.healthWorker && typeof data.healthWorker === "object") {
            populateHealthWorkerInfo(data.healthWorker);
        }

        hideLoadingModal();
        openCheckupModal();
    } catch (error) {
        console.error("Error fetching checkup data:", error);
        hideLoadingModal();
        showErrorNotification(`Failed to load checkup data: ${error.message}`);
    }
});

// ============================================================================
// VIEW MODAL HELPERS
// ============================================================================

function safeSetContent(elementId, value, defaultValue = "N/A") {
    const element = document.getElementById(elementId);
    if (!element) return false;

    if (value === null || value === undefined || value === "") {
        element.innerHTML = defaultValue;
        return true;
    }

    const safeValue = String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

    element.innerHTML = safeValue;
    return true;
}

function formatTime(timeString) {
    if (!timeString || typeof timeString !== "string") return "N/A";

    try {
        const parts = timeString.split(":");
        if (parts.length < 2) return "N/A";

        let [hours, minutes] = parts;
        hours = parseInt(hours, 10);
        minutes = parseInt(minutes, 10);

        if (isNaN(hours) || isNaN(minutes)) return "N/A";

        const ampm = hours >= 12 ? "PM" : "AM";
        hours = hours % 12 || 12;
        minutes = minutes.toString().padStart(2, "0");

        return `${hours}:${minutes} ${ampm}`;
    } catch (error) {
        console.error("Error formatting time:", error);
        return "N/A";
    }
}

function populateCheckupInfo(checkupInfo) {
    if (!checkupInfo || typeof checkupInfo !== "object") return;

    Object.entries(checkupInfo).forEach(([key, value]) => {
        try {
            if (key === "check_up_time") {
                safeSetContent(key, formatTime(value));
                return;
            }
            if (key === "patient_name") {
                safeSetContent("patient_name", value);
                safeSetContent("checkup_patient_name", value);
                return;
            }
            if (key === "date_of_comeback" && value) {
                try {
                    const raw = String(value);
                    const dateStr =
                        raw.includes("T") || raw.includes(" ")
                            ? new Date(
                                  new Date(raw).getTime() + 8 * 60 * 60 * 1000,
                              )
                                  .toISOString()
                                  .split("T")[0]
                            : raw.split("T")[0];
                    const el = document.getElementById("view_date_of_comeback");
                    if (el) el.innerHTML = dateStr;
                } catch (_) {}
                return;
            }
            if (key === "check_up_blood_pressure") {
                safeSetContent(`view_${key}`, value ? `${value}` : null);
                return;
            }
            if (key === "check_up_temperature") {
                safeSetContent(`view_${key}`, value ? `${value}°C` : null);
                return;
            }
            if (key === "check_up_pulse_rate") {
                safeSetContent(`view_${key}`, value ? `${value} bpm` : null);
                return;
            }
            if (key === "check_up_respiratory_rate") {
                safeSetContent(
                    `view_${key}`,
                    value ? `${value} breaths/min` : null,
                );
                return;
            }
            if (key === "check_up_height") {
                safeSetContent(`view_${key}`, value ? `${value} cm` : null);
                return;
            }
            if (key === "check_up_weight") {
                safeSetContent(`view_${key}`, value ? `${value} kg` : null);
                return;
            }
            safeSetContent(key, value);
        } catch (error) {
            console.error(`Error setting field ${key}:`, error);
        }
    });
}

function populateHealthWorkerInfo(healthWorker) {
    if (!healthWorker || typeof healthWorker !== "object") {
        safeSetContent("health_worker_name", null);
        return;
    }
    const fullName =
        healthWorker.full_name ||
        `${healthWorker.first_name || ""} ${healthWorker.last_name || ""}`.trim() ||
        "N/A";
    safeSetContent("health_worker_name", fullName);
}

function clearCheckupModalData() {
    const fieldIds = [
        "check_up_time",
        "patient_name",
        "checkup_patient_name",
        "health_worker_name",
        "check_up_blood_pressure",
        "check_up_weight",
        "check_up_height",
        "check_up_temperature",
        "check_up_pulse_rate",
        "check_up_respiratory_rate",
        "nutritional_status",
        "laboratory_tests_done",
        "hemoglobin_count",
        "urinalysis",
        "complete_blood_count",
        "stool_examination",
        "acetic_acid_wash_test",
        "tetanus_toxoid_vaccination",
        "date_of_visit",
        "age_of_gestation",
        "blood_pressure_systolic",
        "blood_pressure_diastolic",
        "remarks",
        "next_visit_date",
    ];
    fieldIds.forEach((id) => {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
            el.value = "";
        } else {
            el.innerHTML = "";
        }
    });
}

function openCheckupModal() {
    const modalElement = document.getElementById("pregnancyCheckUpModal");
    if (!modalElement) {
        showErrorNotification("Unable to open modal");
        return;
    }
    try {
        bootstrap.Modal.getOrCreateInstance(modalElement).show();
    } catch (error) {
        showErrorNotification("Error opening modal");
    }
}

function showLoadingModal() {
    const modalBody = document
        .getElementById("pregnancyCheckUpModal")
        ?.querySelector(".modal-body");
    if (modalBody) {
        modalBody.style.opacity = "0.5";
        modalBody.style.pointerEvents = "none";
    }
}

function hideLoadingModal() {
    const modalBody = document
        .getElementById("pregnancyCheckUpModal")
        ?.querySelector(".modal-body");
    if (modalBody) {
        modalBody.style.opacity = "1";
        modalBody.style.pointerEvents = "auto";
    }
}

function showErrorNotification(message) {
    alert(message);
}

// Clear view modal on close
document
    .getElementById("pregnancyCheckUpModal")
    ?.addEventListener("hidden.bs.modal", function () {
        clearCheckupModalData();
    });

// ============================================================================
// IS_FINAL TOGGLE — EDIT MODAL
// ============================================================================

/**
 * @param {boolean} isFinal
 * @param {boolean} lockToggle — pass true when the record is already saved
 *                               as final (disables all fields, can't undo)
 */
function applyFinalToggleState(isFinal, lockToggle = false) {
    const toggle = document.getElementById("edit_is_final_toggle");
    const hiddenInput = document.getElementById("edit_is_final_hidden");
    const warning = document.getElementById("edit_is_final_warning");
    const dateWrapper = document.getElementById("edit_comeback_wrapper");
    const dateInput = document.getElementById("edit_date_of_comeback");

    if (!toggle || !hiddenInput) return;

    toggle.checked = isFinal;
    hiddenInput.value = isFinal ? "1" : "0";

    if (warning) warning.classList.toggle("d-none", !isFinal);

    // Dim wrapper so date looks read-only visually
    if (dateWrapper) {
        dateWrapper.style.opacity = isFinal ? "0.45" : "1";
        dateWrapper.style.pointerEvents = isFinal ? "none" : "auto";
    }

    // readonly keeps the value visible; disabled would wipe it from display
    if (dateInput) {
        if (isFinal) {
            dateInput.setAttribute("readonly", "readonly");
        } else {
            dateInput.removeAttribute("readonly");
        }
    }

    if (lockToggle) {
        toggle.disabled = true;

        const form = document.getElementById("edit-check-up-form");
        if (form) {
            form.querySelectorAll("input, select, textarea").forEach((el) => {
                if (el.id === "edit_date_of_comeback") {
                    el.setAttribute("readonly", "readonly");
                    return;
                }
                el.disabled = true;
            });
        }

        const saveBtn = document.getElementById("edit-check-up-save-btn");
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

// ============================================================================
// IS_FINAL TOGGLE — ADD MODAL
// ============================================================================

function applyAddFinalToggleState(isFinal) {
    const hiddenInput = document.getElementById("add_is_final_hidden");
    const warning = document.getElementById("add_is_final_warning");
    const dateWrapper = document.getElementById("add_comeback_wrapper");
    const dateInput = document.getElementById("date_of_comeback");
    const requiredStar = document.getElementById("add_comeback_required_star");

    if (!hiddenInput) return;

    hiddenInput.value = isFinal ? "1" : "0";

    if (warning) warning.classList.toggle("d-none", !isFinal);

    if (dateWrapper) {
        dateWrapper.style.opacity = isFinal ? "0.45" : "1";
        dateWrapper.style.pointerEvents = isFinal ? "none" : "auto";
    }

    if (dateInput) {
        dateInput.disabled = isFinal;
    }

    if (requiredStar) {
        requiredStar.style.display = isFinal ? "none" : "inline";
    }
}

// Wire up both toggles after DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    const addToggle = document.getElementById("add_is_final_toggle");
    if (addToggle) {
        addToggle.addEventListener("change", function () {
            applyAddFinalToggleState(this.checked);
        });
    }

    const editToggle = document.getElementById("edit_is_final_toggle");
    if (editToggle) {
        editToggle.addEventListener("change", function () {
            applyFinalToggleState(this.checked, false);
        });
    }
});

// Reset ADD modal when it closes
document
    .getElementById("prenatalCheckupModal")
    ?.addEventListener("hidden.bs.modal", function () {
        const addToggle = document.getElementById("add_is_final_toggle");
        if (addToggle) addToggle.checked = false;
        applyAddFinalToggleState(false);
        const addError = document.getElementById("add_is_final_error");
        if (addError) addError.textContent = "";
    });

// Reset EDIT modal when it closes
document
    .getElementById("checkUpModal")
    ?.addEventListener("hidden.bs.modal", function () {
        const form = document.getElementById("edit-check-up-form");
        if (form) {
            form.querySelectorAll("input, select, textarea").forEach((el) => {
                el.disabled = false;
                el.removeAttribute("readonly");
            });
        }

        const saveBtn = document.getElementById("edit-check-up-save-btn");
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = "Save Record";
        }

        applyFinalToggleState(false, false);

        const isFinalError = document.getElementById("is_final_error");
        if (isFinalError) isFinalError.textContent = "";
    });

// ============================================================================
// EDIT BUTTON - Event Delegation
// ============================================================================

let medicalId = 0;

document.addEventListener("click", async function (e) {
    const editBtn = e.target.closest(".editPregnancyCheckupBtn");
    if (!editBtn) return;

    const checkupId = editBtn.dataset.checkupId;

    if (!checkupId || checkupId === "undefined" || checkupId === "null") {
        console.error("Invalid checkup ID:", checkupId);
        alert("Unable to load checkup: Invalid ID");
        return;
    }

    try {
        const response = await fetch(
            `/prenatal/view-pregnancy-checkup-info/${checkupId}`,
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
            throw new Error("Invalid response data");

        if (
            data.pregnancy_checkup_info &&
            typeof data.pregnancy_checkup_info === "object"
        ) {
            Object.entries(data.pregnancy_checkup_info).forEach(
                ([key, value]) => {
                    try {
                        if (key === "is_final") return; // handled after loop

                        if (key === "check_up_time") {
                            const el = document.getElementById(`edit_${key}`);
                            if (el) el.value = value || "";
                            return;
                        }

                        if (key === "patient_name") {
                            const nameEl =
                                document.getElementById("edit_patient_name");
                            const hiddenEl = document.getElementById(
                                "edit_check_up_full_name",
                            );
                            if (nameEl) nameEl.value = value || "";
                            if (hiddenEl) hiddenEl.value = value || "";
                            return;
                        }

                        if (value === "Yes" || value === "No") {
                            const el = document.getElementById(
                                `edit_${key}_${value}`,
                            );
                            if (el) el.checked = true;
                            return;
                        }

                        if (key === "date_of_comeback") {
                            const el = document.getElementById(
                                "edit_date_of_comeback",
                            );
                            if (el && value) {
                                const raw = String(value);
                                el.value =
                                    raw.includes("T") || raw.includes(" ")
                                        ? new Date(
                                              new Date(raw).getTime() +
                                                  8 * 60 * 60 * 1000,
                                          )
                                              .toISOString()
                                              .split("T")[0]
                                        : raw.split("T")[0];
                            }
                            return;
                        }

                        const el = document.getElementById(`edit_${key}`);
                        if (el) el.value = value ?? "";
                    } catch (fieldError) {
                        console.error(
                            `Error setting field ${key}:`,
                            fieldError,
                        );
                    }
                },
            );

            // case_is_final is returned by the backend — true when ANY record
            // in this prenatal case has is_final = true, locking all records.
            const caseIsFinal = !!data.case_is_final;
            const thisRecordIsFinal = !!data.this_record_is_final;

            // Toggle ON only if THIS record is the final one
            // Lock applies if ANY record in the case is final
            applyFinalToggleState(thisRecordIsFinal, caseIsFinal);
            
        }

        if (data.healthWorker && typeof data.healthWorker === "object") {
            const handledByEl = document.getElementById(
                "edit_check_up_handled_by",
            );
            if (handledByEl)
                handledByEl.value = data.healthWorker.full_name ?? "";

            const workerIdEl = document.getElementById("edit_health_worker_id");
            if (workerIdEl) workerIdEl.value = data.healthWorker.user_id || "";
        }

        const checkup_blood_pressure = document.getElementById(
            "edit_check_up_blood_pressure",
        );
        const checkup_temperature = document.getElementById(
            "edit_check_up_temperature",
        );
        const checkup_respiratory_rate = document.getElementById(
            "edit_check_up_respiratory_rate",
        );
        const checkup_pulse_rate = document.getElementById(
            "edit_check_up_pulse_rate",
        );
        const checkup_height = document.getElementById("edit_check_up_height");
        const checkup_weight = document.getElementById("edit_check_up_weight");

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

        medicalId = checkupId;

        const editModal = document.getElementById("checkUpModal");
        if (editModal) {
            bootstrap.Modal.getOrCreateInstance(editModal).show();
        }
    } catch (error) {
        console.error("Error fetching checkup data:", error);
        alert(`Failed to load checkup data: ${error.message}`);
    }
});

// ============================================================================
// SAVE (UPDATE) BUTTON
// ============================================================================

const updateBTN = document.getElementById("edit-check-up-save-btn");

updateBTN.addEventListener("click", async (e) => {
    e.preventDefault();

    const originalText = updateBTN.innerHTML;
    updateBTN.disabled = true;
    updateBTN.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

    try {
        const form = document.getElementById("edit-check-up-form");
        const formData = new FormData(form);

        const response = await fetch(`/update/prenatal-check-up/${medicalId}`, {
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

            Object.entries(data.errors).forEach(([key, value]) => {
                const errorEl = document.getElementById(`${key}_error`);
                if (errorEl && value != null) errorEl.innerHTML = value;
            });

            if (data.errors?.is_final) {
                const isFinalError = document.getElementById("is_final_error");
                if (isFinalError) {
                    isFinalError.textContent = Array.isArray(
                        data.errors.is_final,
                    )
                        ? data.errors.is_final[0]
                        : data.errors.is_final;
                }
            }

            const message = data.errors
                ? Object.values(data.errors).flat().join("\n")
                : "An unexpected error occurred.";

            Swal.fire({
                title: "Prenatal Patient",
                text: capitalizeEachWord(message),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });

            updateBTN.disabled = false;
            updateBTN.innerHTML = originalText;
        } else {
            errorElements.forEach((el) => (el.textContent = ""));

            Swal.fire({
                title: "Prenatal Check-Up Info",
                text: data.message,
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                updateBTN.disabled = false;
                updateBTN.innerHTML = originalText;

                if (result.isConfirmed) {
                    bootstrap.Modal.getInstance(
                        document.getElementById("checkUpModal"),
                    )?.hide();
                }

                Livewire.dispatch("prenatalRefreshTable");
            });
        }
    } catch (error) {
        console.error(error);
        updateBTN.disabled = false;
        updateBTN.innerHTML = originalText;
    }
});

// ============================================================================
// ARCHIVE BUTTON - Event Delegation
// ============================================================================

document.addEventListener("click", async function (e) {
    const archiveBtn = e.target.closest(".pregnancy-checkup-archieve-btn");
    if (!archiveBtn) return;

    const caseId = archiveBtn.dataset.caseId;

    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        alert("Unable to archive: Invalid ID");
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

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken)
            throw new Error("CSRF token not found. Please refresh the page.");

        const response = await fetch(`/prenatal/check-up/delete/${caseId}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken.content,
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`,
            );
        }

        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("prenatalRefreshTable");
        }

        archiveBtn.closest("tr")?.remove();

        Swal.fire({
            title: "Archived!",
            text: "The prenatal check-up record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
    } catch (error) {
        console.error("Error archiving checkup:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// ============================================================================
// UTILITIES
// ============================================================================

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
