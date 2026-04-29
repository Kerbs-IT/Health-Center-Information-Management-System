import { fetchHealthworkers } from "../patient/healthWorkerList.js";
import { puroks } from "../patient/healthWorkerList.js";
import { automateAge } from "../automateAge.js";
import Swal from "sweetalert2";

const healthWorkerDropDown = document.getElementById("handled_by");

const healthWorkerId = healthWorkerDropDown.dataset.bsHealthWorkerId;
const currentLoginhealthWorkerId = healthWorkerDropDown.dataset.staffId;
let disablerOption = null;
if (currentLoginhealthWorkerId) {
    disablerOption = true;
}

fetchHealthworkers().then((result) => {
    result.healthWorkers.forEach((element) => {
        healthWorkerDropDown.innerHTML += `<option value="${element.id}" ${
            healthWorkerId == element.id ? "selected" : ""
        }
        ${healthWorkerId != element.id && disablerOption ? "disabled" : ""}>${
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

// update the info
const saveBtn = document.getElementById("edit-save-btn");

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = saveBtn.dataset.bsMedicalId;
    const originalText = saveBtn.innerHTML;

    saveBtn.disabled = true;
    saveBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

    try {
        const form = document.getElementById("edit-senior-form");
        const formData = new FormData(form);

        const response = await fetch(`/update/senior-citizen/details/${id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                Accept: "application/json",
            },
            body: formData,
        });

        const data = await response.json();
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

            let errorMessage = "";

            if (data.errors) {
                errorMessage = Object.values(data.errors).flat().join("\n");
            } else if (data.message) {
                errorMessage = data.message;
            } else {
                errorMessage = "An unexpected error occurred.";
            }

            Swal.fire({
                title: "Error",
                text: errorMessage,
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });

            // Re-enable on validation error
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        } else {
            errorElements.forEach((element) => {
                element.textContent = "";
            });

            Swal.fire({
                title: "Update Senior Citizen Patient Details",
                text: capitalizeEachWord(data.message),
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        }
    } catch (error) {
        console.error("Error updating senior citizen details:", error);

        Swal.fire({
            title: "Error",
            text: `Failed to update record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });

        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    }
});

const dob = document.getElementById("birthdate");
const age = document.getElementById("age");
const hiddenAge = document.getElementById("hiddenAge");

if (dob && age && hiddenAge) {
    automateAge(dob, age, hiddenAge);
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
