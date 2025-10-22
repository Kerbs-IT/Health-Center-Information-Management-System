import Swal from "sweetalert2";


const viewIcon = document.getElementById("view-family-plan-info");

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
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            }
            if (key == "type_of_patient" && value == "current user") {
                if (data.caseInfo.current_user_reason_for_FP != "") {
                    document.getElementById(
                        `view_${key}`
                    ).innerHTML = `${value}/${data.caseInfo.current_user_reason_for_FP}`;
                } else {
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            }
            if (key == "spouse_lname") {
                if (document.getElementById("view_spouse_name")) {
                    console.log("wording");
                    document.getElementById("view_spouse_name").innerHTML = `${
                        data.caseInfo.spouse_fname ?? ""
                    } ${data.caseInfo.spouse_MI ?? ""} ${
                        data.caseInfo.spouse_lname ?? ""
                    }`.trim();
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
                console.log(key);
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

// edit section
const editIcon = document.getElementById("edit-family-plan-info");
const editSaveBtn = document.getElementById("edit-family-planning-case-btn");

editIcon.addEventListener("click", async (e) => {
    e.preventDefault();
    const caseId = editIcon.dataset.caseId;

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
                        data.caseInfo.new_acceptor_reason_for_FP != "spacing" ||
                        data.caseInfo.new_acceptor_reason_for_FP != "spacing"
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
                        data.caseInfo.current_user_reason_for_FP != "spacing" ||
                        data.caseInfo.current_user_reason_for_FP != "spacing"
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
                        const current_method_reason = document.querySelectorAll(
                            "edit_current_method_reason"
                        );

                        if (
                            data.caseInfo.current_method_reason !=
                            "medical condition"
                        ) {
                            document.getElementById(
                                "edit_current_method_reason_side_effect"
                            ).checked = true;
                            document.getElementById("edit_side_effects_text")
                                .value == data.caseInfo.current_method_reason;
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
                    if (document.querySelector(`input[name="edit_${key}"]`)) {
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
});

// update the record
editSaveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("edit-family-plan-form");
    const formData = new FormData(form);

    // id
    const id = editSaveBtn.dataset.caseId;

    const response = await fetch(`/patient-case/family-planning/update-case-info/${id}`, {
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
});

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
