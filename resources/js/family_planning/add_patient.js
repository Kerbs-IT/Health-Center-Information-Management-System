import { data } from "jquery";
import Swal from "sweetalert2";
import initSignatureCapture from "../signature/signature";

// all the radios of extermites UID
const extremites_UID = document.querySelectorAll(
    'input[name="physical_examination_extremites_UID_type"]'
);

function toggleCervicalAbnormalities() {
    const cervical_abnormalities_radio = document.getElementById(
        "extremites_UID_type_cervical_abnormalities"
    );
    const cervical_abnormalities_types = document.querySelectorAll(
        "input[name='cervical_abnormalities_type']"
    );
    const cervical_abnormalities_types_labels = document.querySelectorAll(
        ".cervical_abnormalities_type"
    );
    const isChecked = cervical_abnormalities_radio.checked;
    toggleSubRadioInputs(
        cervical_abnormalities_types,
        cervical_abnormalities_types_labels,
        isChecked
    );

    // ----------------------------------
    // cervical consistency
    const cervical_consistency = document.getElementById(
        "extremites_UID_type_cervical_consistency"
    );
    const cervical_consistency_type = document.querySelectorAll(
        'input[name="cervical_consistency_type"]'
    );
    const cervical_consistency_type_labels = document.querySelectorAll(
        ".cervical_consistency_type_label"
    );
    const isCervicalChecked = cervical_consistency.checked;
    // this function is for making the sub radio toggle if checked or not
    toggleSubRadioInputs(
        cervical_consistency_type,
        cervical_consistency_type_labels,
        isCervicalChecked
    );
    //  ---------------------------------------------------
    // uterine sectio
    const uterine_position = document.getElementById(
        "physical_examination_extremites_UID_type_uterine"
    );
    const uterine_position_type = document.querySelectorAll(
        "input[name='uterine_position_type']"
    );
    const uterine_position_type_label = document.querySelectorAll(
        ".uterine_position_type_label"
    );
    const isUterineChecked = uterine_position.checked;
    toggleSubRadioInputs(
        uterine_position_type,
        uterine_position_type_label,
        isUterineChecked
    );
}

// this function make the sub input visible or not if checked or not
function toggleSubRadioInputs(inputs, label, checked) {
    inputs.forEach((input) => {
        input.disabled = !checked;
        // console.log("working!");
    });
    label.forEach((label) => {
        label.classList.toggle("text-muted", !checked);
    });
}

// initial check
toggleCervicalAbnormalities();

// update dynamically on click

extremites_UID.forEach((radio) => {
    radio.addEventListener("change", toggleCervicalAbnormalities);
});

// for the type of user
const type_of_client = document.querySelectorAll(
    "input[name='family_planning_type_of_patient']"
);
type_of_client.forEach((radio) => {
    radio.addEventListener("change", toggleTypeOfClient);
});

// current method type
const current_user_types = document.querySelectorAll(
    'input[name="current_user_type"]'
);

current_user_types.forEach((radio) => {
    radio.addEventListener("change", toggleCurrentMethod);
});

toggleTypeOfClient();
toggleCurrentMethod();
// ------------------------------------functions-----------------------------

function toggleTypeOfClient() {
    const current_user = document.getElementById("current-user");
    const type_of_curent_user = document.querySelectorAll(
        'input[name="current_user_type"]'
    );
    const type_of_current_user_label = document.querySelectorAll(
        ".current_user_type_label"
    );
    const isCurrentUser = current_user.checked;

    toggleSubRadioInputs(
        type_of_curent_user,
        type_of_current_user_label,
        isCurrentUser
    );
    // toggle for the reason
    const current_user_reason = document.querySelectorAll(
        "input[name='current_user_reason_for_FP']"
    );
    const current_user_label = document.querySelectorAll(".current_user_label");

    toggleSubRadioInputs(
        current_user_reason,
        current_user_label,
        isCurrentUser
    );

    toggleCurrentMethod();

    const new_acceptor = document.getElementById("new-acceptor");
    const isNew = new_acceptor.checked;

    // for the new acceptor
    const new_acceptor_reasons = document.querySelectorAll(
        'input[name="new_acceptor_reason_for_FP"]'
    );
    const new_acceptor_label = document.querySelectorAll(".new_acceptor_label");
    toggleSubRadioInputs(new_acceptor_reasons, new_acceptor_label, isNew);

    if (isNew) {
        const current_method = document.getElementById(
            "family_planning_current-method"
        );
        const current_method_type = document.querySelectorAll(
            'input[name="current_method_reason"]'
        );
        const current_method_label = document.querySelectorAll(
            ".current_method_reason_label"
        );
        current_method.checked = false;
        toggleSubRadioInputs(current_method_type, current_method_label, false);
    } else {
        const new_acceptor_reasons = document.querySelectorAll(
            "input[name='new_acceptor_reason_for_FP']"
        );
        toggleCurrentMethod();
    }
}

