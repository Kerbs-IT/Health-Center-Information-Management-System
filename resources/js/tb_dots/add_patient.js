import Swal from "sweetalert2";

// add medicine to the container
const addRecordBtn = document.getElementById("add_record_btn");

const addBTN = document.getElementById("tb_medicine_add_btn");
const addTableBody = document.getElementById("add_patient_tb_table_body");
if (addBTN) {
    addBTN.addEventListener("click", (e) => {
        const medicine = document.getElementById("tb_medicine");
        const dosage_n_frequency = document.getElementById(
            "tb_dosage_n_frequency"
        );
        const quantity = document.getElementById("tb_quantity");
        const start_date = document.getElementById("tb_start_date");
        const end_date = document.getElementById("tb_end_date");

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

console.log(addTableBody);
// remove element
if (addTableBody) {
    // remove maintenance
    addTableBody.addEventListener("click", (e) => {
        console.log("working delete");
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
        }
    });
}


// upload the data
const tbDotsBtn = document.getElementById('tb_dots_save_record_btn');

tbDotsBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("add-patient-form");
    const formData = new FormData(form);

    const response = await fetch('/patient-record/add/tb-dots', {
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
            text: "Patient is Successfully added.", // this will make the text capitalize each word
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