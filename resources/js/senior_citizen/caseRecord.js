import Swal from "sweetalert2";
const viewCaseBtn = document.querySelectorAll(".viewCaseBtn");

viewCaseBtn.forEach((btn) => {
    btn.addEventListener("click", async (e) => {
        e.preventDefault();
        console.log("working");
        const id = btn.dataset.bsCaseId;
        console.log(id);

        const response = await fetch(
            `/senior-citizen/case-details/${Number(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            }
        );
        if (response.ok) {
            const data = await response.json();

            Object.entries(data.seniorCaseRecord).forEach(([key, value]) => {
                console.log(key);
                if (document.getElementById(`view_${key}`)) {
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });

            const tableBody = document.getElementById("viewCaseBody");
            tableBody.innerHTML = ""; // reset the table first
            data.seniorCaseRecord.senior_citizen_maintenance_med.forEach(
                (record) => {
                    console.log(record);
                    tableBody.innerHTML += ` 
                        <tr>
                            <td>${record.maintenance_medication}</td>
                            <td>${record.dosage_n_frequency}</td>
                            <td>${record.quantity}</td>
                            <td>${record.start_date}</td>
                            <td>${record.end_date}</td>
                           
                        </tr>`;
                }
            );
        }
    });
});

// edit case id
const editBtn = document.querySelectorAll(".editCaseBtn");
const editTableBody = document.getElementById("edit-tbody");

const saveBtn = document.getElementById("edit-save-btn");

editBtn.forEach((btn) => {
    btn.addEventListener("click", async (e) => {
        e.preventDefault();
        console.log("working");
        const id = btn.dataset.bsCaseId;

        saveBtn.dataset.medicalId = btn.dataset.bsCaseId;
        console.log(id);

        const response = await fetch(
            `/senior-citizen/case-details/${Number(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            }
        );
        if (response.ok) {
            const data = await response.json();

            Object.entries(data.seniorCaseRecord).forEach(([key, value]) => {
                if (document.getElementById(`edit_${key}`)) {
                    document.getElementById(`edit_${key}`).value = value;
                }
            });

            editTableBody.innerHTML = ""; // reset the table first
            data.seniorCaseRecord.senior_citizen_maintenance_med.forEach(
                (record) => {
                    console.log(record);
                    editTableBody.innerHTML += ` 
                            <tr class="senior-citizen-maintenance">
                                <td>${record.maintenance_medication}</td>
                                <td>${record.dosage_n_frequency}</td>
                                <td>${record.quantity}</td>
                                <td>${record.start_date}</td>
                                <td>${record.end_date}</td>
                                <td>
                                    <button type=button class="btn btn-danger btn-sm medicine-remove">Remove</button>
                                </td>
                                <input type="hidden" name="medicines[]" value="${record.maintenance_medication}">
                                <input type="hidden" name="dosage_n_frequencies[]" value="${record.dosage_n_frequency}">
                                <input type="hidden" name="maintenance_quantity[]" value="${record.quantity}">
                                <input type="hidden" name="start_date[]" value="${record.start_date}">
                                <input type="hidden" name="end_date[]" value="${record.end_date}">
                            </tr>`;
                }
            );
        }
    });
});

if (editTableBody) {
    // remove maintenance
    editTableBody.addEventListener("click", (e) => {
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
        }
    });
}

// add medicine to the container
const addBTN = document.getElementById("edit-add-btn");
if (addBTN) {
    addBTN.addEventListener("click", (e) => {
        const medicine = document.getElementById("edit_maintenance_medication");
        const dosage_n_frequency = document.getElementById(
            "edit_dosage_n_frequency"
        );
        const quantity = document.getElementById("edit_maintenance_quantity");
        const start_date = document.getElementById(
            "edit_maintenance_start_date"
        );
        const end_date = document.getElementById("edit_maintenance_end_date");

        if (
            medicine.value == "" &&
            dosage_n_frequency.value == "" &&
            quantity.value == "" &&
            start_date.value == "" &&
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
            editTableBody.innerHTML += `
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
                    <input type="hidden" name="maintenance_quantity[]" value="${quantity.value}">
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

            medicine.value = "";
            dosage_n_frequency.value = "";
            quantity.value = "";
            start_date.value = "";
            end_date.value = "";
        }
    });
}

// update the records

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = saveBtn.dataset.medicalId; // id of the case
    const form = document.getElementById("edit-senior-citizen-form");
    const formData = new FormData(form);

    const response = await fetch(`/patient-case/senior-citizen/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

    if (!response.ok) {
        Swal.fire({
            title: "Update",
            text: Object.values(data.errors)
                .map((err) => err) // convert array of errors to text
                .join("\n"), // join with new lines
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        const data = await response.json();
        Swal.fire({
            title: "Update",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
