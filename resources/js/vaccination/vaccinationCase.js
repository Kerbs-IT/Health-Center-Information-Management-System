import { fetchHealthworkers } from "../patient/healthWorkerList";
import { getVaccines } from "../patient/healthWorkerList";
import { addVaccineInteraction } from "../patient/healthWorkerList";
import { removeVaccine } from "../patient/healthWorkerList";
import Swal from "sweetalert2";

// =============================================================================
// VACCINE DOSE CONFIG — loaded from DB once at startup
// =============================================================================
let vaccineDoseConfig = {};

async function loadVaccineDoseConfig() {
    try {
        const res = await fetch("/api/vaccines/active");
        const data = await res.json();
        data.vaccines.forEach((vaccine) => {
            vaccineDoseConfig[String(vaccine.id)] = {
                maxDoses: vaccine.max_doses,
                name: vaccine.type_of_vaccine,
                description:
                    vaccine.max_doses === 1
                        ? "dose 1"
                        : `doses 1-${vaccine.max_doses}`,
            };
        });
    } catch (err) {
        console.error("Failed to load vaccine config:", err);
    }
}

// =============================================================================
// DOSE DROPDOWN — explicit doseDropdownId to avoid wrong modal update
// =============================================================================
function updateDoseDropdown(selectedVaccines, doseDropdownId) {
    const doseDropdown = document.getElementById(doseDropdownId);
    if (!doseDropdown) return;

    let maxDose = 1;
    selectedVaccines.forEach((id) => {
        const config = vaccineDoseConfig[String(id)];
        if (config) maxDose = Math.max(maxDose, config.maxDoses);
    });

    const currentValue = doseDropdown.value;
    doseDropdown.innerHTML = '<option value="">Select Dose</option>';

    for (let i = 1; i <= maxDose; i++) {
        const option = document.createElement("option");
        option.value = `${i}`;
        option.textContent = `Dose ${i}`;
        doseDropdown.appendChild(option);
    }

    if (currentValue && parseInt(currentValue) <= maxDose) {
        doseDropdown.value = currentValue;
    }
}

// =============================================================================
// DOSE VALIDATION
// =============================================================================
function validateVaccinesWithDose(selectedVaccines, selectedDose) {
    const invalidVaccines = [];

    selectedVaccines.forEach((id) => {
        const config = vaccineDoseConfig[String(id)];
        if (config && selectedDose > config.maxDoses) {
            invalidVaccines.push({
                name: config.name,
                maxDoses: config.maxDoses,
                description: config.description,
            });
        }
    });

    return invalidVaccines;
}

// =============================================================================
// ELEMENT REFERENCES
// =============================================================================
const addVaccineBtn = document.getElementById("update-add-vaccine-btn");
let selectedVaccinesCon = document.getElementById("update_selected_vaccine");
let selectedVaccines = [];
const vaccineInputDropdown = document.getElementById("update_vaccine_type");
const healthWorkerDropdown = document.getElementById("update_handled_by");
const editvaccinesContainer = document.querySelector(
    ".update-vaccine-container",
);
let vaccineAdministered;

const vaccineContainer = document.getElementById("add-vaccine-container");
const addselectedVaccineCon = document.getElementById("add-selected-vaccines");
let addSelectedVaccine = [];
const vaccineDropdown = document.getElementById("add_vaccine_type");
const newRecordAddVaccineBtn = document.getElementById("add-vaccination-btn");

