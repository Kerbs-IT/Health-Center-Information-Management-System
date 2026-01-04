import Swal from "sweetalert2";
import { puroks } from "../patient/healthWorkerList.js";
import initSignatureCapture from "../signature/signature.js";
import { refreshToggleStates,initializeEditModal } from "../family_planning/editFamilyPlanningRadioToggle.js";

// Initialize the modal on page load
initializeEditModal();
const viewIcon = document.getElementById("view-family-plan-info") ?? null;

if (viewIcon) {
    viewIcon.addEventListener("click", async (e) => {
        const caseId = viewIcon.dataset.caseId;

        const response = await fetch(
            `/patient-case/family-planning/viewCaseInfo/${caseId}`,
            {
                headers: {
                    Accept: "application/json",
                },
            }
        );

        if (response.ok) {
            const data = await response.json();

            Object.entries(data.caseInfo).forEach(([key, value]) => {
                if (key == "type_of_patient" && value == "new acceptor") {
                    if (data.caseInfo.new_acceptor_reason_for_FP != "") {
                        document.getElementById(
                            `view_${key}`
                        ).innerHTML = `${value}/${data.caseInfo.new_acceptor_reason_for_FP}`;
                    } else {
                        document.getElementById(`view_${key}`).innerHTML =
                            value;
                    }
                }
                if (key == "type_of_patient" && value == "current user") {
                    if (data.caseInfo.current_user_reason_for_FP != "") {
                        document.getElementById(
                            `view_${key}`
                        ).innerHTML = `${value}/${data.caseInfo.current_user_reason_for_FP}`;
                    } else {
                        document.getElementById(`view_${key}`).innerHTML =
                            value;
                    }
                }
                if (key == "spouse_lname") {
                    if (document.getElementById("view_spouse_name")) {
                        // console.log("wording");
                        document.getElementById("view_spouse_name").innerHTML =
                            `${data.caseInfo.spouse_fname ?? ""} ${
                                data.caseInfo.spouse_MI ?? ""
                            } ${data.caseInfo.spouse_lname ?? ""}`.trim();
                    }
                }
                if (key == "signature_image") {
                    const signaturePath = data.caseInfo.signature_image
                        ? `/storage/${data.caseInfo.signature_image}`
                        : null;
                    const signatureImg = document.getElementById(
                        "view_signature_image"
                    );
                    const noSignatureText =
                        document.getElementById("view_no_signature");
                    if (signaturePath) {
                        signatureImg.src = signaturePath;
                        signatureImg.style.display = "block";
                        noSignatureText.style.display = "none";
                    }
                }
                if (key == "acknowledgement_consent_signature_image") {
                    const signaturePath = data.caseInfo
                        .acknowledgement_consent_signature_image
                        ? `/storage/${data.caseInfo.acknowledgement_consent_signature_image}`
                        : null;
                    const signatureImg = document.getElementById(
                        "view_acknowledgement_consent_signature_image"
                    );
                    const noSignatureText = document.getElementById(
                        "view_acknowledgement_consent_signature_image_no"
                    );
                    if (signaturePath) {
                        signatureImg.src = signaturePath;
                        signatureImg.style.display = "block";
                        noSignatureText.style.display = "none";
                    }
                }

                if (document.getElementById(`view_${key}`)) {
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });

            Object.entries(data.caseInfo.medical_history).forEach(
                ([key, value]) => {
                    if (key == "with_dissability" && value == "Yes") {
                        document.getElementById(
                            `view_${key}`
                        ).innerHTML = `${value}- ${data.caseInfo.medical_history.if_with_dissability_specification}`;
                    } else {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(`view_${key}`).innerHTML =
                                value;
                        }
                    }
                }
            );
            // obsterical history
            Object.entries(data.caseInfo.obsterical_history).forEach(
                ([key, value]) => {
                    if (document.getElementById(`view_${key}`)) {
                        document.getElementById(`view_${key}`).innerHTML =
                            value ?? "N/A";
                    }
                }
            );
            // risk for sexuall transmitted
            Object.entries(
                data.caseInfo.risk_for_sexually_transmitted_infection
            ).forEach(([key, value]) => {
                if (key == "referred_to" && value == "others") {
                    // console.log(key);
                    document.getElementById(
                        `view_${key}`
                    ).innerHTML = `${value} - ${data.caseInfo.risk_for_sexually_transmitted_infection.reffered_to_others}`;
                } else {
                    if (document.getElementById(`view_${key}`)) {
                        document.getElementById(`view_${key}`).innerHTML =
                            value ?? "N/A";
                    }
                }
            });

            Object.entries(data.caseInfo.physical_examinations).forEach(
                ([key, value]) => {
                    if (
                        key == "extremites_UID_type" &&
                        value == "cervial abnormalities"
                    ) {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(
                                `view_${key}`
                            ).innerHTML = `${value} - ${data.caseInfo.physical_examinations.cervical_abnormalities_type}`;
                        }
                    } else if (
                        key == "extremites_UID_type" &&
                        value == "cervical consistency"
                    ) {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(
                                `view_${key}`
                            ).innerHTML = `${value} - ${data.caseInfo.physical_examinations.cervical_consistency_type}`;
                        }
                    } else if (
                        key == "extremites_UID_type" &&
                        value == "uterine position"
                    ) {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(
                                `view_${key}`
                            ).innerHTML = `${value} - ${data.caseInfo.physical_examinations.uterine_position_type}`;
                        }
                    } else if (
                        key == "extremites_UID_type" &&
                        value == "uterine depth"
                    ) {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(
                                `view_${key}`
                            ).innerHTML = `${value} - ${data.caseInfo.physical_examinations.uterine_position_text}cm`;
                        }
                    } else {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(`view_${key}`).innerHTML =
                                value ?? "N/A";
                        }
                    }
                }
            );
        }
    });
}

