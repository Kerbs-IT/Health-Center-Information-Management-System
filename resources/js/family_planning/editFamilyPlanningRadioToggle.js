// editFamilyPlanningRadioToggle.js

/**
 * Utility function to toggle sub-radio inputs and labels
 */
function toggleSubRadioInputs(inputs, labels, checked) {
    inputs.forEach((input) => {
        input.disabled = !checked;
    });
    labels.forEach((label) => {
        label.classList.toggle("text-muted", !checked);
    });
}

/**
 * Toggle cervical abnormalities and related sections
 */
function toggleCervicalAbnormalities() {
    const configs = [
        {
            radioId: "edit_extremites_UID_type_cervical_abnormalities",
            inputName: "edit_cervical_abnormalities_type",
            labelClass: "edit_cervical_abnormalities_type_label",
        },
        {
            radioId: "edit_extremites_UID_type_cervical_consistency",
            inputName: "edit_cervical_consistency_type",
            labelClass: "edit_cervical_consistency_type_label",
        },
        {
            radioId: "edit_physical_examination_extremites_UID_type_uterine",
            inputName: "edit_uterine_position_type",
            labelClass: "edit_uterine_position_type_label",
        },
    ];

    configs.forEach((config) => {
        const radio = document.getElementById(config.radioId);
        if (radio) {
            const inputs = document.querySelectorAll(
                `input[name="${config.inputName}"]`
            );
            const labels = document.querySelectorAll(`.${config.labelClass}`);
            toggleSubRadioInputs(inputs, labels, radio.checked);
        }
    });
}

/**
 * Toggle type of client (Current User vs New Acceptor)
 */
function toggleTypeOfClient() {
    const current_user = document.getElementById("edit_current_user");
    const new_acceptor = document.getElementById("edit_new_acceptor");

    if (!current_user || !new_acceptor) return;

    const isCurrentUser = current_user.checked;
    const isNew = new_acceptor.checked;

    // Current user sub-options
    const currentUserConfigs = [
        {
            inputName: "edit_current_user_type",
            labelClass: "edit_current_user_type_label",
        },
        {
            inputName: "edit_current_user_reason_for_FP",
            labelClass: "edit_current_user_label",
        },
    ];

    currentUserConfigs.forEach((config) => {
        const inputs = document.querySelectorAll(
            `input[name="${config.inputName}"]`
        );
        const labels = document.querySelectorAll(`.${config.labelClass}`);
        toggleSubRadioInputs(inputs, labels, isCurrentUser);
    });

    // New acceptor sub-options
    const new_acceptor_reasons = document.querySelectorAll(
        'input[name="edit_new_acceptor_reason_for_FP"]'
    );
    const new_acceptor_label = document.querySelectorAll(
        ".edit_new_acceptor_label"
    );
    toggleSubRadioInputs(new_acceptor_reasons, new_acceptor_label, isNew);

    // Handle current method
    if (isNew) {
        const current_method = document.getElementById("edit_current_method");
        if (current_method) {
            current_method.checked = false;
            const current_method_type = document.querySelectorAll(
                'input[name="edit_current_method_reason"]'
            );
            const current_method_label = document.querySelectorAll(
                ".edit_current_method_reason_label"
            );
            toggleSubRadioInputs(
                current_method_type,
                current_method_label,
                false
            );
        }
    } else {
        toggleCurrentMethod();
    }
}

/**
 * Toggle current method options
 */
function toggleCurrentMethod() {
    const current_user = document.getElementById("edit_current_user");
    const current_method = document.getElementById("edit_current_method");

    if (!current_user || !current_method) return;

    const isCurrentUser = current_user.checked;
    const isChecked = current_method.checked;

    const current_method_type = document.querySelectorAll(
        'input[name="edit_current_method_reason"]'
    );
    const current_method_label = document.querySelectorAll(
        ".edit_current_method_reason_label"
    );

    toggleSubRadioInputs(current_method_type, current_method_label, isChecked);
}

/**
 * Initialize event listeners for the edit modal
 */
function initializeEditModalListeners() {
    // Extremites UID toggles
    const extremites_UID = document.querySelectorAll(
        'input[name="edit_extremites_UID_type"]'
    );
    extremites_UID.forEach((radio) => {
        radio.addEventListener("change", toggleCervicalAbnormalities);
    });

    // Type of client toggles
    const type_of_client = document.querySelectorAll(
        "input[name='edit_type_of_patient']"
    );
    type_of_client.forEach((radio) => {
        radio.addEventListener("change", toggleTypeOfClient);
    });

    // Current user type toggles
    const current_user_types = document.querySelectorAll(
        'input[name="edit_current_user_type"]'
    );
    current_user_types.forEach((radio) => {
        radio.addEventListener("change", toggleCurrentMethod);
    });
}

/**
 * Refresh all toggle states (call after loading data)
 */
function refreshToggleStates() {
    toggleCervicalAbnormalities();
    toggleTypeOfClient();
    toggleCurrentMethod();
}

/**
 * Initialize the modal
 */
function initializeEditModal() {
    const editFamilyPlanningSideA = document.getElementById(
        "editfamilyPlanningCaseModal"
    );

    if (!editFamilyPlanningSideA) {
        console.warn("Edit modal not found");
        return;
    }

    let isInitialized = false;

    editFamilyPlanningSideA.addEventListener("shown.bs.modal", () => {
        // Initialize listeners only once
        if (!isInitialized) {
            initializeEditModalListeners();
            isInitialized = true;
        }

        // Refresh toggle states when modal is shown
        refreshToggleStates();
    });
}

// Export functions for use in other modules
export {
    toggleCervicalAbnormalities,
    toggleTypeOfClient,
    toggleCurrentMethod,
    refreshToggleStates,
    initializeEditModal,
};

// Auto-initialize if not using as module
if (typeof module === "undefined") {
    document.addEventListener("DOMContentLoaded", initializeEditModal);
}