// =============================================================================
// INIT — await config before registering any vaccine interactions
// =============================================================================
async function init() {
    await loadVaccineDoseConfig();
    // console.log("vaccine config loaded:", vaccineDoseConfig);

    // ✅ ADD CASE - Handle it directly, don't use shared function
    if (vaccineDropdown && newRecordAddVaccineBtn) {
        getVaccines().then((item) => {
            item.vaccines.forEach((vaccine) => {
                vaccineDropdown.innerHTML += `<option value='${vaccine.id}'>${vaccine.type_of_vaccine}</option>`;
            });
        });

        // ✅ Direct event listener - no shared function
        newRecordAddVaccineBtn.addEventListener("click", (e) => {
            e.preventDefault();

            const selectedText =
                vaccineDropdown.options[vaccineDropdown.selectedIndex].text;
            const selectedId = Number(
                vaccineDropdown.options[vaccineDropdown.selectedIndex].value,
            );

            if (!vaccineDropdown.value) {
                Swal.fire({
                    title: "Vaccine Type",
                    text: "The input field is empty. Please provide a valid value.",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });
                return;
            }

            if (addSelectedVaccine.includes(selectedId)) {
                Swal.fire({
                    title: "Vaccine Type",
                    text: "The selected vaccine is already added. Please select another type of vaccine.",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });
                vaccineDropdown.value = "";
                return;
            }

            if (selectedId) {
                vaccineContainer.innerHTML += `
                    <div class="vaccine d-flex justify-content-between bg-white align-items-center p-1 w-25 rounded" data-bs-id=${selectedId}>
                        <p class="mb-0">${selectedText}</p>
                        <div class="delete-icon d-flex align-items-center justify-content-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/>
                            </svg>
                        </div>
                    </div>`;

                addSelectedVaccine.push(selectedId);
                addselectedVaccineCon.value = addSelectedVaccine.join(",");

                // ✅ UPDATE DOSE DROPDOWN IMMEDIATELY
                updateDoseDropdown(addSelectedVaccine, "add-dose");
            }

            vaccineDropdown.value = "";
        });
    }

    // Edit case vaccine interaction - keep using shared function
    addVaccineInteraction(
        addVaccineBtn,
        vaccineInputDropdown,
        editvaccinesContainer,
        selectedVaccinesCon,
        selectedVaccines,
        (vaccines) => updateDoseDropdown(vaccines, "edit-dose"),
    );
}

init();

