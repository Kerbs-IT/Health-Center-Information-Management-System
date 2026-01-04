document.addEventListener("click", async (e) => {
    const side_b_view_btn = e.target.closest(".view-side-b-record");
    if (!side_b_view_btn) return;
    const id = side_b_view_btn.dataset.caseId;

    try {
        const response = await fetch(
            `/patient-record/family-planning/view/side-b-record/${id}`,
            {
                headers: {
                    Accept: "application/json",
                },
            }
        );

        if (response.ok) {
            const data = await response.json();

            // console.log(data);
            Object.entries(data.sideBrecord).forEach(([key, value]) => {
                if (document.getElementById(`view_${key}`)) {
                    const element = document.getElementById(`view_${key}`);

                    if (key == "medical_findings") {
                        // console.log(value);
                    }
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });
        }
    } catch (error) {
        console.log("Error:", error);
    }
});
