import Swal from "sweetalert2";

const addBTN = document.getElementById("tb_medicine_add_btn");
const addTableBody = document.getElementById("add_patient_tb_table_body");

if (addBTN) {
    addBTN.addEventListener("click", (e) => {
        const medicine = document.getElementById("tb_medicine");
        const dosage_n_frequency = document.getElementById(
            "tb_dosage_n_frequency",
        );
        const quantity = document.getElementById("tb_quantity");
        const start_date = document.getElementById("tb_start_date");
        const end_date = document.getElementById("tb_end_date");

        // Reset borders
        [medicine, dosage_n_frequency, quantity, start_date, end_date].forEach(
            (el) => (el.style.border = ""),
        );

        const errors = [];

        // Medicine validation
        if (medicine.value === "") {
            errors.push("Please select a medicine.");
            medicine.style.border = "1px solid red";
        }

        // Dosage validation
        if (dosage_n_frequency.value.trim() === "") {
            errors.push("Please enter the dosage and frequency.");
            dosage_n_frequency.style.border = "1px solid red";
        }

        // Quantity validation
        const quantityVal = parseInt(quantity.value);
        if (quantity.value === "") {
            errors.push("Please enter the quantity.");
            quantity.style.border = "1px solid red";
        } else if (isNaN(quantityVal) || quantityVal < 1) {
            // FIX: block negative and zero quantity
            errors.push("Quantity must be at least 1.");
            quantity.style.border = "1px solid red";
        }

        // Start date validation
       if (start_date.value === "") {
           errors.push("Please select a start date.");
           start_date.style.border = "1px solid red";
       } else if (start_date.value > new Date().toISOString().split("T")[0]) {
           errors.push("Start date cannot be a future date.");
           start_date.style.border = "1px solid red";
       }

        // End date validation
        if (end_date.value === "") {
            errors.push("Please select an end date.");
            end_date.style.border = "1px solid red";
        } else if (
            start_date.value !== "" &&
            end_date.value < start_date.value
        ) {
            // FIX: end date cannot be before start date
            errors.push("End date cannot be earlier than the start date.");
            end_date.style.border = "1px solid red";
        }

        if (errors.length > 0) {
            Swal.fire({
                title: "Missing or Invalid Information",
                html: errors.map((e) => `<div>• ${e}</div>`).join(""),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            return;
        }

        // Format dates for display
        const formatDate = (dateStr) => {
            const d = new Date(dateStr);
            return d.toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        };

        // Get the selected medicine label text, not just the value
        const medicineLabel = medicine.options[medicine.selectedIndex].text;

        addTableBody.innerHTML += `
            <tr class="senior-citizen-maintenance-record">
                <td>${medicineLabel}</td>
                <td>${dosage_n_frequency.value}</td>
                <td>${quantityVal}</td>
                <td>${formatDate(start_date.value)}</td>
                <td>${formatDate(end_date.value)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm medicine-remove">Remove</button>
                </td>
                <input type="hidden" name="medicines[]" value="${medicine.value}">
                <input type="hidden" name="dosage_n_frequencies[]" value="${dosage_n_frequency.value}">
                <input type="hidden" name="medicine_quantity[]" value="${quantityVal}">
                <input type="hidden" name="start_date[]" value="${start_date.value}">
                <input type="hidden" name="end_date[]" value="${end_date.value}">
            </tr>
        `;

        // Reset fields and borders
        [medicine, dosage_n_frequency, quantity, start_date, end_date].forEach(
            (el) => {
                el.style.border = "";
                el.value = "";
            },
        );

        // FIX: reset end date min after clearing
        end_date.min = "";
    });
}

// FIX: dynamic end date min — when start date changes, set end date min to start date
const tbStartDate = document.getElementById("tb_start_date");
const tbEndDate = document.getElementById("tb_end_date");

if (tbStartDate && tbEndDate) {
    tbStartDate.addEventListener("change", function () {
        if (this.value) {
            // Set end date minimum to selected start date
            tbEndDate.min = this.value;

            // If end date already has a value that is now before start date, clear it
            if (tbEndDate.value && tbEndDate.value < this.value) {
                tbEndDate.value = "";
                tbEndDate.style.border = "1px solid red";
                Swal.fire({
                    title: "Date Conflict",
                    text: "The previously selected end date was before the new start date and has been cleared. Please select a new end date.",
                    icon: "warning",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });
            } else {
                tbEndDate.style.border = "";
            }
        } else {
            // Start date cleared — remove end date restriction
            tbEndDate.min = "";
        }
    });

    // FIX: also block negative quantity via oninput
    const tbQuantity = document.getElementById("tb_quantity");
    if (tbQuantity) {
        tbQuantity.setAttribute("min", "1");
        tbQuantity.addEventListener("input", function () {
            if (this.value < 1) this.value = 1;
        });
    }
}

// Remove medicine row
if (addTableBody) {
    addTableBody.addEventListener("click", (e) => {
        if (e.target.closest(".medicine-remove")) {
            e.target.closest("tr").remove();
        }
    });
}

const tbDotsBtn = document.getElementById("tb_dots_save_record_btn");

tbDotsBtn.addEventListener("click", async (e) => {
    e.preventDefault();

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
                text: "TB Dots Patient Information is successfully Added",
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
                    // FIX: reset end date min after form reset
                    if (tbEndDate) tbEndDate.min = "";
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
