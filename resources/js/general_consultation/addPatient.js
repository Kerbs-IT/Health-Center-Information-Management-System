import Swal from "sweetalert2";

const gc_save_btn = document.getElementById("gc_save_record_btn");

if (gc_save_btn) {
    gc_save_btn.addEventListener("click", async (e) => {
        e.preventDefault();

        // --- Disable button and show Bootstrap spinner ---
        const originalHTML = gc_save_btn.innerHTML;
        gc_save_btn.disabled = true;
        gc_save_btn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Submitting...
    `;

        const restoreBtn = () => {
            gc_save_btn.disabled = false;
            gc_save_btn.innerHTML = originalHTML;
        };

        try {
            const handledBySelect = document.getElementById("handled_by");
            const handledByBackup =
                document.getElementById("handled_by_backup");

            if (handledBySelect && handledByBackup) {
                handledByBackup.value = handledBySelect.value;
            }

            const form = document.getElementById("add-patient-form");
            const formData = new FormData(form);

            const response = await fetch(
                "/patient-record/add-general-consultation-patient",
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                        Accept: "application/json",
                    },
                    body: formData,
                },
            );

            const data = await response.json();
            const errorElements = document.querySelectorAll(".error-text");

            if (response.ok) {
                errorElements.forEach((el) => (el.textContent = ""));

                await Swal.fire({
                    title: "General Consultation Patient",
                    text: "Patient is Successfully added.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (
                            typeof window.clearPatientRecordSelection ===
                            "function"
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
                            ? Object.values(data.errors).flat().join("<br>")
                            : data.errors;
                } else {
                    message = "An unexpected error occurred.";
                }

                await Swal.fire({
                    title: "General Consultation Patient",
                    html: capitalizeEachWord(message),
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
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}