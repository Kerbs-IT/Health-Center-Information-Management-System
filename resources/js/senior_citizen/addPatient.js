import Swal from "sweetalert2";

const addMedicationBTN = document.getElementById("medication_add_btn");
// container
const tableBody = document.getElementById("medication_table_body");

if (addMedicationBTN) {
    addMedicationBTN.addEventListener("click", (e) => {
        const medicine = document.getElementById("maintenance_medication");
        const dosage_n_frequency =
            document.getElementById("dosage_n_frequency");
        const quantity = document.getElementById("maintenance_quantity");
        const start_date = document.getElementById("maintenance_start_date");
        const end_date = document.getElementById("maintenance_end_date");

        if (
            medicine.value != "" &&
            dosage_n_frequency.value != "" &&
            quantity.value != "" &&
            start_date.value != "" &&
            end_date.value != ""
        ) {
            tableBody.innerHTML += `
                <tr class="senior-citizen-maintenance-record" >
                    <td>${medicine.value}</td>
                    <td>${dosage_n_frequency.value}</td>
                    <td>${quantity.value}</td>
                    <td>${start_date.value}</td>
                    <td>${end_date.value}</td>
                    <td class=" align-middle text-center">
                        <div class="delete-icon maintenance-remove-icon d-flex align-items-center justify-self-center w-100 h-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" width="20" height="20" viewBox="0 0 448 512">
                                <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                            </svg>
                        </div>
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

            // reset value
            medicine.value = "";
            dosage_n_frequency.value = "";
            quantity.value = "";
            start_date.value = "";
            end_date.value = "";
        } else {
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
        }
    });
}

// remove element
if (tableBody) {
    tableBody.addEventListener("click", (e) => {
        if (e.target.closest(".senior-citizen-maintenance-record")) {
            if (e.target.closest(".maintenance-remove-icon")) {
                e.target.closest("tr").remove();
            }
        }
    });
}

// upload the data
const seniorCitizenBtn = document.getElementById(
    "senior_citizen_save_record_btn"
);

seniorCitizenBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("add-patient-form");
    const formData = new FormData(form);

    const response = await fetch("/patient-record/add/senior-citizen-record", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

    const data = await response.json();

    // get all the error elements
    const errorElements = document.querySelectorAll(".error-text");

    if (response.ok) {
         errorElements.forEach((element) => {
             element.textContent = "";
         });
        
        Swal.fire({
            title: "Prenatal Patient",
            text: "Patient is Successfully added.", // this will make the text capitalize each word
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                // reset the steps
                form.reset();
                window.currentStep = 1;
                window.showStep(window.currentStep);
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
            title: "Senior Citizen Patient",
            text: capitalizeEachWord(message), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

