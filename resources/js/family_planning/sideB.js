import Swal from "sweetalert2";

// side b view btn
const side_b_btn = document.getElementById("view-side-b-record");

side_b_btn.addEventListener("click", async () => {
    const id = side_b_btn.dataset.caseId;
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
});

// side b update
const side_b_edit_btn = document.getElementById("edit-side-b-record");
const sideBupdateBTN = document.getElementById(
    "edit-side-b-family-planning-assessment-btn"
);
side_b_edit_btn.addEventListener("click", async () => {
    const id = side_b_edit_btn.dataset.caseId;
    sideBupdateBTN.dataset.caseId = id;

    if (id == "") {
        return;
    }
    // get the record use the previous view record end point
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
        Swal.fire({
            title: "Family Planning Assessment Record",
            text: data.message, // this will make the text capitalize each word
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
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
