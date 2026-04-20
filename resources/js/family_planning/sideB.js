import Swal from "sweetalert2";
import initSignatureCapture from "../signature/signature";

// EVENT DELEGATION IN SIDE B VIEW
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
                    const signatureElement = document.getElementById(
                        "view_signature_of_the_provider",
                    );
                    if (signatureElement) {
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
                        if (signaturePath) {
                            signatureImg.src = signaturePath;
                            signatureImg.style.display = "block";
                            noSignatureText.style.display = "none";
                        }
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

// side b update
const side_b_edit_btn = document.getElementById("edit-side-b-record");
const sideBupdateBTN = document.getElementById(
    "edit-side-b-family-planning-assessment-btn",
);

// SIDE B SIGNATURE APPROACH
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

// SIDE B EDIT BTN EVENT DELEGATION
document.addEventListener("click", async (e) => {
    const side_b_edit_btn = e.target.closest(".edit-side-b-record");
    if (!side_b_edit_btn) return;

    const id = side_b_edit_btn.dataset.caseId;
    sideBupdateBTN.dataset.caseId = id;

    if (id == "") return;

    const errors = document.querySelectorAll(".error-text");
    if (errors) {
        errors.forEach((error) => (error.innerHTML = ""));
    }

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
                } else if (key == "health_worker_id") {
                    document.getElementById(`edit_side_b_${key}`).value = value;
                } else {
                    const radioGroups = document.querySelectorAll(
                        `input[type="radio"][name='edit_${key}']`,
                    );
                    if (radioGroups.length > 0) {
                        radioGroups.forEach((element) => {
                            element.checked = element.value == value;
                        });
                        return;
                    }
                    if (document.getElementById(`edit_${key}`)) {
                        document.getElementById(`edit_${key}`).value = value;
                    }
                }
            });
        }
    } catch (error) {
        console.log("Error:", error);
    }
});

// ============================================================================
// SIDE B UPDATE SAVE — with button state management
// ============================================================================

sideBupdateBTN.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = sideBupdateBTN.dataset.caseId;
    const originalText = sideBupdateBTN.innerHTML;

    sideBupdateBTN.disabled = true;
    sideBupdateBTN.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

    try {
        const form = document.getElementById("edit-side-b-family-plan-form");
        const formData = new FormData(form);

        const hiddenSignature = document.getElementById(
            "edit_side_b_signature_data",
        );
        if (hiddenSignature && hiddenSignature.value) {
            formData.set("edit_side_b_signature_data", hiddenSignature.value);
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
            errorElements.forEach((element) => {
                element.textContent = "";
            });

            if (typeof Livewire !== "undefined") {
                try {
                    Livewire.dispatch("familyPlanningRefreshTable");
                } catch (error) {
                    console.error("Error dispatching Livewire event:", error);
                }
            } else {
                console.warn("Livewire is not available");
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
                    if (modal) {
                        modal.hide();
                    }
                }
            });
        } else {
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
                title: "Family Planning Assessment Record",
                text: capitalizeEachWord(message),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });

            // Re-enable on validation error
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

// ============================================================================
// ARCHIVE SIDE B — with button state management
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

        // Disable after confirmation
        const originalHTML = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML =
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error("CSRF token not found. Please refresh the page.");
        }

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
            Livewire.dispatch("seniorCitizenRefreshTable");
        }

        const row = deleteBtn.closest("tr");
        if (row) {
            row.remove();
        }

        Swal.fire({
            title: "Archived!",
            text: "The Family planning side B Record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
    } catch (error) {
        console.error("Error archiving case:", error);

        // Re-enable on error
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

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
