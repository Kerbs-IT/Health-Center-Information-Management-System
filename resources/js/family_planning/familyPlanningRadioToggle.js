
const side_a_modal = document.getElementById("side-a-add-record");



// all the radios of extermites UID
if (side_a_modal) {
    side_a_modal.addEventListener("show.bs.modal", () => {
        const extremites_UID = document.querySelectorAll(
            'input[name="side_A_add_extremites_UID_type"]'
        );

        function toggleCervicalAbnormalities() {
            const cervical_abnormalities_radio = document.getElementById(
                "side_A_add_extremites_UID_type_cervical_abnormalities"
            );
            const cervical_abnormalities_types = document.querySelectorAll(
                "input[name='side_A_add_cervical_abnormalities_type']"
            );
            const cervical_abnormalities_types_labels =
                document.querySelectorAll(".cervical_abnormalities_type");
            const isChecked = cervical_abnormalities_radio.checked;
            toggleSubRadioInputs(
                cervical_abnormalities_types,
                cervical_abnormalities_types_labels,
                isChecked
            );

            // ----------------------------------
            // cervical consistency
            const cervical_consistency = document.getElementById(
                "side_A_add_extremites_UID_type_cervical_consistency"
            );
            const cervical_consistency_type = document.querySelectorAll(
                'input[name="side_A_add_cervical_consistency_type"]'
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
                "side_a_physical_examination_extremites_UID_type_uterine"
            );
            const uterine_position_type = document.querySelectorAll(
                "input[name='side_A_add_uterine_position_type']"
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

        // initial check
        toggleCervicalAbnormalities();

        // update dynamically on click

        extremites_UID.forEach((radio) => {
            radio.addEventListener("change", toggleCervicalAbnormalities);
        });

        // for the type of user
        const type_of_client = document.querySelectorAll(
            "input[name='side_A_add_type_of_patient']"
        );
        type_of_client.forEach((radio) => {
            radio.addEventListener("change", toggleTypeOfClient);
        });

        // current method type
        const current_user_types = document.querySelectorAll(
            'input[name="side_A_add_current_user_type"]'
        );

        current_user_types.forEach((radio) => {
            radio.addEventListener("change", toggleCurrentMethod);
        });

        toggleTypeOfClient();
        toggleCurrentMethod();
    });
}


// ------------------------------------functions-----------------------------

function toggleTypeOfClient() {
    const current_user = document.getElementById("side_A_add_current_user");
    const type_of_curent_user = document.querySelectorAll(
        'input[name="side_A_add_current_user_type"]'
    );
    const type_of_current_user_label = document.querySelectorAll(
        ".side_a_current_user_type_label"
    );
    const isCurrentUser = current_user.checked;

    toggleSubRadioInputs(
        type_of_curent_user,
        type_of_current_user_label,
        isCurrentUser
    );
    // toggle for the reason
    const current_user_reason = document.querySelectorAll(
        "input[name='side_A_add_current_user_reason_for_FP']"
    );
    const current_user_label = document.querySelectorAll(".side_a_current_user_label");

    toggleSubRadioInputs(
        current_user_reason,
        current_user_label,
        isCurrentUser
    );

    toggleCurrentMethod();

    const new_acceptor = document.getElementById("side_A_add_new_acceptor");
    const isNew = new_acceptor.checked;

    // for the new acceptor
    const new_acceptor_reasons = document.querySelectorAll(
        'input[name="side_A_add_new_acceptor_reason_for_FP"]'
    );
    const new_acceptor_label = document.querySelectorAll(
        ".side_a_new_acceptor_label"
    );
    toggleSubRadioInputs(new_acceptor_reasons, new_acceptor_label, isNew);

    if (isNew) {
        const current_method = document.getElementById(
            "side_A_add_current_method"
        );
        const current_method_type = document.querySelectorAll(
            'input[name="side_A_add_current_method_reason"]'
        );
        const current_method_label = document.querySelectorAll(
            ".side_a_current_method_label"
        );
        current_method.checked = false;
        toggleSubRadioInputs(current_method_type, current_method_label, false);
    } else {
        const new_acceptor_reasons = document.querySelectorAll(
            "input[name='side_A_add_new_acceptor_reason_for_FP']"
        );
        toggleCurrentMethod();
    }
}

function toggleCurrentMethod() {
    // current method toggle
    const current_user = document.getElementById("side_A_add_current_user");
    const isCurrentUser = current_user.checked;
    const current_method = document.getElementById("side_A_add_current_method");
    const current_method_type = document.querySelectorAll(
        'input[name="side_A_add_current_method_reason"]'
    );
    const current_method_label = document.querySelectorAll(
        ".side_a_current_method_label"
    );
    const isChecked = current_method.checked;
    toggleSubRadioInputs(current_method_type, current_method_label, isChecked);
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