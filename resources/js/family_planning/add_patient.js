import { data } from "jquery";
import Swal from "sweetalert2";

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

saveBTN.addEventListener('click', async (e) => {
    e.preventDefault();

    const form = document.getElementById("add-patient-form");
    const formData = new FormData(form);

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

    if (response.ok) {
         
            Swal.fire({
                title: "Family Planning Patient",
                text: data.message, // this will make the text capitalize each word
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
    } else {
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
})

// Helper function to capitalize each word
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, char => char.toUpperCase());
}