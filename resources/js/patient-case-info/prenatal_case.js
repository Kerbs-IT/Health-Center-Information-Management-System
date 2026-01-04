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
    para.innerHTML = data.caseInfo.P ?? "0";
    term.innerHTML = data.caseInfo.T ?? "0";
    premature.innerHTML = data.caseInfo.premature ?? "0";
    abortion.innerHTML = data.caseInfo.abortion ?? "0";
    livingChildren.innerHTML = data.caseInfo.living_children ?? "0";

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
// prenatal case
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

    // console.log(data);

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

// prenatal checkup
document.addEventListener("click", async function (e) {
    const viewBtn = e.target.closest(".viewPregnancyCheckupBtn");

    if (!viewBtn) return; // Not our button, ignore

    // Prevent default Bootstrap modal behavior temporarily
    e.preventDefault();
    e.stopPropagation();

    const checkupId = viewBtn.dataset.checkupId;

    // Validate checkup ID exists
    if (!checkupId || checkupId === "undefined" || checkupId === "null") {
        console.error("Invalid checkup ID:", checkupId);
        showErrorNotification("Unable to load checkup: Invalid ID");
        return;
    }

    // console.log("Loading checkup ID:", checkupId);

    // Show loading state
    showLoadingModal();

    try {
        const response = await fetch(
            `/prenatal/view-pregnancy-checkup-info/${checkupId}`,
            {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Validate response data
        if (!data || typeof data !== "object") {
            throw new Error("Invalid response data format");
        }

        // Clear all modal fields first
        clearCheckupModalData();

        // Populate pregnancy checkup info
        if (
            data.pregnancy_checkup_info &&
            typeof data.pregnancy_checkup_info === "object"
        ) {
            populateCheckupInfo(data.pregnancy_checkup_info);
        } else {
            console.warn("No pregnancy_checkup_info in response");
        }

        // Populate health worker info
        if (data.healthWorker && typeof data.healthWorker === "object") {
            populateHealthWorkerInfo(data.healthWorker);
        } else {
            console.warn("No healthWorker info in response");
        }

        // Hide loading and show modal
        hideLoadingModal();
        openCheckupModal();
    } catch (error) {
        console.error("Error fetching checkup data:", error);
        hideLoadingModal();
        showErrorNotification(`Failed to load checkup data: ${error.message}`);
    }
});

function showLoadingModal() {
    const modalElement = document.getElementById("pregnancyCheckUpModal");
    if (modalElement) {
        const modalBody = modalElement.querySelector(".modal-body");
        if (modalBody) {
            modalBody.style.opacity = "0.5";
            modalBody.style.pointerEvents = "none";
        }
    }
    // Or show a spinner overlay
}
function hideLoadingModal() {
    const modalElement = document.getElementById("pregnancyCheckUpModal");
    if (modalElement) {
        const modalBody = modalElement.querySelector(".modal-body");
        if (modalBody) {
            modalBody.style.opacity = "1";
            modalBody.style.pointerEvents = "auto";
        }
    }
}
function showErrorNotification(message) {
    // Option 1: Simple alert
    alert(message);

    // Option 2: Toast notification (if you have a toast library)
    // toast.error(message);

    // Option 3: Custom notification div
    // const notification = document.createElement('div');
    // notification.className = 'alert alert-danger';
    // notification.textContent = message;
    // document.body.appendChild(notification);
    // setTimeout(() => notification.remove(), 5000);
}
function clearCheckupModalData() {
    // List of all field IDs in your modal
    const fieldIds = [
        "check_up_time",
        "patient_name",
        "checkup_patient_name",
        "health_worker_name",
        "blood_pressure",
        "weight",
        "height",
        "temperature",
        "pulse_rate",
        "respiratory_rate",
        "nutritional_status",
        "laboratory_tests_done",
        "hemoglobin_count",
        "urinalysis",
        "complete_blood_count",
        "stool_examination",
        "acetic_acid_wash_test",
        "tetanus_toxoid_vaccination",
        "date_of_visit",
        "age_of_gestation",
        "blood_pressure_systolic",
        "blood_pressure_diastolic",
        "remarks",
        "next_visit_date",
        // Add any other field IDs your modal uses
    ];

    fieldIds.forEach((id) => {
        const element = document.getElementById(id);
        if (element) {
            if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                element.value = "";
            } else {
                element.innerHTML = "";
            }
        }
    });
}

/**
 * Open the checkup modal
 */
function openCheckupModal() {
    const modalElement = document.getElementById("pregnancyCheckUpModal");

    if (!modalElement) {
        console.error("Modal element not found: pregnancyCheckUpModal");
        showErrorNotification("Unable to open modal");
        return;
    }

    try {
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    } catch (error) {
        console.error("Error opening modal:", error);
        showErrorNotification("Error opening modal");
    }
}
function populateCheckupInfo(checkupInfo) {
    if (!checkupInfo || typeof checkupInfo !== "object") {
        console.warn("Invalid checkup info provided");
        return;
    }

    Object.entries(checkupInfo).forEach(([key, value]) => {
        try {
            // Special handling for time fields
            if (key === "check_up_time") {
                const formattedTime = formatTime(value);
                safeSetContent(key, formattedTime);
                return;
            }

            // Special handling for patient name (populate multiple fields)
            if (key === "patient_name") {
                safeSetContent("patient_name", value);
                safeSetContent("checkup_patient_name", value);
                return;
            }

            // Special handling for date fields
            if (key.includes("date") && value) {
                try {
                    const date = new Date(value);
                    if (!isNaN(date.getTime())) {
                        const formatted = date.toLocaleDateString("en-US", {
                            year: "numeric",
                            month: "short",
                            day: "numeric",
                        });
                        safeSetContent(key, formatted);
                        return;
                    }
                } catch (dateError) {
                    console.warn(
                        `Error formatting date for ${key}:`,
                        dateError
                    );
                }
            }

            // Default: set content as-is
            safeSetContent(key, value);
        } catch (error) {
            console.error(`Error setting field ${key}:`, error);
        }
    });
}
function safeSetContent(elementId, value, defaultValue = "N/A") {
    const element = document.getElementById(elementId);

    if (!element) {
        console.warn(`Element not found: ${elementId}`);
        return false;
    }

    // Handle null/undefined values
    if (value === null || value === undefined || value === "") {
        element.innerHTML = defaultValue;
        return true;
    }

    // Escape HTML to prevent XSS
    const safeValue = String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

    element.innerHTML = safeValue;
    return true;
}
function formatTime(timeString) {
    if (!timeString || typeof timeString !== "string") {
        return "N/A";
    }

    try {
        const parts = timeString.split(":");
        if (parts.length < 2) {
            return "N/A";
        }

        let [hours, minutes] = parts;
        hours = parseInt(hours, 10);
        minutes = parseInt(minutes, 10);

        if (isNaN(hours) || isNaN(minutes)) {
            return "N/A";
        }

        const ampm = hours >= 12 ? "PM" : "AM";
        hours = hours % 12 || 12;
        minutes = minutes.toString().padStart(2, "0");

        return `${hours}:${minutes} ${ampm}`;
    } catch (error) {
        console.error("Error formatting time:", error);
        return "N/A";
    }
}
function populateHealthWorkerInfo(healthWorker) {
    if (!healthWorker || typeof healthWorker !== "object") {
        console.warn("Invalid health worker info provided");
        safeSetContent("health_worker_name", null);
        return;
    }

    const fullName =
        healthWorker.full_name ||
        `${healthWorker.first_name || ""} ${
            healthWorker.last_name || ""
        }`.trim() ||
        "N/A";

    safeSetContent("health_worker_name", fullName);
}

