import { fetchHealthworkers } from "../patient/healthWorkerList";
import { getVaccines } from "../patient/healthWorkerList";
import { addVaccineInteraction } from "../patient/healthWorkerList";
import { removeVaccine } from "../patient/healthWorkerList";
import Swal from "sweetalert2";

// get the element of the view icon
const viewIcon = document.querySelectorAll(".view-case-info");
const handledByDropdown = document.getElementById("");

// add the interaction to the add btn of vaccine
// these are the variables need for the adding and removing vaccine in the edit section
const addVaccineBtn = document.getElementById("update-add-vaccine-btn");
let selectedVaccinesCon = document.getElementById("update_selected_vaccine");
let selectedVaccines = [];
const vaccineInputDropdown = document.getElementById("update_vaccine_type");
const deleteIcon = document.querySelectorAll(".vaccine");

// view the case record
viewIcon.forEach((icon) => {
    icon.addEventListener("click", async (e) => {
        e.preventDefault();

        const caseId = icon.dataset.bsCaseId;
        console.log(caseId);
        // console.log(icon);
        // get the info of the case
        const response = await fetch(`/vaccination-case/record/${caseId}`);
        const data = await response.json();
        console.log(data);

        // get the elements
        const patientName = document.getElementById("view-patient-name");
        const dateOfVaccination = document.getElementById(
            "view-date-of-vaccination"
        );
        const timeOfVaccination = document.getElementById(
            "view-time-of-vaccination"
        );
        const typeOfVaccine = document.getElementById("view-vaccine-type");
        const doseNumber = document.getElementById("view-dose-number");
        const remarks = document.getElementById("view-case-remarks");

        // populate the data

        patientName.innerHTML = data.vaccinationCase.patient_name ?? "none";
        dateOfVaccination.innerHTML = data.vaccinationCase.date_of_vaccination
            ? new Date(
                  data.vaccinationCase.date_of_vaccination
              ).toLocaleDateString("en-US", {
                  month: "short",
                  day: "numeric",
                  year: "numeric",
              })
            : "none";
        timeOfVaccination.innerHTML = data.vaccinationCase.time ?? "none";
        typeOfVaccine.innerHTML = data.vaccinationCase.vaccine_type ?? "none";
        (doseNumber.innerHTML = data.vaccinationCase.dose_number ?? "none"),
            (remarks.innerHTML = data.vaccinationCase.remarks ?? "none");
    });
});
// load the healthworker list for the update

const caseEditBtn = document.querySelectorAll(".case-edit-btn");
const healthWorkerDropdown = document.getElementById("update_handled_by");
const vaccinesContainer = document.querySelector(".update-vaccine-container");
let vaccineAdministered;

// ADD VACCINATION CASE SECTION

const addCaseBtn = document.getElementById("add-vaccination-case-record-btn");

