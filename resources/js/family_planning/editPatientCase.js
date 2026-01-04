// all the radios of extermites UID
const extremites_UID = document.querySelectorAll(
    'input[name="edit_extremites_UID_type"]'
);

function toggleCervicalAbnormalities() {
    const cervical_abnormalities_radio = document.getElementById(
        "edit_extremites_UID_type_cervical_abnormalities"
    );
    const cervical_abnormalities_types = document.querySelectorAll(
        "input[name='edit_cervical_abnormalities_type']"
    );
    const cervical_abnormalities_types_labels = document.querySelectorAll(
        ".edit_cervical_abnormalities_type"
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
        "edit_extremites_UID_type_cervical_consistency"
    );
    const cervical_consistency_type = document.querySelectorAll(
        'input[name="edit_cervical_consistency_type"]'
    );
    const cervical_consistency_type_labels = document.querySelectorAll(
        ".edit_cervical_consistency_type_label"
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
        "edit_physical_examination_extremites_UID_type_uterine"
    );
    const uterine_position_type = document.querySelectorAll(
        "input[name='edit_uterine_position_type']"
    );
    const uterine_position_type_label = document.querySelectorAll(
        ".edit_uterine_position_type_label"
    );
    const isUterineChecked = uterine_position.checked;
    toggleSubRadioInputs(
        uterine_position_type,
        uterine_position_type_label,
        isUterineChecked
    );
}

extremites_UID.forEach((radio) => {
    radio.addEventListener("change", toggleCervicalAbnormalities);
});


document.addEventListener('click', (e) => {
    const editBtn = e.target.closest("side-A-family-plan-info");

    if (!editBtn) return;
    setTimeout(() => {
        // initial check
        toggleCervicalAbnormalities();

        // update dynamically on click

    
        // for the type of user
        const type_of_client = document.querySelectorAll(
            "input[name='edit_type_of_patient']"
        );

        type_of_client.forEach((radio) => {
            radio.addEventListener("change", toggleTypeOfClient);
        });

        // current method type
        const current_user_types = document.querySelectorAll(
            'input[name="edit_current_user_type"]'
        );

        current_user_types.forEach((radio) => {
            radio.addEventListener("change", toggleCurrentMethod);
        });

        // Now call after content is loaded
        toggleTypeOfClient();
    }, 1000); // Adjust delay as needed
})


// ------------------------------------functions-----------------------------

function toggleTypeOfClient() {
    const current_user = document.getElementById("edit_current_user");
    const new_acceptor = document.getElementById("edit_new_acceptor");
    
    const isCurrentUser = current_user.checked;
    const isNew = new_acceptor.checked;

    // 1. Toggle Current User Type radios
    const type_of_curent_user = document.querySelectorAll(
        'input[name="edit_current_user_type"]'
    );
    const type_of_current_user_label = document.querySelectorAll(
        ".edit_current_user_type_label"
    );
    // console.log("first run:", isCurrentUser);
    toggleSubRadioInputs(
        type_of_curent_user,
        type_of_current_user_label,
        isCurrentUser
    );
    
    // 2. Toggle Current User Reasons
    const current_user_reason = document.querySelectorAll(
        "input[name='edit_current_user_reason_for_FP']"
    );
    const current_user_label = document.querySelectorAll(".edit_current_user_label");
    // console.log("After first run(must be true):", isCurrentUser);
    toggleCurrentMethod();
    toggleSubRadioInputs(
        current_user_reason,
        current_user_label,
        isCurrentUser
    );

    // 3. Toggle New Acceptor Reasons
    const new_acceptor_reasons = document.querySelectorAll(
        'input[name="edit_new_acceptor_reason_for_FP"]'
    );
    const new_acceptor_label = document.querySelectorAll(".edit_new_acceptor_label");
    toggleSubRadioInputs(new_acceptor_reasons, new_acceptor_label, isNew);

    // 4. Handle Current Method section based on selection
    if (isNew) {
        // If New Acceptor selected, disable current method
        const current_method = document.getElementById("edit_current_method");
        const current_method_type = document.querySelectorAll(
            'input[name="edit_current_method_reason"]'
        );
        const current_method_label = document.querySelectorAll(
            ".edit_current_method_reason_label"
        );
        current_method.checked = false;
        toggleSubRadioInputs(current_method_type, current_method_label, false);
    } else if (isCurrentUser == true) {
        // Only toggle current method if Current User is selected
        // console.log("check if true first run(must be true):", isCurrentUser);
        toggleCurrentMethod();
    } else {
        // Disable current method for other options
        const current_method_type = document.querySelectorAll(
            'input[name="edit_current_method_reason"]'
        );
        const current_method_label = document.querySelectorAll(
            ".edit_current_method_reason_label"
        );
        toggleSubRadioInputs(current_method_type, current_method_label, false);
    }
}

function toggleCurrentMethod() {
    const current_user = document.getElementById("edit_current_user");
    const current_method = document.getElementById("edit_current_method");
    
    // Only proceed if Current User is selected
    if (!current_user.checked) {
        return;
    }
    
    const current_method_type = document.querySelectorAll(
        'input[name="edit_current_method_reason"]'
    );
    const current_method_label = document.querySelectorAll(
        ".edit_current_method_reason_label"
    );
    
    const isChecked = current_method.checked;
    toggleSubRadioInputs(current_method_type, current_method_label, isChecked);
}

// this function make the sub input visible or not if checked or not
function toggleSubRadioInputs(inputs, label, checked) {
    inputs.forEach((input) => {
        input.disabled = !checked;
    });
    label.forEach((label) => {
        label.classList.toggle("text-muted", !checked);
    });
}