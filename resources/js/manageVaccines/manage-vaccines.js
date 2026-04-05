import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
    function clearModalErrors() {
        ["typeOfVaccine", "vaccineAcronym", "maxDoses"].forEach((field) => {
            const errEl = document.getElementById(field + "Error");
            const inputEl = document.getElementById(field);
            if (errEl) {
                errEl.textContent = "";
                errEl.classList.add("d-none");
            }
            if (inputEl) inputEl.classList.remove("is-invalid");
        });
    }

    function showFieldError(field, message) {
        const errEl = document.getElementById(field + "Error");
        const inputEl = document.getElementById(field);
        if (errEl) {
            errEl.textContent = message;
            errEl.classList.remove("d-none");
        }
        if (inputEl) inputEl.classList.add("is-invalid");
    }

    function setSaveLoading(loading) {
        const btnText = document.getElementById("saveBtnText");
        const btnSpinner = document.getElementById("saveBtnSpinner");
        const saveBtn = document.getElementById("saveVaccineBtn");
        if (btnText)
            btnText.textContent = loading
                ? "Saving..."
                : saveBtn.dataset.mode === "edit"
                  ? "Update Vaccine"
                  : "Save Vaccine";
        if (btnSpinner) btnSpinner.classList.toggle("d-none", !loading);
        if (saveBtn) saveBtn.disabled = loading;
    }

    function getModalInstance() {
        return bootstrap.Modal.getOrCreateInstance(
            document.getElementById("vaccineModal"),
        );
    }

    // -------------------------------------------------------------------------
    // Open Add Modal (called from Livewire blade via onclick)
    // -------------------------------------------------------------------------
    window.openAddModal = function () {
        clearModalErrors();
        document.getElementById("vaccineId").value = "";
        document.getElementById("typeOfVaccine").value = "";
        document.getElementById("vaccineAcronym").value = "";
        document
            .querySelectorAll('input[name="maxDoses"]')
            .forEach((r) => (r.checked = false));
        document.getElementById("modalTitleText").textContent = "Add Vaccine";
        document.getElementById("saveVaccineBtn").dataset.mode = "add";

        const btnText = document.getElementById("saveBtnText");
        if (btnText) btnText.textContent = "Save Vaccine";

        getModalInstance().show();
    };

    // -------------------------------------------------------------------------
    // Open Edit Modal (called from Livewire blade via onclick)
    // -------------------------------------------------------------------------
    window.openEditModal = function (id, name, acronym, maxDoses) {
        clearModalErrors();
        document.getElementById("vaccineId").value = id;
        document.getElementById("typeOfVaccine").value = name;
        document.getElementById("vaccineAcronym").value = acronym;

        const radio = document.querySelector(
            `input[name="maxDoses"][value="${maxDoses}"]`,
        );
        if (radio) radio.checked = true;

        document.getElementById("modalTitleText").textContent = "Edit Vaccine";
        document.getElementById("saveVaccineBtn").dataset.mode = "edit";

        const btnText = document.getElementById("saveBtnText");
        if (btnText) btnText.textContent = "Update Vaccine";

        getModalInstance().show();
    };

    // -------------------------------------------------------------------------
    // Save — Add or Edit
    // -------------------------------------------------------------------------
    document
        .getElementById("saveVaccineBtn")
        .addEventListener("click", async function () {
            clearModalErrors();

            const id = document.getElementById("vaccineId").value;
            const name = document.getElementById("typeOfVaccine").value.trim();
            const acronym = document
                .getElementById("vaccineAcronym")
                .value.trim();
            const doseEl = document.querySelector(
                'input[name="maxDoses"]:checked',
            );
            const maxDoses = doseEl ? doseEl.value : null;

            const isEdit = id !== "";
            const url = isEdit ? `/api/vaccines/${id}` : "/api/vaccines";
            const method = isEdit ? "PUT" : "POST";

            setSaveLoading(true);

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        type_of_vaccine: name,
                        vaccine_acronym: acronym,
                        max_doses: maxDoses ? parseInt(maxDoses) : null,
                    }),
                });

                const data = await res.json();

                // Validation errors
                if (res.status === 422) {
                    const errors = data.errors || {};
                    if (errors.type_of_vaccine)
                        showFieldError(
                            "typeOfVaccine",
                            errors.type_of_vaccine[0],
                        );
                    if (errors.vaccine_acronym)
                        showFieldError(
                            "vaccineAcronym",
                            errors.vaccine_acronym[0],
                        );
                    if (errors.max_doses)
                        showFieldError("maxDoses", errors.max_doses[0]);
                    if (errors.server) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: errors.server[0],
                        });
                    }
                    return;
                }

                if (!res.ok) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.message || "Something went wrong.",
                    });
                    return;
                }

                // Success
                getModalInstance().hide();

                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                });

                Livewire.dispatch("manageVaccineRefresh");
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Unexpected Error",
                    text: "Please try again or contact support.",
                });
            } finally {
                setSaveLoading(false);
            }
        });

    // -------------------------------------------------------------------------
    // Archive
    // -------------------------------------------------------------------------
    window.confirmArchive = async function (id, name) {
        const result = await Swal.fire({
            icon: "warning",
            title: "Archive Vaccine?",
            html: `<strong>${name}</strong> will be archived.<br>
                   <span class="text-muted small">Past vaccination records will not be affected.</span>`,
            showCancelButton: true,
            confirmButtonText: "Yes, Archive",
            confirmButtonColor: "#f0ad4e",
            cancelButtonText: "Cancel",
        });

        if (!result.isConfirmed) return;

        try {
            const res = await fetch(`/api/vaccines/${id}/archive`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
            });

            const data = await res.json();

            Swal.fire({
                icon: res.ok ? "success" : "error",
                title: res.ok ? "Archived" : "Error",
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
            });

            if (res.ok) Livewire.dispatch("manageVaccineRefresh");
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Please try again.",
            });
        }
    };

    // -------------------------------------------------------------------------
    // Restore
    // -------------------------------------------------------------------------
    window.confirmRestore = async function (id, name) {
        const result = await Swal.fire({
            icon: "question",
            title: "Restore Vaccine?",
            html: `<strong>${name}</strong> will be set back to Active.`,
            showCancelButton: true,
            confirmButtonText: "Yes, Restore",
            confirmButtonColor: "#0b8433",
            cancelButtonText: "Cancel",
        });

        if (!result.isConfirmed) return;

        try {
            const res = await fetch(`/api/vaccines/${id}/restore`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
            });

            const data = await res.json();

            Swal.fire({
                icon: res.ok ? "success" : "error",
                title: res.ok ? "Restored" : "Error",
                text: data.message,
                timer: 2000,
                showConfirmButton: false,
            });

            if (res.ok) Livewire.dispatch("manageVaccineRefresh");
        } catch (err) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Please try again.",
            });
        }
    };

    // -------------------------------------------------------------------------
    // Clear errors on modal close
    // -------------------------------------------------------------------------
    const vaccineModal = document.getElementById("vaccineModal");
    if (vaccineModal) {
        vaccineModal.addEventListener("hidden.bs.modal", function () {
            clearModalErrors();
        });
    }
});
