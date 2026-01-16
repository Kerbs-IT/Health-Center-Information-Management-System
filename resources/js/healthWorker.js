import { error } from "jquery";
import Swal from "sweetalert2";
import { loadAddress } from "./address/address";
import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;
import { automateAge } from "./automateAge";
import resetPasswordManually from "./passwordReset";
import { copyPassword } from "./passwordReset";
// import the address function

document.addEventListener("DOMContentLoaded", () => {
    const removeIcons = document.querySelectorAll(".remove-icon-con");

    removeIcons.forEach((icon) => {
        icon.addEventListener("click", (e) => {
            e.preventDefault();
            const removeId = icon.dataset.id;
            // console.log(removeId);

            Swal.fire({
                title: "Are you sure?",
                text: "This health worker account will be moved to archived status.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, archive it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/health-worker/${removeId}`, {
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
                            icon.closest("tr").remove(); // remove row from table
                        } else {
                            Swal.fire(
                                "Error",
                                "Failed to delete user.",
                                "error"
                            );
                        }
                    });
                }
            });
        });
    });
});

const submitBtn = document.getElementById("submit-btn");
// EDIT PROFILE the pop-up
document.addEventListener("DOMContentLoaded", () => {
    const editIcon = document.querySelectorAll(".edit-icon");
    const popUp = document.getElementById("pop-up");
    const cancelBtn = document.getElementById("cancel-btn");

    const editErrors = document.querySelectorAll(".edit-healthworker-info");

    if (editErrors) {
        editErrors.forEach((error) => (error.innerHTML = ""));
    }
    editIcon.forEach((icon) => {
        icon.addEventListener("click", (e) => {
            e.preventDefault();
            const id = icon.dataset.id;
            // console.log(id);

            fetch(`/health-worker/get-info/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
            })
                .then((response) => {
                    if (response.ok) {
                        return response.json();
                    }
                })
                .then((data) => {
                    // console.log(data);

                    // reset first
                    submitBtn.dataset.user = data.response.user_id;
                    const profileImg = document.getElementById("profile-image");
                    const fullname = document.getElementById("full_name");
                    const fname = document.getElementById("first_name");
                    const lname = document.getElementById("last_name");
                    const mInitial = document.getElementById("middle_initial");
                    const age = document.getElementById("age");
                    const bday = document.getElementById("birthdate");
                    const contact = document.getElementById("contact_num");
                    const nationality = document.getElementById("nationality");
                    const email = document.getElementById("email");
                    const blkNstreet = document.getElementById("blk_n_street");
                    const postalCode = document.getElementById("postal_code");

                    // address
                    const region = document.getElementById("region");
                    const province = document.getElementById("province");
                    const city = document.getElementById("city");
                    const brgy = document.getElementById("brgy");
                    // suffix
                    const suffix = document.getElementById("edit_suffix");
                    const resetPassword =
                        document.getElementById("reset_password");

                    // reset first
                    region.dataset.selected = "";
                    province.dataset.selected = "";
                    city.dataset.selected = "";
                    brgy.dataset.selected = "";

                    if (data.response.region_id) {
                        region.dataset.selected = data.response.region_id;
                        province.dataset.selected = data.response.province_id;
                        city.dataset.selected = data.response.city_id;
                        brgy.dataset.selected = data.response.brgy_id;
                        region.value = data.response.region_id;
                    } else {
                        region.innerHTML =
                            '<option value="" dissabled>Select Region</option>';
                        province.innerHTML =
                            '<option value="">Select Province</option>';
                        city.innerHTML =
                            '<option value="">Select City/Municipality</option>';
                        brgy.innerHTML =
                            '<option value="">Select Barangay</option>';
                    }
                    loadAddress(province, city, brgy, region, region.value); // load the address to populate the inputs

                    // input values
                    const baseUrl = profileImg.dataset.baseUrl; // gets data-base-url
                    profileImg.src = baseUrl + data.response.profile_image;
                    // profileImg.src = `{{ asset('${data.response.profile_image}') }}`;
                    // console.log(profileImg);
                    fullname.innerHTML = data.response.full_name;
                    fname.value = data.response.first_name;
                    lname.value = data.response.last_name;
                    mInitial.value = data.response.middle_initial;
                    age.value = data.response.age;
                    bday.value = data.response.date_of_birth;
                    contact.value = data.response.contact_number;
                    nationality.value = data.response.nationality;

                    email.value = data.response.email;

                    blkNstreet.value = data.response.street ?? "none";
                    postalCode.value = data.response.postal_code;
                    suffix.value = data.response.suffix ?? "";

                    if (resetPassword) {
                        resetPassword.dataset.id = data.response.id ?? null;
                    }
                })
                .catch((error) => {
                    console.error("Fetch error: ", error);
                });

            if (popUp) {
                popUp.classList.remove("d-none");
                popUp.classList.add("d-flex");
            }
        });
    });

    cancelBtn.addEventListener("click", (e) => {
        e.preventDefault();
        popUp.classList.add("d-none");
        popUp.classList.remove("d-flex");
    });
});

if (submitBtn) {
    submitBtn.addEventListener("click", (e) => {
        e.preventDefault();
        const userId = submitBtn.dataset.user;

        let form = document.getElementById("profile-form");
        let formData = new FormData(form);

        // for (let [key, value] of formData.entries()) {
        //     console.log(`${key}: ${value}`);
        // }

        formData.append("_method", "PUT"); // Laravel will detect this

        fetch(`/health-worker/update/${userId}`, {
            method: "POST", // Yes, use POST
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: formData,
        })
            .then(async (response) => {
                const data = await response.json(); // 👈 parse the body
                return { ok: response.ok, data }; // 👈 pass both ok status and data
            })
            .then(({ ok, data }) => {
                const imgError = document.getElementById("image-error");
                const fnameError = document.getElementById("fname-error");
                const middleError = document.getElementById(
                    "middle-initial-error"
                );
                const lnameError = document.getElementById("lname-error");
                const ageError = document.getElementById("age-error");
                const birthdateError =
                    document.getElementById("birthdate-error");
                const sexError = document.getElementById("sex-error");
                const civilStatusError =
                    document.getElementById("civil-status-error");
                const contactError = document.getElementById("contact-error");
                const nationalityError =
                    document.getElementById("nationality-error");

                const emailError = document.getElementById("email-error");
                const streetError = document.getElementById("street-error");
                const postalError = document.getElementById("postal-error");
                const regionError = document.getElementById("region-error");
                const provinceError = document.getElementById("province-error");
                const cityError = document.getElementById("city-error");
                const brgyError = document.getElementById("brgy-error");
                const imageFile = document.getElementById("fileInput");
                const editErrors = document.querySelectorAll(
                    ".edit-healthworker-info"
                );

                if (ok) {
                    if (editErrors) {
                        editErrors.forEach((error) => (error.innerHTML = ""));
                    }
                    Swal.fire({
                        title: "Update",
                        text: "Health Worker Information is successfully updated",
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK",
                    });

                    // Optional: clear error messages on success
                    imgError.innerHTML = "";
                    middleError.innerHTML = "";
                    // clear others as needed...
                } else {
                    if (editErrors) {
                        editErrors.forEach((error) => (error.innerHTML = ""));
                    }
                    Swal.fire({
                        title: "Update",
                        text: "Invalid input value",
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK",
                    });

                    // Fill error fields
                    imgError.innerHTML = data.errors?.profile_image?.[0] ?? "";
                    middleError.innerHTML =
                        data.errors?.middle_initial?.[0] ?? "";
                    fnameError.innerHTML = data.errors?.first_name?.[0] ?? "";
                    lnameError.innerHTML = data.errors?.last_name?.[0] ?? "";
                    ageError.innerHTML = data.errors?.age?.[0] ?? "";
                    birthdateError.innerHTML =
                        data.errors?.date_of_birth?.[0] ?? "";
                    sexError.innerHTML = data.errors?.sex?.[0] ?? "";
                    civilStatusError.innerHTML =
                        data.errors?.civil_status?.[0] ?? "";
                    contactError.innerHTML =
                        data.errors?.contact_number?.[0] ?? "";
                    nationalityError.innerHTML =
                        data.errors?.nationality?.[0] ?? "";

                    emailError.innerHTML = data.errors?.email?.[0] ?? "";
                    streetError.innerHTML = data.errors?.street?.[0] ?? "";
                    postalError.innerHTML = data.errors?.postal_code?.[0] ?? "";
                    regionError.innerHTML = data.errors?.regionKey?.[0] ?? "";
                    provinceError.innerHTML =
                        data.errors?.provinceKey?.[0] ?? "";
                    cityError.innerHTML = data.errors?.cityKey?.[0] ?? "";
                    brgyError.innerHTML = data.errors?.barangayKey?.[0] ?? "";

                    imageFile.value = "";
                    document.getElementById("fileName").innerHTML =
                        "No choosen File";
                }
            })
            .catch((err) => {
                // console.error('Fetch error:', err);
            });
    });
}

// for update and delete
document.querySelectorAll(".status-btn").forEach((button) => {
    button.addEventListener("click", async function () {
        const userId = this.getAttribute("data-id");
        const decision = this.getAttribute("data-decision");

        const response = await fetch(`/update/status/${userId}/${decision}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        if (!response.ok) {
            console.log("Error status:", response.status);
        }

        button.closest("tr").remove();
    });
});

// show the puroks for the assigned area
const puroks = async function () {
    const dropdown = document.getElementById("assigned_area");

    const occupiedAreas = JSON.parse(dropdown.dataset.occupiedAreas);

    try {
        const response = await fetch("/showBrgyUnit");
        const brgyData = await response.json();

        brgyData.forEach((element) => {
            let inUse = "";
            let inUseText = "";

            inUse = occupiedAreas.includes(Number(element.id))
                ? "disabled"
                : "";
            inUseText = occupiedAreas.includes(Number(element.id))
                ? "(assigned to other)"
                : "";
            dropdown.innerHTML += `<option value="${element.id}"  ${inUse}>${element.brgy_unit}  ${inUseText}</option>`;
        });
    } catch (error) {
        console.log("Errors", error);
    }
};
puroks();
// add health workers

const addHealthWorkerSubmitBTN = document.getElementById("add-Health-worker");

addHealthWorkerSubmitBTN.addEventListener("click", async (e) => {
    e.preventDefault();

    // create a form
    let form = document.getElementById("add-health-worker-form");
    let formData = new FormData(form);

    // for (let [key, value] of formData.entries()) {
    //     console.log(`${key}: ${value}`);
    // }

    try {
        // errors container

        const fname_error = document.querySelector(".fname-error");
        const middle_initial_error = document.querySelector(
            ".middle-initial-error"
        );
        const lname_error = document.querySelector(".lname-error");
        const email_error = document.querySelector(".email-error");
        const password_error = document.querySelector(".password-error");
        const assigned_area_error = document.querySelector(
            ".assigned-area-error"
        );
        // const recovery_question_error = document.querySelector('.recovery-question-error');
        // const recovery_answer_error = document.querySelector('.recovery-answer-error');

        const response = await fetch("/add-health-worker-account", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                Accept: "application/json",
            },
            body: formData,
        });

        // get the response in json format
        const data = await response.json();

        if (response.ok) {
            const errorMessages = document.querySelectorAll(
                ".add-healthworker-error"
            );
            // remove all error messages after submission
            errorMessages.forEach((error) => (error.innerHTML = ""));
            Swal.fire({
                title: "Add New Health Worker",
                text: "Health Worker Account is successfully added",
                icon: "success",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            document.getElementById("add-health-worker-form").reset();
        } else {
            // reset the error first
            const errorMessages = document.querySelectorAll(
                ".add-healthworker-error"
            );
            // remove all error messages after submission
            errorMessages.forEach((error) => (error.innerHTML = ""));
            // set the errors
            Object.entries(data.errors).forEach(([key, value]) => {
                if (document.getElementById(`${key}_error`)) {
                    document.getElementById(`${key}_error`).innerHTML = value;
                }
            });
            Swal.fire({
                title: "Add New Health Worker",
                text: "Invalid input value",
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
        }
    } catch (error) {
        console.log("Error status:", error);
    }
});
// add health worker modal reset
const addHealthWorkerBtn = document.getElementById("add-health-worker-modal");

addHealthWorkerBtn.addEventListener("click", () => {
    // get the modal form
    const modalForm = document.getElementById("add-health-worker-form");
    // reset first
    modalForm.reset();

    const fname_error = document.querySelector(".fname-error");
    const middle_initial_error = document.querySelector(
        ".middle-initial-error"
    );
    const lname_error = document.querySelector(".lname-error");
    const email_error = document.querySelector(".email-error");
    const password_error = document.querySelector(".password-error");
    const assigned_area_error = document.querySelector(".assigned-area-error");

    // set the errors to empty for reset

    const errors = document.querySelectorAll(".add-healthworker-error");

    if (errors) {
        errors.forEach((error) => {
            error.innerHTML = "";
        });
    }
});

// age
const dob = document.getElementById("birthdate");
const age = document.getElementById("age");
const hiddenAge = document.getElementById("hiddenAge");

if (dob && age && hiddenAge) {
    automateAge(dob, age, hiddenAge);
}

// Reset password
const resetPasswordElement = document.getElementById("reset_password");

if (resetPasswordElement) {
    resetPasswordElement.addEventListener("click", (e) => {
        const id = e.target.dataset.id;

        const profileModalEl = document.getElementById("profileModal");
        const profileModal =
            bootstrap.Modal.getInstance(profileModalEl) ||
            new bootstrap.Modal(profileModalEl);

        profileModal.hide();

        if (!id) {
            Swal.fire({
                title: "Health worker ID not found",
                text: "This user has no data.",
                icon: "warning",
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: "Ok",
            });
        } else {
            Swal.fire({
                title: "Reset Password",
                text: `Are you sure to reset the password?`,
                icon: "question",
                confirmButtonColor: "#198754",
                showCancelButton: true,
                confirmButtonText: "Yes, show Password Here!",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    resetPasswordManually(id, "/health-worker/reset-password/");
                }
            });
        }
    });
}
