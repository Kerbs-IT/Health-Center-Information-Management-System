document.addEventListener("click", async (e) => {
    const viewIcon = e.target.closest(".view-case-info");
    if (!viewIcon) return;
    const caseId = viewIcon.dataset.bsCaseId;
    // handle error
    if (!caseId || caseId === "undefined" || caseId === "null") {
        console.error("Invalid case ID:", caseId);
        alert("Unable to load case details: Invalid ID");
        return;
    }

    try {
        const response = await fetch(`/vaccination-case/record/${caseId}`);
        const data = await response.json();
        console.log(data);

        // get the elements
        const patientName = document.getElementById("view-patient-name");
        const dateOfVaccination = document.getElementById(
            "view-date-of-vaccination"
        );
        const timeOfVaccination = document.getElementById(
            "view-time-of-vaccination"
        );
        const typeOfVaccine = document.getElementById("view-vaccine-type");
        const doseNumber = document.getElementById("view-dose-number");
        const remarks = document.getElementById("view-case-remarks");
        // handled by name
        // const handledBy = document.getElementById("view-handled-by");
        const height = document.getElementById("view-height");
        const weight = document.getElementById("view-weight");
        const temperature = document.getElementById("view-temperature");
        const dateOfComeback = document.getElementById("view-date-of-comeback");

        // if (handledBy) {
        //     handledBy.innerHTML = data.healthWorkerName??'n/a';
        // }

        // populate the data

        patientName.innerHTML = data.vaccinationCase.patient_name ?? "none";
        dateOfVaccination.innerHTML = data.vaccinationCase.date_of_vaccination
            ? new Date(
                  data.vaccinationCase.date_of_vaccination
              ).toLocaleDateString("en-US", {
                  month: "short",
                  day: "numeric",
                  year: "numeric",
              })
            : "none";
        dateOfComeback.innerHTML = data.vaccinationCase.date_of_comeback
            ? new Date(
                  data.vaccinationCase.date_of_comeback
              ).toLocaleDateString("en-US", {
                  month: "short",
                  day: "numeric",
                  year: "numeric",
              })
            : "none";
        // timeOfVaccination.innerHTML = data.vaccinationCase.time ?? "none";
        typeOfVaccine.innerHTML = data.vaccinationCase.vaccine_type ?? "none";
        (doseNumber.innerHTML = data.vaccinationCase.dose_number ?? "none"),
            (remarks.innerHTML = data.vaccinationCase.remarks ?? "none");
        // height,weight
        height.innerHTML = `${data.vaccinationCase.height} cm` ?? 'none';
        weight.innerHTML = `${data.vaccinationCase.weight } kg` ?? "none";
        temperature.innerHTML = `${data.vaccinationCase.temperature} °C` ?? "none";
    } catch (error) {
        console.error("Error viewing case:", error);
        Swal.fire({
            title: "Error",
            text: `Failed to view record: ${error.message}`,
            icon: "error",
            confirmButtonColor: "#3085d6",
        });
    }
});
