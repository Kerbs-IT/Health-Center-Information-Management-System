import Swal from "sweetalert2";
import { puroks } from "../patient/healthWorkerList.js";

const viewIcon = document.getElementById("view-family-plan-info") ?? null;

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".side-A-view-family-plan-info");
    if (!viewBtn) return;
    const caseId = viewBtn.dataset.caseId;
    // Validate case ID
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        alert("Unable to archive: Invalid ID");
        return;
    }
    console.log("event delegation working!!!");
    try {
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
                        console.log("wording");
                        document.getElementById("view_spouse_name").innerHTML =
                            `${data.caseInfo.spouse_fname ?? ""} ${
                                data.caseInfo.spouse_MI ?? ""
                            } ${data.caseInfo.spouse_lname ?? ""}`.trim();
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
    } catch (error) {
        console.error("Error:", error);
        Swal.fire({
            title: "Error",
            text: `An error occurred: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// edit section
const editIcon = document.getElementById("edit-family-plan-info") ?? null;
const editSaveBtn = document.getElementById("edit-family-planning-case-btn");

// ================== EDIT EVENT DELEGATION HERE =================================
document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".side-A-edit-family-plan-info");
    if (!editBtn) return;
    const caseId = editBtn.dataset.caseId;

    editSaveBtn.dataset.caseId = caseId;
    // Validate case ID
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        alert("Unable to archive: Invalid ID");
        return;
    }

    // try catch block
    try {
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
                    console.log(addressText);
                    document.getElementById("edit_street").value = addressText;
                    puroks(
                        document.getElementById("edit_brgy"),
                        data.address.purok
                    );
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

           
        }
    } catch (error) {
        console.error("Error:", error);
        Swal.fire({
            title: "Error",
            text: `An error occurred: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});

// update the record
editSaveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("edit-family-plan-form");
    const formData = new FormData(form);

    // id
    const id = editSaveBtn.dataset.caseId;
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

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
        
        // refresh the wra masterlist
        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("wraMasterlistRefreshTable");
        } else {
            console.warn("Livewire is not available");
        }
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
                console.log("working dapat");
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

// side btn interaction
const side_b_BTN = document.getElementById("side-b-add-record-btn");

side_b_BTN.addEventListener("click", () => {
    const patientInfo = JSON.parse(side_b_BTN.dataset.patientInfo);
    // populate the hidden input
    const medical_case_record_id_element = document.getElementById(
        "side_b_medical_record_case_id"
    );
    const health_worker_id_element = document.getElementById(
        "side_b_health_worker_id"
    );

    // give the value
    medical_case_record_id_element.value = patientInfo.id;
    health_worker_id_element.value =
        patientInfo.family_planning_medical_record.health_worker_id;

    // date of the visit
    const date_of_visit = document.getElementById("side_b_date_of_visit");

    const today = new Date().toISOString().split("T")[0];
    date_of_visit.value = today;
});

// upload the data to the database
const side_b_save_record_btn = document.getElementById(
    "side-b-save-record-btn"
);

