import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    // -------------------- ADD PRENANCY TIMELINE HISTORY ----------------------------------------------------------------------
    const addBtn = document.getElementById("add-pregnancy-history-btn");
    // variables
    const year = document.getElementById("pregnancy_year");
    const typeOfDelivery = document.getElementById("type_of_delivery");
    const placeOfDelivery = document.getElementById("place_of_delivery");
    const birthAttendant = document.getElementById("birth_attendant");
    const complication = document.getElementById("complication");
    const outcome = document.getElementById("pregnancyOutcome");

    // errors variables

    const yearError = document.getElementById("preg_year_error");
    const typeOfDeliveryError = document.getElementById(
        "type_of_delivery_error"
    );
    const placeOfDeliveryError = document.getElementById(
        "place_of_delivery_error"
    );
    const birthAttendantError = document.getElementById(
        "birth_attendant_error"
    );
    const outcomeError = document.getElementById("outcome_error");

    // record container
    const pregnancyTimelineContainer = document.getElementById(
        "previous-records-body"
    );
    let firstclicked = true;

    addBtn.addEventListener("click", (e) => {
        e.preventDefault();

        let pregnacyYear = document.querySelectorAll(
            'input[name="preg_year[]"]'
        );

        // --------------- check if there's an information input field that has been empty

        if (
            year.value == "" ||
            typeOfDelivery.value == "" ||
            placeOfDelivery.value == "" ||
            birthAttendant.value == "" ||
            outcome.value == ""
        ) {
            // add the error message
            Swal.fire({
                title: "Pregnancy Timeline Error",
                text: "Information provided is incomplete or invalid.", // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });

            yearError.innerHTML = year.value ? "" : "Year input is empty";
            typeOfDeliveryError.innerHTML = typeOfDelivery.value
                ? ""
                : "Type of Delivery input is empty";
            placeOfDeliveryError.innerHTML = placeOfDelivery.value
                ? ""
                : "Place of Delivery input is empty";
            birthAttendantError.innerHTML = birthAttendant.value
                ? ""
                : "Birth Attendant input is empty";
            outcomeError.innerHTML = outcome.value
                ? ""
                : "Outcome input is empty";

            return;
        }
        // if no error then
        yearError.innerHTML = "";
        typeOfDeliveryError.innerHTML = "";
        placeOfDeliveryError.innerHTML = "";
        birthAttendantError.innerHTML = "";
        outcomeError.innerHTML = "";

        // check if the provided year is valid
        if (year.value.toString().length > 4) {
            Swal.fire({
                title: "Pregnancy Timeline Error",
                text: "Information provided is incomplete or invalid.", // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            yearError.innerHTML = "Invalid year input";

            return;
        }
        yearError.innerHTML = "";

        // clear the blank column first
        if (firstclicked) {
            pregnancyTimelineContainer.innerHTML = "";
            firstclicked = false;
        }
        pregnancyTimelineContainer.innerHTML += `
                    <tr class="text-center prenatal-record">
                        <td>${year.value}</td>
                        <input type="hidden"  name="preg_year[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${year.value}>
                        <td>${typeOfDelivery.value}</td>
                        <input type="hidden"  name="type_of_delivery[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${typeOfDelivery.value}>
                        <td>${placeOfDelivery.value}</td>
                        <input type="hidden"  name="place_of_delivery[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${placeOfDelivery.value}>
                        <td>${birthAttendant.value}</td>
                        <input type="hidden"  name="birth_attendant[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${birthAttendant.value}>
                        <td>${complication.value}</td>
                        <input type="hidden"  name="compilation[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${complication.value}>
                        <td>${outcome.value}</td>
                        <input type="hidden"  name="outcome[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${outcome.value}>
                        <td>
                            <button type=button class="btn btn-danger btn-sm timeline-remove">Remove</button>
                        </td>
                    </tr>`;

        pregnacyYear.forEach((input) => {
            console.log(input.value);
        });

        // reset the inputs
        year.value = "";
        typeOfDelivery.value = "";
        placeOfDelivery.value = "";
        birthAttendant.value = "";
        outcome.value = "";
    });

    // --------------------------------------------- END OF PRENANCY TIMELINE HISTORY -----------------------------------------------

    // --------------------------------------------- ADD BLOOD DONOR SECTION ------------------------------------------------------------

    const addBloodDonorBtn = document.getElementById("donor_name_add_btn");
    const bloodDonorInput = document.getElementById("donor_name_input");
    const bloodDonorContainer = document.querySelector(
        ".blood-donor-name-container"
    );
    console.log(addBloodDonorBtn);
    if (addBloodDonorBtn) {
        // add event listerner to the btn
        addBloodDonorBtn.addEventListener("click", (e) => {
            e.preventDefault();

            // get the name of the blood donor
            const bloodDonorName = bloodDonorInput.value;

            if (bloodDonorName == "") {
                Swal.fire({
                    title: "Donor Name Error",
                    text: "Information provided is incomplete or invalid.", // this will make the text capitalize each word
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                return;
            }

            bloodDonorContainer.innerHTML += `
                <div class="box prenatal-box d-flex justify-content-between bg-white align-items-center p-1 w-25 rounded">
                    <h5 class="mb-0">${bloodDonorName}</h5>
                    <div class="delete-icon d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                        </svg>
                    </div>
                    <input type="text" name="names_of_donor[]" hidden value="${bloodDonorName}" class="donor_name_input">
                </div>`;

            // let donors = document.querySelectorAll(
            //     'input[name="names_of_donor[]"]'
            // );

            // donors.forEach((donor) => {
            //     console.log(donor.value);
            // });
        });
    }

    // handle the remove of the selected donor
    bloodDonorContainer.addEventListener("click", (e) => {
        let donors = document.querySelectorAll(
            'input[name="names_of_donor[]"]'
        );
        if (e.target.closest(".prenatal-box")) {
            if (e.target.closest(".delete-icon")) {
                e.target.closest(".prenatal-box").remove();
            }
        }
        console.log("");
        console.log("updated donor");
        // check if working
        // donors.forEach((donor) => {
        //     console.log(donor.value);
        // });
    });

    pregnancyTimelineContainer.addEventListener("click", (e) => {
        if (e.target.closest(".prenatal-record")) {
            if (e.target.closest(".timeline-remove")) {
                e.target.closest("tr").remove();
            }
        }
    });

    // handle the patient full name info

    const firstNext = document.getElementById("first_next");
    firstNext.addEventListener("click", (e) => {
        const fname = document.getElementById("first_name");
        const middle_initial = document.getElementById("middle_initial");
        const lname = document.getElementById("last_name");
        const fullNameCon = document.getElementById(
            "prenatal_patient_full_name"
        );

        // it combines the value, removing the empty such as the middle initial
        fullNameCon.value = [fname.value, middle_initial.value, lname.value]
            .filter(Boolean) // removes empty strings
            .join(" ");

        console.log(fullNameCon);
    });
});

// upload the data on the database

const prenatalAddBtn = document.getElementById("prenatal-save-btn");

prenatalAddBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const form = document.getElementById("add-patient-form");
    const formData = new FormData(form);

    // call the route
    const response = await fetch("/add-prenatal-patient", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });
    const data = await response.json();

    // get all the error elements
    const errorElements = document.querySelectorAll(".error-text");
    if (response.ok) {
         errorElements.forEach((element) => {
             element.textContent = "";
         });
        Swal.fire({
            title: "Prenatal Patient",
            text: "Patient is Successfully added.", // this will make the text capitalize each word
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
        // reset first

         errorElements.forEach((element) => {
             element.textContent = "";
         });
        
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
            title: "Prenatal Patient",
            text: capitalizeEachWord(message), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

