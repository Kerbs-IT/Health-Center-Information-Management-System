const healthWorkerElement = document.getElementById("handled_by");
const brgyElement = document.getElementById("brgy");

if (healthWorkerElement && brgyElement) {
    // Worker selected → filter brgy to their areas, auto-select first
    healthWorkerElement.addEventListener("change", async (e) => {
        const id = e.target.value;
        if (!id) return;

        try {
            const response = await fetch(
                `/add-patient/get-assigned-area/${id}`,
                {
                    headers: { Accept: "application/json" },
                },
            );

            const data = await response.json();
            if (response.ok && data.assigned_areas?.length > 0) {
                // Show only this worker's areas
                Array.from(brgyElement.options).forEach((opt) => {
                    if (opt.value === "" || opt.disabled) return;
                    opt.hidden = !data.assigned_areas.includes(opt.value);
                });

                // Auto-select first area if current selection not in their areas
                if (!data.assigned_areas.includes(brgyElement.value)) {
                    brgyElement.value = data.assigned_areas[0];
                }
            } else {
                // No areas — show all
                Array.from(brgyElement.options).forEach(
                    (opt) => (opt.hidden = false),
                );
            }
        } catch (error) {
            console.error("Error fetching assigned areas:", error);
        }
    });

    // Brgy selected → auto-select the assigned health worker, re-filter brgy to that worker's areas
    brgyElement.addEventListener("change", async (e) => {
        const purok = e.target.value;

        if (!purok || purok.trim() === "") {
            Array.from(brgyElement.options).forEach(
                (opt) => (opt.hidden = false),
            );
            healthWorkerElement.value = "";
            return;
        }

        try {
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
            if (response.ok && data.health_worker_id) {
                healthWorkerElement.value = data.health_worker_id;

                // Re-fetch this worker's areas to keep brgy filter in sync
                const areaResponse = await fetch(
                    `/add-patient/get-assigned-area/${data.health_worker_id}`,
                    { headers: { Accept: "application/json" } },
                );
                const areaData = await areaResponse.json();

                if (areaResponse.ok && areaData.assigned_areas?.length > 0) {
                    Array.from(brgyElement.options).forEach((opt) => {
                        if (opt.value === "" || opt.disabled) return;
                        opt.hidden = !areaData.assigned_areas.includes(
                            opt.value,
                        );
                    });
                }
            } else {
                healthWorkerElement.value = "";
                Array.from(brgyElement.options).forEach(
                    (opt) => (opt.hidden = false),
                );
            }
        } catch (error) {
            console.error("Error fetching health worker:", error);
        }
    });
}
