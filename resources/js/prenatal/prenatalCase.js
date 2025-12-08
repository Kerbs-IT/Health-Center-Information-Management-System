import Swal from "sweetalert2";
// load the existing info

const viewBtn = document.getElementById("viewCaseBtn");

const medicalId = viewBtn.dataset.bsMedicalId;

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".viewCaseBtn");
    if (!viewBtn) return;
    const medicalId = viewBtn.dataset.bsMedicalId;
    const response = await fetch(
        `/view-case/case-record/prenatal/${medicalId}`
    );

    const data = await response.json();

    // load the value to the modal
    // OB HISTORY
    const gravida = document.getElementById("gravida_value");
    const para = document.getElementById("para_value");
    const term = document.getElementById("term_value");
    const premature = document.getElementById("premature_value");
    const abortion = document.getElementById("abortion_value");
    const livingChildren = document.getElementById("livingChildren_value");

    // load the value
    gravida.innerHTML = data.caseInfo.G ?? "0";
    para.innerHTML = data.caseInfo.P ??"0";
    term.innerHTML = data.caseInfo.T ??"0";
    premature.innerHTML = data.caseInfo.premature
        ??"0";
    abortion.innerHTML = data.caseInfo.abortion ??"0";
    livingChildren.innerHTML = data.caseInfo.living_children
        ??"0";

    // load the pregnancy timeline
    const tableBody = document.getElementById("pregnancy_history_body");
    // reset the table first
    tableBody.innerHTML = "";
    data.caseInfo.pregnancy_timeline_records.forEach((record) => {
        tableBody.innerHTML += ` 
            <tr class="text-center">
                <td>${record.year}</td>
                <td>${record.type_of_delivery}</td>
                <td>${record.place_of_delivery}</td>
                <td>${record.birth_attendant}</td>
                <td>${record.compilation ?? "none"}</td>
                <td>${record.outcome}</td>
            </tr>`;
    });
    if (tableBody.children.length == 0) {
        tableBody.innerHTML += ` 
            <tr class="text-center">
               <td colspan='12'>No available records</td>
            </tr>`;
    }

    // subjective info
    const lmp = document.getElementById("lmp_value");
    const expected_delivery = document.getElementById(
        "expected_delivery_value"
    );
    const menarche = document.getElementById("menarche_value");
    const tt1 = document.getElementById("tt1_value");
    const tt2 = document.getElementById("tt2_value");
    const tt3 = document.getElementById("tt3_value");
    const tt4 = document.getElementById("tt4_value");
    const tt5 = document.getElementById("tt5_value");

    // load the value
    lmp.innerHTML = data.caseInfo.LMP ?? "N/A";
    expected_delivery.innerHTML = data.caseInfo.expected_delivery ?? "N/A";
    menarche.innerHTML = data.caseInfo.menarche ?? "N/A";
    tt1.innerHTML = data.caseInfo.tetanus_toxoid_1 ?? "N/A";
    tt2.innerHTML = data.caseInfo.tetanus_toxoid_2 ?? "N/A";
    tt3.innerHTML = data.caseInfo.tetanus_toxoid_3 ?? "N/A";
    tt4.innerHTML = data.caseInfo.tetanus_toxoid_4 ?? "N/A";
    tt5.innerHTML = data.caseInfo.tetanus_toxoid_5 ?? "N/A";

    // prenatal physical assessment
    const spotting = document.getElementById("spotting_value");
    const edema = document.getElementById("edema_value");
    const severe_headache = document.getElementById("severe_headache_value");
    const blurring_vission = document.getElementById(
        "blurring_of_vission_value"
    );
    const water_discharge = document.getElementById("water_discharge_value");
    const severe_vomitting = document.getElementById("severe_vomiting_value");
    const smoking = document.getElementById("smoking_value");
    const alcohol_drinker = document.getElementById("alcohol_drinker_value");
    const drug_intake = document.getElementById("drug_intake_value");

    // load the value
    spotting.innerHTML =
        data.caseInfo.prenatal_assessment.spotting.charAt(0).toUpperCase() +
            data.caseInfo.prenatal_assessment.spotting.slice(1) ?? "no";
    edema.innerHTML = data.caseInfo.prenatal_assessment.edema ?? "no";
    severe_headache.innerHTML =
        data.caseInfo.prenatal_assessment.severe_headache ?? "no";
    blurring_vission.innerHTML =
        data.caseInfo.prenatal_assessment.blumming_vission ?? "no";
    water_discharge.innerHTML =
        data.caseInfo.prenatal_assessment.water_discharge ?? "no";
    severe_vomitting.innerHTML =
        data.caseInfo.prenatal_assessment.severe_vomitting ?? "no";
    smoking.innerHTML = data.caseInfo.prenatal_assessment.hx_smoking ?? "no";
    alcohol_drinker.innerHTML =
        data.caseInfo.prenatal_assessment.alchohol_drinker ?? "no";
    drug_intake.innerHTML =
        data.caseInfo.prenatal_assessment.drug_intake ?? "no";

    // decision
    // const decision = document.getElementById("decision_value");

    // const caseDecision = (decision.innerHTML = data.caseInfo.decision);

    // console.log("datas:", data);
});

