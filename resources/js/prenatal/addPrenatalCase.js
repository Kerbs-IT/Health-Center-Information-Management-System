const addPrenatalCase =
    document.getElementById("add_case_record_add_btn") ?? null;

if (addPrenatalCase) {
    addPrenatalCase.addEventListener("click", async (e) => {
        const patientInfo = JSON.parse(addPrenatalCase.dataset.patientInfo);

        // console.log(patientInfo);
        const form = document.getElementById("add-prenatal-case-record-form");
        form.reset();

        // add value to the hidden inputs
        const medicalRecordElement =
            document.getElementById(
                "add_prenatal_case_medical_record_case_id"
            ) ?? null;

        const healthWorkerId =
            document.getElementById("add_prenatal_case_health_worker_id") ??
            null;

        const patientName = document.getElementById(
            "add_prenatal_case_patient_name"
        );
        if (medicalRecordElement && healthWorkerId && patientName) {
            medicalRecordElement.value = patientInfo.id;
            healthWorkerId.value =
                patientInfo.prenatal_medical_record.health_worker_id;
            patientName.value = patientInfo.patient.full_name;
        } else {
            console.error("important element is missing");
        }

        const addBtn = document.getElementById(
            "add-prenatal-case-pregnancy-history-btn"
        );
        const year = document.getElementById("add_pregnancy_year") ?? null;
        const typeOfDelivery =
            document.getElementById("add_type_of_delivery") ?? null;
        const placeOfDelivery =
            document.getElementById("add_place_of_delivery") ?? null;
        const birthAttendant =
            document.getElementById("add_birth_attendant") ?? null;
        const complication =
            document.getElementById("add_complication") ?? null;
        const outcome = document.getElementById("add_outcome") ?? null;

        // errors variables

        const yearError = document.getElementById("add_pregnancy_year_error");
        const typeOfDeliveryError = document.getElementById(
            "add_type_of_delivery_error"
        );
        const placeOfDeliveryError = document.getElementById(
            "add_place_of_delivery_error"
        );
        const birthAttendantError = document.getElementById(
            "add_birth_attendant_error"
        );
        const outcomeError = document.getElementById("add_outcome_error");

        // load the existing timeline first
        const tableBody = document.getElementById("add-records-body");

        let firstclicked = true;
        if (addBtn == null) return;
        addBtn.addEventListener("click", (e) => {
            if (
                year == null ||
                typeOfDelivery == null ||
                placeOfDelivery == null ||
                birthAttendant == null ||
                outcome == null
            )
                return;
            // reset the error firest
            yearError.innerHTML = "";
            typeOfDeliveryError.innerHTML = "";
            placeOfDeliveryError.innerHTML = "";
            birthAttendantError.innerHTML = "";
            outcomeError.innerHTML = "";

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
            // add a condition for year if it is greater than the current year then return
            const currentYear = new Date().getFullYear();
            if (year.value > currentYear || year.value < 1000) {
                yearError.innerHTML = "The year entered is not valid";
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

            // after validating the inputs proceed to inserting the

            tableBody.innerHTML += `
                           <tr class="text-center prenatal-record">
                               <td>${year.value}</td>
                               <input type="hidden"  name="add_preg_year[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${year.value}>
                               <td>${typeOfDelivery.value}</td>
                               <input type="hidden"  name="add_type_of_delivery[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${typeOfDelivery.value}>
                               <td>${placeOfDelivery.value}</td>
                               <input type="hidden"  name="add_place_of_delivery[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${placeOfDelivery.value}>
                               <td>${birthAttendant.value}</td>
                               <input type="hidden"  name="add_birth_attendant[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${birthAttendant.value}>
                               <td>${complication.value}</td>
                               <input type="hidden"  name="add_compilation[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${complication.value}>
                               <td>${outcome.value}</td>
                               <input type="hidden"  name="add_outcome[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${outcome.value}>
                               <td>
                                   <button type=button class="btn btn-danger btn-sm timeline-remove">Remove</button>
                               </td>
                           </tr>`;

            // reset the inputs
            year.value = "";
            typeOfDelivery.value = "";
            placeOfDelivery.value = "";
            birthAttendant.value = "";
            outcome.value = "";
        });

        // remove timeline
        tableBody.addEventListener("click", (e) => {
            if (e.target.closest(".prenatal-record")) {
                if (e.target.closest(".timeline-remove")) {
                    e.target.closest("tr").remove();
                }
            }
        });
    });
}
const addCaseSaveBtn = document.getElementById("add-case-record-save-btn");

addCaseSaveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("add-prenatal-case-record-form");
    const formData = new FormData(form);

    // for (const [key, value] of formData.entries()) {
    //     console.log(key, value);
    // }

    try {
        const response = await fetch(
            `/prenatal/add-prenatal-case-record`,
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

        // get all the error elements
        const errorElements = document.querySelectorAll(".error-text");

        if (response.ok) {
            errorElements.forEach((element) => {
                element.textContent = "";
            });
             if (typeof Livewire !== "undefined") {
                 Livewire.dispatch("prenatalRefreshTable"); // ✅ Update dispatch name if needed
             }
            Swal.fire({
                title: "Add Prenatal Case",
                text: data.message, // this will make the text capitalize each word
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("addPrenatalCaseRecordModal")
                    );
                   if (modal) {
                       modal.hide();
                   }
                    form.reset();
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
                title: "Add Prenatal Case",
                text: capitalizeEachWord(message), // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    } catch (error) {
        console.error("Error adding case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to add record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}