import Swal from "sweetalert2";
import changeLmp from "../LMP/lmp";
import initSignatureCapture from "../signature/signature";
import { vitalSignInputMask } from "../vitalSign";
// load the existing info

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".viewCaseBtn");
    if (!viewBtn) return;
    const medicalId = viewBtn.dataset.bsMedicalId;
    const response = await fetch(
        `/view-case/case-record/prenatal/${medicalId}`,
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
     const blood_pressure = document.getElementById("blood_pressure_value");
     const weight = document.getElementById("weight_value");
     const height = document.getElementById("height_value");
     const temperature = document.getElementById("temperature_value");
     const respiratory_rate = document.getElementById("respiratory_rate_value");
     const pulse_rate = document.getElementById("pulse_rate_value");
     const planning = document.getElementById("planning_value");

    // load the value
    gravida.innerHTML = data.caseInfo.G ?? "0";
    para.innerHTML = data.caseInfo.P ?? "0";
    term.innerHTML = data.caseInfo.T ?? "0";
    premature.innerHTML = data.caseInfo.premature ?? "0";
    abortion.innerHTML = data.caseInfo.abortion ?? "0";
    livingChildren.innerHTML = data.caseInfo.living_children ?? "0";
    // load the vital
    if (blood_pressure) {
        blood_pressure.innerHTML = data.caseInfo.blood_pressure ?? 0;
    }
    if (height) {
      height.innerHTML = data.caseInfo.height ? `${data.caseInfo.height} cm` : '0 cm';  
    }
    if (weight) {
        weight.innerHTML = data.caseInfo.weight
            ? `${data.caseInfo.weight} cm`
            : "0 cm";
    }
    
    if (temperature) {
        temperature.innerHTML = data.caseInfo.temperature
            ? `${data.caseInfo.temperature} °C`
            : "0 °C";
    }
    
    if (respiratory_rate) {
        respiratory_rate.innerHTML = data.caseInfo.respiratory_rate
        ? `${data.caseInfo.respiratory_rate}`
        : "0";
    }
    
    if (pulse_rate) {
        pulse_rate.innerHTML = data.caseInfo.pulse_rate
            ? `${data.caseInfo.pulse_rate}`
            : "0";
    }
    
    if (planning) planning.innerHTML = data.caseInfo.planning ?? 'N/A';

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
        "expected_delivery_value",
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
        "blurring_of_vission_value",
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

    // signature path
});

// pregnancy plan view

document.addEventListener("click", async (e) => {
    const pregnancyPlanviewBtn = e.target.closest(".pregnancy-plan-view-btn");

    if (!pregnancyPlanviewBtn) return;

    const pregnancyPlanId = pregnancyPlanviewBtn.dataset.bsId;

    // fetch the pregnancy plan information from the database
    const response = await fetch(
        `/view-prenatal/pregnancy-plan/${pregnancyPlanId}`,
    );

    // get the response data
    const data = await response.json();

    // console.log(data);

    // get the id of response container
    const midwifeName = document.getElementById("midwife_name_value");
    const placeOfPregnancy = document.getElementById(
        "place_of_pregnancy_value",
    );
    const authorizedByPH = document.getElementById(
        "authorized_by_philhealth_value",
    );
    const costOfPregnancy = document.getElementById("cost_of_pregnancy_value");
    const modeOfPayment = document.getElementById("mode_of_payment_value");
    const transportation = document.getElementById(
        "mode_of_transportation_value",
    );
    const accompanyPerson = document.getElementById(
        "accompany_person_to_hospital_value",
    );
    const accompanyThroughPregnancy = document.getElementById(
        "accompany_person_through_pregnancy_value",
    );
    const care_person = document.getElementById("care_person_value");
    const blood_donor = document.getElementById("blood_donor_value");
    const emergencyPersonName = document.getElementById(
        "emergency_person_name_value",
    );
    const emergencyPersonResidency = document.getElementById(
        "emergency_person_residency_value",
    );
    const emergencyPersonContactNo = document.getElementById(
        "emergency_person_contact_number_value",
    );
    const patientName = document.getElementById("patient_name_value");
    const signatureImg = document.getElementById("signature_value");
    const noSignatureText = document.getElementById("no_signature");

   
   

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

    // load the vital sign
    

    const signaturePath = data.pregnancyPlan.signature
        ? `/storage/${data.pregnancyPlan.signature}`
        : null;
    if (signaturePath) {
        signatureImg.src = signaturePath;
        signatureImg.style.display = "block";
        noSignatureText.style.display = "none";
    }
});
// add event listener and load the data to the modal

