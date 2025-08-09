
// get the element of the view icon

const viewIcon = document.querySelectorAll(".view-case-info");


viewIcon.forEach(icon => {
    icon.addEventListener('click',async (e) => {
        e.preventDefault()

        const caseId = icon.dataset.bsCaseId;
        console.log(caseId);
        // console.log(icon);
        // get the info of the case
        const response = await fetch(`/vaccination-case/record/${caseId}`);
        const data = await response.json();
        console.log(data);

        // get the elements
        const patientName = document.getElementById('view-patient-name');
        const dateOfVaccination = document.getElementById('view-date-of-vaccination');
        const timeOfVaccination = document.getElementById("view-time-of-vaccination");
        const typeOfVaccine = document.getElementById("view-vaccine-type");
        const doseNumber = document.getElementById("view-dose-number");
        const remarks = document.getElementById("view-case-remarks");


        // populate the data

        patientName.innerHTML = data.vaccinationCase.patient_name ?? 'none';
        dateOfVaccination.innerHTML = data.vaccinationCase.date_of_vaccination
            ? new Date(
                  data.vaccinationCase.date_of_vaccination
              ).toLocaleDateString("en-US", {
                  month: "short",
                  day: "numeric",
                  year: "numeric",
              })
            : "none";
        timeOfVaccination.innerHTML = data.vaccinationCase.time ?? 'none';
        typeOfVaccine.innerHTML = data.vaccinationCase.vaccine_type ?? 'none';
        doseNumber.innerHTML = data.vaccinationCase.dose_number ?? 'none',
        remarks.innerHTML = data.vaccinationCase.remarks ?? 'none';  
        

    });
});