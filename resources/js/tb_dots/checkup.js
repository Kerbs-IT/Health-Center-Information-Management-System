import Swal from "sweetalert2";
import { vitalSignInputMask } from "../vitalSign";

const saveBtn = document.getElementById("add-check-up-save-btn");

const addCheckup = document.getElementById("add-check-up-record-btn");

if (addCheckup) {
    addCheckup.addEventListener("click", () => {
        const errors = document.querySelectorAll(".error-text");
        if (errors) {
            errors.forEach((error) => (error.innerHTML = ""));
        }
        const form = document.getElementById("add-check-up-form");

        form.reset();
        // handle the vital sign
        const checkup_blood_pressure = document.getElementById(
            "add_checkup_blood_pressure",
        );
        const checkup_temperature = document.getElementById(
            "add_checkup_temperature",
        );
        const checkup_respiratory_rate = document.getElementById(
            "add_checkup_respiratory_rate",
        );
        const checkup_pulse_rate = document.getElementById(
            "add_checkup_pulse_rate",
        );
        const checkup_height = document.getElementById("add_checkup_height");
        const checkup_weight = document.getElementById("add_checkup_weight");

        if (
            checkup_blood_pressure &&
            checkup_temperature &&
            checkup_height &&
            checkup_weight &&
            checkup_respiratory_rate &&
            checkup_pulse_rate
        ) {
            vitalSignInputMask(
                checkup_blood_pressure,
                checkup_temperature,
                checkup_pulse_rate,
                checkup_respiratory_rate,
                checkup_height,
                checkup_weight,
            );
        }
    })
}

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = saveBtn.dataset.medicalId;

    const form = document.getElementById("add-check-up-form");
    const formData = new FormData(form);

    const response = await fetch(`/patient-record/add/check-up/tb-dots/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

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
            title: "Tb Dots Check-Up Record",
            text: capitalizeEachWord(errorMessage),
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        // reset the error element text first
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        // update the display record
        Livewire.dispatch("tbRefreshTable");
        Swal.fire({
            title: "Tb Dots Check-Up Record",
            text: capitalizeEachWord(data.message),
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("tbDotsAddCheckUpModal")
                );
                modal.hide();
            }
        });
    }
});

// -------------------------------- view the checkup information ------------------------------------

// EVENT DELEGATION FOR VIEW

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".tb-dots-view-check-up");
    if (!viewBtn) return;
    const id = viewBtn.dataset.caseId ?? null;

    const response = await fetch(`/patient-record/view-check-up/tb-dots/${id}`);

    const data = await response.json();
    if (!response.ok) {
        // console.log(data.errors);
    } else {
        Object.entries(data.checkUpInfo).forEach(([key, value]) => {
            if (key == "date_of_comeback") {
                if (document.getElementById(`view_${key}`)) {
                    const date = new Date(value);
                    const formatted = date.toISOString().split("T")[0];
                    document.getElementById(`view_${key}`).innerHTML =
                        formatted;
                }
            }
            if (document.getElementById(`view_checkup_${key}`)) {
                document.getElementById(`view_checkup_${key}`).innerHTML =
                    value ?? "N/A";
            }
             
        });
    }
});

const editCheckupBtn = document.querySelectorAll(".edit-check-up");
const editSaveBtn = document.getElementById("edit-checkup-save-btn");

document.addEventListener("click", async (e) => {
    const editCheckUpBtn = e.target.closest(".tb-dots-edit-check-up");
    // console.log("working");
    if (!editCheckUpBtn) return;
    const id = editCheckUpBtn.dataset.caseId ?? null;
    editSaveBtn.dataset.caseId = id;

    const errors = document.querySelectorAll(".error-text");
    if (errors) {
        errors.forEach((error) => (error.innerHTML = ""));
    }

    const response = await fetch(`/patient-record/view-check-up/tb-dots/${id}`);

    const data = await response.json();
    if (!response.ok) {
        // console.log(data.errors);
    } else {
        Object.entries(data.checkUpInfo).forEach(([key, value]) => {
            if (document.getElementById(`edit_checkup_${key}`)) {
                document.getElementById(`edit_checkup_${key}`).value =
                    value ?? "";
            }
            if (key == "date_of_comeback" && value != null) {
                const date = new Date(value);
                document.getElementById(`edit_${key}`).value = date
                    .toISOString()
                    .split("T")[0];
            }
        });

        // handle the vital sign
        const checkup_blood_pressure = document.getElementById(
            "edit_checkup_blood_pressure",
        );
        const checkup_temperature = document.getElementById(
            "edit_checkup_temperature",
        );
        const checkup_respiratory_rate = document.getElementById(
            "edit_checkup_respiratory_rate",
        );
        const checkup_pulse_rate = document.getElementById(
            "edit_checkup_pulse_rate",
        );
        const checkup_height = document.getElementById("edit_checkup_height");
        const checkup_weight = document.getElementById("edit_checkup_weight");

        if (
            checkup_blood_pressure &&
            checkup_temperature &&
            checkup_height &&
            checkup_weight &&
            checkup_respiratory_rate &&
            checkup_pulse_rate
        ) {
            vitalSignInputMask(
                checkup_blood_pressure,
                checkup_temperature,
                checkup_pulse_rate,
                checkup_respiratory_rate,
                checkup_height,
                checkup_weight,
            );
        }
    }
});

// update the data

editSaveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = editSaveBtn.dataset.caseId;
    const form = document.getElementById("edit-checkup-form");
    const formData = new FormData(form);

    const response = await fetch(
        `/patient-record/tb-dots/update-checkup/${id}`,
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
            title: "Update",
            text: capitalizeEachWord(errorMessage),
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        // reset the error element text first
        errorElements.forEach((element) => {
            element.textContent = "";
        });

        Swal.fire({
            title: "Update",
            text: capitalizeEachWord(data.message),
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("edit_tb_dots_checkup_Modal")
                );
                modal.hide();
            }
        });
    }
});

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

// delete a checkup record
document.addEventListener("click", async (e) => {
    const archiveBtn = e.target.closest(".tb-check-up-delete-btn");
    if (!archiveBtn) return;
    const id = archiveBtn.dataset.caseId;
    // console.log("caseId", id);

    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        // ✅ Show confirmation dialog FIRST
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "The Tb dots Check-up Record will be moved to archived status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
            cancelButtonText: "Cancel",
        });

        // ✅ Exit if user cancelled
        if (!result.isConfirmed) return;

        // ✅ Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error("CSRF token not found. Please refresh the page.");
        }

        const response = await fetch(
            `/patient-record/tb-dots/checkup/delete/${id}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken.content,
                    Accept: "application/json",
                },
            }
        );

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`
            );
        }

        // Success - refresh table
        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("seniorCitizenRefreshTable"); // ✅ Update dispatch name if needed
        }

        // Remove the row from DOM
        const row = archiveBtn.closest("tr");
        if (row) {
            row.remove();
        }

        // Show success message
        Swal.fire({
            title: "Archived!",
            text: "The Tb dots Check-up Record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
    } catch (error) {
        console.error("Error archiving case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});
