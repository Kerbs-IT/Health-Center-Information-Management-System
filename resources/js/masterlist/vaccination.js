import { puroks } from "../patient/healthWorkerList.js";
import { automateAge } from "../automateAge.js";
import Swal from "sweetalert2";
import { displayAage } from "../automateAge.js";

const saveBtn = document.getElementById(
    "update_vaccination_masterlist_save_btn",
);
const dob = document.getElementById("birthdate");
const age = document.getElementById("age");
const hiddenAge = document.getElementById("hiddenAge");

document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".vaccination-masterlist-edit-btn");
    if (!editBtn) return;

    const id = editBtn.dataset.masterlistId;

    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid masterlist ID:", id);
        alert("Unable to open record: Invalid ID");
        return;
    }

    // Reset all error messages
    document
        .querySelectorAll(".error-text")
        .forEach((el) => (el.innerHTML = ""));

    try {
        const response = await fetch(`/masterist/vaccination/${id}`, {
            headers: { Accept: "application/json" },
        });

        if (!response.ok) {
            throw new Error(`Server responded with status ${response.status}`);
        }

        const data = await response.json();

        // Populate fields from API response
        Object.entries(data.info).forEach(([key, value]) => {
            if (key === "name_of_child") {
                document.getElementById("vaccination_masterlist_fname").value =
                    data.patientDetails.first_name ?? "";
                document.getElementById("vaccination_masterlist_MI").value =
                    data.patientDetails.middle_initial ?? "";
                document.getElementById("vaccination_masterlist_lname").value =
                    data.patientDetails.last_name ?? "";
                document.getElementById("vaccination_masterlist_suffix").value =
                    data.patientDetails.suffix ?? "";
            } else if (key === "date_of_birth") {
                const input = document.querySelector(`input[name="${key}"]`);
                if (input) {
                    // FIX Risk 6: avoid timezone offset shifting the date by 1 day.
                    // Instead of new Date(value).toISOString(), just slice the date part directly.
                    const formatted = value ? value.split("T")[0] : "";
                    input.value = formatted;
                    if (formatted) {
                        input.classList.add(
                            "bg-light",
                            "border-dark",
                            "border-2",
                        );
                    } else {
                        input.classList.remove(
                            "bg-light",
                            "border-dark",
                            "border-2",
                        );
                    }
                }
            } else if (key === "age" && value != null) {
                const ageEl = document.getElementById("age");
                const hiddenAgeEl = document.getElementById("hiddenAge");
                if (ageEl) ageEl.value = value;
                if (hiddenAgeEl) hiddenAgeEl.value = value;
            } else if (key === "sex") {
                document
                    .querySelectorAll('input[name="sex"]')
                    .forEach((input) => {
                        input.checked = input.value === value;
                    });
            } else if (key === "Address") {
                const addressText = [
                    data.address_info.house_number,
                    data.address_info.street,
                ]
                    .filter(Boolean)
                    .join(", ")
                    .trim();

                const streetEl = document.getElementById("street");
                if (streetEl) streetEl.value = addressText;

                const brgyEl = document.getElementById("brgy");
                if (brgyEl) puroks(brgyEl, data.address_info.purok);
            } else {
                // Generic field handler — covers BCG, Hepatitis B, PENTA_1, etc.
                const input = document.querySelector(`input[name="${key}"]`);
                if (input) {
                    // FIX Risk 6: same timezone-safe slicing for all date fields
                    if (input.type === "date" && value) {
                        input.value = value.split("T")[0];
                    } else {
                        input.value = value ?? "";
                    }

                    if (value != null && value !== "") {
                        input.classList.add(
                            "bg-light",
                            "border-dark",
                            "border-2",
                        );
                    } else {
                        input.classList.remove(
                            "bg-light",
                            "border-dark",
                            "border-2",
                        );
                    }
                }
            }
        });

        // Recompute displayed age from DOB
        if (dob && age && hiddenAge) {
            displayAage(dob, age, hiddenAge);
        }

        // Attach record ID to save button for the update request
        saveBtn.dataset.medicalRecordCaseId = data.info.medical_record_case_id;
    } catch (error) {
        console.error("Error fetching vaccination record:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to load record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';

    try {
        const form = document.getElementById(
            "edit-vaccination-masterlist-form",
        );
        const formData = new FormData(form);
        const id = e.target.dataset.medicalRecordCaseId;

        const response = await fetch(`/masterlist/update/vaccination/${id}`, {
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
            // Clear previous errors
            errorElements.forEach((el) => (el.textContent = ""));

            // Map validation errors to their elements
            if (data.errors) {
                Object.entries(data.errors).forEach(([key, value]) => {
                    // FIX: error element IDs use underscores, but field name may have spaces
                    // e.g. key "Hepatitis B" -> look for "Hepatitis_B_error"
                    const safeKey = key.replace(/ /g, "_");
                    const errorEl = document.getElementById(`${safeKey}_error`);
                    if (errorEl)
                        errorEl.textContent = Array.isArray(value)
                            ? value[0]
                            : value;
                });
            }

            const errorMessage = data.errors
                ? Object.values(data.errors).flat().join("\n")
                : (data.message ?? "An unexpected error occurred.");

            Swal.fire({
                title: "Error",
                text: capitalizeEachWord(errorMessage),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then(() => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        } else {
            errorElements.forEach((el) => (el.textContent = ""));

            Swal.fire({
                title: "Update",
                text: capitalizeEachWord(data.message),
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById(
                            "editvaccinationMasterListModal",
                        ),
                    );
                    if (modal) modal.hide();
                }

                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;

                if (typeof Livewire !== "undefined") {
                    Livewire.dispatch("vaccinationMasterlistRefreshTable");
                }
            });
        }
    } catch (error) {
        console.error("Error updating vaccination record:", error);
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    }
});

// Automate age calculation from DOB
if (dob && age && hiddenAge) {
    automateAge(dob, age, hiddenAge);
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
