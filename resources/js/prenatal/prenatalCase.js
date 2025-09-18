import Swal from "sweetalert2";
// load the existing info

const viewBtn = document.getElementById("viewCaseBtn");

const medicalId = viewBtn.dataset.bsMedicalId;

viewBtn.addEventListener("click", async (e) => {
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
    gravida.innerHTML = data.caseInfo.G ? data.caseInfo.G : "0";
    para.innerHTML = data.caseInfo.P ? data.caseInfo.P : "0";
    term.innerHTML = data.caseInfo.T ? data.caseInfo.T : "0";
    premature.innerHTML = data.caseInfo.premature
        ? data.caseInfo.premature
        : "0";
    abortion.innerHTML = data.caseInfo.abortion ? data.caseInfo.abortion : "0";
    livingChildren.innerHTML = data.caseInfo.living_children
        ? data.caseInfo.living_children
        : "0";

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
    const decision = document.getElementById("decision_value");

    const caseDecision = 
    decision.innerHTML = data.caseInfo.decision;

    console.log("datas:", data);
});

// pregnancy plan view
const pregnancyPlanviewBtn = document.getElementById("pregnancy-plan-view-btn");

// add event listener and load the data to the modal
pregnancyPlanviewBtn.addEventListener("click", async (e) => {
    const pregnancyPlanId = pregnancyPlanviewBtn.dataset.bsId;

    // fetch the pregnancy plan information from the database
    const response = await fetch(
        `/view-prenatal/pregnancy-plan/${pregnancyPlanId}`
    );

    // get the response data
    const data = await response.json();

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
    placeOfPregnancy.innerHTML = data.pregnancyPlan.place_of_birth ?? "N/A";
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

// fetch the same data for viewing the case
const caseEditBtn = document.getElementById("case-edit-icon");

caseEditBtn.addEventListener("click", async (e) => {
    // this is the id of the case record itself
    const medicalId = viewBtn.dataset.bsMedicalId;

    const response = await fetch(
        `/view-case/case-record/prenatal/${medicalId}`
    );

    const data = await response.json();

    // input fields
    const grada = document.getElementById("grada_input");
    const para = document.getElementById("para_input");
    const term = document.getElementById("term_input");
    const premature = document.getElementById("premature_input");
    const abortion = document.getElementById("abortion_input");
    const livingChildren = document.getElementById("living_children_input");

    // load the data

    grada.value = data.caseInfo.G ?? 0;
    para.value = data.caseInfo.P ?? 0;
    term.value = data.caseInfo.T ?? 0;
    premature.value = data.caseInfo.premature ?? 0;
    abortion.value = data.caseInfo.abortion ?? 0;
    livingChildren.value = data.caseInfo.living_children ?? 0;

    // -------------------------- handle the interaction adding and removing pregnancy timeline history ----------------------------------------
    const addBtn = document.getElementById("add-pregnancy-history-btn");
    const year = document.getElementById("pregnancy_year");
    const typeOfDelivery = document.getElementById("type_of_delivery");
    const placeOfDelivery = document.getElementById("place_of_delivery");
    const birthAttendant = document.getElementById("birth_attendant");
    const complication = document.getElementById("complication");
    const outcome = document.getElementById("outcome");

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
    addBtn.addEventListener("click", (e) => {
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

    // deciscion
    const nurse_decision_1 = document.getElementById("nurse_f1_option");
    const nurse_decision_2 = document.getElementById("nurse_f2_option");
    const nurse_decision_3 = document.getElementById("nurse_f3_option");

    nurse_decision_1.checked = data.caseInfo.decision == "1";
    nurse_decision_2.checked = data.caseInfo.decision == "2";
    nurse_decision_3.checked = data.caseInfo.decision == "3";
});

// update the case
const saveRecordBtn = document.getElementById("update-save-btn");

saveRecordBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("update-prenatal-case-record-form");
    const formData = new FormData(form);

    for (const [key, value] of formData.entries()) {
        console.log(key, value);
    }

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

    if (response.ok) {
        Swal.fire({
            title: "Prenatal Patient",
            text: data.message, // this will make the text capitalize each word
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        Swal.fire({
            title: "Prenatal Patient",
            text: "Error occur updating Patient is not Successful.", // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
