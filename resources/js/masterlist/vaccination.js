import { puroks } from "../patient/healthWorkerList.js";
import { automateAge } from "../automateAge.js";
import Swal from "sweetalert2";

const editBtn = document.querySelectorAll(".vaccination-masterlist-edit-btn");
const saveBtn = document.getElementById(
    "update_vaccination_masterlist_save_btn"
);

document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".vaccination-masterlist-edit-btn");
    if (!editBtn) return;
    const id = editBtn.dataset.masterlistId;
    // Validate case ID
    if (!id || id === "undefined" || id === "null") {
        console.error("Invalid case ID:", id);
        alert("Unable to archive: Invalid ID");
        return;
    }
    // console.log(id);

    // == try catch block ==
    try {
        const response = await fetch(`/masterist/vaccination/${id}`, {
            headers: {
                accept: "application/json",
            },
        });

        if (response.ok) {
            const data = await response.json();

            // console.log(data.info);

            // populate the existing records
            Object.entries(data.info).forEach(([key, value]) => {
                if (key == "name_of_child") {
                    const fname = document.getElementById(
                        "vaccination_masterlist_fname"
                    );
                    const MI = document.getElementById(
                        "vaccination_masterlist_MI"
                    );
                    const lname = document.getElementById(
                        "vaccination_masterlist_lname"
                    );

                    const fullName = value.split(" ");
                    fname.value = fullName[0];
                    MI.value = fullName[1];
                    lname.value = fullName[2];

                    // set the bg
                } else if (key == "date_of_birth") {
                    const formatted = new Date(value)
                        .toISOString()
                        .split("T")[0];
                    // console.log("Formatted: ", formatted);

                    document.querySelector(`input[name="${key}"]`).value =
                        formatted;
                    if (value != null) {
                        document
                            .querySelector(`input[name="${key}"]`)
                            .classList.add("bg-light");
                        document
                            .querySelector(`input[name="${key}"]`)
                            .classList.add("border-dark", "border-2");
                    } else {
                        document
                            .querySelector(`input[name="${key}"]`)
                            .classList.remove("bg-light");
                    }
                } else if (key == 'age' && value !=null) {
                    const age = document.getElementById("age");
                    const hiddenAge = document.getElementById("hiddenAge");
                    if (age && hiddenAge) {
                        age.value = value;
                        hiddenAge.value = value;
                    }
                } else if (key == "sex") {
                    const sex = document.querySelectorAll('input[name="sex"]');
                    sex.forEach((input) => {
                        input.checked = input.value == value;
                    });
                } else if (key == "Address") {
                    const addressText = (
                        data.address_info.house_number +
                        "," +
                        data.address_info.street
                    ).trim(" ");
                    const street = document.getElementById("street");
                    street.value = addressText;

                    const brgy = document.getElementById("brgy");

                    puroks(brgy, data.address_info.purok);
                } else {
                    if (document.querySelector(`input[name="${key}"]`)) {
                        document.querySelector(`input[name="${key}"]`).value =
                            value;
                        if (value != null) {
                            document
                                .querySelector(`input[name="${key}"]`)
                                .classList.add("bg-light");
                            document
                                .querySelector(`input[name="${key}"]`)
                                .classList.add("border-dark", "border-2");
                        } else {
                            document
                                .querySelector(`input[name="${key}"]`)
                                .classList.remove("bg-light");
                        }
                        // console.log(value);
                    }
                }
            });

            // add the medical record case id to update btn

            saveBtn.dataset.medicalRecordCaseId =
                data.info.medical_record_case_id;
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




saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const form = document.getElementById("edit-vaccination-masterlist-form");
    const formData = new FormData(form);

    const id = e.target.dataset.medicalRecordCaseId;
    // for (let [key, value] of formData.entries()) {
    //     console.log(`${key}: ${value}`);
    // }

    const response = await fetch(`/masterlist/update/vaccination/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
        // // reset the error element text first
        // errorElements.forEach((element) => {
        //     element.textContent = "";
        // });
        // // if there's an validation error load the error text
        // Object.entries(data.errors).forEach(([key, value]) => {
        //     if (document.getElementById(`${key}_error`)) {
        //         document.getElementById(`${key}_error`).textContent = value;
        //     }
        // });
        let errorMessage = "";

        if (data.errors) {
            // Handle ValidationException
            errorMessage = Object.values(data.errors)
                .flat() // flatten nested arrays if present
                .join("\n");
        } else if (data.message) {
            // Handle general backend errors
            errorMessage = data.message;
        } else {
            // Handle unexpected responses
            errorMessage = "An unexpected error occurred.";
        }

        Swal.fire({
            title: "Error",
            text: capitalizeEachWord(errorMessage),
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        // errorElements.forEach((element) => {
        //     element.textContent = "";
        // });
        Swal.fire({
            title: "Update",
            text: capitalizeEachWord(data.message),
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("editvaccinationMasterListModal")
                );
                modal.hide();
            }
        });
    }
});

// handle the automation of age
const dob = document.getElementById("birthdate");
const age = document.getElementById("age");
const hiddenAge = document.getElementById("hiddenAge");

if (dob && age && hiddenAge) {
    console.log("checking if its working");
    automateAge(dob, age, hiddenAge);
}
function capitalizeEachWord(str) {
    return str.replace(/\b\w/g, (char) => char.toUpperCase());
}