function toggleCurrentMethod() {
    // current method toggle
    const current_user = document.getElementById("current-user");
    const isCurrentUser = current_user.checked;
    const current_method = document.getElementById(
        "family_planning_current-method"
    );
    const current_method_type = document.querySelectorAll(
        'input[name="current_method_reason"]'
    );
    const current_method_label = document.querySelectorAll(
        ".current_method_reason_label"
    );
    const isChecked = current_method.checked;
    toggleSubRadioInputs(current_method_type, current_method_label, isChecked);
}

// --------------------------------------------------------------
// ADD FAMILY PLANNING RECORD

const saveBTN = document.getElementById("family_planning_submit_btn");

saveBTN.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("add-patient-form");
    const formData = new FormData(form);
    const hiddenSignature = document.getElementById(
        "add_family_planning_signature_data"
    );
    if (hiddenSignature && hiddenSignature.value) {
        formData.set(
            "add_family_planning_signature_data",
            hiddenSignature.value
        );
        // console.log("✅ Manually added signature data");
    }

    // signature consent
    const hiddenSignatureConsent = document.getElementById(
        "add_family_planning_consent_signature_data"
    );
    if (hiddenSignatureConsent && hiddenSignatureConsent.value) {
        formData.set(
            "add_family_planning_consent_signature_data",
            hiddenSignatureConsent.value
        );
        // console.log("✅ Manually added signature data");
    }

    // side b
    const sideBsignature = document.getElementById(
        "add_side_b_name_n_signature_data"
    );
    if (sideBsignature && sideBsignature.value) {
        formData.set("add_side_b_name_n_signature_data", sideBsignature.value);
        // console.log("✅ Manually added signature data");
    }
    

    const response = await fetch("/patient-record/family-planning/add-record", {
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
            title: "Family Planning Patient",
            text: data.message, // this will make the text capitalize each word
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
        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("wraMasterlistRefreshTable");
        } else {
            console.warn("Livewire is not available");
        }
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
            title: "Prenatal Patient",
            text: capitalizeEachWord(message), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

// Helper function to capitalize each word
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

document.addEventListener("DOMContentLoaded", () => {
    const addPatientSignatureAcknowledgement = initSignatureCapture({
        drawBtnId: "add_family_planning_drawSignatureBtn",
        uploadBtnId: "add_family_planning_uploadSignatureBtn",
        canvasId: "add_family_planning_signaturePad",
        canvasSectionId: "add_family_planning_signatureCanvas",
        uploadSectionId: "add_family_planning_signatureUpload",
        previewSectionId: "add_family_planning_signaturePreview",
        fileInputId: "add_family_planning_signature_image",
        previewImageId: "add_family_planning_previewImage",
        errorElementId: "add_family_planning_signature_error",
        clearBtnId: "add_family_planning_clearSignature",
        saveBtnId: "add_family_planning_saveSignature",
        removeBtnId: "add_family_planning_removeSignature",
        hiddenInputId: "add_family_planning_signature_data",
        maxFileSizeMB: 2,
    });

    const addPatientSignatureAcknowledgementConsent = initSignatureCapture({
        drawBtnId: "add_family_planning_consent_drawSignatureBtn",
        uploadBtnId: "add_family_planning_consent_uploadSignatureBtn",
        canvasId: "add_family_planning_consent_signaturePad",
        canvasSectionId: "add_family_planning_consent_signatureCanvas",
        uploadSectionId: "add_family_planning_consent_signatureUpload",
        previewSectionId: "add_family_planning_consent_signaturePreview",
        fileInputId: "add_family_planning_consent_signature_image",
        previewImageId: "add_family_planning_consent_previewImage",
        errorElementId: "add_family_planning_consent_signature_error",
        clearBtnId: "add_family_planning_consent_clearSignature",
        saveBtnId: "add_family_planning_consent_saveSignature",
        removeBtnId: "add_family_planning_consent_removeSignature",
        hiddenInputId: "add_family_planning_consent_signature_data",
        maxFileSizeMB: 2,
    });
     const sideBsignature = initSignatureCapture({
         drawBtnId: "add_side_b_name_n_drawSignatureBtn",
         uploadBtnId: "add_side_b_name_n_uploadSignatureBtn",
         canvasId: "add_side_b_name_n_signaturePad",
         canvasSectionId: "add_side_b_name_n_signatureCanvas",
         uploadSectionId: "add_side_b_name_n_signatureUpload",
         previewSectionId: "add_side_b_name_n_signaturePreview",
         fileInputId: "add_side_b_name_n_signature_image",
         previewImageId: "add_side_b_name_n_previewImage",
         errorElementId: "add_side_b_name_n_signature_error",
         clearBtnId: "add_side_b_name_n_clearSignature",
         saveBtnId: "add_side_b_name_n_saveSignature",
         removeBtnId: "add_side_b_name_n_removeSignature",
         hiddenInputId: "add_side_b_name_n_signature_data",
         maxFileSizeMB: 2,
     });
});
