import Swal from "sweetalert2";
import initSignatureCapture from "../signature/signature";

// ============================================================================
// IS_FINAL TOGGLE — ADD MODAL
// ============================================================================

document.addEventListener("DOMContentLoaded", function () {
    const addToggle = document.getElementById("add_is_final_toggle");
    if (addToggle) {
        addToggle.addEventListener("change", function () {
            const warning = document.getElementById("add_is_final_warning");
            const hiddenInput = document.getElementById("add_is_final_hidden");
            if (warning) warning.classList.toggle("d-none", !this.checked);
            if (hiddenInput) hiddenInput.value = this.checked ? "1" : "0";
        });
    }

    const editToggle = document.getElementById("edit_is_final_toggle");
    if (editToggle) {
        editToggle.addEventListener("change", function () {
            applyEditFinalToggleState(this.checked, false);
        });
    }
});

// ============================================================================
// IS_FINAL TOGGLE — EDIT MODAL HELPER
// ============================================================================

function applyEditFinalToggleState(isFinal, lockToggle = false) {
    const toggle = document.getElementById("edit_is_final_toggle");
    const hiddenInput = document.getElementById("edit_is_final_hidden");
    const warning = document.getElementById("edit_is_final_warning");

    if (!toggle || !hiddenInput) return;

    toggle.checked = isFinal;
    hiddenInput.value = isFinal ? "1" : "0";

    if (warning) warning.classList.toggle("d-none", !isFinal);

    if (lockToggle) {
        toggle.disabled = true;

        const form = document.getElementById("edit-side-b-family-plan-form");
        if (form) {
            form.querySelectorAll("input, select, textarea").forEach((el) => {
                el.disabled = true;
            });
        }

        if (sideBupdateBTN) {
            sideBupdateBTN.disabled = true;
            sideBupdateBTN.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                </svg>
                Record Locked
            `;
        }
    } else {
        toggle.disabled = false;
    }
}

// Reset EDIT modal when it closes
document
    .getElementById("editSideBcaseModal")
    ?.addEventListener("hidden.bs.modal", function () {
        const form = document.getElementById("edit-side-b-family-plan-form");
        if (form) {
            form.querySelectorAll("input, select, textarea").forEach((el) => {
                el.disabled = false;
                el.removeAttribute("readonly");
            });
        }

        if (sideBupdateBTN) {
            sideBupdateBTN.disabled = false;
            sideBupdateBTN.innerHTML = "Save Changes";
        }

        applyEditFinalToggleState(false, false);

        const isFinalError = document.getElementById("edit_is_final_error");
        if (isFinalError) isFinalError.textContent = "";
    });

// Reset ADD modal when it closes
document
    .getElementById("addSideBcaseModal")
    ?.addEventListener("hidden.bs.modal", function () {
        const addToggle = document.getElementById("add_is_final_toggle");
        const addHidden = document.getElementById("add_is_final_hidden");
        const addWarning = document.getElementById("add_is_final_warning");
        const addError = document.getElementById("add_is_final_error");

        if (addToggle) addToggle.checked = false;
        if (addHidden) addHidden.value = "0";
        if (addWarning) addWarning.classList.add("d-none");
        if (addError) addError.textContent = "";
    });

// ============================================================================
// EVENT DELEGATION — VIEW SIDE B
// ============================================================================

document.addEventListener("click", async (e) => {
    const side_b_view_btn = e.target.closest(".view-side-b-record");
    if (!side_b_view_btn) return;
    const id = side_b_view_btn.dataset.caseId;

    try {
        const response = await fetch(
            `/patient-record/family-planning/view/side-b-record/${id}`,
            { headers: { Accept: "application/json" } },
        );

        if (response.ok) {
            const data = await response.json();

            Object.entries(data.sideBrecord).forEach(([key, value]) => {
                if (key == "signature_of_the_provider") {
                    const signaturePath = data.sideBrecord
                        .signature_of_the_provider
                        ? `/storage/${data.sideBrecord.signature_of_the_provider}`
                        : null;
                    const signatureImg = document.getElementById(
                        "view_signature_of_the_provider",
                    );
                    const noSignatureText = document.getElementById(
                        "view_signature_of_the_provide_no",
                    );
                    if (signatureImg && signaturePath) {
                        signatureImg.src = signaturePath;
                        signatureImg.style.display = "block";
                        if (noSignatureText)
                            noSignatureText.style.display = "none";
                    }
                }
                if (document.getElementById(`view_${key}`)) {
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });
        }
    } catch (error) {
        console.log("Error:", error);
    }
});

// ============================================================================
// SIDE B SIGNATURE — EDIT MODAL
// ============================================================================

const sideBupdateBTN = document.getElementById(
    "edit-side-b-family-planning-assessment-btn",
);

const editModal = document.getElementById("editSideBcaseModal");
let editSideBsignature = null;

if (editModal) {
    editModal.addEventListener("shown.bs.modal", function () {
        if (!editSideBsignature) {
            editSideBsignature = initSignatureCapture({
                drawBtnId: "edit_side_b_drawSignatureBtn",
                uploadBtnId: "edit_side_b_uploadSignatureBtn",
                canvasId: "edit_side_b_signaturePad",
                canvasSectionId: "edit_side_b_signatureCanvas",
                uploadSectionId: "edit_side_b_signatureUpload",
                previewSectionId: "edit_side_b_signaturePreview",
                fileInputId: "edit_side_b_signature_image",
                previewImageId: "edit_side_b_previewImage",
                errorElementId: "edit_side_b_signature_error",
                clearBtnId: "edit_side_b_clearSignature",
                saveBtnId: "edit_side_b_saveSignature",
                removeBtnId: "edit_side_b_removeSignature",
                hiddenInputId: "edit_side_b_signature_data",
                maxFileSizeMB: 2,
            });
        } else {
            editSideBsignature.clear();
        }
    });
}

// ============================================================================
// EVENT DELEGATION — EDIT SIDE B (load data)
// ============================================================================

document.addEventListener("click", async (e) => {
    const side_b_edit_btn = e.target.closest(".edit-side-b-record");
    if (!side_b_edit_btn) return;

    const id = side_b_edit_btn.dataset.caseId;
    if (sideBupdateBTN) sideBupdateBTN.dataset.caseId = id;

    if (!id) return;

    document
        .querySelectorAll(".error-text")
        .forEach((el) => (el.innerHTML = ""));

    try {
        const response = await fetch(
            `/patient-record/family-planning/view/side-b-record/${id}`,
            { headers: { Accept: "application/json" } },
        );

        if (response.ok) {
            const data = await response.json();

            Object.entries(data.sideBrecord).forEach(([key, value]) => {
                if (key == "medical_record_case_id") {
                    document.getElementById(`edit_side_b_${key}`).value = value;
                    return;
                }
                if (key == "health_worker_id") {
                    document.getElementById(`edit_side_b_${key}`).value = value;
                    return;
                }

                // Radio buttons
                const radioGroups = document.querySelectorAll(
                    `input[type="radio"][name='edit_${key}']`,
                );
                if (radioGroups.length > 0) {
                    radioGroups.forEach((el) => {
                        el.checked = el.value == value;
                    });
                    return;
                }

                const el = document.getElementById(`edit_${key}`);
                if (el) el.value = value ?? "";
            });

            // Apply final toggle state
            const caseIsFinal = !!data.case_is_final;
            const thisRecordIsFinal = !!data.this_record_is_final;
            applyEditFinalToggleState(thisRecordIsFinal, caseIsFinal);
        }
    } catch (error) {
        console.log("Error:", error);
    }
});

// ============================================================================
// SIDE B UPDATE SAVE
// ============================================================================

if (sideBupdateBTN) {
    sideBupdateBTN.addEventListener("click", async (e) => {
        e.preventDefault();

        const id = sideBupdateBTN.dataset.caseId;
        const originalText = sideBupdateBTN.innerHTML;

        sideBupdateBTN.disabled = true;
        sideBupdateBTN.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

        try {
            const form = document.getElementById(
                "edit-side-b-family-plan-form",
            );
            const formData = new FormData(form);

            const hiddenSignature = document.getElementById(
                "edit_side_b_signature_data",
            );
            if (hiddenSignature && hiddenSignature.value) {
                formData.set(
                    "edit_side_b_signature_data",
                    hiddenSignature.value,
                );
            }

            const response = await fetch(
                `/patient-record/family-planning/update/side-b-record/${id}`,
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

                if (typeof Livewire !== "undefined") {
                    try {
                        Livewire.dispatch("familyPlanningRefreshTable");
                    } catch (err) {
                        console.error("Error dispatching Livewire event:", err);
                    }
                }

                Swal.fire({
                    title: "Family Planning Assessment Record",
                    text: data.message,
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                }).then((result) => {
                    sideBupdateBTN.disabled = false;
                    sideBupdateBTN.innerHTML = originalText;

                    if (result.isConfirmed) {
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("editSideBcaseModal"),
                        );
                        if (modal) modal.hide();
                    }
                });
            } else {
                errorElements.forEach((el) => (el.textContent = ""));

                if (data.errors) {
                    Object.entries(data.errors).forEach(([key, value]) => {
                        const el = document.getElementById(`${key}_error`);
                        if (el)
                            el.textContent = Array.isArray(value)
                                ? value[0]
                                : value;
                    });

                    // is_final specific error
                    if (data.errors?.is_final) {
                        const isFinalError = document.getElementById(
                            "edit_is_final_error",
                        );
                        if (isFinalError) {
                            isFinalError.textContent = Array.isArray(
                                data.errors.is_final,
                            )
                                ? data.errors.is_final[0]
                                : data.errors.is_final;
                        }
                    }
                }

                Swal.fire({
                    title: "Family Planning Assessment Record",
                    html: Object.values(data.errors ?? {})
                        .flat()
                        .map((e) => `<div>${e}</div>`)
                        .join(""),
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                sideBupdateBTN.disabled = false;
                sideBupdateBTN.innerHTML = originalText;
            }
        } catch (error) {
            console.error("Error updating side B record:", error);

            Swal.fire({
                title: "Error",
                text: `Failed to update record: ${error.message}`,
                icon: "error",
                confirmButtonColor: "#3085d6",
            });

            sideBupdateBTN.disabled = false;
            sideBupdateBTN.innerHTML = originalText;
        }
    });
}

// ============================================================================
// ARCHIVE SIDE B
// ============================================================================

document.addEventListener("click", async (e) => {
    const deleteBtn = e.target.closest(".delete-side-b-record");
    if (!deleteBtn) return;
    const id = deleteBtn.dataset.caseId;

    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "The Family Planning Client Assessment Record - Side B will be moved to archived status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
            cancelButtonText: "Cancel",
        });

        if (!result.isConfirmed) return;

        const originalHTML = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken)
            throw new Error("CSRF token not found. Please refresh the page.");

        const response = await fetch(
            `/patient-record/family-planning/case-record/delete/side-B/${id}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken.content,
                    Accept: "application/json",
                },
            },
        );

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`,
            );
        }

        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("familyPlanningRefreshTable");
        }

        const row = deleteBtn.closest("tr");
        if (row) row.remove();

        Swal.fire({
            title: "Archived!",
            text: "The Family Planning Side B Record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
    } catch (error) {
        console.error("Error archiving case:", error);

        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalHTML;

        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// ============================================================================
// HELPERS
// ============================================================================

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