addCaseBtn.addEventListener("click", (e) => {
    e.preventDefault();

    // populating the health workder input
    const addHealthWorkerDropDown = document.getElementById("add_handled_by");

    // use the function to load the health workers from the database
    fetchHealthworkers().then((result) => {
        // console.log(result);
        result.healthWorkers.forEach((worker) => {
            addHealthWorkerDropDown.innerHTML += `<option value="${worker.id}">${worker.staff.full_name}</option>`;
        });
    });
    // -------------------------- END OF POPULATING HEALTH WORKER DROPDOWN--------------------------------------------

    // ----------- ADD VALUE TO THE DATE ---------------------
    // MAKE IT THE TODAY'S DATE
    const dateCon = document.getElementById("add-date-of-vaccination");
    const today = new Date();
    let currentDate = today.toISOString().split("T")[0];

    // provide the value
    dateCon.value = currentDate;
    // ----------- END OF ADDING NEW DATE -------------------

    // ------- PROVIDE VALUE TO THE CURRENT TIME -----------------
    // LET USE THE VARIABLE WE USE IN THE DATE
    const timeCon = document.getElementById("add-time-of-vaccination");
    // Get hours and minutes
    let hours = today.getHours().toString().padStart(2, "0");
    let minutes = today.getMinutes().toString().padStart(2, "0");

    // Format HH:MM
    let currentTime = `${hours}:${minutes}`;

    console.log(currentTime);

    // provide the value
    timeCon.value = currentTime;

    // ----------- END OF ADDING THE TIME ------------------

    // ----------- SELECTING VACCINE ----------------------
    const vaccineDropdown = document.getElementById("add_vaccine_type");
    const vaccineContainer = document.getElementById("add-vaccine-container");
    const addselectedVaccineCon = document.getElementById(
        "add-selected-vaccines"
    );
    let addSelectedVaccine = [];
    const deleteIcon = document.querySelectorAll(".vaccine");
    const addVaccineBtn = document.getElementById("add-vaccination-btn");
    getVaccines().then((item) => {
        item.vaccines.forEach((vaccine) => {
            vaccineDropdown.innerHTML += `<option value='${vaccine.id}'>${vaccine.type_of_vaccine}</option>`;
        });
    });

    // add vaccine interaction
    addVaccineInteraction(
        addVaccineBtn,
        vaccineDropdown,
        vaccineContainer,
        addselectedVaccineCon,
        addSelectedVaccine
    );

    // removing selected vaccines
    vaccineContainer.addEventListener("click", (e) => {
        console.log(
            "before deletion selected input:",
            addselectedVaccineCon.value
        );
        console.log("before deletion:", addSelectedVaccine);
        if (e.target.closest(".vaccine")) {
            const vaccineId = e.target.closest(".vaccine").dataset.bsId;
            console.log("id of element:", vaccineId);
            const deleteBtn = e.target.closest(".delete-icon");
            if (deleteBtn) {
                if (addSelectedVaccine.includes(Number(vaccineId))) {
                    const selectedElement = addSelectedVaccine.indexOf(
                        Number(vaccineId)
                    );
                    console.log("index", selectedElement);
                    addSelectedVaccine.splice(selectedElement, 1);
                    addselectedVaccineCon.value = addSelectedVaccine.join(",");
                }
                e.target.closest(".vaccine").remove();
            }

            console.log("update with deleted id:", addSelectedVaccine);
            console.log("updated value:", addselectedVaccineCon.value);
        }
    });

    // --------------- END OF REMOVING VACCINE ------------------------

    // --------------- UPLOAD THE DATA IN THE DATABASE

    // list down the important variables
    const addCaseBtn = document.getElementById("add_case_save_btn");

    addCaseBtn.addEventListener("click", async (e) => {
        e.preventDefault();
        // form data
        const addCaseForm = document.getElementById(
            "add-vaccination-case-form"
        );
        const caseFormData = new FormData(addCaseForm);
        const caseId = e.target.dataset.bsCaseId;
        console.log(caseId);

        for (let [key, value] of caseFormData.entries()) {
            console.log(`${key}: ${value}`);
        }

        const response = await fetch(`/add-vaccination-case/${caseId}`, {
            method: "POST", // Yes, use POST
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: caseFormData,
        });

        const data = await response.json();
        if (!response.ok) {
            Swal.fire({
                title: "Adding New Vaccination Case",
                text: "There are empty or invalid input.Fill out accordingly", // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });

            // errors variables
            const healthWorkerError = document.getElementById(
                "add-health-worker-error"
            );
            const dateError = document.getElementById("add-date-error");
            const timeError = document.getElementById("add-time-error");
            const selectedVaccineError = document.getElementById(
                "selected-vaccine-error"
            );
            const doseError = document.getElementById("add-dose-error");

            healthWorkerError.innerHTML = data.errors?.add_handled_by ?? "";
            dateError.innerHTML = data.errors?.add_date_of_vaccination ?? "";
            timeError.innerHTML = data.errors?.add_time_of_vaccination ?? "";
            selectedVaccineError.innerHTML =
                data.errors?.selected_vaccine_type ?? "";
            doseError.innerHTML = data.errors?.add_record_dose ?? "";

            // if the cancele btn is click
            const cancelBtn = document.getElementById("add-cancel-btn");
            cancelBtn.addEventListener("click", (e) => {
                e.preventDefault();
                healthWorkerError.innerHTML = "";
                dateError.innerHTML = "";
                timeError.innerHTML = "";
                selectedVaccineError.innerHTML = "";
                doseError.innerHTML = "";
            });
        }

        // if there's no error
        Swal.fire({
            title: "Adding New Vaccination Case",
            text: data.message
                .split(" ")
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                .join(" "), // this will make the text capitalize each word,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    });
});

// END OF ADDING VACCINATION CASE SECTION

// editvaccineCase modal
const editCaseModal = document.getElementById("editVaccinationModal");
// this functionality empty the selected vaccines to avoid the redundancy when open and closing the modal

// id of the record
let caseRecordId = document.getElementById("case_record_id");
caseEditBtn.forEach((btn) => {
    btn.addEventListener("click", async (e) => {
        e.preventDefault();

        selectedVaccines = [];

        //  getVaccine function is use to get the list of the vaccines
        const vaccineCon = document.getElementById("update_vaccine_type");

        // empty the vaccine container to avoid the redundancy from open and closing of the edit modal
        vaccinesContainer.innerHTML = "";

        // get the case id
        const caseId = btn.dataset.bsCaseId;

        caseRecordId.value = caseId;

        const response = await fetch(`/vaccination-case/record/${caseId}`);
        const data = await response.json();
        console.log(data);
        vaccineAdministered = data.vaccineAdministered;
        console.log(vaccineAdministered);

        // get the id from the data we got from the database
        const healthWorkerId = data.vaccinationCase.health_worker_id;

        // since the function fetch Health workers is will return promises we need to use the 'then' to use the properties of it.
        // then loop through the result, it is collection of healthworkers
        fetchHealthworkers().then((result) => {
            result.healthWorkers.forEach((element) => {
                // console.log(element);
                healthWorkerDropdown.innerHTML += `<option value="${
                    element.id
                }" ${healthWorkerId == element.id ? "selected" : ""}>${
                    element.staff.full_name
                }</option>`;
            });
        });

        getVaccines().then((item) => {
            item.vaccines.forEach((vaccine) => {
                vaccineCon.innerHTML += `<option value='${vaccine.id}'>${vaccine.type_of_vaccine}</option>`;
            });
        });

        // load the current selected vaccines
        const vaccines = data.vaccineAdministered;
        vaccines.forEach((vaccine) => {
            vaccinesContainer.innerHTML += ` <div class="vaccine d-flex justify-content-between bg-white align-items-center p-1 w-25 rounded" data-bs-id=${vaccine.vaccine_id}>
                    <p class="mb-0">${vaccine.vaccine_type}</p>
                    <div class="delete-icon d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                        </svg>
                    </div>
                </div>`;
            selectedVaccines.push(vaccine.vaccine_id);
            selectedVaccinesCon.value = selectedVaccines.join();
        });

        console.log("currently seleected:", selectedVaccines);
        console.log("hidden input value:", selectedVaccinesCon.value);

        // LOAD OTHER DATA OF THE PATIENT
        const doseSelect = document.getElementById("edit-dose");
        const patientName = document.getElementById("edit-patient-name");
        const date0fVaccination = document.getElementById(
            "edit_date_of_vaccination"
        );
        const timeOfVaccination = document.getElementById(
            "edit-time-of-vaccination"
        );
        const remarks = document.getElementById("edit-remarks");

        // provide the values
        patientName.value = data.vaccinationCase.patient_name;
        date0fVaccination.value = data.vaccinationCase.date_of_vaccination;
        timeOfVaccination.value = data.vaccinationCase.time;
        remarks.value = data.vaccinationCase.remarks;

        for (let option of doseSelect.options) {
            console.log(data.vaccinationCase.dose_number);
            if (option.value == data.vaccinationCase.dose_number) {
                option.selected = true;
                break;
            }
        }

        // this is the function for the adding of selected vaccine in the option

        addVaccineInteraction(
            addVaccineBtn,
            vaccineInputDropdown,
            vaccinesContainer,
            selectedVaccinesCon,
            selectedVaccines
        );
    });
});

// const vaccineAdministered = document.getElementById("vaccine-administered");
// console.log(vaccineAdministered.dataset.bsVaccineData);
// function to remove the vaccine from the selected

vaccinesContainer.addEventListener("click", (e) => {
    console.log("before deletion:", selectedVaccines);
    if (e.target.closest(".vaccine")) {
        const vaccineId = e.target.closest(".vaccine").dataset.bsId;
        console.log("id of element:", vaccineId);
        const deleteBtn = e.target.closest(".delete-icon");
        if (deleteBtn) {
            if (selectedVaccines.includes(Number(vaccineId))) {
                const selectedElement = selectedVaccines.indexOf(
                    Number(vaccineId)
                );
                console.log("index", selectedElement);
                selectedVaccines.splice(selectedElement, 1);
                selectedVaccinesCon.value = selectedVaccines.join(",");
            }
            e.target.closest(".vaccine").remove();
        }

        console.log("update with deleted id:", selectedVaccines);
        console.log("updated value:", selectedVaccinesCon.value);
    }
});

// --------------------------------------------------------------------------------------------------------------
// handles the update form of the vaccination case
// get the update save btn
const updateSaveBtn = document.getElementById("update-save-btn");

updateSaveBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const form = document.getElementById("edit-vaccination-case-form");
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    const caseId = document.getElementById("case_record_id");

    const response = await fetch(
        `/vaccine/update/case-record/${caseId.value}`,
        {
            method: "POST", // Yes, use POST
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: formData,
        }
    );

    const data = await response.json();
    if (!response.ok) {
        Swal.fire({
            title: "Update",
            text: data.errors
                .split(" ")
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                .join(" "), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }

    // if there's no error
    Swal.fire({
        title: "Update",
        text: data.message
            .split(" ")
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" "), // this will make the text capitalize each word,
        icon: "success",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "OK",
    });
});

// ARCHIVE FUNCTIONALITY
const archiveBtns = document.querySelectorAll(".archive-record-icon");

archiveBtns.forEach((btn) => {
    btn.addEventListener("click",(e) => {
        Swal.fire({
            title: "Are you sure?",
            text: "The Vaccination Case Record will be deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Archive",
        }).then(async (result) => {
            if (result.isConfirmed) {
               const caseId = e.target.dataset.bsCaseId;
                console.log(caseId);
                const response = await fetch(
                    `/delete-vaccination-case/${caseId}`,
                    {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    }
                );

                e.target.closest("tr").remove();
            }
        });
       
    });
});
