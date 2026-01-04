import { error, trim } from "jquery";
import Swal from "sweetalert2";

const editIcon = document.getElementById("patient_profile_edit");
const submitBtn = document.getElementById("submit-btn");

editIcon.addEventListener("click", (e) => {
    e.preventDefault();
    const id = editIcon.dataset.id;
    // console.log(id);

    fetch(`/patient-profile-edit/${id}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
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
            const profileImg = document.getElementById("profile-image") ?? null;
            const fullname = document.getElementById("full_name");
            const fname = document.getElementById("first_name") ?? null;
            const lname = document.getElementById("last_name") ?? null;
            const mInitial = document.getElementById("middle_initial") ?? null;
            const age = document.getElementById("age")??null;
            const bday = document.getElementById("birthdate") ?? null;
            const contact = document.getElementById("contact_num")??null;
            const nationality = document.getElementById("nationality") ?? null;
            const username = document.getElementById("username") ?? null;
            const email = document.getElementById("email") ?? null;
            const blkNstreet =
                document.getElementById("update_blk_n_street") ?? null;
            const patient_purok_address = document.getElementById(
                "update_patient_purok_dropdown"
            );

            // address
            const region = document.getElementById("region");
            const province = document.getElementById("province");
            const city = document.getElementById("city");
            const brgy = document.getElementById("brgy");

            // initialize the additional input field for patient dashboard edit
            // vaccination
            const motherName = document.getElementById("mother_name");
            const fatherName = document.getElementById("father_name");
            const birthHeight = document.getElementById("vaccination_height");
            const birthWeight = document.getElementById("vaccination_weight");
            // prenatal
            const headOfFamily = document.getElementById("head_of_the_family");
            const bloodType = document.getElementById("blood_type");
            const religion = document.getElementById("religion");
            const philHealthYes = document.getElementById("philhealth_yes");
            const philHealthNo = document.getElementById("philhealth_no");
            const philhealthNumberPrenatal =
                document.getElementById("philhealth_number");
            // tb-dots
            const philhealthId = document.getElementById("philheath_id");
            // senior citizen
            const occupation = document.getElementById("occupation");
            const memberOfsss = document.querySelectorAll('input[name="SSS"]');
            // familyPlanning
            const philHealthNumber = document.getElementById("philhealth_no");
            const civil_status = document.getElementById("civil_status")??null;

            // Safe handling of patient data with fallback to user data
            const patient = data.response.patient;
            const user = data.response.user;




            // Safely construct full name with fallback
            if (patient?.full_name) {
                fullname.innerHTML = patient.full_name;
            } else {
                // Fallback to user data, handling null/undefined values
                const firstName = user?.first_name || "";
                const middleInitial = user?.middle_initial || "";
                const lastName = user?.last_name || "";
                fullname.innerHTML =
                    `${firstName} ${middleInitial} ${lastName}`.trim();
            }

            // input values
            const baseUrl = profileImg.dataset.baseUrl; // gets data-base-url
            profileImg.src =
                baseUrl + data.response.user.profile_image ??
                baseUrl + data.response.patient.profile_image;
            // profileImg.src = `{{ asset('${data.response.profile_image}') }}`;
            // console.log(profileImg);
           
            fname.value = patient?.first_name || user?.first_name || "";
            lname.value = patient?.last_name || user?.last_name || "";
            mInitial.value =
                patient?.middle_initial || user?.middle_initial || "";
            if (age) age.value = data.response.patient?.age ?? "";
            
            // slice date
            function dateFormat(date) {
                if (!date) return "";
                const newDate = new Date(date);
                // Check if date is valid
                if (isNaN(newDate.getTime())) return "";
                return newDate.toISOString().split("T")[0];
            }

            bday.value = dateFormat(
                data.response.patient?.date_of_birth ??
                    data.response.user.date_of_birth
            );
            contact.value = data.response.patient?.contact_number ?? data.response.user.contact_number ?? "";
            if (nationality) nationality.value = data.response.patient?.nationality ?? "";
            if (civil_status) civil_status.value = data.response.patient?.civil_status ?? '';

            username.value = data.response.user.username;
            email.value = data.response.user.email;

            // console.log(username.value);
            blkNstreet.value =
                data.response.patient_address.house_number +
                (data.response.patient_address.street
                    ? ", " + data.response.patient_address.street
                    : "");

            if (data.response.typeOfPatient != null) {
                if (data.response.typeOfPatient == "vaccination") {
                    motherName.value =
                        data.response.medicalRecord.mother_name ?? "";
                    fatherName.value =
                        data.response.medicalRecord.father_name ?? "";
                    birthHeight.value =
                        data.response.medicalRecord.birth_height ?? "";
                    birthWeight.value =
                        data.response.medicalRecord.birth_weight ?? "";
                } else if (data.response.typeOfPatient == "prenatal") {
                    headOfFamily.value =
                        data.response.medicalRecord.family_head_name ?? "";
                    bloodType.value =
                        data.response.medicalRecord.blood_type ?? "";
                    religion.value = data.response.medicalRecord.religion ?? "";

                    philHealthNo.checked =
                        data.response.medicalRecord.philHealth_number == "no";
                    philHealthYes.checked =
                        data.response.medicalRecord.philHealth_number != null &&
                        data.response.medicalRecord.philHealth_number != "no";
                    philhealthNumberPrenatal.value =
                        data.response.medicalRecord.philHealth_number == "no"
                            ? ""
                            : data.response.medicalRecord.philHealth_number;
                } else if (data.response.typeOfPatient == "tb-dots") {
                    philhealthId.value =
                        data.response.medicalRecord.philhealth_id_no ?? "";
                } else if (data.response.typeOfPatient == "senior-citizen") {
                    occupation.value =
                        data.response.medicalRecord.occupation ?? "";
                    religion.value = data.response.medicalRecord.religion ?? "";
                    memberOfsss.forEach((radio) => {
                        radio.checked =
                            radio.value == data.response.medicalRecord.SSS;
                    });
                } else if (data.response.typeOfPatient == "family-planning") {
                    occupation.value =
                        data.response.medicalRecord.occupation ?? "";
                    religion.value = data.response.medicalRecord.religion ?? "";
                    philHealthNo.value =
                        data.response.medicalRecord.philhealth_no ?? "";
                }
            }

            // submit btn data-user value
            submitBtn.dataset.user = data.response.user.id;

            // select purok
            const puroks = async function () {
                // const dropdown = document.getElementById('update_assigned_area');

                // const occupiedAreas = JSON.parse(dropdown.dataset.occupiedAreas);

                try {
                    const response = await fetch("/showBrgyUnit");
                    const brgyData = await response.json();

                    brgyData.forEach((element) => {
                        let inUse = "";
                        let inUseText = "";

                        const selected =
                            element.brgy_unit ==
                            data.response.patient_address.purok
                                ? "selected"
                                : "";
                        // add other option
                        const currentlyUse =
                            element.id == data.response.assigned_area_id
                                ? "(currently)"
                                : "";
                        patient_purok_address.innerHTML += `<option value="${element.brgy_unit}" ${selected} >${element.brgy_unit} </option>`;
                    });
                } catch (error) {
                    console.log("Errors", error);
                }
            };

            puroks();
        })
        .catch((error) => {
            console.error("Fetch error: ", error);
        });
});

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
            document.getElementById("fileName").innerHTML = "No chosen File";
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