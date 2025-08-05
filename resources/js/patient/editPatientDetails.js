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

    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
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

    if (response.ok) {
        Swal.fire({
            title: "Update",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        Swal.fire({
            title: "Update",
            text: data.message,
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
