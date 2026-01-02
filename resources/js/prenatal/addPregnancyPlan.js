import initSignatureCapture from "../signature/signature";
const addPregnancyPlanBtn = document.getElementById(
    "add_pregnancy_plan_add_btn"
);
const pregnacyPlanSaveBtn = document.getElementById("add_pregnancy_plan_btn");
document.addEventListener("DOMContentLoaded", function () {
    const addPregnancyModal = document.getElementById("addPregnancyPlanModal");

    let addPatientSignature = null;

    // BUTTON CLICK - ONLY SETS DATA
    if (addPregnancyPlanBtn) {
        addPregnancyPlanBtn.addEventListener("click", function (e) {
            const patientInfo = JSON.parse(
                addPregnancyPlanBtn.dataset.patientInfo
            );

            const patientNameElement =
                document.getElementById("add_patient_name");
            if (patientNameElement) {
                patientNameElement.value = patientInfo.patient.full_name;
            }

            const hiddenPatientName = document.getElementById(
                "add_pregnancy_plan_patient_name"
            );
            if (hiddenPatientName) {
                hiddenPatientName.value = patientInfo.patient.full_name;
            }

            if (pregnacyPlanSaveBtn) {
                pregnacyPlanSaveBtn.dataset.medicalCaseId = patientInfo.id;
            }
        });
    }

    // WAIT FOR MODAL TO FULLY OPEN - THIS RUNS **AFTER** MODAL IS VISIBLE
    if (addPregnancyModal) {
        addPregnancyModal.addEventListener("shown.bs.modal", function () {
            console.log("Modal is NOW visible!");

            if (!addPatientSignature) {
                addPatientSignature = initSignatureCapture({
                    drawBtnId: "add_drawSignatureBtn",
                    uploadBtnId: "add_uploadSignatureBtn",
                    canvasId: "add_signaturePad",
                    canvasSectionId: "add_signatureCanvas",
                    uploadSectionId: "add_signatureUpload",
                    previewSectionId: "add_signaturePreview",
                    fileInputId: "add_signature_image",
                    previewImageId: "add_previewImage",
                    errorElementId: "add_signature_error",
                    clearBtnId: "add_clearSignature",
                    saveBtnId: "add_saveSignature",
                    removeBtnId: "add_removeSignature",
                    hiddenInputId: "add_signature_data",
                    maxFileSizeMB: 2,
                });
                console.log("✅ SIGNATURE INITIALIZED!");
            } else {
                addPatientSignature.clear();
            }
        });
    }
});
const donor_names_con = document.getElementById("add_donor_names_con");
const donor_name_input = document.getElementById("add_name_of_donor");

const addBtn = document.getElementById("add_donor_name_add_btn");
// event listener for adding the name
addBtn.addEventListener("click", (e) => {
    if (donor_name_input.value !== "") {
        donor_names_con.innerHTML += `
            <div class="box vaccine d-flex justify-content-between bg-white align-items-center p-1 w-50 rounded">
                <h5 class="mb-0">${donor_name_input.value}</h5>
                <div class="delete-icon d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                        <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                    </svg>
                </div>
                <input type="hidden" name="add_donor_names[]" value="${donor_name_input.value}" class="donor_name_input">
            </div>
            `;
        // reset the input field
        donor_name_input.value = "";
    } else {
        Swal.fire({
            title: "Adding Blood Donor Name",
            text: "Please provide valid name.", // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

// handle the remove of the selected donor
donor_names_con.addEventListener("click", (e) => {
    let donors = document.querySelectorAll('input[name="donor_names[]"]');
    if (e.target.closest(".box")) {
        if (e.target.closest(".delete-icon-svg")) {
            e.target.closest(".box").remove();
        }
    }
    console.log("donor deleted");
});

// ==== upload the record

pregnacyPlanSaveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = pregnacyPlanSaveBtn.dataset.medicalCaseId;

    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        const form = document.getElementById("add_pregnancy_plan_form");
        const formData = new FormData(form);

        // Manually add the hidden signature data
        const hiddenSignature = document.getElementById("add_signature_data");
        if (hiddenSignature && hiddenSignature.value) {
            formData.set("add_signature_data", hiddenSignature.value);
            console.log("✅ Manually added signature data");
        }

        // for (let [key, value] of formData.entries()) {
        //     console.log(key, value);
        // }
        const response = await fetch(`/prenatal/add-pregnancy-plan/${id}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
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
            if (typeof Livewire !== "undefined") {
                Livewire.dispatch("prenatalRefreshTable"); // ✅ Update dispatch name if needed
            }
            Swal.fire({
                title: "Add Pregnancy Plan",
                text: data.message, // this will make the text capitalize each word
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("addPregnancyPlanModal")
                    );
                    if (modal) {
                        modal.hide();
                    }
                    form.reset();
                }
            });
        } else {
            // reset first

            errorElements.forEach((element) => {
                element.textContent = "";
            });

            Object.entries(data.errors).forEach(([key, value]) => {
                if (document.getElementById(`add_${key}_error`)) {
                    document.getElementById(`add_${key}_error`).textContent =
                        value;
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
                title: "Add Pregnancy Plan",
                text: capitalizeEachWord(message), // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    } catch (error) {
        console.error("Error adding case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to add record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