// edit section
const editIcon = document.getElementById("edit-family-plan-info") ?? null;
const editSaveBtn =
    document.getElementById("edit-family-planning-case-btn") ?? null;

if (editIcon) {
    editIcon.addEventListener("click", async (e) => {
        e.preventDefault();
        const caseId = editIcon.dataset.caseId;

        const editModal = document.getElementById(
            "editfamilyPlanningCaseModal"
        );

        editSaveBtn.dataset.caseId = caseId;

        const response = await fetch(
            `/patient-case/family-planning/viewCaseInfo/${caseId}`,
            {
                headers: {
                    Accept: "application/json",
                },
            }
        );

        if (response.ok) {
            const data = await response.json();

            // provide the info for case
            Object.entries(data.caseInfo).forEach(([key, value]) => {
                if (key == "plan_to_have_more_children") {
                    const plan = document.querySelectorAll(
                        'input[name="edit_plan_to_have_more_children"]'
                    );
                    if (plan) {
                        plan.forEach((element) => {
                            element.checked = element.value == value;
                        });
                    }
                } else if (key == "type_of_patient") {
                    const plan = document.querySelectorAll(
                        'input[name="edit_type_of_patient"]'
                    );
                    if (plan) {
                        plan.forEach((element) => {
                            element.checked = element.value == value;
                        });
                    }
                    // this condition is for the radio buttons if the 'other' radio is selected
                    if (value == "new acceptor") {
                        const elements = document.querySelectorAll(
                            'input[name="edit_new_acceptor_reason_for_FP"]'
                        );
                        if (
                            data.caseInfo.new_acceptor_reason_for_FP !=
                                "spacing" ||
                            data.caseInfo.new_acceptor_reason_for_FP !=
                                "spacing"
                        ) {
                            document.getElementById(
                                "edit_new_acceptor_reason_for_FP_others"
                            ).checked = true;
                            document.getElementById(
                                "edit_new_acceptor_reason_text"
                            ).value = data.caseInfo.new_acceptor_reason_for_FP;
                        } else {
                            elements.forEach((element) => {
                                element.checked =
                                    element.value ==
                                    data.caseInfo.new_acceptor_reason_for_FP;
                            });
                        }
                    } else if (value == "current user") {
                        const elements = document.querySelectorAll(
                            'input[name="edit_current_user_reason_for_FP"]'
                        );

                        if (
                            data.caseInfo.current_user_reason_for_FP !=
                                "spacing" ||
                            data.caseInfo.current_user_reason_for_FP !=
                                "spacing"
                        ) {
                            document.getElementById(
                                "edit_current_user_reason_for_FP_others"
                            ).checked = true;
                            document.getElementById(
                                "edit_current_user_reason_text"
                            ).value = data.caseInfo.current_user_reason_for_FP;
                        } else {
                            elements.forEach((element) => {
                                element.checked =
                                    element.value ==
                                    data.caseInfo.current_user_reason_for_FP;
                            });
                        }

                        // types of current user
                        const current_user_type = document.querySelectorAll(
                            "edit_current_user_type"
                        );

                        if (current_user_type == "current method") {
                            const current_method_reason =
                                document.querySelectorAll(
                                    "edit_current_method_reason"
                                );

                            if (
                                data.caseInfo.current_method_reason !=
                                "medical condition"
                            ) {
                                document.getElementById(
                                    "edit_current_method_reason_side_effect"
                                ).checked = true;
                                document.getElementById(
                                    "edit_side_effects_text"
                                ).value == data.caseInfo.current_method_reason;
                            }
                        } else {
                            current_user_type.forEach((element) => {
                                element.checked =
                                    element.value ==
                                    data.caseInfo.current_user_type;
                            });
                        }
                    }
                } else if (key == "previously_used_method") {
                    // this condition is for spliting the selected used method then populate the checkbox
                    const methods = document.querySelectorAll(
                        "input[name='edit_previously_used_method[]']"
                    );
                    let used_method = "";

                    if (value) {
                        used_method = value.split(",");
                    }

                    if (used_method) {
                        methods.forEach((method) => {
                            if (used_method.includes(method.value)) {
                                method.checked = true;
                            }
                        });
                    }
                } else if (key == "client_name") {
                    const fname = document.getElementById("edit_client_fname");
                    const MI = document.getElementById("edit_client_MI");
                    const lname = document.getElementById("edit_client_lname");
                    // split the data
                    let full_name = value.split(" ");

                    if (fname && MI && lname) {
                        fname.value = full_name[0];
                        MI.value = full_name[1];
                        lname.value = full_name[2];
                    }
                } else if (key == "NHTS") {
                    inputPicker(key, value);
                } else if (key == "client_address") {
                    const addressText = `${data.address.house_number},${
                        data.address.street || ""
                    }`.trim();
                    // console.log(addressText);
                    document.getElementById("edit_street").value = addressText;
                    puroks(
                        document.getElementById("edit_brgy"),
                        data.address.purok
                    );
                } else if (
                    key == "signature_image" ||
                    key == "acknowledgement_consent_signature_image"
                ) {
                    
                } else {
                    if (document.getElementById(`edit_${key}`)) {
                        // console.log("gumagana boy", key, "value: ", value);
                        document.getElementById(`edit_${key}`).value = value;
                    }
                }
            });

            // medical histories
            Object.entries(data.caseInfo.medical_history).forEach(
                ([key, value]) => {
                    inputPicker(key, value);
                }
            );
            // obsterical histories
            Object.entries(data.caseInfo.obsterical_history).forEach(
                ([key, value]) => {
                    //  this condition is for the element with 2 or more same name
                    if (
                        key == "type_of_last_delivery" ||
                        key == "type_of_menstrual"
                    ) {
                        inputPicker(key, value);
                    } else {
                        if (
                            document.querySelector(`input[name="edit_${key}"]`)
                        ) {
                            //  console.log("gumagana boy", key, "value: ", value);
                            const element = document.querySelector(
                                `input[name="edit_${key}"]`
                            );
                            //  handles both checkbox and text type of input
                            if (element.type != "checkbox") {
                                element.value = value;
                            } else {
                                element.checked = element.value == value;
                            }
                        }
                    }
                }
            );

            // risk for sexually transmitted
            Object.entries(
                data.caseInfo.risk_for_sexually_transmitted_infection
            ).forEach(([key, value]) => {
                //  this condition is for the element with 2 or more same name
                if (key == "reffered_to_others") {
                    document.querySelector(
                        'input[name="edit_reffered_to_others"]'
                    ).value = value;
                }

                inputPicker(key, value);
            });
            // physical examination
            Object.entries(data.caseInfo.physical_examinations).forEach(
                ([key, value]) => {
                    //  this condition is for the element with 2 or more same name
                    const element = document.querySelector(
                        `input[name='edit_${key}']`
                    );

                    // skip if no matching element found
                    if (!element) return;

                    if (element.type === "text" || element.type === "number") {
                        element.value = value ?? "";
                    } else if (key == "cervical_abnormalities_type") {
                        const physical_types = document.querySelectorAll(
                            `input[name="edit_cervical_abnormalities_type"]`
                        );
                        radioValuePopulator(physical_types, value);
                    } else if (key == "cervical_consistency_type") {
                        const physical_types = document.querySelectorAll(
                            `input[name="edit_cervical_consistency_type"]`
                        );
                        radioValuePopulator(physical_types, value);
                    } else if (key == "uterine_position_type") {
                        const physical_types = document.querySelectorAll(
                            `input[name="edit_uterine_position_type"]`
                        );
                        radioValuePopulator(physical_types, value);
                    } else if (key == "uterine_depth_text") {
                        if (value != "") {
                            const text_element = document.getElementById(
                                "edit_uterine_depth_text"
                            );
                            if (text_element) {
                                text_element.value = value;
                            }
                        }
                    } else {
                        inputPicker(key, value);
                    }
                }
            );

            // assign the case id
        }

          setTimeout(() => {
                    refreshToggleStates();
                }, 100);

        let editFamilyPlanningSignature = null;
        let editFamilyPlanningConsentSignature = null;
        // signature functionality
        if (editModal) {
            editModal.addEventListener("shown.bs.modal", function () {
                // console.log("Modal is NOW visible!");

                if (
                    !editFamilyPlanningSignature &&
                    !editFamilyPlanningConsentSignature
                ) {
                    editFamilyPlanningSignature = initSignatureCapture({
                        drawBtnId:
                            "edit_family_planning_acknowledgement_drawSignatureBtn",
                        uploadBtnId:
                            "edit_family_planning_acknowledgement_uploadSignatureBtn",
                        canvasId:
                            "edit_family_planning_acknowledgement_signaturePad",
                        canvasSectionId:
                            "edit_family_planning_acknowledgement_signatureCanvas",
                        uploadSectionId:
                            "edit_family_planning_acknowledgement_signatureUpload",
                        previewSectionId:
                            "edit_family_planning_acknowledgement_signaturePreview",
                        fileInputId:
                            "edit_family_planning_acknowledgement_signature_image",
                        previewImageId:
                            "edit_family_planning_acknowledgement_previewImage",
                        errorElementId:
                            "edit_family_planning_acknowledgement_signature_error",
                        clearBtnId:
                            "edit_family_planning_acknowledgement_clearSignature",
                        saveBtnId:
                            "edit_family_planning_acknowledgement_saveSignature",
                        removeBtnId:
                            "edit_family_planning_acknowledgement_removeSignature",
                        hiddenInputId:
                            "edit_family_planning_acknowledgement_signature_data",
                        maxFileSizeMB: 2,
                    });

                    // consent
                    editFamilyPlanningConsentSignature = initSignatureCapture({
                        drawBtnId:
                            "edit_family_planning_consent_drawSignatureBtn",
                        uploadBtnId:
                            "edit_family_planning_consent_uploadSignatureBtn",
                        canvasId: "edit_family_planning_consent_signaturePad",
                        canvasSectionId:
                            "edit_family_planning_consent_signatureCanvas",
                        uploadSectionId:
                            "edit_family_planning_consent_signatureUpload",
                        previewSectionId:
                            "edit_family_planning_consent_signaturePreview",
                        fileInputId:
                            "edit_family_planning_consent_signature_image",
                        previewImageId:
                            "edit_family_planning_consent_previewImage",
                        errorElementId:
                            "edit_family_planning_consent_signature_error",
                        clearBtnId:
                            "edit_family_planning_consent_clearSignature",
                        saveBtnId: "edit_family_planning_consent_saveSignature",
                        removeBtnId:
                            "edit_family_planning_consent_removeSignature",
                        hiddenInputId:
                            "edit_family_planning_consent_signature_data",
                        maxFileSizeMB: 2,
                    });
                    // console.log("✅ SIGNATURE INITIALIZED!");
                } else {
                    editFamilyPlanningSignature.clear();
                    editFamilyPlanningConsentSignature.clear();
                }
            });
        }
    });
}

