const viewIcon = document.getElementById("view-family-plan-info") ?? null;

if (viewIcon) {
    viewIcon.addEventListener("click", async (e) => {
        const caseId = viewIcon.dataset.caseId;

        const response = await fetch(
            `/patient-case/family-planning/viewCaseInfo/${caseId}`,
            {
                headers: {
                    Accept: "application/json",
                },
            }
        );

        if (response.ok) {
            const data = await response.json();

            Object.entries(data.caseInfo).forEach(([key, value]) => {
                if (key == "type_of_patient" && value == "new acceptor") {
                    if (data.caseInfo.new_acceptor_reason_for_FP != "") {
                        document.getElementById(
                            `view_${key}`
                        ).innerHTML = `${value}/${data.caseInfo.new_acceptor_reason_for_FP}`;
                    } else {
                        document.getElementById(`view_${key}`).innerHTML =
                            value;
                    }
                }
                if (key == "type_of_patient" && value == "current user") {
                    if (data.caseInfo.current_user_reason_for_FP != "") {
                        document.getElementById(
                            `view_${key}`
                        ).innerHTML = `${value}/${data.caseInfo.current_user_reason_for_FP}`;
                    } else {
                        document.getElementById(`view_${key}`).innerHTML =
                            value;
                    }
                }
                if (key == "spouse_lname") {
                    if (document.getElementById("view_spouse_name")) {
                        console.log("wording");
                        document.getElementById("view_spouse_name").innerHTML =
                            `${data.caseInfo.spouse_fname ?? ""} ${
                                data.caseInfo.spouse_MI ?? ""
                            } ${data.caseInfo.spouse_lname ?? ""}`.trim();
                    }
                }

                if (document.getElementById(`view_${key}`)) {
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });

            Object.entries(data.caseInfo.medical_history).forEach(
                ([key, value]) => {
                    if (key == "with_dissability" && value == "Yes") {
                        document.getElementById(
                            `view_${key}`
                        ).innerHTML = `${value}- ${data.caseInfo.medical_history.if_with_dissability_specification}`;
                    } else {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(`view_${key}`).innerHTML =
                                value;
                        }
                    }
                }
            );
            // obsterical history
            Object.entries(data.caseInfo.obsterical_history).forEach(
                ([key, value]) => {
                    if (document.getElementById(`view_${key}`)) {
                        document.getElementById(`view_${key}`).innerHTML =
                            value ?? "N/A";
                    }
                }
            );
            // risk for sexuall transmitted
            Object.entries(
                data.caseInfo.risk_for_sexually_transmitted_infection
            ).forEach(([key, value]) => {
                if (key == "referred_to" && value == "others") {
                    console.log(key);
                    document.getElementById(
                        `view_${key}`
                    ).innerHTML = `${value} - ${data.caseInfo.risk_for_sexually_transmitted_infection.reffered_to_others}`;
                } else {
                    if (document.getElementById(`view_${key}`)) {
                        document.getElementById(`view_${key}`).innerHTML =
                            value ?? "N/A";
                    }
                }
            });

            Object.entries(data.caseInfo.physical_examinations).forEach(
                ([key, value]) => {
                    if (
                        key == "extremites_UID_type" &&
                        value == "cervial abnormalities"
                    ) {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(
                                `view_${key}`
                            ).innerHTML = `${value} - ${data.caseInfo.physical_examinations.cervical_abnormalities_type}`;
                        }
                    } else if (
                        key == "extremites_UID_type" &&
                        value == "cervical consistency"
                    ) {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(
                                `view_${key}`
                            ).innerHTML = `${value} - ${data.caseInfo.physical_examinations.cervical_consistency_type}`;
                        }
                    } else if (
                        key == "extremites_UID_type" &&
                        value == "uterine position"
                    ) {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(
                                `view_${key}`
                            ).innerHTML = `${value} - ${data.caseInfo.physical_examinations.uterine_position_type}`;
                        }
                    } else if (
                        key == "extremites_UID_type" &&
                        value == "uterine depth"
                    ) {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(
                                `view_${key}`
                            ).innerHTML = `${value} - ${data.caseInfo.physical_examinations.uterine_position_text}cm`;
                        }
                    } else {
                        if (document.getElementById(`view_${key}`)) {
                            document.getElementById(`view_${key}`).innerHTML =
                                value ?? "N/A";
                        }
                    }
                }
            );
        }
    });
}