// pregnancy plan view

document.addEventListener("click", async (e) => {
    const pregnancyPlanviewBtn = e.target.closest(".pregnancy-plan-view-btn");

    if (!pregnancyPlanviewBtn) return;

    const pregnancyPlanId = pregnancyPlanviewBtn.dataset.bsId;

    // fetch the pregnancy plan information from the database
    const response = await fetch(
        `/view-prenatal/pregnancy-plan/${pregnancyPlanId}`
    );

    // get the response data
    const data = await response.json();

    console.log(data);

    // get the id of response container
    const midwifeName = document.getElementById("midwife_name_value");
    const placeOfPregnancy = document.getElementById(
        "place_of_pregnancy_value"
    );
    const authorizedByPH = document.getElementById(
        "authorized_by_philhealth_value"
    );
    const costOfPregnancy = document.getElementById("cost_of_pregnancy_value");
    const modeOfPayment = document.getElementById("mode_of_payment_value");
    const transportation = document.getElementById(
        "mode_of_transportation_value"
    );
    const accompanyPerson = document.getElementById(
        "accompany_person_to_hospital_value"
    );
    const accompanyThroughPregnancy = document.getElementById(
        "accompany_person_through_pregnancy_value"
    );
    const care_person = document.getElementById("care_person_value");
    const blood_donor = document.getElementById("blood_donor_value");
    const emergencyPersonName = document.getElementById(
        "emergency_person_name_value"
    );
    const emergencyPersonResidency = document.getElementById(
        "emergency_person_residency_value"
    );
    const emergencyPersonContactNo = document.getElementById(
        "emergency_person_contact_number_value"
    );
    const patientName = document.getElementById("patient_name_value");

    // load the value

    midwifeName.innerHTML = data.pregnancyPlan.midwife_name ?? "N/A";
    placeOfPregnancy.innerHTML = data.pregnancyPlan.place_of_pregnancy ?? "N/A";
    authorizedByPH.innerHTML =
        data.pregnancyPlan.authorized_by_philhealth ?? "N/A";
    costOfPregnancy.innerHTML = data.pregnancyPlan.cost_of_pregnancy ?? "N/A";
    modeOfPayment.innerHTML = data.pregnancyPlan.payment_method ?? "N/A";
    transportation.innerHTML = data.pregnancyPlan.transportation_mode ?? "N/A";
    accompanyPerson.innerHTML =
        data.pregnancyPlan.accompany_person_to_hospital ?? "N/A";
    accompanyThroughPregnancy.innerHTML =
        data.pregnancyPlan.accompany_through_pregnancy ?? "N/A";
    care_person.innerHTML = data.pregnancyPlan.care_person ?? "N/A";
    blood_donor.innerHTML = data.pregnancyPlan.donor_name
        .map((person) => person.donor_name)
        .join(", ");
    emergencyPersonName.innerHTML =
        data.pregnancyPlan.emergency_person_name ?? "N/A";
    emergencyPersonResidency.innerHTML =
        data.pregnancyPlan.emergency_person_residency ?? "N/A";
    emergencyPersonContactNo.innerHTML =
        data.pregnancyPlan.emergency_person_contact_number ?? "N/A";
    patientName.innerHTML = data.pregnancyPlan.patient_name ?? "N/A";
});
// add event listener and load the data to the modal

