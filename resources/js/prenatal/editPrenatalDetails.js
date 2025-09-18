import { fetchHealthworkers } from "../patient/healthWorkerList.js";
import { puroks } from "../patient/healthWorkerList.js";
import Swal from "sweetalert2";

const healthWorkerDropDown = document.getElementById("edit_handled_by");

const healthWorkerId = healthWorkerDropDown.dataset.bsHealthWorkerId;
console.log("health-worker-id", healthWorkerId);

fetchHealthworkers().then((result) => {
    result.healthWorkers.forEach((element) => {
        console.log(element);
        healthWorkerDropDown.innerHTML += `<option value="${element.id}" ${
            healthWorkerId == element.id ? "selected" : ""
        }>${element.staff.full_name}</option>`;
    });
});

// add the selected in blood type
const blood_type = document.getElementById("blood_type");
const selectedBloodType = blood_type.dataset.bsBloodType;
for (let option of blood_type.options) {
    if (option.value == selectedBloodType) {
        option.selected = true;
    }
}

// load the brgys
const brgy = document.getElementById("brgy");
const selectedPurok = brgy.dataset.bsSelectedBrgy;
puroks(brgy, selectedPurok);

// disable the philHealth Number if the 'no' is selected

const philHealthRadios = document.querySelectorAll('input[name="philhealth"]');
const philHealthNumber = document.getElementById("philhealth_number");

// run once on page load to set correct state
const checkedRadio = document.querySelector('input[name="philhealth"]:checked');
philHealthNumber.disabled = !(checkedRadio && checkedRadio.value === "yes");

// update whenever the radio changes
philHealthRadios.forEach((radio) => {
    radio.addEventListener("change", (e) => {
        philHealthNumber.disabled = e.target.value !== "yes";
    });
});

// UPDATE THE DATA

const updateBTN = document.getElementById("update-patient-detail-BTN");
const medicalRecordId = updateBTN.dataset.bsId;

updateBTN.addEventListener("click", async (e) => {
    e.preventDefault();
    const form = document.getElementById(
        "update-prenatal-patient-details-form"
    );
    const formData = new FormData(form);

    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    const response = await fetch(
        `/update/prenatal-patient-details/${medicalRecordId}`,
        {
            method: "POST",
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
            text: Object.values(data.errors)
                .map((err) => err) // convert array of errors to text
                .join("\n"), // join with new lines
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });

        // show the error message
        // const fname_error = document.getElementById("first_name_error");
        // const middle_initial_error = document.getElementById('middle_initial_error');
        // const last_name_error = document.getElementById("last_name_error");
        // const birth_place_error = document.getElementById("birth_place_error");
        // const date_of_birth_error = document.getElementById('date_of_birth_error');
        // const age_error = document.getElementById('age_error');
        // const sex_error = document.getElementById('sex_error');
        // const contact_num_error = document.getElementById("contact_error");
        // const nationality_error = document.getElementById("nationality_error");
        // const date_of_registration_error = document.getElementById('date_of_registration_error');
        // const handle_by_error = document.getElementById('handle_by_error');
        // const head_of_family_error = document.getElementById('head_of_family_error');
        // const civil_status_error = document.getElementById('civil_status_error');
        // const blood_type_error = document.getElementById('blood_typee_error');
        // const religion_error = document.getElementById('religion_error');
        // const philhealth_error = document.getElementById("philhealth_error");
        // const family_serial_error = document.getElementById('family_serial_error');
        // const street_error = document.getElementById('street_error');
        // const brgy_error = document.getElementById('brgy_error');
        // const blood_pressure_error = document.getElementById('blood_pressure_error');
        // const temperature_error = document.getElementById("temperature_error");
        // const pulse_rate_error = document.getElementById("pulse_rate_error");
        // const respiratory_rate_error = document.getElementById('respiratory_rate_error');

        // so instead of manual typing of errors
        // we rely on the input field name then added error since that is the id of the error text ex. first_name_error

        // the response is object, so we use object.keys()

        Object.keys(data.errors).forEach((error) => {
            const message = data.errors[error];
            document.getElementById(`${error}_error`).innerHTML = message[0];
        });
    } else if (response.ok) {
        // empty the error messages
        document.querySelectorAll(".text-errors").forEach((text) => {
            text.innerHTML = "";
        });
        Swal.fire({
            title: "Update",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
