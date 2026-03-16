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

const tbDotsBtn = document.getElementById("tb_dots_save_record_btn");

tbDotsBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    // --- Disable button and show Bootstrap spinner ---
    const originalHTML = tbDotsBtn.innerHTML;
    tbDotsBtn.disabled = true;
    tbDotsBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Submitting...
    `;

    const restoreBtn = () => {
        tbDotsBtn.disabled = false;
        tbDotsBtn.innerHTML = originalHTML;
    };

    try {
        const handledBySelect = document.getElementById("handled_by");
        const handledByBackup = document.getElementById("handled_by_backup");

        if (handledBySelect && handledByBackup) {
            handledByBackup.value = handledBySelect.value;
        }

        const form = document.getElementById("add-patient-form");
        const formData = new FormData(form);

        const response = await fetch("/patient-record/add/tb-dots", {
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
            errorElements.forEach((el) => (el.textContent = ""));

            await Swal.fire({
                title: "Add",
                text: "Tb Dots Patient Information is successfully Added",
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    if (
                        typeof window.clearPatientRecordSelection === "function"
                    ) {
                        window.clearPatientRecordSelection();
                    }
                    form.reset();
                    window.currentStep = 1;
                    window.showStep(window.currentStep);
                }
            });
        } else {
            errorElements.forEach((el) => (el.textContent = ""));

            Object.entries(data.errors).forEach(([key, value]) => {
                const el = document.getElementById(`${key}_error`);
                if (el) el.textContent = value;
            });

            let message = "";
            if (data.errors) {
                message =
                    typeof data.errors === "object"
                        ? Object.values(data.errors).flat().join("\n")
                        : data.errors;
            } else {
                message = "An unexpected error occurred.";
            }

            await Swal.fire({
                title: "TB Dots Patient",
                text: capitalizeEachWord(message),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    } catch (err) {
        console.error("Submission error:", err);
    } finally {
        restoreBtn();
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
