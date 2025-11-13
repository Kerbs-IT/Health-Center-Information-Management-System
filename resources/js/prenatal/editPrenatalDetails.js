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

    // for (let [key, value] of formData.entries()) {
    //     console.log(`${key}: ${value}`);
    // }

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

    // error elements
    const errorElements = document.querySelectorAll(".error-text");
    if (!response.ok) {

        errorElements.forEach((element) => {
            element.textContent = "";
        });
        Object.entries(data.errors).forEach(([key, value]) => {
            if (document.getElementById(`${key}_error`)) {
                document.getElementById(`${key}_error`).textContent = value;
            }
        });

        let message = "";

        if (data.errors) {
            if (typeof data.errors == "object") {
                message = Object.values(data.errors).flat().join("\n");
            } else {
                message = data.errors;
            }
        } else {
            message = "An unexpected error occurred.";
        }

        Swal.fire({
            title: "Update Case Information",
            text: capitalizeEachWord(message), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else if (response.ok) {
        // empty the error messages
         errorElements.forEach((element) => {
             element.textContent = "";
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

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
