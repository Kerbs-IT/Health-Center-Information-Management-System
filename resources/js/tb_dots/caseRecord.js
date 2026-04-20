import Swal from "sweetalert2";

const viewBtn = document.querySelectorAll(".viewCaseBtn");
const viewTbody = document.getElementById("view-table-body");

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".viewCaseBtn");
    if (!viewBtn) return;
    const id = viewBtn.dataset.caseId;
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        return;
    }

    try {
        const response = await fetch(`/patient/tb-dots/get-case-info/${id}`);

        if (!response.ok) {
        } else {
            const data = await response.json();

            Object.entries(data.caseInfo).forEach(([key, value]) => {
                if (key == "date_of_comeback") {
                    if (document.getElementById(`view_${key}`)) {
                        const date = new Date(value);
                        const formatted = date.toISOString().split("T")[0];
                        document.getElementById(`view_${key}`).innerHTML =
                            formatted;
                    }
                }
                if (document.getElementById(`view_${key}`)) {
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });

            viewTbody.innerHTML = "";

            data.caseInfo.tb_dots_maintenance_med.forEach((meds) => {
                viewTbody.innerHTML += `
                    <tr>
                        <td>${meds.medicine_name}</td>
                        <td>${meds.dosage_n_frequency}</td>
                        <td>${meds.quantity}</td>
                        <td>${meds.start_date}</td>
                        <td>${meds.end_date}</td>
                    </tr>`;
            });

            const viewHealthWorker = document.getElementById(
                "view_assigned_health_worker",
            );
            viewHealthWorker.innerHTML = `
                ${data.healthWorker.first_name ?? ""}
                ${data.healthWorker.middle_initial ?? ""}
                ${data.healthWorker.last_name ?? ""}
            `.trim();
        }
    } catch (error) {
        console.error("Error archiving case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to view record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// edit case
const editTable = document.getElementById("edit_tb_tbody");
let saveBtn = document.getElementById("edit_save_btn");

document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".editCaseBtn");
    if (!editBtn) return;
    const id = editBtn.dataset.caseId;
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        return;
    }

    const errors = document.querySelectorAll(".error-text");
    if (errors) {
        errors.forEach((error) => (error.innerHTML = ""));
    }

    try {
        const response = await fetch(`/patient/tb-dots/get-case-info/${id}`);

        if (!response.ok) {
        } else {
            const data = await response.json();

            Object.entries(data.caseInfo).forEach(([key, value]) => {
                if (document.getElementById(`edit_${key}`)) {
                    document.getElementById(`edit_${key}`).value = value;
                }
            });

            editTable.innerHTML = "";

            data.caseInfo.tb_dots_maintenance_med.forEach((meds) => {
                editTable.innerHTML += `
                    <tr>
                        <td>${meds.medicine_name}</td>
                        <td>${meds.dosage_n_frequency}</td>
                        <td>${meds.quantity}</td>
                        <td>${meds.start_date}</td>
                        <td>${meds.end_date}</td>
                        <td>
                            <button type=button class="btn btn-danger btn-sm medicine-remove">Remove</button>
                        </td>
                        <input type="hidden" name="medicines[]" value="${meds.medicine_name}">
                        <input type="hidden" name="dosage_n_frequencies[]" value="${meds.dosage_n_frequency}">
                        <input type="hidden" name="medicine_quantity[]" value="${meds.quantity}">
                        <input type="hidden" name="start_date[]" value="${meds.start_date}">
                        <input type="hidden" name="end_date[]" value="${meds.end_date}">
                    </tr>`;
            });
        }

        saveBtn.dataset.caseId = id;
    } catch (error) {
        console.error("Error archiving case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to edit record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// remove element
if (editTable) {
    editTable.addEventListener("click", (e) => {
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
        }
    });
}

// add new item to the container
const addBTN = document.getElementById("edit_tb_medicine_add_btn");

if (addBTN) {
    addBTN.addEventListener("click", (e) => {
        const medicine = document.getElementById("edit_tb_medicine");
        const dosage_n_frequency = document.getElementById(
            "edit_tb_dosage_n_frequency",
        );
        const quantity = document.getElementById("edit_tb_quantity");
        const start_date = document.getElementById("edit_tb_start_date");
        const end_date = document.getElementById("edit_tb_end_date");

        const errors = document.querySelectorAll(".error-text");
        if (errors) {
            errors.forEach((error) => (error.innerHTML = ""));
        }

        if (
            medicine.value == "" ||
            dosage_n_frequency.value == "" ||
            quantity.value == "" ||
            start_date.value == "" ||
            end_date.value == ""
        ) {
            Swal.fire({
                title: "Missing Information",
                text: "Information provided is incomplete or invalid.",
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            medicine.style.border =
                medicine.value === "" ? "1px solid red" : "";
            dosage_n_frequency.style.border =
                dosage_n_frequency.value === "" ? "1px solid red" : "";
            quantity.style.border =
                quantity.value === "" ? "1px solid red" : "";
            start_date.style.border =
                start_date.value === "" ? "1px solid red" : "";
            end_date.style.border =
                end_date.value === "" ? "1px solid red" : "";
        } else {
            editTable.innerHTML += `
                <tr class="senior-citizen-maintenance-record" >
                    <td>${medicine.value}</td>
                    <td>${dosage_n_frequency.value}</td>
                    <td>${quantity.value}</td>
                    <td>${start_date.value}</td>
                    <td>${end_date.value}</td>
                    <td>
                        <button type=button class="btn btn-danger btn-sm medicine-remove">Remove</button>
                    </td>
                    <input type="hidden" name="medicines[]" value="${medicine.value}">
                    <input type="hidden" name="dosage_n_frequencies[]" value="${dosage_n_frequency.value}">
                    <input type="hidden" name="medicine_quantity[]" value="${quantity.value}">
                    <input type="hidden" name="start_date[]" value="${start_date.value}">
                    <input type="hidden" name="end_date[]" value="${end_date.value}">
                </tr>
            `;

            medicine.style.border =
                medicine.value === "" ? "1px solid red" : "";
            dosage_n_frequency.style.border =
                dosage_n_frequency.value === "" ? "1px solid red" : "";
            quantity.style.border =
                quantity.value === "" ? "1px solid red" : "";
            start_date.style.border =
                start_date.value === "" ? "1px solid red" : "";
            end_date.style.border =
                end_date.value === "" ? "1px solid red" : "";
            medicine.value = "";
            dosage_n_frequency.value = "";
            quantity.value = "";
            start_date.value = "";
            end_date.value = "";
        }
    });
}

// ============================================================================
// EDIT SAVE — with button state management
// ============================================================================

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = saveBtn.dataset.caseId;
    const originalText = saveBtn.innerHTML;

    saveBtn.disabled = true;
    saveBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

    try {
        const form = document.getElementById("edit_case_info");
        const formData = new FormData(form);

        const response = await fetch(`/patient-case/tb-dots/update/${id}`, {
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

        if (response.ok) {
            errorElements.forEach((element) => {
                element.textContent = "";
            });

            Swal.fire({
                title: "Tuberculosis Medical Record Details Updated Successfully",
                text: capitalizeEachWord(data.message),
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;

                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("edit_Tb_dots_Record_Modal"),
                    );
                    modal.hide();
                    form.reset();
                }
            });
        } else {
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
                title: "Tuberculosis Medical Record Details Updated Successfully",
                text: capitalizeEachWord(errorMessage),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });

            // Re-enable on validation error
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error("Error updating record:", error);

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

// ============================================================================
// ARCHIVE — with button state management
// ============================================================================

document.addEventListener("click", async (e) => {
    const deleteBtn = e.target.closest(".archiveCaseBtn");
    if (!deleteBtn) return;
    const id = deleteBtn.dataset.caseId;

    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "Tb dots Case Record will be moved to archived status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
            cancelButtonText: "Cancel",
        });

        if (!result.isConfirmed) return;

        // Disable after confirmation
        const originalHTML = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error("CSRF token not found. Please refresh the page.");
        }

        const response = await fetch(
            `/patient-record/tb-dots/case-record/delete/${id}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken.content,
                    Accept: "application/json",
                },
            },
        );

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`,
            );
        }

        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("tbRefreshTable");
        }

        const row = deleteBtn.closest("tr");
        if (row) {
            row.remove();
        }

        Swal.fire({
            title: "Archived!",
            text: "Tb dots Case Record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
    } catch (error) {
        console.error("Error archiving case:", error);

        // Re-enable on error
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalHTML;

        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
