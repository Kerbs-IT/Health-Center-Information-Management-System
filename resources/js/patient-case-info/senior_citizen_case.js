document.addEventListener("click", async (e) => {
    const viewBtn = e.target.closest(".senior-citizen-view-icon");

    if (!viewBtn) return;
    const id = viewBtn.dataset.bsCaseId;
    // console.log("caseId", id);

    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }
    try {
        // Validate ID
        if (!id || isNaN(Number(id))) {
            console.error("Invalid ID:", id);
            alert("Unable to load case details: Invalid ID");
            return;
        }

        const response = await fetch(
            `/senior-citizen/case-details/${Number(id)}`,
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

        // Safely populate case record fields
        if (
            data.seniorCaseRecord &&
            typeof data.seniorCaseRecord === "object"
        ) {
            Object.entries(data.seniorCaseRecord).forEach(([key, value]) => {
                try {
                    // console.log("Processing key:", key);
                    const element = document.getElementById(`view_${key}`);

                    if (element) {
                        // Escape HTML to prevent XSS
                        const safeValue =
                            value !== null && value !== undefined
                                ? String(value)
                                      .replace(/&/g, "&amp;")
                                      .replace(/</g, "&lt;")
                                      .replace(/>/g, "&gt;")
                                      .replace(/"/g, "&quot;")
                                      .replace(/'/g, "&#039;")
                                : "N/A";

                        element.innerHTML = safeValue;
                    } else {
                        // console.warn(`Element not found: view_${key}`);
                    }
                } catch (fieldError) {
                    // console.error(`Error setting field ${key}:`, fieldError);
                }
            });
        } else {
            console.warn("No seniorCaseRecord in response");
        }

        // Safely populate maintenance medication table
        const tableBody = document.getElementById("viewCaseBody");

        if (!tableBody) {
            console.error("Table body element not found: viewCaseBody");
            throw new Error("Table element not found");
        }

        // Clear table
        tableBody.innerHTML = "";

        // Check if maintenance medications exist
        if (
            data.seniorCaseRecord?.senior_citizen_maintenance_med &&
            Array.isArray(data.seniorCaseRecord.senior_citizen_maintenance_med)
        ) {
            const medications =
                data.seniorCaseRecord.senior_citizen_maintenance_med;

            if (medications.length === 0) {
                // Show "No records" message
                tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted">No maintenance medications found</td>
                </tr>
            `;
            } else {
                medications.forEach((record, index) => {
                    try {
                        // console.log("Processing medication record:", record);

                        // Safely get values with defaults
                        const medication =
                            record.maintenance_medication || "N/A";
                        const dosage = record.dosage_n_frequency || "N/A";
                        const quantity = record.quantity || "N/A";
                        const startDate = record.start_date || "N/A";
                        const endDate = record.end_date || "N/A";

                        // Create table row
                        const row = document.createElement("tr");
                        row.innerHTML = `
                        <td>${escapeHtml(medication)}</td>
                        <td>${escapeHtml(dosage)}</td>
                        <td>${escapeHtml(quantity)}</td>
                        <td>${escapeHtml(startDate)}</td>
                        <td>${escapeHtml(endDate)}</td>
                    `;

                        tableBody.appendChild(row);
                    } catch (rowError) {
                        console.error(
                            `Error adding medication row ${index}:`,
                            rowError
                        );
                    }
                });
            }
        } else {
            console.warn("No maintenance medications in response");
            tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">No maintenance medications found</td>
            </tr>
        `;
        }
    } catch (error) {
        console.error("Error fetching senior case details:", error);
        alert(`Failed to load case details: ${error.message}`);

        // Optionally clear/reset the UI on error
        const tableBody = document.getElementById("viewCaseBody");
        if (tableBody) {
            tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load data. Please try again.
                </td>
            </tr>
        `;
        }
    }

    // Helper function to escape HTML (prevents XSS)
    function escapeHtml(text) {
        if (text === null || text === undefined) {
            return "N/A";
        }

        const div = document.createElement("div");
        div.textContent = String(text);
        return div.innerHTML;
    }
});
