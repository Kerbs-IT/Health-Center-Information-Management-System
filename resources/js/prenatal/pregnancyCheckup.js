import Swal from "sweetalert2";

// view the check up info
const viewBTN = document.querySelectorAll(".viewPregnancyCheckupBtn");

viewBTN.forEach((btn) => {
    btn.addEventListener("click", async () => {
        const checkupId = btn.dataset.checkupId;

        console.log(checkupId);

        const response = await fetch(
            `/prenatal/view-pregnancy-checkup-info/${checkupId}`
        );

        if (response.ok) {
            const data = await response.json();
            Object.entries(data.pregnancy_checkup_info).forEach(
                ([key, value]) => {
                    if (document.getElementById(`${key}`)) {
                        if (key == "check_up_time") {
                            let [h, m] = value.split(":");
                            let ampm = h >= 12 ? "PM" : "AM";
                            h = h % 12 || 12;
                            let formatted = `${h}:${m} ${ampm}`;
                            console.log("workingsss1");
                            document.getElementById(`${key}`).innerHTML =
                                formatted ?? "N/A";
                        }
                        if (key == "patient_name") {
                            document.getElementById(
                                "checkup_patient_name"
                            ).innerHTML = value;
                        }
                        document.getElementById(`${key}`).innerHTML =
                            value ?? "N/A";
                    }
                }
            );

            // add the health worker name
            document.getElementById("health_worker_name").innerHTML =
                data.healthWorker.full_name ?? "N/A";
        }
    });
});

// edit btn
const editBTN = document.querySelectorAll(".editPregnancyCheckupBtn");
let medicalId = 0;

editBTN.forEach((btn) => {
    btn.addEventListener("click", async () => {
        const checkupId = btn.dataset.checkupId;
        console.log(checkupId);

        const response = await fetch(
            `/prenatal/view-pregnancy-checkup-info/${checkupId}`
        );

        if (response.ok) {
            const data = await response.json();
            Object.entries(data.pregnancy_checkup_info).forEach(
                ([key, value]) => {
                    if (
                        document.getElementById(`edit_${key}`) ||
                        document.getElementById(
                            `edit_${key}_${value}` || key === "patient_name"
                        )
                    ) {
                        if (key == "check_up_time") {
                            document.getElementById(`edit_${key}`).value =
                                value;
                        } else if (key == "patient_name") {
                            document.getElementById("edit_patient_name").value =
                                value;
                            // set the hidden input - matches the name attribute that gets validated
                            document.getElementById(
                                "edit_check_up_full_name"
                            ).value = value;
                            console.log("name", value);
                            console.log(
                                document.getElementById(
                                    "edit_check_up_full_name"
                                ).value
                            );
                        } else if (value == "Yes" || value == "No") {
                            document.getElementById(
                                `edit_${key}_${value}`
                            ).checked = true;
                        } else {
                            document.getElementById(`edit_${key}`).value =
                                value ?? "";
                        }
                    }
                }
            );
            document.getElementById("edit_check_up_handled_by").value =
                data.healthWorker.full_name ?? "";
            document.getElementById("edit_health_worker_id").value =
                data.healthWorker.user_id;
            // give the id for update
            medicalId = checkupId;
        }
    });
});

const updateBTN = document.getElementById("edit-check-up-save-btn");

updateBTN.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("edit-check-up-form");
    const formData = new FormData(form);

    for (const [key, value] of formData.entries()) {
        console.log(key, value);
    }

    const response = await fetch(`/update/prenatal-check-up/${medicalId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

    const data = response.json();

    const errorElements = document.querySelectorAll(".error-text");
    if (!response.ok) {
        // reset the error element text first
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        // if there's an validation error load the error text
        Object.entries(data.errors).forEach(([key, value]) => {
            if (document.getElementById(`${key}_error`)) {
                document.getElementById(`${key}_error`).textContent = value;
            }
        });

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
            title: "Prenatal Patient",
            text: capitalizeEachWord(message), // this will make the text capitalize each word
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        errorElements.forEach((element) => {
            element.textContent = "";
        });
        Swal.fire({
            title: "Prenatal check-Up Info",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
