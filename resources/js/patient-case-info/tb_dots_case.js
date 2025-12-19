const viewBtn = document.querySelectorAll(".viewCaseBtn");
const viewTbody = document.getElementById("view-table-body");

document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".viewCaseBtn");
    if (!viewBtn) return;
    // initialize the id
    const id = viewBtn.dataset.caseId;
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        const response = await fetch(`/patient/tb-dots/get-case-info/${id}`);

        if (!response.ok) {
        } else {
            const data = await response.json();

            Object.entries(data.caseInfo).forEach(([key, value]) => {
                if (document.getElementById(`view_${key}`)) {
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });
            // reset the table first
            viewTbody.innerHTML = "";

            data.caseInfo.tb_dots_maintenance_med.forEach((meds) => {
                viewTbody.innerHTML += `
                    <tr>
                        <td>${meds.medicine_name}</td>
                        <td>${meds.dosage_n_frequency}</td>
                        <td>${meds.quantity}</td>
                        <td>${meds.start_date}</td>
                        <td>${meds.end_date}</td>
                    </tr>`;
            });

            // assign the health worker
            const viewHealthWorker = document.getElementById(
                "view_assigned_health_worker"
            );

            viewHealthWorker.innerHTML = `
                ${data.healthWorker.first_name ?? ""}
                ${data.healthWorker.middle_initial ?? ""}
                ${data.healthWorker.last_name ?? ""}
            `.trim();
        }
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
    const viewBtn = e.target.closest(".tb-dots-view-check-up");
    if (!viewBtn) return;
    const id = viewBtn.dataset.caseId ?? null;

    const response = await fetch(`/patient-record/view-check-up/tb-dots/${id}`);

    const data = await response.json();
    if (!response.ok) {
        console.log(data.errors);
    } else {
        Object.entries(data.checkUpInfo).forEach(([key, value]) => {
            if (document.getElementById(`view_date_of_comeback`)) {
                if (key == "date_of_comeback" && value != null) {
                    const date = new Date(value);
                    document.getElementById(`view_${key}`).innerHTML =
                        date.toISOString().split("T")[0] ?? "N/A";
                }
            }
            if (document.getElementById(`view_checkup_${key}`)) {
                document.getElementById(`view_checkup_${key}`).innerHTML =
                    value ?? "N/A";
            }
            
        });
    }
});