const saveRecordBtn = document.getElementById("update-save-btn") ?? null;

// Helper function to get element safely
const getElement = (id) => document.getElementById(id) ?? null;

// Helper function to create timeline row HTML
const createTimelineRow = (timeline) => `
    <tr class="text-center prenatal-record">
        <td>${timeline.year}</td>
        <input type="hidden" name="preg_year[]" value="${timeline.year}">
        <td>${timeline.type_of_delivery}</td>
        <input type="hidden" name="type_of_delivery[]" value="${timeline.type_of_delivery}">
        <td>${timeline.place_of_delivery}</td>
        <input type="hidden" name="place_of_delivery[]" value="${timeline.place_of_delivery}">
        <td>${timeline.birth_attendant}</td>
        <input type="hidden" name="birth_attendant[]" value="${timeline.birth_attendant}">
        <td>${timeline.complication ?? "none"}</td>
        <input type="hidden" name="compilation[]" value="${timeline.complication ?? "none"}">
        <td>${timeline.outcome}</td>
        <input type="hidden" name="outcome[]" value="${timeline.outcome}">
        <td>
            <button type="button" class="btn btn-danger btn-sm timeline-remove">Remove</button>
        </td>
    </tr>
`;

// Validation function for pregnancy timeline inputs
const validateTimelineInputs = (
    year,
    typeOfDelivery,
    placeOfDelivery,
    birthAttendant,
    outcome,
) => {
    const errors = {};
    const currentYear = new Date().getFullYear();

    if (!year.value) errors.year = "Year input is empty";
    else if (year.value > currentYear || year.value < 1000)
        errors.year = "The year entered is not valid";
    else if (year.value.toString().length > 4)
        errors.year = "Invalid year input";

    if (!typeOfDelivery.value)
        errors.typeOfDelivery = "Type of Delivery input is empty";
    if (!placeOfDelivery.value)
        errors.placeOfDelivery = "Place of Delivery input is empty";
    if (!birthAttendant.value)
        errors.birthAttendant = "Birth Attendant input is empty";
    if (!outcome.value) errors.outcome = "Outcome input is empty";

    return errors;
};

