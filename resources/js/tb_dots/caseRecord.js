import Swal from "sweetalert2";
// get the case info

const viewBtn = document.querySelectorAll(".viewCaseBtn");
const viewTbody = document.getElementById("view-table-body");

viewBtn.forEach((btn) => {
    btn.addEventListener("click", async (e) => {
        const id = btn.dataset.caseId;
        const response = await fetch(`/patient/tb-dots/get-case-info/${id}`);

        if (!response.ok) {
        } else {
            const data = await response.json();

            Object.entries(data.caseInfo).forEach(([key, value]) => {
                if (document.getElementById(`view_${key}`)) {
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });

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

            // assign the health worker
            const viewHealthWorker = document.getElementById(
                "view_assigned_health_worker"
            );

            viewHealthWorker.innerHTML = `
                ${data.healthWorker.first_name ?? ""}
                ${data.healthWorker.middle_initial ?? ""}
                ${data.healthWorker.last_name ?? ""}
            `.trim();
        }
    });
});

// edit case
const editBtn = document.querySelectorAll(".editCaseBtn");
const editTable = document.getElementById("edit_tb_tbody");
let saveBtn = document.getElementById("edit_save_btn");
editBtn.forEach((btn) => {
    btn.addEventListener("click", async (e) => {
        const id = btn.dataset.caseId;
        const response = await fetch(`/patient/tb-dots/get-case-info/${id}`);

        if (!response.ok) {
        } else {
            const data = await response.json();
            // loop through the select input
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
    });
});

// remove element
if (editTable) {
    // remove maintenance
    editTable.addEventListener("click", (e) => {
        console.log("working delete");
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
            "edit_tb_dosage_n_frequency"
        );
        const quantity = document.getElementById("edit_tb_quantity");
        const start_date = document.getElementById("edit_tb_start_date");
        const end_date = document.getElementById("edit_tb_end_date");

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

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const id = saveBtn.dataset.caseId;

    const form = document.getElementById("edit_case_info");
    const formData = new FormData(form);

    const response = await fetch(`/patient-case/tb-dots/update/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });
    if (response.ok) {
        Swal.fire({
            title: "Prenatal Patient",
            text: "Patient is Successfully Updated.", // this will make the text capitalize each word
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        Swal.fire({
            title: "Prenatal Patient",
            text: "Error occur Patient is not Successfully added.", // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
