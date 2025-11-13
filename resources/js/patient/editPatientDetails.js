import { fetchHealthworkers } from "./healthWorkerList.js";
import { puroks } from "./healthWorkerList.js";

import Swal from "sweetalert2";

// call the function from the healthworker list
// it fetch the list of health workers from the database
const healthWorkers = fetchHealthworkers();
const healthWorkerDropDown = document.getElementById("healthWorkersDropDown");
const healthWorkerId = healthWorkerDropDown.dataset.bsSelectedHealthWorker;
const dropdown = document.getElementById("brgy"); // the brgy input dropdown
console.log(healthWorkerId);
fetchHealthworkers().then((result) => {
    result.healthWorkers.forEach((element) => {
        console.log(element);
        healthWorkerDropDown.innerHTML += `<option value="${element.id}" ${
            healthWorkerId == element.id ? "selected" : ""
        }>${element.staff.full_name}</option>`;
    });
});

// load the current selected address of the patient
const selected = dropdown.dataset.bsPurok;
console.log(selected);
puroks(dropdown, selected);

// update the record
const updateBtn = document.getElementById("update-record-btn");
updateBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const form = document.getElementById("update-form");
    const formData = new FormData(form);

    const patientId = updateBtn.dataset.bsPatientId;

    formData.append("_method", "PUT"); // to simulate PUT if your route uses Route::put()
    const response = await fetch(`/patient-record/update/${patientId}`, {
        method: "POST",
        headers: {
            Accept: "application/json",
            // Don't set Content-Type for FormData - let browser set it
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
        });
    } else {
        // reset first
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
            text: capitalizeEachWord(message), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