// update the record
if (editSaveBtn) {
    editSaveBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const form = document.getElementById("edit-family-plan-form");
        const formData = new FormData(form);
        const hiddenSignature = document.getElementById(
            "edit_family_planning_acknowledgement_signature_data"
        );
        if (hiddenSignature && hiddenSignature.value) {
            formData.set(
                "edit_family_planning_acknowledgement_signature_data",
                hiddenSignature.value
            );
            // console.log("✅ Manually added signature data");
        }
        const hiddenConsentSignature = document.getElementById(
            "edit_family_planning_consent_signature_data"
        );
        if (hiddenConsentSignature && hiddenConsentSignature.value) {
            formData.set(
                "edit_family_planning_consent_signature_data",
                hiddenConsentSignature.value
            );
            // console.log("✅ Manually added signature data");
        }

        // id
        const id = editSaveBtn.dataset.caseId;
        // for (let [key, value] of formData.entries()) {
        //     console.log(`${key}: ${value}`);
        // }

        const response = await fetch(
            `/patient-case/family-planning/update-case-info/${id}`,
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
        if (response.ok) {
            errorElements.forEach((element) => {
                element.textContent = "";
            });
            Swal.fire({
                title: "Family Planning Patient",
                text: data.message, // this will make the text capitalize each word
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("editfamilyPlanningCaseModal")
                    );
                    // console.log("working dapat");
                    if (modal) {
                        modal.hide();
                    }

                    form.reset();
                }
            });
        } else {
            // reset
            errorElements.forEach((element) => {
                element.textContent = "";
            });
            // handles the validation error
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
                title: "Family Planning Patient",
                text: capitalizeEachWord(message), // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    });
}
// ===== Archive side a
document.addEventListener("click", async (e) => {
    const archiveBtn = e.target.closest(".archive-family-plan-side-A");
    if (!archiveBtn) return;
    const caseId = archiveBtn.dataset.caseId;
    // Validate case ID
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        // ✅ Show confirmation dialog FIRST
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "The Family Planning Client Assessment Record - Side A will be deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Archive",
            cancelButtonText: "Cancel",
        });

        // ✅ Exit if user cancelled
        if (!result.isConfirmed) return;

        // ✅ Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error("CSRF token not found. Please refresh the page.");
        }

        const response = await fetch(
            `/patient-record/family-planning/case-record/delete/side-A/${caseId}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken.content,
                    Accept: "application/json",
                },
            }
        );

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`
            );
        }

        // Success - refresh table
        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("familyPlanningRefreshTable");
            Livewire.dispatch("wraMasterlistRefreshTable"); // ✅ Update dispatch name if needed
        }

        // Remove the row from DOM
        const row = archiveBtn.closest("tr");
        if (row) {
            row.remove();
        }

        // Show success message
        Swal.fire({
            title: "Archived!",
            text: "The Tb dots Check-up Record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("editfamilyPlanningCaseModal")
                );
                // console.log("working dapat");
                if (modal) {
                    modal.hide();
                }
                form.reset();
            }
        });
    } catch (error) {
        console.error("Error archiving case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to archive record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// --------------------------------------------------------------- side B -------------------------------------------

// ------------------------------- FUNCTIONS --------------------------------------------------------

function inputPicker(key, value) {
    const elements = document.querySelectorAll(`input[name="edit_${key}"]`);

    if (key == "if_with_dissability_specification") {
        const dissability = document.getElementById(
            "edit_if_with_dissability_specification"
        );
        if (dissability) {
            dissability.value = value;
        }
    } else {
        if (elements) {
            elements.forEach((element) => {
                element.checked = element.value == value;
            });
        }
    }
}

function radioValuePopulator(types, value) {
    if (types) {
        types.forEach((type) => {
            type.checked = type.value == value;
        });
    }
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
