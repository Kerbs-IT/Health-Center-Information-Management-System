import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    let currentStep = 1;
    const typeSelect = document.getElementById("type-of-patient");

    function showStep(step) {
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
    }
    window.nextStep = function () {
        // get important values
        const fname = document.getElementById("first_name");
        const lname = document.getElementById("last_name");
        const street = document.getElementById("street");
        const brgy = document.getElementById("brgy");

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
            brgy.value == ""
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
            return; // stop the function here
        }

        currentStep++;
        showStep(currentStep);
    };

    window.prevStep = function () {
        currentStep--;
        showStep(currentStep);
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
                .querySelector(".family-planning-inputs")
                .classList.replace("d-none", "d-flex");
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

    handled_by.addEventListener("change", (e) => {
        if (typeSelect.value == "vaccination") {
            const selectedText =
                handled_by.options[handled_by.selectedIndex].text;
            const handledByViewInput = document.getElementById(
                "handle_by_view_input"
            );
            // console.log(selectedText);
            handledByViewInput.value = selectedText;
        }
    });

    // handle adding the vaccine

    const addVaccineBtn = document.getElementById("vaccine-add-btn");
    const vaccinesContainer = document.querySelector(".vaccines-container");
    const selectedVaccinesCon = document.getElementById("selected_vaccines");
    const selectedVaccines = [];

    function addInteraction(btn) {
        btn.addEventListener("click", (e) => {
            const vaccineInput = document.getElementById("vaccine_input");
            const selectedText =
                vaccineInput.options[vaccineInput.selectedIndex].text;
            const selectedId =
                vaccineInput.options[vaccineInput.selectedIndex].value;

            if (!selectedVaccines.includes(selectedId) && selectedId != "") {
                vaccinesContainer.innerHTML += ` <div class="vaccine d-flex justify-content-between bg-white align-items-center p-1 w-25 rounded" data-bs-id=${selectedId}>
                    <p class="mb-0">${selectedText}</p>
                    <div class="delete-icon d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                        </svg>
                    </div>
                </div>`;
                // push the id to the selectedVaccines array
                selectedVaccines.push(selectedId);
                selectedVaccinesCon.value = selectedVaccines.toString();
                selectedVaccinesCon.value = selectedVaccinesCon.value.trim();
                console.log(selectedVaccinesCon.value);
            }
        });
    }

    addInteraction(addVaccineBtn);

    // SUBMIT THE FORM FOR VACCINATION

    const vaccinationSubmitBtn = document.getElementById(
        "vaccination-submit-btn"
    );

    vaccinationSubmitBtn.addEventListener("click", async (e) => {
        e.preventDefault();
        const form = document.getElementById("add-patient-form");
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

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

        const data = response.json();
        if (response.ok) {
            Swal.fire({
                title: "Add",
                text: "Vaccination Patient Information is successfully Added",
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        } else {
            Swal.fire({
                title: "Update",
                text: "Adding Vaccination Patient Information failed",
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    });
});
