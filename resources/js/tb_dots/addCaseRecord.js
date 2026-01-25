import { error } from "jquery";

const addTable = document.getElementById("add_tb_tbody");
if (addTable) {
    // remove maintenance
    addTable.addEventListener("click", (e) => {
        // console.log("working delete");
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
        }

        // output if the container is empty
        if (addTable.children.length === 0) {
            addTable.innerHTML += `<tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No records available.
                    </td>
                </tr>`;
        }
    });

    
}

// add new item to the container
const addBTN = document.getElementById("add_tb_medicine_add_btn");

if (addBTN) {
    addBTN.addEventListener("click", (e) => {
        const medicine = document.getElementById("add_tb_medicine");
        const dosage_n_frequency = document.getElementById(
            "add_tb_dosage_n_frequency"
        );
        const quantity = document.getElementById("add_tb_quantity");
        const start_date = document.getElementById("add_tb_start_date");
        const end_date = document.getElementById("add_tb_end_date");

        if (
            medicine.value == "" ||
            dosage_n_frequency.value == "" ||
            quantity.value == "" ||
            start_date.value == "" ||
            end_date.value == ""
        ) {
            Swal.fire({
                title: "Missing Information",
                text: "Information provided is incomplete or invalid.", // this will make the text capitalize each word
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
            addTable.innerHTML = '';
            addTable.innerHTML += `
                <tr class="senior-citizen-maintenance-record" >
                    <td>${medicine.value}</td>
                    <td>${dosage_n_frequency.value}</td>
                    <td>${quantity.value}</td>
                    <td>${start_date.value}</td>
                    <td>${end_date.value}</td>
                    <td>
                        <button type=button class="btn btn-danger btn-sm medicine-remove">Remove</button>
                    </td>
                    <input type="hidden" name="add_medicines[]" value="${medicine.value}">
                    <input type="hidden" name="add_dosage_n_frequencies[]" value="${dosage_n_frequency.value}">
                    <input type="hidden" name="add_medicine_quantity[]" value="${quantity.value}">
                    <input type="hidden" name="add_start_date[]" value="${start_date.value}">
                    <input type="hidden" name="add_end_date[]" value="${end_date.value}">
                </tr>
            `;

            // reset the borders
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
            // reset the value
            medicine.value = "";
            dosage_n_frequency.value = "";
            quantity.value = "";
            start_date.value = "";
            end_date.value = "";
        }
    });
}

// === click the add case btn
const addCaseBtn = document.getElementById("add-case-record-btn") ?? null;
const addCaseSaveBtn = document.getElementById("add_case_save_btn");
if (addCaseBtn) {
    addCaseBtn.addEventListener('click', (e) => {
        const patientInfo = JSON.parse(addCaseBtn.dataset.patientInfo);

        const form = document.getElementById("add_tb_dots_case_record_form");
        // reset the form
        form.reset();

        const errors = document.querySelectorAll(".error-text");
        if (errors) {
            errors.forEach(error => error.innerHTML = '');
        }

        // console.log(patientInfo);

        // get the id of important input fields
        const patientNameElement = document.getElementById("add_tb_case_patient_name")??null;
        const healthWorkerIdElement = document.getElementById("add_tb_health_worker_id") ?? null;
        
        // display name
        const displayName = document.getElementById("display_add_tb_case_patient_name");
        if (displayName) {
            displayName.value = patientInfo.patient.full_name;
        }

        // give the value
        if (patientNameElement && healthWorkerIdElement) {
            patientNameElement.value = patientInfo.patient.full_name;
            healthWorkerIdElement.value = patientInfo.tb_dots_medical_record.health_worker_id;
        }
        // give the medical id to the saveBtn
        if (addCaseSaveBtn) {
            addCaseSaveBtn.dataset.medicalRecordCaseId = patientInfo.id;
        }

    });
}

addCaseSaveBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    const id = addCaseSaveBtn.dataset.medicalRecordCaseId;

    const form = document.getElementById("add_tb_dots_case_record_form");
    const formData = new FormData(form);

    try {
        const response = await fetch(
            `/patient-record/tb-dots/add/case-record/${id}`,
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

        // get all the error elements
        const errorElements = document.querySelectorAll(".error-text");

        if (response.ok) {
            errorElements.forEach((element) => {
                element.textContent = "";
            });
            if (typeof Livewire !== "undefined") {
                Livewire.dispatch("tbRefreshTable"); // ✅ Update dispatch name if needed
            }
            Swal.fire({
                title: "Add Prenatal Case",
                text: data.message, // this will make the text capitalize each word
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("tbDotsCaseRecordModal")
                    );
                    modal.hide();
                    form.reset();
                }
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
                title: "Add Tb Dots Case",
                text: capitalizeEachWord(message), // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    } catch (error) {
        console.error("Error adding case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to add record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}