side_b_save_record_btn.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("side-b-add-form");
    const formData = new FormData(form);

    const response = await fetch(
        "/patient-record/family-planning/add/side-b-record/",
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

    // handle the response, its either success or there are errors
    const errorElements = document.querySelectorAll(".error-text");

    if (response.ok) {
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        // ✅ Safe Livewire dispatch
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
            title: "Family Planning Patient",
            text: capitalizeEachWord(data.message), // this will make the text capitalize each word
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("side-b-add-record")
                );
                modal.hide();
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
// add side A (in case the record is deleted)

const add_side_A_BTN = document.getElementById("side-a-add-record-btn");

add_side_A_BTN.addEventListener("click", () => {
    // reset the form first
    const form = document.getElementById("side-a-add-form");
    form.reset();

    const patientInfo = JSON.parse(add_side_A_BTN.dataset.patientInfo);
    const address = JSON.parse(add_side_A_BTN.dataset.patientAddress);
    console.log(patientInfo);
    console.log(address);
    // get the important element
    const client_fname = document.getElementById("side_A_add_client_fname");
    const client_MI = document.getElementById("side_A_add_client_MI");
    const client_lname = document.getElementById("side_A_add_client_lname");
    const client_bday = document.getElementById(
        "side_A_add_client_date_of_birth"
    );
    const client_age = document.getElementById("side_A_add_client_age");
    const client_occupation = document.getElementById("side_A_add_occupation");
    const client_civil_status = document.getElementById(
        "side_A_add_client_civil_status"
    );
    const client_religion = document.getElementById(
        "side_A_add_client_religion"
    );

    // get the add street and add brgy
    const add_street = document.getElementById("add_street") ?? null;
    const add_brgy = document.getElementById("add_brgy") ?? null;

    if (!add_street && !add_brgy) return;

    // populate the address
    const addressText = `${address.house_number},${
        address.street || ""
    }`.trim();
    //  console.log(addressText);
    add_street.value = addressText;
    puroks(add_brgy, address.purok);

    // ============= POPULATE THE HEALTH WORKER ID
    const add_health_worker_id_element =
        document.getElementById("side_A_add_health_worker_id") ?? null;
    if (!add_health_worker_id_element) return;
    add_health_worker_id_element.value =
        patientInfo.family_planning_medical_record.health_worker_id ?? null;
    console.log(add_health_worker_id_element.value);
    // populate the inputs
    client_fname.value = patientInfo.patient.first_name;
    client_MI.value = patientInfo.patient.middle_initial;
    client_lname.value = patientInfo.patient.last_name;
    client_age.value = patientInfo.patient.age;
    client_bday.value = patientInfo.patient.date_of_birth;
    client_occupation.value =
        patientInfo.family_planning_medical_record.occupation;
    client_civil_status.value = patientInfo.patient.civil_status;
    client_religion.value = patientInfo.family_planning_medical_record.religion;
});

// upload the record
const side_A_upload_btn = document.getElementById("side-a-save-record-btn");
side_A_upload_btn.addEventListener("click", async (e) => {
    e.preventDefault();

    const id = add_side_A_BTN.dataset.medicalCaseRecordId;
    const form = document.getElementById("side-a-add-form");
    const formData = new FormData(form);

    const response = await fetch(
        `/patient-record/family-planning/add/side-a-record/${id}`,
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

    // handle the response, its either success or there are errors
    const errorElements = document.querySelectorAll(".error-text");
    if (response.ok) {
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        Swal.fire({
            title: "Family Planning Patient Assessment Record",
            text: capitalizeEachWord(data.message), // this will make the text capitalize each word
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("side-a-add-record")
                );
               if (modal) {
                   modal.hide();
               }
            }
        });

        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("familyPlanningRefreshTable");
            Livewire.dispatch("wraMasterlistRefreshTable");// ✅ Update dispatch name if needed
        } else {
            console.warn("Livewire is not available");
        }

        
    } else {
        // Clear all error text first
        errorElements.forEach((element) => {
            element.textContent = "";
        });

        // Stop if no response
        if (!data) return;

        // If no validation errors, handle message only
        const hasErrors = data.errors && typeof data.errors === "object";

        if (hasErrors) {
            const returnErrors = Object.entries(data.errors);

            returnErrors.forEach(([key, value]) => {
                const el = document.getElementById(`${key}_error`);
                if (el) el.textContent = value;
            });
        }

        // Build popup message
        let message = "";

        if (hasErrors) {
            message = Object.values(data.errors).flat().join("\n");
        } else {
            message = data.message ?? "An unexpected error occurred.";
        }

        // Show popup
        Swal.fire({
            title: "Family Planning Patient Assessment Record",
            text: capitalizeEachWord(message),
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("side-a-add-record")
                );
                if (modal) {
                    modal.hide();
                }
            }
        });
    }
});

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

// archive side a

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
            Livewire.dispatch("familyPlanningRefreshTable"); // ✅ Update dispatch name if needed
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
