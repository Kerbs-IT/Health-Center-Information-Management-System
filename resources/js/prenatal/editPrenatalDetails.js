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
    } else {
        Swal.fire({
            title: "Update",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