// =============================================================================
// VIEW CASE RECORD
// =============================================================================
document.addEventListener("click", async (e) => {
    const viewIcon = e.target.closest(".view-case-info");
    if (!viewIcon) return;

    const caseId = viewIcon.dataset.bsCaseId;
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        alert("Unable to load case details: Invalid ID");
        return;
    }

    try {
        const response = await fetch(`/vaccination-case/record/${caseId}`);
        const data = await response.json();

        const patientName = document.getElementById("view-patient-name");
        const dateOfVaccination = document.getElementById(
            "view-date-of-vaccination",
        );
        const typeOfVaccine = document.getElementById("view-vaccine-type");
        const doseNumber = document.getElementById("view-dose-number");
        const remarks = document.getElementById("view-case-remarks");
        const height = document.getElementById("view-height");
        const weight = document.getElementById("view-weight");
        const temperature = document.getElementById("view-temperature");
        const dateOfComeback = document.getElementById("view-date-of-comeback");

        patientName.innerHTML = data.vaccinationCase.patient_name ?? "none";

        dateOfVaccination.innerHTML = data.vaccinationCase.date_of_vaccination
            ? new Date(
                  data.vaccinationCase.date_of_vaccination,
              ).toLocaleDateString("en-US", {
                  month: "short",
                  day: "numeric",
                  year: "numeric",
              })
            : "none";

        dateOfComeback.innerHTML = data.vaccinationCase.date_of_comeback
            ? new Date(
                  data.vaccinationCase.date_of_comeback,
              ).toLocaleDateString("en-US", {
                  month: "short",
                  day: "numeric",
                  year: "numeric",
              })
            : "none";

        typeOfVaccine.innerHTML = data.vaccinationCase.vaccine_type ?? "none";
        doseNumber.innerHTML = data.vaccinationCase.dose_number ?? "none";
        remarks.innerHTML = data.vaccinationCase.remarks ?? "none";
        height.innerHTML = `${data.vaccinationCase.height} cm` ?? "none";
        weight.innerHTML = `${data.vaccinationCase.weight} kg` ?? "none";
        temperature.innerHTML =
            `${data.vaccinationCase.temperature} °C` ?? "none";
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

// =============================================================================
// ADD VACCINATION CASE — open modal, reset state
// =============================================================================
const addCaseBtn = document.getElementById("add-vaccination-case-record-btn");
const addCaseForm = document.getElementById("add-vaccination-case-form");

if (addCaseBtn) {
    addCaseBtn.addEventListener("click", (e) => {
        e.preventDefault();

        vaccineContainer.innerHTML = "";
        addselectedVaccineCon.value = "";
        addCaseForm.reset();
        addSelectedVaccine.length = 0;

        // Reset dose dropdown
        updateDoseDropdown([], "add-dose");

        const vaccinationCaseErrors = document.querySelectorAll(
            ".add_vaccination_case_record_errors",
        );
        if (vaccinationCaseErrors) {
            vaccinationCaseErrors.forEach((error) => (error.innerHTML = ""));
        }

        const addHealthWorkerDropDown = document.getElementById(
            "dissabled_add_handled_by",
        );
        const selectedHealthWorkerId = addCaseBtn.dataset.healthWorkerId;
        const hidden_handled_by_input = document.getElementById(
            "hidden_add_handled_by",
        );

        hidden_handled_by_input.value = selectedHealthWorkerId;

        if (addHealthWorkerDropDown) {
            addHealthWorkerDropDown.disabled = true;
            fetchHealthworkers().then((result) => {
                result.healthWorkers.forEach((worker) => {
                    addHealthWorkerDropDown.innerHTML += `<option value="${worker.id}" ${
                        selectedHealthWorkerId == worker.id ? "selected" : ""
                    }>${worker.staff.full_name}</option>`;
                });
            });
        }

        const dateCon = document.getElementById("add-date-of-vaccination");
        const today = new Date();
        dateCon.value = today.toISOString().split("T")[0];

        const timeCon = document.getElementById("add-time-of-vaccination");
        timeCon.value = `${today.getHours().toString().padStart(2, "0")}:${today.getMinutes().toString().padStart(2, "0")}`;
    });
}

// =============================================================================
// ADD CASE — remove vaccine from container
// =============================================================================
if (vaccineContainer) {
    vaccineContainer.addEventListener("click", (e) => {
        if (e.target.closest(".vaccine")) {
            const vaccineId = e.target.closest(".vaccine").dataset.bsId;
            const deleteBtn = e.target.closest(".delete-icon");

            if (deleteBtn) {
                if (addSelectedVaccine.includes(Number(vaccineId))) {
                    const idx = addSelectedVaccine.indexOf(Number(vaccineId));
                    addSelectedVaccine.splice(idx, 1);
                    addselectedVaccineCon.value = addSelectedVaccine.join(",");
                }
                e.target.closest(".vaccine").remove();
                // ✅ ADD THIS LINE - update immediately without setTimeout
                updateDoseDropdown(addSelectedVaccine, "add-dose");
            }
        }
    });
}

// =============================================================================
// ADD CASE — submit with dose validation
// =============================================================================
const vaccinationSubmitCaseBtn = document.getElementById("add_case_save_btn");

if (vaccinationSubmitCaseBtn) {
    vaccinationSubmitCaseBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        // Dose validation before submit
        const addDoseDropdown = document.getElementById("add-dose");
        const selectedDose = parseInt(addDoseDropdown?.value);

        if (addSelectedVaccine.length > 0 && selectedDose) {
            const invalidVaccines = validateVaccinesWithDose(
                addSelectedVaccine,
                selectedDose,
            );
            if (invalidVaccines.length > 0) {
                const invalidList = invalidVaccines
                    .map((v) => `• ${v.name} (${v.description})`)
                    .join("<br>");
                Swal.fire({
                    title: "Dose Mismatch",
                    html: `The following vaccines cannot have Dose ${selectedDose}:<br><br>${invalidList}<br><br><strong>Please separate these vaccines or select an appropriate dose number.</strong>`,
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });
                return;
            }
        }

        const addCaseForm = document.getElementById(
            "add-vaccination-case-form",
        );
        const caseFormData = new FormData(addCaseForm);
        const caseId = e.target.dataset.bsCaseId;

        const response = await fetch(`/add-vaccination-case/${caseId}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                Accept: "application/json",
            },
            body: caseFormData,
        });

        const data = await response.json();

        if (!response.ok) {
            const vaccinationCaseErrors = document.querySelectorAll(
                ".add_vaccination_case_record_errors",
            );
            if (vaccinationCaseErrors) {
                vaccinationCaseErrors.forEach(
                    (error) => (error.innerHTML = ""),
                );
            }

            let errorMessage = "";
            if (typeof data.errors === "string") {
                errorMessage = data.errors
                    .replace(/&lt;br&gt;/g, "<br>")
                    .replace(/&lt;br\/&gt;/g, "<br>")
                    .replace(/&lt;br \/&gt;/g, "<br>");
            } else if (typeof data.errors === "object") {
                errorMessage = Object.entries(data.errors)
                    .map(([field, message]) => {
                        const fieldName = field
                            .replace(/^add_/, "")
                            .replace(/_/g, " ")
                            .replace(/\b\w/g, (char) => char.toUpperCase());
                        return `• ${fieldName}: ${message}`;
                    })
                    .join("<br>");
            }

            Swal.fire({
                title: "Adding New Vaccination Case",
                html: errorMessage,
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });

            const healthWorkerError = document.getElementById(
                "add-health-worker-error",
            );
            const dateError = document.getElementById("add-date-error");
            const timeError = document.getElementById("add-time-error");
            const selectedVaccineError = document.getElementById(
                "selected-vaccine-error",
            );
            const doseError = document.getElementById("add-dose-error");
            const dateOfComebackError = document.getElementById(
                "add-date-of-comeback-error",
            );

            healthWorkerError.innerHTML = data.errors?.add_handled_by ?? "";
            dateError.innerHTML = data.errors?.add_date_of_vaccination ?? "";
            timeError.innerHTML = data.errors?.add_time_of_vaccination ?? "";
            selectedVaccineError.innerHTML =
                data.errors?.selected_vaccine_type ?? "";
            doseError.innerHTML = data.errors?.add_record_dose ?? "";
            if (dateOfComebackError) {
                dateOfComebackError.innerHTML =
                    data.errors?.add_date_of_comeback ?? "";
            }

            const cancelBtn = document.getElementById("add-cancel-btn");
            cancelBtn.addEventListener("click", (e) => {
                e.preventDefault();
                healthWorkerError.innerHTML = "";
                dateError.innerHTML = "";
                timeError.innerHTML = "";
                selectedVaccineError.innerHTML = "";
                doseError.innerHTML = "";
            });

            return;
        }

        Livewire.dispatch("refreshTable");

        const vaccinationCaseErrors = document.querySelectorAll(
            ".add_vaccination_case_record_errors",
        );
        if (vaccinationCaseErrors) {
            vaccinationCaseErrors.forEach((error) => (error.innerHTML = ""));
        }

        Swal.fire({
            title: "Adding New Vaccination Case",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("vaccinationModal"),
                );
                modal.hide();
            }
        });

        addCaseForm.reset();
    });
}

// =============================================================================
// EDIT CASE — open modal, load data
// =============================================================================
const editCaseModal = document.getElementById("editVaccinationModal");
let caseRecordId = document.getElementById("case_record_id");
selectedVaccines = [];

document.addEventListener("click", async (e) => {
    const caseEditBtn = e.target.closest(".case-edit-btn");
    if (!caseEditBtn) return;

    const caseId = caseEditBtn.dataset.bsCaseId;
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        alert("Unable to load case details: Invalid ID");
        return;
    }

    const errorElements = document.querySelectorAll(".error-text");
    errorElements.forEach((error) => (error.innerHTML = ""));

    selectedVaccines.length = 0;
    editvaccinesContainer.innerHTML = "";

    // Clear previous options to avoid duplicates on re-open
    const vaccineCon = document.getElementById("update_vaccine_type");
    vaccineCon.innerHTML = '<option value="">Select Vaccine</option>';

    try {
        caseRecordId.value = caseId;

        const response = await fetch(`/vaccination-case/record/${caseId}`);
        const data = await response.json();

        vaccineAdministered = data.vaccineAdministered;
        const healthWorkerId = data.vaccinationCase.health_worker_id;

        if (healthWorkerDropdown) {
            healthWorkerDropdown.innerHTML = "";
            fetchHealthworkers().then((result) => {
                result.healthWorkers.forEach((element) => {
                    healthWorkerDropdown.innerHTML += `<option value="${element.id}" ${
                        healthWorkerId == element.id ? "selected" : ""
                    }>${element.staff.full_name}</option>`;
                });
            });
        }

        getVaccines().then((item) => {
            item.vaccines.forEach((vaccine) => {
                vaccineCon.innerHTML += `<option value='${vaccine.id}'>${vaccine.type_of_vaccine}</option>`;
            });
        });

        // Load current selected vaccines
        data.vaccineAdministered.forEach((vaccine) => {
            editvaccinesContainer.innerHTML += `
                <div class="vaccine d-flex justify-content-between bg-white align-items-center p-1 w-25 rounded" data-bs-id=${vaccine.vaccine_id}>
                    <p class="mb-0">${vaccine.vaccine_type}</p>
                    <div class="delete-icon d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/>
                        </svg>
                    </div>
                </div>`;
            selectedVaccines.push(vaccine.vaccine_id);
            selectedVaccinesCon.value = selectedVaccines.join();
        });

        // Update edit dose dropdown based on loaded vaccines
        updateDoseDropdown(selectedVaccines, "edit-dose");

        const doseSelect = document.getElementById("edit-dose");
        const patientName = document.getElementById("edit-patient-name");
        const date0fVaccination = document.getElementById(
            "edit_date_of_vaccination",
        );
        const timeOfVaccination = document.getElementById(
            "edit-time-of-vaccination",
        );
        const remarks = document.getElementById("edit-remarks");
        const height = document.getElementById("edit-height");
        const weight = document.getElementById("edit-weight");
        const temperature = document.getElementById("edit-temperature");
        const date_of_comeback = document.getElementById(
            "edit-date-of-comeback",
        );

        patientName.value = data.vaccinationCase.patient_name;
        date0fVaccination.value = data.vaccinationCase.date_of_vaccination;
        timeOfVaccination.value = data.vaccinationCase.time;
        remarks.value = data.vaccinationCase.remarks;
        height.value = data.vaccinationCase.height;
        weight.value = data.vaccinationCase.weight;
        temperature.value = data.vaccinationCase.temperature;
        date_of_comeback.value = data.vaccinationCase.date_of_comeback;

        for (let option of doseSelect.options) {
            if (option.value == data.vaccinationCase.dose_number) {
                option.selected = true;
                break;
            }
        }
    } catch (error) {
        console.error("Error loading edit case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to view record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// =============================================================================
// EDIT CASE — remove vaccine from container
// =============================================================================
editvaccinesContainer.addEventListener("click", (e) => {
    if (e.target.closest(".vaccine")) {
        const vaccineId = e.target.closest(".vaccine").dataset.bsId;
        const deleteBtn = e.target.closest(".delete-icon");

        if (deleteBtn) {
            if (selectedVaccines.includes(Number(vaccineId))) {
                const idx = selectedVaccines.indexOf(Number(vaccineId));
                selectedVaccines.splice(idx, 1);
                selectedVaccinesCon.value = selectedVaccines.join(",");
            }
            e.target.closest(".vaccine").remove();
            setTimeout(
                () => updateDoseDropdown(selectedVaccines, "edit-dose"),
                100,
            );
        }
    }
});

// =============================================================================
// EDIT CASE — submit with dose validation
// =============================================================================
const updateSaveBtn = document.getElementById("update-save-btn");

updateSaveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    // Dose validation before submit
    const editDoseDropdown = document.getElementById("edit-dose");
    const selectedDose = parseInt(editDoseDropdown?.value);

    if (selectedVaccines.length > 0 && selectedDose) {
        const invalidVaccines = validateVaccinesWithDose(
            selectedVaccines,
            selectedDose,
        );
        if (invalidVaccines.length > 0) {
            const invalidList = invalidVaccines
                .map((v) => `• ${v.name} (${v.description})`)
                .join("<br>");
            Swal.fire({
                title: "Dose Mismatch",
                html: `The following vaccines cannot have Dose ${selectedDose}:<br><br>${invalidList}<br><br><strong>Please separate these vaccines or select an appropriate dose number.</strong>`,
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            return;
        }
    }

    try {
        const form = document.getElementById("edit-vaccination-case-form");
        const formData = new FormData(form);
        const caseId = document.getElementById("case_record_id");

        const response = await fetch(
            `/vaccine/update/case-record/${caseId.value}`,
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
            errorElements.forEach((element) => {
                element.textContent = "";
            });

            Object.entries(data.errors).forEach(([key, value]) => {
                const el = document.getElementById(`update_${key}_error`);
                if (el) el.textContent = value;
            });

            const message = data.errors
                ? typeof data.errors == "object"
                    ? Object.values(data.errors).flat().join("\n")
                    : data.errors
                : "An unexpected error occurred.";

            Swal.fire({
                title: "Update Case Information",
                text: capitalizeEachWord(message),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        } else {
            Livewire.dispatch("refreshTable");
            errorElements.forEach((element) => {
                element.textContent = "";
            });

            Swal.fire({
                title: "Update",
                text: data.message,
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("editVaccinationModal"),
                    );
                    modal.hide();
                }
            });
        }
    } catch (error) {
        console.error(error);
    }
});

// =============================================================================
// ARCHIVE CASE
// =============================================================================
document.addEventListener("click", async (e) => {
    const archiveBtn = e.target.closest(".archive-record-icon");
    if (!archiveBtn) return;

    const caseId = archiveBtn.dataset.bsCaseId;
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        alert("Unable to load case details: Invalid ID");
        return;
    }

    try {
        Swal.fire({
            title: "Are you sure?",
            text: "The Vaccination Case Record will be moved to archived status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(
                        `/delete-vaccination-case/${caseId}`,
                        {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]',
                                ).content,
                            },
                        },
                    );

                    if (response.ok) {
                        const data = await response.json();
                        e.target.closest("tr").remove();

                        if (typeof Livewire !== "undefined") {
                            Livewire.dispatch(
                                "vaccinationMasterlistRefreshTable",
                            );
                        }

                        Swal.fire({
                            icon: "success",
                            title: "Archived!",
                            text:
                                data.message ||
                                "Vaccination case has been archived successfully.",
                            timer: 1000,
                            showConfirmButton: false,
                        });
                    } else {
                        const data = await response.json();
                        Swal.fire({
                            icon: "error",
                            title: "Archive Failed",
                            text:
                                data.message ||
                                "Failed to archive vaccination case. Please try again.",
                        });
                    }
                } catch (error) {
                    console.error("Error archiving vaccination case:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "An unexpected error occurred. Please check your connection and try again.",
                    });
                }
            }
        });
    } catch (error) {
        console.error("Error archiving case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// =============================================================================
// HELPERS
// =============================================================================
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
