document.addEventListener("DOMContentLoaded", async (e) => {

    try {

        const response = await fetch("/dashboard/info", {
            headers: {
                accept: "application/json",
            },
        });

        if (!response.ok) {
            console.log("Errors:", response.status);
            return;
        }

        const data = await response.json();

        // initialize the data
        const overallPatientElement = document.getElementById(
            "overall-patient-counts"
        );
        const vaccinationPatientElement =
            document.getElementById("vaccination-count");
        const prenatalPatientElement =
            document.getElementById("prenatal-count");
        const tbDotsPatientElement =
            document.getElementById("tb-dots-count");
        const seniorCitizenElement = document.getElementById("senior-citizen-count");
        const familyPlanningElement =  document.getElementById("family-planning-count");
        // provide the values
        overallPatientElement.innerHTML = data.overallPatients??0;
        vaccinationPatientElement.innerHTML = data.vaccinationCount ?? 0;
        prenatalPatientElement.innerHTML = data.prenatalCount ?? 0;
        tbDotsPatientElement.innerHTML = data.tbDotsCount ?? 0
        seniorCitizenElement.innerHTML = data.seniorCitizenCount ?? 0;
        familyPlanningElement.innerHTML = data.familyPlanningCount ?? 0;

        // console.log(data.baseData);
        


        
    } catch (error) {
        console.error("Error")
    }
})

// Update your download button handle