// fetch the same data for viewing the case
document.addEventListener("click", async (e) => {
    const caseEditBtn = e.target.closest(".case-edit-icon");
    if (!caseEditBtn) return;
    console.log("working tong case");
    // this is the id of the case record itself
    const medicalId = caseEditBtn.dataset.bsMedicalId;

    const response = await fetch(
        `/view-case/case-record/prenatal/${medicalId}`,
        {
            method: "GET",
            headers: {
                Accept: "application/json",
            },
        }
    );

    const data = await response.json();

    // input fields
    const grada = document.getElementById("grada_input") ?? null;
    const para = document.getElementById("para_input") ?? null;
    const term = document.getElementById("term_input") ?? null;
    const premature = document.getElementById("premature_input") ?? null;
    const abortion = document.getElementById("abortion_input") ?? null;
    const livingChildren =
        document.getElementById("living_children_input") ?? null;

    // load the data
    if (
        grada == null ||
        para == null ||
        term == null ||
        premature == null ||
        abortion == null ||
        abortion == null ||
        livingChildren == null
    )
        return;
    grada.value = data.caseInfo.G ?? 0;
    para.value = data.caseInfo.P ?? 0;
    term.value = data.caseInfo.T ?? 0;
    premature.value = data.caseInfo.premature ?? 0;
    abortion.value = data.caseInfo.abortion ?? 0;
    livingChildren.value = data.caseInfo.living_children ?? 0;

    // -------------------------- handle the interaction adding and removing pregnancy timeline history ----------------------------------------
    const addBtn = document.getElementById("add-pregnancy-history-btn");
    const year = document.getElementById("pregnancy_year") ?? null;
    const typeOfDelivery = document.getElementById("type_of_delivery") ?? null;
    const placeOfDelivery =
        document.getElementById("place_of_delivery") ?? null;
    const birthAttendant = document.getElementById("birth_attendant") ?? null;
    const complication = document.getElementById("complication") ?? null;
    const outcome = document.getElementById("outcome") ?? null;

    // errors variables

    const yearError = document.getElementById("pregnancy_year_error");
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

    // load the existing timeline first
    const tableBody = document.getElementById("edit-previous-records-body");

    // use foreach to load the data
    const pregnancyTimeline = data.caseInfo.pregnancy_timeline_records.sort(
        (a, b) => a.year - b.year
    );
    // reset first everytime edit icon is clicked
    tableBody.innerHTML = "";
    pregnancyTimeline.forEach((timeline) => {
        tableBody.innerHTML += `
                        <tr class="text-center prenatal-record">
                            <td>${timeline.year}</td>
                            <input type="hidden"  name="preg_year[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${
                                timeline.year
                            }>
                            <td>${timeline.type_of_delivery}</td>
                            <input type="hidden"  name="type_of_delivery[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${
                                timeline.type_of_delivery
                            }>
                            <td>${timeline.place_of_delivery}</td>
                            <input type="hidden"  name="place_of_delivery[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${
                                timeline.place_of_delivery
                            }>
                            <td>${timeline.birth_attendant}</td>
                            <input type="hidden"  name="birth_attendant[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${
                                timeline.birth_attendant
                            }>
                            <td>${timeline.compilation}</td>
                            <input type="hidden"  name="compilation[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${
                                timeline.compilation ?? "none"
                            }>
                            <td>${timeline.outcome}</td>
                            <input type="hidden"  name="outcome[]" min="1900" max="2099" placeholder="YYYY" class="form-control w-100" required value= ${
                                timeline.outcome
                            }>
                            <td>
                                <button type=button class="btn btn-danger btn-sm timeline-remove">Remove</button>
                            </td>
                        </tr>`;
    });

    // ------------------------ UPDATE ADD BTN -----------------------------------
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

    // input fields of subjective section
    const lmp = document.getElementById("LMP_input");
    const expected_delivery = document.getElementById(
        "expected_delivery_input"
    );
    const menarche = document.getElementById("menarche_input");
    const tt1 = document.getElementById("tt1_input");
    const tt2 = document.getElementById("tt2_input");
    const tt3 = document.getElementById("tt3_input");
    const tt4 = document.getElementById("tt4_input");
    const tt5 = document.getElementById("tt5_input");

    // load the value
    lmp.value = data.caseInfo.LMP ?? "";
    expected_delivery.value = data.caseInfo.expected_delivery ?? "";
    menarche.value = data.caseInfo.menarche ?? "";
    tt1.value = data.caseInfo.tetanus_toxoid_1 ?? "";
    tt2.value = data.caseInfo.tetanus_toxoid_2 ?? "";
    tt3.value = data.caseInfo.tetanus_toxoid_3 ?? "";
    tt4.value = data.caseInfo.tetanus_toxoid_4 ?? "";
    tt5.value = data.caseInfo.tetanus_toxoid_5 ?? "";

    // assessment input field
    const spotting = document.getElementById("spotting_input");
    const edema = document.getElementById("edema_input");
    const severe_headache = document.getElementById("severe_headache_input");
    const blurring_of_vission = document.getElementById(
        "blurring_of_vission_input"
    );
    const watery_discharge = document.getElementById("watery_discharge_input");
    const severe_vomitting = document.getElementById("severe_vomiting_input");
    const hx_smoking = document.getElementById("hx_smoking_input");
    const alcohol_drinker = document.getElementById("alcohol_drinker_input");
    const drug_intake = document.getElementById("drug_intake_input");

    // load the data

    spotting.checked = data.caseInfo.prenatal_assessment.spotting == "yes";
    edema.checked = data.caseInfo.prenatal_assessment.edema == "yes";
    severe_headache.checked =
        data.caseInfo.prenatal_assessment.severe_headache == "yes";
    blurring_of_vission.checked =
        data.caseInfo.prenatal_assessment.blumming_vission == "yes";
    watery_discharge.checked =
        data.caseInfo.prenatal_assessment.water_discharge == "yes";
    severe_vomitting.checked =
        data.caseInfo.prenatal_assessment.severe_vomitting == "yes";
    hx_smoking.checked = data.caseInfo.prenatal_assessment.hx_smoking == "yes";
    alcohol_drinker.checked =
        data.caseInfo.prenatal_assessment.alchohol_drinker == "yes";
    drug_intake.checked =
        data.caseInfo.prenatal_assessment.drug_intake == "yes";
});

