import { fetchHealthworkers } from "../patient/healthWorkerList.js";
import { puroks } from "../patient/healthWorkerList.js";
import Swal from "sweetalert2";

const healthWorkerDropDown = document.getElementById("handled_by");

const healthWorkerId = healthWorkerDropDown.dataset.bsHealthWorkerId;
console.log("health-worker-id", healthWorkerId);

fetchHealthworkers().then((result) => {
    result.healthWorkers.forEach((element) => {
        // console.log(element);
        healthWorkerDropDown.innerHTML += `<option value="${element.id}" ${
            healthWorkerId == element.id ? "selected" : ""
        }>${element.staff.full_name}</option>`;
    });
});

// load the brgys
const brgy = document.getElementById("brgy");
const selectedPurok = brgy.dataset.bsSelectedBrgy;
puroks(brgy, selectedPurok);


// update patient details
// update the infor
const saveBtn = document.getElementById("edit-save-btn");

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const id = saveBtn.dataset.bsMedicalId;
    console.log(id);
    const form = document.getElementById("edit-tb-dots-patient-details-form");
    const formData = new FormData(form);

    const response = await fetch(`/patient-record/tb-dots/update-details/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

    const data = await response.json();
    if (!response.ok) {
       let errorMessage = "";

       if (data.errors) {
           // Handle ValidationException
           errorMessage = Object.values(data.errors)
               .flat() // flatten nested arrays if present
               .join("\n");
       } else if (data.message) {
           // Handle general backend errors
           errorMessage = data.message;
       } else {
           // Handle unexpected responses
           errorMessage = "An unexpected error occurred.";
       }

       Swal.fire({
           title: "Error",
           text: errorMessage,
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