// Display validation errors
const displayErrors = (errors, errorElements) => {
    errorElements.year.innerHTML = errors.year || "";
    errorElements.typeOfDelivery.innerHTML = errors.typeOfDelivery || "";
    errorElements.placeOfDelivery.innerHTML = errors.placeOfDelivery || "";
    errorElements.birthAttendant.innerHTML = errors.birthAttendant || "";
    errorElements.outcome.innerHTML = errors.outcome || "";

    if (Object.keys(errors).length > 0) {
        Swal.fire({
            title: "Pregnancy Timeline Error",
            text: "Information provided is incomplete or invalid.",
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
        return true;
    }
    return false;
};

// Reset timeline input fields
const resetTimelineInputs = (inputs) => {
    inputs.year.value = "";
    inputs.typeOfDelivery.value = "";
    inputs.placeOfDelivery.value = "";
    inputs.birthAttendant.value = "";
    inputs.complication.value = "";
    inputs.outcome.value = "";
};

// Main event handler for case editing
document.addEventListener("click", async (e) => {
    const caseEditBtn = e.target.closest(".case-edit-icon");
    if (!caseEditBtn) return;

    const medicalId = caseEditBtn.dataset.bsMedicalId;
    saveRecordBtn.dataset.medicalId = medicalId ?? null;

    // Fetch case data
    const response = await fetch(
        `/view-case/case-record/prenatal/${medicalId}`,
        {
            method: "GET",
            headers: { Accept: "application/json" },
        },
    );

    const data = await response.json();

    // Load basic case information
    const basicFields = {
        grada: getElement("grada_input"),
        para: getElement("para_input"),
        term: getElement("term_input"),
        premature: getElement("premature_input"),
        abortion: getElement("abortion_input"),
        livingChildren: getElement("living_children_input"),
    };

    // Validate all basic fields exist
    if (Object.values(basicFields).some((field) => field === null)) return;

    // Load basic field values
    basicFields.grada.value = data.caseInfo.G ?? 0;
    basicFields.para.value = data.caseInfo.P ?? 0;
    basicFields.term.value = data.caseInfo.T ?? 0;
    basicFields.premature.value = data.caseInfo.premature ?? 0;
    basicFields.abortion.value = data.caseInfo.abortion ?? 0;
    basicFields.livingChildren.value = data.caseInfo.living_children ?? 0;

    // Pregnancy timeline elements
    const timelineInputs = {
        year: getElement("pregnancy_year"),
        typeOfDelivery: getElement("type_of_delivery"),
        placeOfDelivery: getElement("place_of_delivery"),
        birthAttendant: getElement("birth_attendant"),
        complication: getElement("complication"),
        outcome: getElement("outcome"),
    };

    const errorElements = {
        year: getElement("pregnancy_year_error"),
        typeOfDelivery: getElement("type_of_delivery_error"),
        placeOfDelivery: getElement("place_of_delivery_error"),
        birthAttendant: getElement("birth_attendant_error"),
        outcome: getElement("outcome_error"),
    };

    const addBtn = getElement("add-pregnancy-history-btn");
    const tableBody = getElement("edit-previous-records-body");

    // Load existing pregnancy timeline
    const pregnancyTimeline = data.caseInfo.pregnancy_timeline_records.sort(
        (a, b) => a.year - b.year,
    );
    tableBody.innerHTML = pregnancyTimeline.map(createTimelineRow).join("");

    // Remove old event listeners by cloning the button
    if (addBtn) {
        const newAddBtn = addBtn.cloneNode(true);
        addBtn.parentNode.replaceChild(newAddBtn, addBtn);

        // Add new event listener to the cloned button
        newAddBtn.addEventListener("click", () => {
            // Validate all inputs exist
            if (Object.values(timelineInputs).some((input) => input === null))
                return;

            // Validate inputs
            const errors = validateTimelineInputs(
                timelineInputs.year,
                timelineInputs.typeOfDelivery,
                timelineInputs.placeOfDelivery,
                timelineInputs.birthAttendant,
                timelineInputs.outcome,
            );

            // Display errors if any
            if (displayErrors(errors, errorElements)) return;

            // Add new timeline record
            const newTimeline = {
                year: timelineInputs.year.value,
                type_of_delivery: timelineInputs.typeOfDelivery.value,
                place_of_delivery: timelineInputs.placeOfDelivery.value,
                birth_attendant: timelineInputs.birthAttendant.value,
                complication: timelineInputs.complication.value,
                outcome: timelineInputs.outcome.value,
            };

            tableBody.innerHTML += createTimelineRow(newTimeline);

            // Reset inputs
            resetTimelineInputs(timelineInputs);
        });
    }

    // Handle timeline row removal
    tableBody.addEventListener("click", (e) => {
        const removeBtn = e.target.closest(".timeline-remove");
        if (removeBtn) {
            e.target.closest("tr").remove();
        }
    });

    // Load subjective section data
    const subjectiveFields = {
        lmp: getElement("LMP_input"),
        expected_delivery: getElement("expected_delivery_input"),
        menarche: getElement("menarche_input"),
        tt1: getElement("tt1_input"),
        tt2: getElement("tt2_input"),
        tt3: getElement("tt3_input"),
        tt4: getElement("tt4_input"),
        tt5: getElement("tt5_input"),
    };

    if (subjectiveFields.lmp)
        subjectiveFields.lmp.value = data.caseInfo.LMP ?? "";
    if (subjectiveFields.expected_delivery)
        subjectiveFields.expected_delivery.value =
            data.caseInfo.expected_delivery ?? "";
    if (subjectiveFields.menarche)
        subjectiveFields.menarche.value = data.caseInfo.menarche ?? "";
    if (subjectiveFields.tt1)
        subjectiveFields.tt1.value = data.caseInfo.tetanus_toxoid_1 ?? "";
    if (subjectiveFields.tt2)
        subjectiveFields.tt2.value = data.caseInfo.tetanus_toxoid_2 ?? "";
    if (subjectiveFields.tt3)
        subjectiveFields.tt3.value = data.caseInfo.tetanus_toxoid_3 ?? "";
    if (subjectiveFields.tt4)
        subjectiveFields.tt4.value = data.caseInfo.tetanus_toxoid_4 ?? "";
    if (subjectiveFields.tt5)
        subjectiveFields.tt5.value = data.caseInfo.tetanus_toxoid_5 ?? "";

    const vitalSign = {
        blood_pressure: getElement("edit_case_blood_pressure"),
        weight: getElement("edit_case_weight"),
        height: getElement("edit_case_height"),
        temperature: getElement("edit_case_temperature"),
        respiratory_rate: getElement("edit_case_respiratory_rate"),
        pulse_rate: getElement("edit_case_pulse_rate"),
    };

    if (
        vitalSign.blood_pressure &&
        vitalSign.temperature &&
        vitalSign.height &&
        vitalSign.weight &&
        vitalSign.respiratory_rate &&
        vitalSign.pulse_rate
    ) {
        vitalSignInputMask(
            vitalSign.blood_pressure,
            vitalSign.temperature,
            vitalSign.pulse_rate,
            vitalSign.respiratory_rate,
            vitalSign.height,
            vitalSign.weight,
        );
    }

    if (vitalSign.blood_pressure) vitalSign.blood_pressure.value = data.caseInfo.blood_pressure ?? null;
    if (vitalSign.height) vitalSign.height.value = data.caseInfo.height ?? null;
    if (vitalSign.weight)
        vitalSign.weight.value = data.caseInfo.weight ?? null;
    if (vitalSign.temperature)
        vitalSign.temperature.value = data.caseInfo.temperature ?? null;
    if (vitalSign.respiratory_rate)
        vitalSign.respiratory_rate.value = data.caseInfo.respiratory_rate ?? null;
    if (vitalSign.pulse_rate)
        vitalSign.pulse_rate.value = data.caseInfo.pulse_rate ?? null;

    const planning = document.getElementById("edit_case_planning");
    if(planning){
        planning.value = data.caseInfo.planning ?? '';
    }


    // Load assessment data
    const assessmentFields = {
        spotting: getElement("spotting_input"),
        edema: getElement("edema_input"),
        severe_headache: getElement("severe_headache_input"),
        blurring_of_vission: getElement("blurring_of_vission_input"),
        watery_discharge: getElement("watery_discharge_input"),
        severe_vomitting: getElement("severe_vomiting_input"),
        hx_smoking: getElement("hx_smoking_input"),
        alcohol_drinker: getElement("alcohol_drinker_input"),
        drug_intake: getElement("drug_intake_input"),
    };

    const assessment = data.caseInfo.prenatal_assessment;
    if (assessmentFields.spotting)
        assessmentFields.spotting.checked = assessment.spotting === "yes";
    if (assessmentFields.edema)
        assessmentFields.edema.checked = assessment.edema === "yes";
    if (assessmentFields.severe_headache)
        assessmentFields.severe_headache.checked =
            assessment.severe_headache === "yes";
    if (assessmentFields.blurring_of_vission)
        assessmentFields.blurring_of_vission.checked =
            assessment.blumming_vission === "yes";
    if (assessmentFields.watery_discharge)
        assessmentFields.watery_discharge.checked =
            assessment.water_discharge === "yes";
    if (assessmentFields.severe_vomitting)
        assessmentFields.severe_vomitting.checked =
            assessment.severe_vomitting === "yes";
    if (assessmentFields.hx_smoking)
        assessmentFields.hx_smoking.checked = assessment.hx_smoking === "yes";
    if (assessmentFields.alcohol_drinker)
        assessmentFields.alcohol_drinker.checked =
            assessment.alchohol_drinker === "yes";
    if (assessmentFields.drug_intake)
        assessmentFields.drug_intake.checked = assessment.drug_intake === "yes";
});
// add a expected delivery change in the LMP
const LMP = document.getElementById("LMP_input") ?? null;

if (LMP) {
    const expectedDelivery = document.getElementById("expected_delivery_input");

    LMP.addEventListener("change", () => {
        changeLmp(LMP, expectedDelivery);
    });
}

// update the case

if (saveRecordBtn) {
    saveRecordBtn.addEventListener("click", async (e) => {
        e.preventDefault();

        const form = document.getElementById(
            "update-prenatal-case-record-form",
        );
        const formData = new FormData(form);

        // for (const [key, value] of formData.entries()) {
        //     console.log(key, value);
        // }

        const medicalId = e.target.dataset.medicalId;
        const response = await fetch(
            `/patient-record/update/prenatal-case/${medicalId}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                },
                body: formData,
            },
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
                        document.getElementById("editPrenatalCaseModal"),
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

    // reset the errors
    const errors = document.querySelectorAll(".error-text");
    errors.forEach((error) => (error.innerHTML = ""));
    // fetch the pregnancy plan information from the database
    const response = await fetch(
        `/view-prenatal/pregnancy-plan/${pregnancyPlanId}`,
    );
    const data = await response.json();
    // console.log(data);
    // get the inputs container
    const midwife_name = document.getElementById("midwife_name");
    const place_of_birth = document.getElementById("place_of_birth");
    const authorized_by_philhealth_yes = document.getElementById(
        "authorized_by_philhealth_yes",
    );
    const authorized_by_philhealth_no = document.getElementById(
        "authorized_by_philhealth_no",
    );
    const cost_of_pregnancy = document.getElementById("cost_of_pregnancy");
    const payment_method = document.getElementById("payment_method");
    const transportation_mode = document.getElementById("transportation_mode");
    const accompany_person_to_hospital = document.getElementById(
        "accompany_person_to_hospital",
    );
    const accompany_through_pregnancy = document.getElementById(
        "accompany_through_pregnancy",
    );
    const care_person = document.getElementById("care_person");
    const emergency_person_name = document.getElementById(
        "emergency_person_name",
    );
    const emergency_person_residency = document.getElementById(
        "emergency_person_residency",
    );
    const emergency_person_contact_number = document.getElementById(
        "emergency_person_contact_number",
    );
    const patient_name = document.getElementById("patient_name");
    // lets reset the container of donor names so it only show the data from the database removing the redundancy
    donor_names_con.innerHTML = "";

    Object.entries(data.pregnancyPlan).forEach(([key, value]) => {
        if (key == "authorized_by_philhealth") {
            if (value == "yes") {
                // console.log("yes");
                document.getElementById(
                    "authorized_by_philhealth_yes",
                ).checked = true;
            } else if (value == "no") {
                document.getElementById("authorized_by_philhealth_no").checked =
                    true;
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
        // console.log("donor deleted");
    });

    // signature part
});

// WAIT FOR MODAL TO FULLY OPEN - THIS RUNS **AFTER** MODAL IS VISIBLE
const editPregnancyModal = document.getElementById("case2PrenatalModal");
let editPatientSignature = null;
if (editPregnancyModal) {
    editPregnancyModal.addEventListener("shown.bs.modal", function () {
        // console.log("Modal is NOW visible!");

        if (!editPatientSignature) {
            editPatientSignature = initSignatureCapture({
                drawBtnId: "edit_drawSignatureBtn",
                uploadBtnId: "edit_uploadSignatureBtn",
                canvasId: "edit_signaturePad",
                canvasSectionId: "edit_signatureCanvas",
                uploadSectionId: "edit_signatureUpload",
                previewSectionId: "edit_signaturePreview",
                fileInputId: "edit_signature_image",
                previewImageId: "edit_previewImage",
                errorElementId: "edit_signature_error",
                clearBtnId: "edit_clearSignature",
                saveBtnId: "edit_saveSignature",
                removeBtnId: "edit_removeSignature",
                hiddenInputId: "edit_signature_data",
                maxFileSizeMB: 2,
            });
            // console.log("✅ SIGNATURE INITIALIZED!");
        } else {
            editPatientSignature.clear();
        }
    });
}

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
        // console.log(pregnancyPlanId);
        const form = document.getElementById("pregnancy_plan_update_form");
        const formData = new FormData(form);

        // Manually add the hidden signature data
        const hiddenSignature = document.getElementById("edit_signature_data");
        if (hiddenSignature && hiddenSignature.value) {
            formData.set("edit_signature_data", hiddenSignature.value);
            // console.log("✅ Manually added signature data");
        }

        // for (const [key, value] of formData.entries()) {
        //     console.log(key, value);
        // }

        const response = await fetch(
            `/update/pregnancy-plan-record/${pregnancyPlanId}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                    Accept: "application/json",
                },
                body: formData,
            },
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
                        document.getElementById("case2PrenatalModal"),
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

    // reset the error
    const errors = document.querySelectorAll(".error-text");
    errors.forEach((error) => (error.innerHTML = ""));
    // console.log("medical id: ", medicalId);
    const response = await fetch(`/patient-record/view-details/${medicalId}`);
    // get the data
    const data = await response.json();

    // fields
    const patient_name = document.getElementById("check_up_patient_name");
    const handled_by = document.getElementById("check_up_handled_by");
    const healthworkerId = document.getElementById("health_worker_id");
    const hiddenPatientName = document.getElementById(
        "hidden_check_up_patient_name",
    );

    // provide the info
    patient_name.value = data.prenatalRecord.patient.full_name ?? "";
    handled_by.value = data.healthWorker.full_name ?? "";
    healthworkerId.value = data.healthWorker.user_id;
    hiddenPatientName.value = data.prenatalRecord.patient.full_name;

    // vital sign
    const checkup_blood_pressure = document.getElementById("check_up_blood_pressure");
    const checkup_temperature = document.getElementById("check_up_temperature");
    const checkup_respiratory_rate = document.getElementById("check_up_respiratory_rate");
    const checkup_pulse_rate = document.getElementById("check_up_pulse_rate");
    const checkup_height = document.getElementById("check_up_height");
    const checkup_weight = document.getElementById("check_up_weight");

    if (
        checkup_blood_pressure &&
        checkup_temperature &&
        checkup_height &&
        checkup_weight &&
        checkup_respiratory_rate &&
        checkup_pulse_rate
    ) {
        vitalSignInputMask(
            checkup_blood_pressure,
            checkup_temperature,
            checkup_pulse_rate,
            checkup_respiratory_rate,
            checkup_height,
            checkup_weight,
        );
    }
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

    const data = await response.json();

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
                    document.getElementById("prenatalCheckupModal"),
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
            text: "Prenatal Case Record will be moved to archived status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
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
            },
        );

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`,
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
            text: "Pregnancy Plan Record will be moved to archived status.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
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
            },
        );

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`,
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
