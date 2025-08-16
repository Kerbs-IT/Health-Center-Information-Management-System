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
            text: data.errors,
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }

    // if there's no error
    Swal.fire({
        title: "Update",
        text: "",
        icon: "success",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "OK",
    });
});
