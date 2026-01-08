import { puroks } from "../patient/healthWorkerList.js";
import { automateAge } from "../automateAge.js";
import Swal from "sweetalert2";

const plan_to_have_more_children_yes = document.getElementById("plan_yes");
const plan_yes_sub_input = document.querySelectorAll(
    'input[name="plan_to_have_more_children_yes"]'
);
const plan_yes_sub_label = document.querySelectorAll(".plan_yes_label");

// plan to have more child inputs
const plan_to_have_more_children = document.querySelectorAll(
    'input[name="plan_to_have_more_children"]'
);

const isPlanYesChecked = plan_to_have_more_children_yes.checked;
toggleSubRadioInputs(plan_yes_sub_input, plan_yes_sub_label, isPlanYesChecked);

plan_to_have_more_children.forEach((input) => {
    input.addEventListener("change", () => {
        const yesChecked = plan_to_have_more_children_yes.checked;
        toggleSubRadioInputs(
            plan_yes_sub_input,
            plan_yes_sub_label,
            yesChecked
        );
    });
});

// for the plan to have more children
const methodCheckBoxes = document.querySelectorAll(
    'input[name="currently_using_methods[]"]'
);
const checkboxLabel = document.querySelectorAll(".check-box-label");
const currently_using_FP_methods_yes = document.getElementById("fp_yes");
const isFPmethodsYes = currently_using_FP_methods_yes.checked;
const currently_using_FP_methods = document.querySelectorAll(
    'input[name="currently_using_any_FP_method"]'
);

toggleSubRadioInputs(methodCheckBoxes, checkboxLabel, isFPmethodsYes);

currently_using_FP_methods.forEach((input) => {
    input.addEventListener("change", () => {
        const yesChecked = currently_using_FP_methods_yes.checked;
        toggleSubRadioInputs(methodCheckBoxes, checkboxLabel, yesChecked);
    });
});

// add toggle functionality
const accept_any_modern_FP_method = document.querySelectorAll(
    'input[name="wra_accept_any_modern_FP_method"]'
);
const modernFPmethods = document.querySelectorAll(".modern-FP-inputs");
const modernFPlabels = document.querySelectorAll(".modern-check-box-label");
const wra_accept_any_modern_FP_method_yes = document.getElementById(
    "wra_accept_any_modern_FP_method_yes"
);
let isModernFPcheck = wra_accept_any_modern_FP_method_yes.checked;

toggleSubRadioInputs(modernFPmethods, modernFPlabels, isModernFPcheck);

accept_any_modern_FP_method.forEach((input) => {
    input.addEventListener("change", () => {
        const isChecked = wra_accept_any_modern_FP_method_yes.checked;
        toggleSubRadioInputs(modernFPmethods, modernFPlabels, isChecked);
    });
});

// this function make the sub input visible or not if checked or not
function toggleSubRadioInputs(inputs, label, checked) {
    inputs.forEach((input) => {
        input.disabled = !checked;
        // console.log("working!");
        // if (!checked) {
        //     input.checked = false;
        // }
    });
    if (label) {
        label.forEach((label) => {
            label.classList.toggle("text-muted", !checked);
        });
    }
}

// edit btn
const editBtn = document.querySelectorAll(".wra-masterlist-edit-btn");
const wra_update_btn = document.getElementById(
    "update_wra_masterlist_save_btn"
);

