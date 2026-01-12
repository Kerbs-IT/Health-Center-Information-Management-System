import Swal from "sweetalert2";
import { addVaccineInteraction } from "../patient/healthWorkerList";
import { automateAge } from "../automateAge";
import changeLmp from "../LMP/lmp";
window.currentStep = 1;
document.addEventListener("DOMContentLoaded", () => {
    const typeSelect = document.getElementById("type-of-patient");

    window.showStep = function (step) {
        const selected = document.getElementById("type-of-patient").value;
        if (currentStep == 1) {
            document.getElementById("head-text").innerHTML =
                "Basic Information";
        } else if (currentStep == 2) {
            document.getElementById("head-text").innerHTML =
                "Medical Service Record";
        } else if (currentStep == 3) {
            document.getElementById("head-text").innerHTML =
                "Additional Information";
        }
        document.querySelectorAll(".step").forEach((div) => {
            if (div && div.classList) {
                div.classList.remove("d-flex");
                div.classList.add("d-none");
            }
        });
        if (currentStep == 2) {
            document.querySelectorAll(".patient-type").forEach((box) => {
                box.classList.add("d-none");
            });
            const selectedDiv = document.getElementById(selected + "-con");
            if (selectedDiv) {
                selectedDiv.classList.remove("d-none");
                selectedDiv.classList.add("d-flex", "flex-column");
            }
        }
        if (currentStep == 3) {
            if (selected == "prenatal") {
                document
                    .getElementById("step" + step)
                    .classList.remove("d-none");
                document.getElementById("step" + step).classList.add("d-flex");
                document
                    .getElementById("step" + step)
                    .classList.add("flex-column");
                // target the specific div

                // hide the family planning
                document
                    .getElementById("family-planning-step3")
                    .classList.remove("d-flex");
                document
                    .getElementById("family-planning-step3")
                    .classList.add("d-none");

                document
                    .getElementById("prenatal-step3")
                    .classList.remove("d-none");

                document
                    .getElementById("family-planning-step3")
                    .classList.remove("d-flex");
                document
                    .getElementById("family-planning-step3")
                    .classList.add("d-none");
            } else if (selected == "family-planning") {
                // console.log("taena gumana kaya boy");
                document
                    .getElementById("step" + step)
                    .classList.remove("d-none");
                document.getElementById("step" + step).classList.add("d-flex");
                document
                    .getElementById("step" + step)
                    .classList.add("flex-column");

                document
                    .getElementById("family-planning-step3")
                    .classList.replace("d-none", "d-flex");
                // console.log(
                //     document.querySelectorAll("#family-planning-step3")
                // );

                document
                    .getElementById("prenatal-step3")
                    .classList.remove("d-flex");
                document
                    .getElementById("prenatal-step3")
                    .classList.add("d-none");
            }
        } else {
            document.getElementById("step" + step).classList.remove("d-none");
            document.getElementById("step" + step).classList.add("d-flex");
            document.getElementById("step" + step).classList.add("flex-column");
        }
    };

    window.nextStep = function () {
        // get important values
        const fname = document.getElementById("first_name");
        const lname = document.getElementById("last_name");
        const street = document.getElementById("street");
        const brgy = document.getElementById("brgy");
        const handled_by = document.getElementById("handled_by");
        const errors = { fname, lname, street, brgy, handled_by };
        const suffix = document.getElementById("add_suffix");

        Object.values(errors).forEach((element) => {
            element.style.border = "";
        });

        if (typeSelect.value === "") {
            Swal.fire({
                // title: 'Type of Patient',
                text: "Select the Type of Patient",
                icon: "warning",
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ok",
            });
            typeSelect.focus();

            return; // stop the function here
        }
        if (
            fname.value == "" ||
            lname.value == "" ||
            street.value == "" ||
            brgy.value == "" ||
            handled_by.value == ""
        ) {
            Swal.fire({
                // title: 'Type of Patient',
                text: "Important information is empty",
                icon: "warning",
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ok",
            });
            fname.style.border = fname.value ? "" : "2px solid red";
            lname.style.border = lname.value ? "" : "2px solid red";
            street.style.border = street.value ? "" : "2px solid red";
            brgy.style.border = brgy.value ? "" : "2px solid red";
            handled_by.style.border = handled_by.value ? "" : "2px solid red";
            return; // stop the function here
        }

        const patient_name_view = document.getElementById(
            "vaccination_patient_name_view"
        );
        const MI = document.getElementById("middle_initial");
        if (patient_name_view) {
            insertNameValue(fname, MI, lname, patient_name_view, suffix);
        }
        // give all the patient name id
        const senior_patient_name = document.getElementById(
            "senior_patient_name"
        );

        if (senior_patient_name) {
            insertNameValue(fname, MI, lname, senior_patient_name, suffix);
        }
        const tb_dots_patient_name = document.getElementById("tb_patient_name");

        if (tb_dots_patient_name) {
            insertNameValue(fname, MI, lname, tb_dots_patient_name, suffix);
        }

        window.currentStep++;
        window.showStep(window.currentStep);
    };

    function insertNameValue(fname, MI, lname, element,suffix) {
        const fullname = fname.value + " " + MI.value + " " + lname.value + " " + suffix.value;

        element.value = fullname.trim();
    }

    window.prevStep = function () {
        window.currentStep--;
        window.showStep(window.currentStep);
    };

    window.showAdditional = function () {
        let dropdown = document.getElementById("type-of-patient");
        let dropdownValue = dropdown.value;
        if (dropdownValue == "vaccination") {
            // hide the prenatal
            document
                .querySelector(".prenatal-inputs")
                .classList.replace("d-flex", "d-none");
            document
                .querySelector(".vaccination-inputs")
                .classList.replace("d-none", "d-flex");
            // hide family planning inputs
            document
                .querySelector(".family-planning-inputs")
                .classList.replace("d-flex", "d-none");
            // vital sign
            document
                .querySelector(".first-row")
                .classList.replace("d-flex", "d-none");
            document
                .querySelector(".second-row")
                .classList.replace("d-flex", "d-none");
            document
                .querySelector(".third-row")
                .classList.replace("d-none", "d-flex");
        } else if (dropdownValue == "prenatal") {
            // hide the vaccination
            document
                .querySelector(".vaccination-inputs")
                .classList.replace("d-flex", "d-none");

            document
                .querySelector(".prenatal-inputs")
                .classList.replace("d-none", "d-flex");
            // hide family planning
            document
                .querySelector(".family-planning-inputs")
                .classList.replace("d-flex", "d-none");
            //
            document
                .querySelector(".first-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".second-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".third-row")
                .classList.replace("d-flex", "d-none");
        } else if (dropdownValue == "family-planning") {
            document
                .querySelector(".vaccination-inputs")
                .classList.replace("d-flex", "d-none");

            document
                .querySelector(".prenatal-inputs")
                .classList.replace("d-flex", "d-none");
            // vital
            document
                .querySelector(".first-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".second-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".third-row")
                .classList.replace("d-flex", "d-none");
            // close otherrr input
            document
                .querySelector(".tb-dots-inputs")
                .classList.replace("d-flex", "d-none");
            document
                .querySelector(".senior-citizen-inputs")
                .classList.replace("d-flex", "d-none");

            // show the family planning
            document
                .querySelectorAll(".family-planning-inputs")
                .forEach((element) => {
                    element.classList.replace("d-none", "d-flex");
                });
        } else if (dropdownValue == "senior-citizen") {
            document
                .querySelector(".vaccination-inputs")
                .classList.replace("d-flex", "d-none");

            document
                .querySelector(".prenatal-inputs")
                .classList.replace("d-flex", "d-none");
            document
                .querySelector(".family-planning-inputs")
                .classList.replace("d-flex", "d-none");
            // vital
            document
                .querySelector(".first-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".second-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".third-row")
                .classList.replace("d-flex", "d-none");
            // close otherrr input
            document
                .querySelector(".tb-dots-inputs")
                .classList.replace("d-flex", "d-none");
            // show senior citizen
            document
                .querySelector(".senior-citizen-inputs")
                .classList.replace("d-none", "d-flex");
        } else if (dropdownValue == "tb-dots") {
            document
                .querySelector(".vaccination-inputs")
                .classList.replace("d-flex", "d-none");

            document
                .querySelector(".prenatal-inputs")
                .classList.replace("d-flex", "d-none");
            document
                .querySelector(".family-planning-inputs")
                .classList.replace("d-flex", "d-none");
            // vital
            document
                .querySelector(".first-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".second-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".third-row")
                .classList.replace("d-flex", "d-none");

            // close otherrr input
            document
                .querySelector(".senior-citizen-inputs")
                .classList.replace("d-flex", "d-none");
            // show senior citizen
            document
                .querySelector(".tb-dots-inputs")
                .classList.replace("d-none", "d-flex");
        } else {
            document
                .querySelector(".vaccination-inputs")
                .classList.replace("d-flex", "d-none");

            document
                .querySelector(".prenatal-inputs")
                .classList.replace("d-flex", "d-none");
            // vital
            document
                .querySelector(".first-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".second-row")
                .classList.replace("d-none", "d-flex");
            document
                .querySelector(".third-row")
                .classList.replace("d-flex", "d-none");
        }
    };

    const handled_by = document.getElementById("handled_by");
    const handledByViewInput = document.getElementById("handle_by_view_input");

    if (handled_by && handledByViewInput) {
        if (handled_by.tagName.toLowerCase() === "select") {
            handled_by.addEventListener("change", (e) => {
                if (typeSelect.value === "vaccination") {
                    const selectedText =
                        handled_by.options[handled_by.selectedIndex].text;
                    handledByViewInput.value = selectedText;
                }
            });
        } else if (handled_by.type === "hidden") {
            handledByViewInput.value =
                handled_by.dataset.healthWorkerName || "";
        }
    }

    typeSelect.addEventListener("change", function () {
        disableSubmitBtn(typeSelect.value);
    });

    // handle adding the vaccine

    const addVaccineBtn = document.getElementById("vaccine-add-btn");
    const vaccinesContainer = document.querySelector(".vaccines-container");
    const selectedVaccinesCon = document.getElementById("selected_vaccines");
    const selectedVaccines = [];

    const vaccineInput = document.getElementById("vaccine_input");

    addVaccineInteraction(
        addVaccineBtn,
        vaccineInput,
        vaccinesContainer,
        selectedVaccinesCon,
        selectedVaccines
    );

    if (vaccinesContainer) {
        vaccinesContainer.addEventListener("click", (e) => {
            console.log("before deletion:", selectedVaccines);
            if (e.target.closest(".vaccine")) {
                const vaccineId = e.target.closest(".vaccine").dataset.bsId;
                console.log("id of element:", vaccineId);
                const deleteBtn = e.target.closest(".delete-icon");
                if (deleteBtn) {
                    if (selectedVaccines.includes(Number(vaccineId))) {
                        const selectedElement = selectedVaccines.indexOf(
                            Number(vaccineId)
                        );
                        console.log("index", selectedElement);
                        selectedVaccines.splice(selectedElement, 1);
                        selectedVaccinesCon.value = selectedVaccines.join(",");
                    }

                    setTimeout(() => {
                        updateDoseDropdown(selectedVaccines);
                    }, 100);

                    e.target.closest(".vaccine").remove();
                }

                console.log("update with deleted id:", selectedVaccines);
                console.log("updated value:", selectedVaccinesCon.value);
            }
        });
    }

    // SUBMIT THE FORM FOR VACCINATION

    const vaccinationSubmitBtn = document.getElementById(
        "vaccination-submit-btn"
    );

    if (!vaccinationSubmitBtn) return;
    vaccinationSubmitBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const doseDropdown = document.getElementById("dose"); // adjust selector
        const selectedDose = parseInt(doseDropdown.value);
        // Validate vaccines against selected dose
        const invalidVaccines = validateVaccinesWithDose(
            selectedVaccines,
            selectedDose
        );

        if (invalidVaccines.length > 0) {
            const invalidList = invalidVaccines
                .map((v) => `• ${v.name} (${v.description})`)
                .join("<br>");

            Swal.fire({
                title: "Dose Mismatch",
                html: `The following vaccines cannot have Dose ${selectedDose}:<br><br>${invalidList}<br><br><strong>Please separate these vaccines into different records or select an appropriate dose number.</strong>`,
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            return;
        }

        const form = document.getElementById("add-patient-form");
        const formData = new FormData(form);
        // for (let [key, value] of formData.entries()) {
        //     console.log(`${key}: ${value}`);
        // }

        const response = await fetch("/add-patient/vaccination", {
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

        const errorElements = document.querySelectorAll(".error-text");
        if (response.ok) {
            errorElements.forEach((element) => {
                element.textContent = "";
            });
            Swal.fire({
                title: "Add",
                text: "Vaccination Patient Information is successfully Added",
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
        } else {
            // reset the error element text first
            errorElements.forEach((element) => {
                element.textContent = "";
            });
            // if there's an validation error load the error text
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
                message = data.message ?? "An unexpected error occurred.";
            }

            Swal.fire({
                title: "Vaccination Patient",
                html: capitalizeEachWord(message).replace(/\n/g, "<br>"), // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    });
});
// ================================= HANDLE THE DATE OF BIRTH ========================================
const date_of_birth = document.getElementById("birthdate");
const age = document.getElementById("age");
const hiddenAge = document.getElementById("hiddenAge");

if (date_of_birth && age) {
    automateAge(date_of_birth, age, hiddenAge);
}

// ====================== LMP ====================
const LMP = document.getElementById("LMP") ?? null;

if (LMP) {
    const expectedDelivery = document.getElementById(
        "add_patient_expected_delivery"
    );

    LMP.addEventListener("change", () => {
        changeLmp(LMP, expectedDelivery);
    });
}
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

// add a validation for he vaccine doses
const vaccineDoseConfig = {
    1: { maxDoses: 1, description: "at birth", name: "BCG Vaccine" },
    2: { maxDoses: 1, description: "at birth", name: "Hepatitis B Vaccine" },
    3: {
        maxDoses: 3,
        description: "doses 1-3",
        name: "Pentavalent Vaccine (DPT-HEP B-HIB)",
    },
    4: {
        maxDoses: 3,
        description: "doses 1-3",
        name: "Oral Polio Vaccine (OPV)",
    },
    5: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Inactived Polio Vaccine (IPV)",
    },
    6: {
        maxDoses: 3,
        description: "doses 1-3",
        name: "Pnueumococcal Conjugate Vaccine (PCV)",
    },
    7: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Measles, Mumps, Rubella Vaccine (MMR)",
    },
    8: {
        maxDoses: 1,
        description: "dose 1",
        name: "Measles Containing Vaccine (MCV) MR/MMR (Grade 1)",
    },
    9: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Measles Containing Vaccine (MCV) MR/MMR (Grade 7)",
    },
    10: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Tetanus Diphtheria (TD)",
    },
    11: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Human Papiliomavirus Vaccine",
    },
    12: { maxDoses: 3, description: "doses 1-3", name: "Influenza Vaccine" },
    13: { maxDoses: 3, description: "doses 1-3", name: "Pnuemococcal Vaccine" },
};

function updateDoseDropdown(selectedVaccines) {
    const doseDropdown = document.getElementById("dose");

    if (!doseDropdown) return;

    // Find maximum dose from selected vaccines
    let maxDose = 1;
    selectedVaccines.forEach((id) => {
        if (vaccineDoseConfig[id]) {
            maxDose = Math.max(maxDose, vaccineDoseConfig[id].maxDoses);
        }
    });

    // Store current selection
    const currentValue = doseDropdown.value;

    // Clear and rebuild options
    doseDropdown.innerHTML = '<option value="">Select Dose</option>';

    for (let i = 1; i <= maxDose; i++) {
        const option = document.createElement("option");
        option.value = `${i}`;
        option.textContent = `Dose ${i}`;
        doseDropdown.appendChild(option);
    }

    // add a condition if the selectedVaccines is empty
    if (selectedVaccines.length <= 0) {
        doseDropdown.innerHTML = "";
        doseDropdown.innerHTML = '<option value="">Select Dose</option>';
        for (let i = 1; i <= 3; i++) {
            const option = document.createElement("option");
            option.value = `${i}`;
            option.textContent = `Dose ${i}`;
            doseDropdown.appendChild(option);
        }
    }

    // Restore selection if still valid
    if (currentValue && currentValue <= maxDose) {
        doseDropdown.value = currentValue;
    }
}

// Function to validate vaccines with selected dose
function validateVaccinesWithDose(selectedVaccines, selectedDose) {
    const invalidVaccines = [];

    selectedVaccines.forEach((id) => {
        const config = vaccineDoseConfig[id];
        if (config && selectedDose > config.maxDoses) {
            invalidVaccines.push({
                name: config.name,
                maxDoses: config.maxDoses,
                description: config.description,
            });
        }
    });

    return invalidVaccines;
}

// handle the submit

function disableSubmitBtn(typeOfPatient) {
    // IDS
    const vaccination = document.getElementById("vaccination-submit-btn");
    const prenatal = document.getElementById("prenatal-save-btn");
    const tbDots = document.getElementById("tb_dots_save_record_btn");
    const seniorCitizen = document.getElementById(
        "senior_citizen_save_record_btn"
    );
    const familyPlanning = document.getElementById(
        "family_planning_submit_btn"
    );
    // FIRST: Disable ALL buttons
    vaccination.disabled = true;
    prenatal.disabled = true;
    tbDots.disabled = true;
    seniorCitizen.disabled = true;
    familyPlanning.disabled = true;

    // THEN: Enable only the selected one
    switch (typeOfPatient) {
        case "vaccination":
            vaccination.disabled = false;
            break;
        case "prenatal":
            prenatal.disabled = false;
            break;
        case "tb-dots":
            tbDots.disabled = false;
            break;
        case "senior-citizen":
            seniorCitizen.disabled = false;
            break;
        case "family-planning":
            familyPlanning.disabled = false;
            break;
        default:
            break;
    }
}
