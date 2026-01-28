import { fetchHealthworkers } from "../patient/healthWorkerList.js";
import { puroks } from "../patient/healthWorkerList.js";
import { automateAge } from "../automateAge.js";
import Swal from "sweetalert2";

const healthWorkerDropDown = document.getElementById("handled_by");

const healthWorkerId = healthWorkerDropDown.dataset.bsHealthWorkerId;
// console.log("health-worker-id", healthWorkerId);
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
        } ${healthWorkerId != element.id && disablerOption ? "disabled" : ""}>${
            element.staff.full_name
        }</option>`;
    });
});

// load the brgys
const brgy = document.getElementById("brgy");
const selectedPurok = brgy.dataset.bsSelectedBrgy;
const healthWorkerAssignedArea = brgy.dataset.healthWorkerAssignedAreaId;
if (healthWorkerAssignedArea) {
    puroks(brgy, selectedPurok, "staff", healthWorkerAssignedArea);
} else {
    puroks(brgy, selectedPurok);
}

// update patient details
// update the infor
const saveBtn = document.getElementById("edit-save-btn");

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const id = saveBtn.dataset.bsMedicalId;
    // console.log(id);
    const form = document.getElementById("edit-tb-dots-patient-details-form");
    const formData = new FormData(form);

    const response = await fetch(
        `/patient-record/tb-dots/update-details/${id}`,
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

    const errorElements = document.querySelectorAll(".error-text");
    if (!response.ok) {
        // reset the error element text first
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        // if there's an validation error load the error text
        Object.entries(data.errors).forEach(([key, value]) => {
            if (document.getElementById(`${key}_error`)) {
                document.getElementById(`${key}_error`).textContent = value;
            }
        });

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
            title: "Update Tb Dots Patient Details",
            text: errorMessage,
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        Swal.fire({
            title: "Update Tb Dots Patient Details",
            text: capitalizeEachWord(data.message),
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
// HANDLE THE AUTOMATED AGE VIA DATE OF BIRTH

const dob = document.getElementById("birthdate");
const age = document.getElementById("age");
const hiddenAge = document.getElementById("hiddenAge");

if (dob && age && hiddenAge) {
    automateAge(dob, age, hiddenAge);
}
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
