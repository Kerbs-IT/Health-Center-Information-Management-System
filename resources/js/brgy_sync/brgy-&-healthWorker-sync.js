//  ===================== HANDLE THE SYNC OF HEALTH WORKER AND BRGY IN ADD PATIENT
const healthWorkerElement = document.getElementById("handled_by");
const brgyElement = document.getElementById("brgy");
const isHealthWorker = healthWorkerElement.dataset.isHealthWorker;
if (healthWorkerElement) {
    healthWorkerElement.addEventListener("change", async (e) => {
        const id = e.target.value;

        try {
            // get the assigned area
            const response = await fetch(
                `/add-patient/get-assigned-area/${id}`,
                {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                    },
                },
            );

            const data = await response.json();
            if (response.ok) {
                brgyElement.value = data.assigned_area;
            }
        } catch (error) {
            console.log("Error happened:", error);
        }
    });
}
// sync the change in brgy and health worker
if (brgyElement) {
    brgyElement.addEventListener("change", async (e) => {
        const purok = e.target.value;

        // Check if purok is empty - don't make API call
        if (!purok || purok.trim() === "") {
            // console.log("No barangay selected, skipping health worker fetch");

            // ✅ Clear the health worker dropdown when no brgy is selected
            if (healthWorkerElement) {
                healthWorkerElement.value = "";
            }
            return; // Exit early
        }

        try {
            // get the assigned area
            const response = await fetch(
                `/get-health-worker?assigned_area=${encodeURIComponent(purok)}`,
                {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                },
            );

            const data = await response.json();
            if (response.ok) {
                const handledByViewInput = document.getElementById(
                    "handle_by_view_input",
                );
                if (response.ok) {
                    healthWorkerElement.value = data.health_worker_id;
                    const handledByViewInput = document.getElementById(
                        "handle_by_view_input",
                    );
                   
                }
            } else {
                console.error("Failed to fetch health worker:", data);
            }
        } catch (error) {
            console.log("Error happened:", error);
        }
    });
}