document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".wra-masterlist-edit-btn");
    if (!editBtn) return;
    const id = editBtn.dataset.wraMasterlistId;
    wra_update_btn.dataset.masterlistId = id;
    // console.log(wra_update_btn.dataset.masterlistId);
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        // fetch the data
        const response = await fetch(`/masterist/wra/${id}`);

        if (response.ok) {
            const data = await response.json();

            // console.log(data.info);

            // populate the existing records
            Object.entries(data.info).forEach(([key, value]) => {
                // skip nulls early

                switch (key) {
                    case "name_of_wra":
                        const [fnameVal, MIVal, lnameVal] = value.split(" ");
                        document.getElementById("wra_masterlist_fname").value =
                            fnameVal || "";
                        document.getElementById("wra_masterlist_MI").value =
                            MIVal || "";
                        document.getElementById("wra_masterlist_lname").value =
                            lnameVal || "";
                        break;

                    case "date_of_birth":

                        const date_of_birth = value ?? '';
                        // console.log("Formatted: ", formatted);

                        document.querySelector(`input[name="${key}"]`).value =
                            date_of_birth;

                        break;
                    case 'age':
                        if (key == 'age' && value != null) {
                            const age = document.getElementById("age");
                            const hiddenAge = document.getElementById("hiddenAge");
                            if (age && hiddenAge) {
                                age.value = value;
                                hiddenAge.value = value;
                            }
                        }
                        break;

                    case "sex":
                        document
                            .querySelectorAll('input[name="sex"]')
                            .forEach((input) => {
                                input.checked = input.value === value;
                            });
                        break;

                    case "address":
                        const addressText = `${
                            data.address_info.house_number
                        },${data.address_info.street || ""}`.trim();
                        document.getElementById("street").value = addressText;
                        puroks(
                            document.getElementById("edit_brgy"),
                            data.address_info.purok
                        );
                        break;

                    case "plan_to_have_more_children_yes":
                        // Cache DOM references
                        const planYesRadio = document.querySelector(
                            "input[name='plan_to_have_more_children'][value='Yes']"
                        );
                        const planNoRadio = document.querySelector(
                            "input[name='plan_to_have_more_children'][value='No']"
                        );
                        const planYesSubRadios = document.querySelectorAll(
                            'input[name="plan_to_have_more_children_yes"]'
                        );

                        // Function to safely check a radio or checkbox
                        const checkInput = (input) => {
                            if (input) input.checked = true;
                        };

                        // Main logic
                        if (value === "now" || value === "spacing") {
                            // Check main "Yes" radio
                            checkInput(planYesRadio);

                            // Enable/toggle sub radios
                            toggleSubRadioInputs(
                                plan_yes_sub_input,
                                plan_yes_sub_label,
                                true
                            );

                            // Check all sub radios
                            planYesSubRadios.forEach(checkInput);
                        } else if (
                            data.info?.plan_to_have_more_children_no ===
                            "limiting"
                        ) {
                            // Check main "No" radio
                            checkInput(planNoRadio);
                        }
                        break;

                    case "current_FP_methods":
                        if (value === "" || value === null) {
                            const currently_using_any_FP_method_no =
                                document.querySelector(
                                    "input[name='currently_using_any_FP_method'][value='no']"
                                );
                            currently_using_any_FP_method_no.checked = true;
                            // console.log("this must be working!");
                            break;
                        }

                        document.querySelector(
                            "input[name='currently_using_any_FP_method'][value='yes']"
                        ).checked = true;
                        toggleSubRadioInputs(
                            methodCheckBoxes,
                            checkboxLabel,
                            true
                        );
                        const methods = value ? value.split(",") : [];
                        methodCheckBoxes.forEach((box) => {
                            box.checked = methods.includes(box.value);
                        });
                        break;

                    case "selected_modern_FP_method":
                        const modernSelected = value ? value.split(",") : [];
                        modernFPmethods.forEach((box) => {
                            box.checked = modernSelected.includes(box.value);
                        });
                        break;

                    default:
                        const inputs = document.querySelectorAll(
                            `input[name="${key}"]`
                        );
                        if (!inputs.length) break;

                        if (inputs[0].type === "radio") {
                            inputs.forEach((input) => {
                                input.checked = input.value === value;
                            });
                        } else if (inputs[0].type === "checkbox") {
                            const values = Array.isArray(value)
                                ? value
                                : value.split(",");
                            inputs.forEach((input) => {
                                input.checked = values.includes(input.value);
                            });
                        } else {
                            inputs[0].value = value; // text, number, date
                        }
                        break;
                }
            });

            isModernFPcheck = wra_accept_any_modern_FP_method_yes.checked;

            toggleSubRadioInputs(
                modernFPmethods,
                modernFPlabels,
                isModernFPcheck
            );

            // add the medical record case id to update btn

            wra_update_btn.dataset.masterlistId = id;
        }
    } catch (error) {
        console.error("Error in fetching:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to fetch record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});


// UPLOADING THE DATA
wra_update_btn.addEventListener("click", async (e) => {
    e.preventDefault();

    // form
    const form = document.getElementById("edit-wra-masterlist-form");
    const formData = new FormData(form);
    // for (let [key, value] of formData.entries()) {
    //     console.log(`${key}: ${value}`);
    // }

    const id = e.target.dataset.masterlistId;

    const response = await fetch(`/masterlist/update/wra/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
        // // reset the error element text first
        // errorElements.forEach((element) => {
        //     element.textContent = "";
        // });
        // // if there's an validation error load the error text
        // Object.entries(data.errors).forEach(([key, value]) => {
        //     if (document.getElementById(`${key}_error`)) {
        //         document.getElementById(`${key}_error`).textContent = value;
        //     }
        // });
        let errorMessage = "";

        if (data.errors) {
            // Handle ValidationException
            errorMessage = Object.values(data.errors)
                .flat() // flatten nested arrays if present
                .join("\n");
        } else if (data.message) {
            // Handle general backend errors
            errorMessage = data.message;
        } else {
            // Handle unexpected responses
            errorMessage = "An unexpected error occurred.";
        }

        Swal.fire({
            title: "Error",
            text: capitalizeEachWord(errorMessage),
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        // errorElements.forEach((element) => {
        //     element.textContent = "";
        // });
        Swal.fire({
            title: "Update",
            text: capitalizeEachWord(data.message),
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("wraMasterListModal")
                );
                modal.hide();
            }
        });
    }
});

const dob = document.getElementById("birthdate");
const age = document.getElementById("age");
const hiddenAge = document.getElementById("hiddenAge");

if (dob && age && hiddenAge) {
    automateAge(dob, age, hiddenAge);
}

function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