// update the case
const saveRecordBtn = document.getElementById("update-save-btn") ?? null;

if (saveRecordBtn) {
    saveRecordBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const form = document.getElementById(
            "update-prenatal-case-record-form"
        );
        const formData = new FormData(form);

        // for (const [key, value] of formData.entries()) {
        //     console.log(key, value);
        // }

        const response = await fetch(
            `/patient-record/update/prenatal-case/${medicalId}`,
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
            Swal.fire({
                title: "Prenatal case Update",
                text: data.message, // this will make the text capitalize each word
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("editPrenatalCaseModal")
                    );
                    modal.hide();
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
                title: "Prenatal case Update",
                text: capitalizeEachWord(message), // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    });
}
// edit section of pregnancy plan -- viewing pregnancy plan value

// this is the btn in modal to save the new information
const updateBTN = document.getElementById("pregnancy_plan_update_btn");

document.addEventListener("click", async (e) => {
    const pregnancyPlanEditBTN = e.target.closest(".pregnancy_plan_edit_btn");
    if (!pregnancyPlanEditBTN) return;
    const pregnancyPlanId = pregnancyPlanEditBTN.dataset.bsId;
    // let set the custom variable for the save btn since i place it outside this event listener to avoid redundancy and overlapping
    updateBTN.dataset.pregnancyPlanId = pregnancyPlanId;

    // fetch the pregnancy plan information from the database
    const response = await fetch(
        `/view-prenatal/pregnancy-plan/${pregnancyPlanId}`
    );
    const data = await response.json();
    console.log(data);
    // get the inputs container
    const midwife_name = document.getElementById("midwife_name");
    const place_of_birth = document.getElementById("place_of_birth");
    const authorized_by_philhealth_yes = document.getElementById(
        "authorized_by_philhealth_yes"
    );
    const authorized_by_philhealth_no = document.getElementById(
        "authorized_by_philhealth_no"
    );
    const cost_of_pregnancy = document.getElementById("cost_of_pregnancy");
    const payment_method = document.getElementById("payment_method");
    const transportation_mode = document.getElementById("transportation_mode");
    const accompany_person_to_hospital = document.getElementById(
        "accompany_person_to_hospital"
    );
    const accompany_through_pregnancy = document.getElementById(
        "accompany_through_pregnancy"
    );
    const care_person = document.getElementById("care_person");
    const emergency_person_name = document.getElementById(
        "emergency_person_name"
    );
    const emergency_person_residency = document.getElementById(
        "emergency_person_residency"
    );
    const emergency_person_contact_number = document.getElementById(
        "emergency_person_contact_number"
    );
    const patient_name = document.getElementById("patient_name");
    // lets reset the container of donor names so it only show the data from the database removing the redundancy
    donor_names_con.innerHTML = "";

    Object.entries(data.pregnancyPlan).forEach(([key, value]) => {
        if (key == "authorized_by_philhealth") {
            if (value == "yes") {
                console.log("yes");
                document.getElementById(
                    "authorized_by_philhealth_yes"
                ).checked = true;
            } else if (value == "no") {
                document.getElementById(
                    "authorized_by_philhealth_no"
                ).checked = true;
            }
        }
        if (document.getElementById(`${key}`)) {
            document.getElementById(`${key}`).value = value;
        }
        // loop through the donor names
        if (key == "donor_name") {
            data.pregnancyPlan.donor_name.forEach((name) => {
                donor_names_con.innerHTML += `
                 <div class="box vaccine d-flex justify-content-between bg-white align-items-center p-1 w-50 rounded">
                                                                <h5 class="mb-0">${name.donor_name}</h5>
                                                                <div class="delete-icon d-flex align-items-center justify-content-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                                                                        <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                                                                    </svg>
                                                                </div>
                                                                <input type="hidden" name="donor_names[]" value="${name.donor_name}" class="donor_name_input">
                                                            </div>`;
            });
        }
    });

    // handle the remove of the selected donor
    donor_names_con.addEventListener("click", (e) => {
        let donors = document.querySelectorAll('input[name="donor_names[]"]');
        if (e.target.closest(".box")) {
            if (e.target.closest(".delete-icon-svg")) {
                e.target.closest(".box").remove();
            }
        }
        console.log("donor deleted");
    });
});

const donor_names_con = document.getElementById("donor_names_con");
const donor_name_input = document.getElementById("name_of_donor");
// add btn
const addBtn = document.getElementById("donor_name_add_btn");
// event listener for adding the name
addBtn.addEventListener("click", (e) => {
    if (donor_name_input.value !== "") {
        donor_names_con.innerHTML += `
            <div class="box vaccine d-flex justify-content-between bg-white align-items-center p-1 w-50 rounded">
                <h5 class="mb-0">${donor_name_input.value}</h5>
                <div class="delete-icon d-flex align-items-center justify-content-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                        <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                    </svg>
                </div>
                <input type="hidden" name="donor_names[]" value="${donor_name_input.value}" class="donor_name_input">
            </div>
            `;
        // reset the input field
        donor_name_input.value = "";
    } else {
        Swal.fire({
            title: "Adding Blood Donor Name",
            text: "Please provide valid name.", // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

// -------------- update the pregnancy plan record only trigger when the save button is click
// there add a condition identify if the btn is present

if (updateBTN) {
    updateBTN.addEventListener("click", async (e) => {
        e.preventDefault();
        const pregnancyPlanId = updateBTN.dataset.pregnancyPlanId;
        console.log(pregnancyPlanId);
        const form = document.getElementById("pregnancy_plan_update_form");
        const formData = new FormData(form);

        for (const [key, value] of formData.entries()) {
            console.log(key, value);
        }

        const response = await fetch(
            `/update/pregnancy-plan-record/${pregnancyPlanId}`,
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
            Swal.fire({
                title: "Prenatal Patient",
                text: data.message, // this will make the text capitalize each word
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("case2PrenatalModal")
                    );
                    modal.hide();
                    form.reset();
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
                message = "An unexpected error occurred.";
            }
            Swal.fire({
                title: "Prenatal Patient",
                text: capitalizeEachWord(message),
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    });
}

// add prenatal checkup record
const prentalCheckUpBTN = document.getElementById("prenatal_check_up_add_btn");
const uploadBTN = document.getElementById("check-up-save-btn");

prentalCheckUpBTN.addEventListener("click", async (e) => {
    const medicalId = e.target.dataset.bsMedicalRecordId;
    uploadBTN.dataset.bsMedicalRecordId = medicalId;
    console.log("medical id: ", medicalId);
    const response = await fetch(`/patient-record/view-details/${medicalId}`);
    // get the data
    const data = await response.json();

    // fields
    const patient_name = document.getElementById("check_up_patient_name");
    const handled_by = document.getElementById("check_up_handled_by");
    const healthworkerId = document.getElementById("health_worker_id");
    const hiddenPatientName = document.getElementById(
        "hidden_check_up_patient_name"
    );

    // provide the info
    patient_name.value = data.prenatalRecord.patient.full_name ?? "";
    handled_by.value = data.healthWorker.full_name ?? "";
    healthworkerId.value = data.healthWorker.user_id;
    hiddenPatientName.value = data.prenatalRecord.patient.full_name;
});

// upload the information to the database
// check save btn

uploadBTN.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("check-up-form");
    const formData = new FormData(form);
    const medicalId = e.target.dataset.bsMedicalRecordId;
    const response = await fetch(`/prenatal/add-check-up-record/${medicalId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

    const data = response.json();

    const errorElements = document.querySelectorAll(".error-text");
    if (!response.ok) {
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
            message = "An unexpected error occurred.";
        }

        Swal.fire({
            title: "Prenatal Patient",
            text: capitalizeEachWord(message),
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        errorElements.forEach((element) => {
            element.textContent = "";
        });

        // THIS IS THE BEST SOLUTION FOR UPDATING THE RECORD
        Livewire.dispatch("prenatalRefreshTable");

        Swal.fire({
            title: "Prenatal check-Up Info",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("prenatalCheckupModal")
                );
                modal.hide();
                form.reset();
            }
        });
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}

// ===== DELETE PATIENT CASE RECORD
document.addEventListener("click", async (e) => {
    const deleteBtn = e.target.closest(".case-archive-record-icon");

    if (!deleteBtn) return;
    const id = deleteBtn.dataset.caseId;

    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        // ✅ Show confirmation dialog FIRST
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "Prenatal Case Record will be Deleted.",
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
            `/patient-record/prenatal/case-record/${id}`,
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
            Livewire.dispatch("prenatalRefreshTable"); // ✅ Update dispatch name if needed
        }

        // Remove the row from DOM
        const row = deleteBtn.closest("tr");
        if (row) {
            row.remove();
        }

        // Show success message
        Swal.fire({
            title: "Archived!",
            text: "Prenatal Case Record has been archived.",
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

document.addEventListener("click", async (e) => {
    const deleteBtn = e.target.closest(".pregnancy-plan-archive-record-icon");

    if (!deleteBtn) return;
    const id = deleteBtn.dataset.caseId;

    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        // ✅ Show confirmation dialog FIRST
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "Pregnancy Plan Record will be Deleted.",
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
            `/patient-record/prenatal/pregnancy-plan/${id}`,
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
            Livewire.dispatch("prenatalRefreshTable"); // ✅ Update dispatch name if needed
        }

        // Remove the row from DOM
        const row = deleteBtn.closest("tr");
        if (row) {
            row.remove();
        }

        // Show success message
        Swal.fire({
            title: "Archived!",
            text: "Pregnancy Plan Record has been archived.",
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

// event delegation for 
