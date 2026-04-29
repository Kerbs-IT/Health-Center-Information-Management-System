import { fetchHealthworkers } from "./healthWorkerList.js";
import { puroks } from "./healthWorkerList.js";
import { automateAge } from "../automateAge.js";

import Swal from "sweetalert2";

// call the function from the healthworker list
// it fetch the list of health workers from the database
const healthWorkers = fetchHealthworkers();
const healthWorkerDropDown = document.getElementById("handled_by");
const healthWorkerId = healthWorkerDropDown.dataset.bsSelectedHealthWorker;
const dropdown = document.getElementById("brgy"); // the brgy input dropdown
// console.log(healthWorkerId);
const currentLoginhealthWorkerId = healthWorkerDropDown.dataset.staffId;
let disablerOption = null;
if (currentLoginhealthWorkerId) {
    disablerOption = true;
}
fetchHealthworkers().then((result) => {
    result.healthWorkers.forEach((element) => {
        // console.log(element);
        healthWorkerDropDown.innerHTML += `<option value="${element.id}" ${
            healthWorkerId == element.id ? "selected" : ""
        } 
        ${healthWorkerId != element.id && disablerOption ? "disabled" : ""}>${
            element.staff.full_name
        }</option>`;
    });
});

// load the current selected address of the patient
const selected = dropdown.dataset.bsPurok;
// console.log(selected);
const healthWorkerAssignedArea = brgy.dataset.healthWorkerAssignedAreaId;
if (healthWorkerAssignedArea) {
    puroks(dropdown, selected, "staff", healthWorkerAssignedArea);
} else {
    puroks(dropdown, selected);
}

// update the record
const updateBtn = document.getElementById("update-record-btn");
updateBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    // Store original text and disable button
    const originalText = updateBtn.innerHTML;
    updateBtn.disabled = true;
    updateBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';

    try {
        const form = document.getElementById("update-form");
        const formData = new FormData(form);

        const patientId = updateBtn.dataset.bsPatientId;

        formData.append("_method", "PUT");
        const response = await fetch(`/patient-record/update/${patientId}`, {
            method: "POST",
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: formData,
        });

        const data = await response.json();

        const errorElements = document.querySelectorAll(".error-text");

        if (response.ok) {
            errorElements.forEach((element) => {
                element.textContent = "";
            });

            Swal.fire({
                title: "Update",
                text: data.message,
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then(() => {
                // Re-enable button AFTER SweetAlert is dismissed
                updateBtn.disabled = false;
                updateBtn.innerHTML = originalText;
            });
        } else {
            // reset errors first
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
                title: "Update Details",
                text: capitalizeEachWord(message),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then(() => {
                // Re-enable button AFTER SweetAlert is dismissed
                updateBtn.disabled = false;
                updateBtn.innerHTML = originalText;
            });
        }
    } catch (error) {
        console.log("Error:", error);
        // Re-enable button on error
        updateBtn.disabled = false;
        updateBtn.innerHTML = originalText;
    }
});

// date of birth and age automated
const dob = document.getElementById("birthdate");
const age = document.getElementById("age");
const hiddenAge = document.getElementById("hiddenAge");

if (dob && age && hiddenAge) {
    automateAge(dob, age, hiddenAge);
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
