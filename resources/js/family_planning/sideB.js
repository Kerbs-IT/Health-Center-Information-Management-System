import Swal from "sweetalert2";

// EVENT DELEGATION IN SIDE B VIEW

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

            console.log(data);
            Object.entries(data.sideBrecord).forEach(([key, value]) => {
                if (document.getElementById(`view_${key}`)) {
                    const element = document.getElementById(`view_${key}`);

                    if (key == "medical_findings") {
                        console.log(value);
                    }
                    document.getElementById(`view_${key}`).innerHTML = value;
                }
            });
        }
    } catch (error) {
        console.log("Error:", error);
    }
});

// side b update
const side_b_edit_btn = document.getElementById("edit-side-b-record");
const sideBupdateBTN = document.getElementById(
    "edit-side-b-family-planning-assessment-btn"
);

// SIDE B EDIT BTN EVENT DELEGATION
document.addEventListener("click", async (e) => {
    const side_b_edit_btn = e.target.closest(".edit-side-b-record");
    if (!side_b_edit_btn) return;

    const id = side_b_edit_btn.dataset.caseId;
    sideBupdateBTN.dataset.caseId = id;

    if (id == "") {
        return;
    }
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

            //  dispath the livewire

            console.log(data);
            Object.entries(data.sideBrecord).forEach(([key, value]) => {
                if (key == "medical_record_case_id") {
                    document.getElementById(`edit_side_b_${key}`).value = value;
                } else if (key == "health_worker_id") {
                    document.getElementById(`edit_side_b_${key}`).value = value;
                } else {
                    const radioGroups = document.querySelectorAll(
                        `input[type="radio"][name='edit_${key}']`
                    );

                    if (radioGroups.length > 0) {
                        // loop through
                        radioGroups.forEach((element) => {
                            element.checked = element.value == value;
                        });
                        return;
                    }
                    if (document.getElementById(`edit_${key}`)) {
                        document.getElementById(`edit_${key}`).value = value;
                    }
                }
            });
        }
    } catch (error) {
        console.log("Error:", error);
    }
});

// update the record with the new data

sideBupdateBTN.addEventListener("click", async (e) => {
    e.preventDefault();
    const id = sideBupdateBTN.dataset.caseId;
    // form
    const form = document.getElementById("edit-side-b-family-plan-form");
    const formData = new FormData(form);
    const response = await fetch(
        `/patient-record/family-planning/update/side-b-record/${id}`,
        {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: formData,
        }
    );

    const data = await response.json();

    if (response.ok) {
        // ✅ Safe Livewire dispatch
        if (typeof Livewire !== "undefined") {
            try {
                Livewire.dispatch("familyPlanningRefreshTable");
            } catch (error) {
                console.error("Error dispatching Livewire event:", error);
            }
        } else {
            console.warn("Livewire is not available");
        }

        Swal.fire({
            title: "Family Planning Assessment Record",
            text: data.message, // this will make the text capitalize each word
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("editSideBcaseModal")
                );
                if (modal) {
                    modal.hide();
                }
            }
        });
    } else {
        let message = "";

        if (data.errors) {
            if (typeof data.errors == "object") {
                message = Object.values(data.errors).flat().join("\n");
            } else {
                message = data.errors;
            }
        } else {
            message = "An unexpected error occurred.";
        }

        Swal.fire({
            title: "Family Planning Assessment Record",
            text: capitalizeEachWord(message), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

// delete side b record

document.addEventListener("click", async (e) => {
    const deleteBtn = e.target.closest(".delete-side-b-record");

    if (!deleteBtn) return;
    const id = deleteBtn.dataset.caseId;

    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }

    try {
        // ✅ Show confirmation dialog FIRST
        const result = await Swal.fire({
            title: "Are you sure?",
            text: "The Family Planning Client Assessment Record - Side B will be Deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Archive",
            cancelButtonText: "Cancel",
        });

        // ✅ Exit if user cancelled
        if (!result.isConfirmed) return;

        // ✅ Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error("CSRF token not found. Please refresh the page.");
        }

        const response = await fetch(
            `/patient-record/family-planning/case-record/delete/side-B/${id}`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken.content,
                    Accept: "application/json",
                },
            }
        );

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(
                data.message || `HTTP error! status: ${response.status}`
            );
        }

        // Success - refresh table
        if (typeof Livewire !== "undefined") {
            Livewire.dispatch("seniorCitizenRefreshTable"); // ✅ Update dispatch name if needed
        }

        // Remove the row from DOM
        const row = deleteBtn.closest("tr");
        if (row) {
            row.remove();
        }

        // Show success message
        Swal.fire({
            title: "Archived!",
            text: "The Tb dots Check-up Record has been archived.",
            icon: "success",
            confirmButtonColor: "#3085d6",
        });
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
