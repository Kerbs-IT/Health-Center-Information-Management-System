import Swal from "sweetalert2";

// add medicine to the container
const addRecordBtn = document.getElementById("add_record_btn");

if (addRecordBtn) {
    addRecordBtn.addEventListener("click", () => {
        const errors = document.querySelectorAll(".error-text");
        errors.forEach((error) => (error.innerHTML = ""));
    });
}

const addBTN = document.getElementById("add-record-btn");
const addTableBody = document.getElementById("add-record-body");
if (addBTN) {
    addBTN.addEventListener("click", (e) => {
        const medicine = document.getElementById("add_maintenance_medication");
        const dosage_n_frequency = document.getElementById(
            "add_dosage_n_frequency"
        );
        const quantity = document.getElementById("add_maintenance_quantity");
        const start_date = document.getElementById(
            "add_maintenance_start_date"
        );
        const end_date = document.getElementById("add_maintenance_end_date");

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
            addTableBody.innerHTML += `
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
            // reset the value
            medicine.value = "";
            dosage_n_frequency.value = "";
            quantity.value = "";
            start_date.value = "";
            end_date.value = "";
        }
    });
}

// console.log(addTableBody);
// remove element
if (addTableBody) {
    // remove maintenance
    addTableBody.addEventListener("click", (e) => {
        // console.log("working delete");
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
        }
    });
}

// handle the request

const saveBtn = document.getElementById("add-new-record-save-btn");

saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = saveBtn.dataset.bsMedicalId;

    // form
    const form = document.getElementById("add-new-record-form");
    const formData = new FormData(form);

    const response = await fetch(
        `/patient-case/senior-citizen/new-case/${id}`,
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

        Swal.fire({
            title: "Add new Medicine Maintenance Record",
            text: Object.values(data.errors)
                .map((err) => err) // convert array of errors to text
                .join("\n"), // join with new lines
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        // reset the error element text first
        errorElements.forEach((element) => {
            element.textContent = "";
        });

        Livewire.dispatch("seniorCitizenRefreshTable");

        Swal.fire({
            title: "Add new Medicine Maintenance Record",
            text: capitalizeEachWord(data.message),
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

