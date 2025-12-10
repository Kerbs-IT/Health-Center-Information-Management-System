import Swal from "sweetalert2";
import { addVaccineInteraction } from "../patient/healthWorkerList";
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
                console.log(
                    document.querySelectorAll("#family-planning-step3")
                );
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
            insertNameValue(fname, MI, lname, patient_name_view);
        }
        // give all the patient name id
        const senior_patient_name = document.getElementById(
            "senior_patient_name"
        );

        if (senior_patient_name) {
            insertNameValue(fname, MI, lname, senior_patient_name);
        }
        const tb_dots_patient_name = document.getElementById("tb_patient_name");

        if (tb_dots_patient_name) {
            insertNameValue(fname, MI, lname, tb_dots_patient_name);
        }

        window.currentStep++;
        window.showStep(window.currentStep);
    };

    function insertNameValue(fname, MI, lname, element) {
        const fullname = fname.value + " " + MI.value + " " + lname.value;

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
                html: capitalizeEachWord(message).replace(/\n/g,"<br>"), // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    });
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}


