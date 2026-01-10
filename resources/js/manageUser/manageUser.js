import { error } from "jquery";
import Swal from "sweetalert2";

fetch("/showBrgyUnit")
    .then((response) => response.json())
    .then((data) => {
        let dropdown = document.getElementById("edit_patient_purok_dropdown");

        // console.log(data);
        data.forEach((item) => {
            let option = document.createElement("option");
            option.value = item.brgy_unit;
            option.text = item.brgy_unit;
            dropdown.appendChild(option);
        });
    });

    fetch("/showBrgyUnit")
        .then((response) => response.json())
        .then((data) => {
            let dropdown = document.getElementById("patient_purok_dropdown");

            // console.log(data);
            data.forEach((item) => {
                let option = document.createElement("option");
                option.value = item.brgy_unit;
                option.text = item.brgy_unit;
                dropdown.appendChild(option);
            });
        });

// add the patient account
const addPatientSubmitBtn = document.getElementById("add-patient-submit-btn");

addPatientSubmitBtn.addEventListener("click", async (e) => {
    e.preventDefault();
    const formDataElement = document.getElementById("add-patient-form");
    const formData = new FormData(formDataElement);

    try {
        // errors container
        const username_error = document.querySelector(".username-error");
        const fname_error = document.querySelector(".fname-error");
        const middle_initial_error = document.querySelector(
            ".middle-initial-error"
        );
        const lname_error = document.querySelector(".lname-error");
        const email_error = document.querySelector(".email-error");
        const password_error = document.querySelector(".password-error");
        const blk_n_street_error = document.querySelector(
            ".blk-n-street-error"
        );
        const purok_dropdown_error = document.querySelector(
            ".purok-dropdown-error"
        );
        const contact_number_error = document.querySelector(
            ".contact_number_error"
        );
        const date_of_birth_error = document.querySelector(
            ".date_of_birth_error"
        );
        const patient_type_error = document.querySelector(
            ".patient_type_error"
        );

        const response = await fetch("/add-patient-account", {
            method: "POST", // Yes, use POST
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: formData,
        });

        const result = await response.json();
        if (!response.ok) {
            // set the errors
            username_error.innerHTML = result.errors?.username?.[0] ?? "";
            fname_error.innerHTML = result.errors?.first_name?.[0] ?? "";
            middle_initial_error.innerHTML =
                result.errors?.middle_initial?.[0] ?? "";
            lname_error.innerHTML = result.errors?.last_name?.[0] ?? "";
            email_error.innerHTML = result.errors?.email?.[0] ?? "";
            password_error.innerHTML = result.errors?.password?.[0] ?? "";

            blk_n_street_error.innerHTML =
                result.errors?.blk_n_street?.[0] ?? "";
            purok_dropdown_error.innerHTML =
                result.errors?.patient_purok_dropdown?.[0] ?? "";
            contact_number_error.innerHTML =
                result.errors?.contact_number?.[0] ?? "";
            date_of_birth_error.innerHTML =
                result.errors?.date_of_birth?.[0] ?? "";
            patient_type_error.innerHTML =
                result.errors?.patient_type?.[0] ?? "";

            Swal.fire({
                title: "Update",
                text: "Invalid input value",
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        } else {
            formDataElement.reset();
            // make the errors gone
            username_error.innerHTML = "";
            fname_error.innerHTML = "";
            middle_initial_error.innerHTML = "";
            lname_error.innerHTML = "";
            email_error.innerHTML = "";
            password_error.innerHTML = "";

            blk_n_street_error.innerHTML = "";
            purok_dropdown_error.innerHTML = "";
            Swal.fire({
                title: "Creation",
                text: "Patient Account is successfully created",
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    } catch (error) {
        console.log(error);
    }
});

// import the address function




const submitBtn = document.getElementById("edit-user-submit-btn")??null;

// get data for the form
document.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".edit-user-profile");
    if (!editBtn) return;
    const id = editBtn.dataset.id;

    // set the id of the submit btn
    submitBtn.dataset.user = id;
    if (!id) {
        console.error("id is not provided");
        return;
    }
    const response = await fetch(`/user/profile/${id}`, {
        headers: {
            accept: "application/json",
        },
    });

    if (!response.ok) {
        console.log("Error in fetching data");
        return;
    }

    // if there are no errors
    const data = await response.json();

    if (data) {
        const brgy = document.getElementById("edit_patient_purok_dropdown");
        const street = document.getElementById("edit_blk_n_street");
        const fullName = document.getElementById("full_name");
        Object.entries(data.info).forEach(([key, value]) => {
            const input = document.getElementById(`edit_${key}`);
            if (input) {
                if (key == "date_of_birth") {
                    const date = new Date(value);
                    input.value = date.toISOString().split("T")[0];
                } else if (key == "profile_image") {
                    const img = document.getElementById("edit_profile_image");
                    const baseUrl = img.dataset.baseUrl;

                    // Remove escaped slashes
                    const cleanPath = value ? value.replace(/\\/g, "") : "";

                    img.src = cleanPath
                        ? baseUrl + cleanPath
                        : baseUrl + "images/default_profile.png";
                } else {
                    input.value = value;
                }
            }
        });
        const name = [
            data.info?.first_name,
            data.info?.middle_initial,
            data.info?.last_name,
        ]
            .filter(Boolean)
            .join(" ");

        // provide the full name
        fullName.innerHTML = name;
        // populate the brgy
        const blk_n_street = [data.address?.house_number, data.address?.street]
            .filter(Boolean)
            .join(" ");

        if (brgy && street) {
            brgy.value = data.address.purok;
            street.value = blk_n_street;
        }
    }
});

if (submitBtn) {
    submitBtn.addEventListener("click", async (e) => {
        e.preventDefault();
        const userId = submitBtn.dataset.user;

        const form = document.getElementById("profile-form");
        const formData = new FormData(form);
        formData.append("_method", "PUT");

        try {
            const response = await fetch(`/patient-profile/update/${userId}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                clearAllErrors();

                Swal.fire({
                    title: "Update Successful",
                    text: "Profile information has been successfully updated",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });
            } else {
                displayErrors(data.errors);

                // Format errors for SweetAlert
                const errorMessages = formatErrorMessages(data.errors);

                Swal.fire({
                    title: "Validation Error",
                    html: errorMessages,
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });

                // Clear file input
                const imageFile = document.getElementById("fileInput");
                imageFile.value = "";
                document.getElementById("fileName").innerHTML =
                    "No chosen File";
            }
        } catch (err) {
            Swal.fire({
                title: "Error",
                text: "An unexpected error occurred. Please try again.",
                icon: "error",
                confirmButtonColor: "#d33",
                confirmButtonText: "OK",
            });
            console.error("Fetch error:", err);
        }
    });
}

// remove user
document.addEventListener('click', async (e) => {
    const deleteBtn = e.target.closest(".delete-user");
    if (!deleteBtn) return;
    const id = deleteBtn.dataset.id;

    // set the id of the submit btn
    if (!id) {
        console.error("id is not provided");
        return;
    }
     Swal.fire({
         title: "Are you sure?",
         text: "This user account will be moved to archived status.",
         icon: "warning",
         showCancelButton: true,
         confirmButtonColor: "#d33",
         cancelButtonColor: "#3085d6",
         confirmButtonText: "Yes, archived it!",
     }).then((result) => {
         if (result.isConfirmed) {
             fetch(`/delete-patient-account/${id}`, {
                 method: "POST",
                 headers: {
                     "X-CSRF-TOKEN": document.querySelector(
                         'meta[name="csrf-token"]'
                     ).content,
                     Accept: "application/json",
                 },
             }).then((response) => {
                 if (response.ok) {
                     Swal.fire(
                         "Deleted!",
                         "The user has been removed.",
                         "success"
                     );
                     deleteBtn.closest("tr").remove(); // remove row from table
                 } else {
                     Swal.fire("Error", "Failed to delete user.", "error");
                 }
             });
         }
     });
})

// Error field mapping
const errorFieldMap = {
    profile_image: "image-error",
    first_name: "fname-error",
    middle_initial: "middle-initial-error",
    last_name: "lname-error",
    age: "age-error",
    date_of_birth: "birthdate-error",
    sex: "sex-error",
    civil_status: "civil-status-error",
    contact_number: "contact-error",
    nationality: "nationality-error",
    username: "username-error",
    email: "email-error",
    street: "street-error",
    postal_code: "postal-error",
    regionKey: "region-error",
    provinceKey: "province-error",
    cityKey: "city-error",
    barangayKey: "brgy-error",
};

function displayErrors(errors) {
    if (!errors) return;

    Object.entries(errorFieldMap).forEach(([fieldName, errorId]) => {
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.innerHTML = errors[fieldName]?.[0] ?? "";
        }
    });
}

function clearAllErrors() {
    Object.values(errorFieldMap).forEach((errorId) => {
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.innerHTML = "";
        }
    });
}

function formatErrorMessages(errors) {
    if (!errors || Object.keys(errors).length === 0) {
        return "Please check your input and try again.";
    }

    const errorList = Object.entries(errors)
        .map(([field, messages]) => {
            const fieldLabel = field
                .replace(/_/g, " ")
                .replace(/\b\w/g, (char) => char.toUpperCase());
            return `<strong>${fieldLabel}:</strong> ${messages[0]}`;
        })
        .join("<br>");

    return `<div style="text-align: left; max-height: 300px; overflow-y: auto;">${errorList}</div>`;
}
