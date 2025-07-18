import { error } from 'jquery';
import Swal from 'sweetalert2';

fetch('/showBrgyUnit')
.then(response => response.json())
.then(data => {
    let dropdown = document.getElementById('patient_purok_dropdown');

    // console.log(data);
    data.forEach(item => {
        let option = document.createElement('option');
        option.value = item.brgy_unit;
        option.text = item.brgy_unit;
        dropdown.appendChild(option);
    });
});

// add the patient account
const addPatientSubmitBtn = document.getElementById('add-patient-submit-btn');

addPatientSubmitBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    const formDataElement = document.getElementById('add-patient-form');
    const formData = new FormData(formDataElement);

    try {
         // errors container
        const username_error = document.querySelector('.username-error');
        const fname_error = document.querySelector('.fname-error');
        const middle_initial_error = document.querySelector('.middle-initial-error');
        const lname_error = document.querySelector('.lname-error');
        const email_error = document.querySelector('.email-error');
        const password_error = document.querySelector('.password-error');
        const blk_n_street_error = document.querySelector('.blk-n-street-error');
        const purok_dropdown_error = document.querySelector('.purok-dropdown-error');
        const recovery_question_error = document.querySelector('.recovery-question-error');
        const recovery_answer_error = document.querySelector('.recovery-answer-error');

        const response = await fetch('/add-patient-account', {
            method: 'POST', // Yes, use POST
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })

        const result = await response.json();
        if (!response.ok) {
             // set the errors
            username_error.innerHTML = result.errors?.username?.[0] ?? '';
            fname_error.innerHTML = result.errors?.first_name?.[0] ?? '';
            middle_initial_error.innerHTML = result.errors?.middle_initial?.[0] ?? '';
            lname_error.innerHTML = result.errors?.last_name?.[0] ?? '';
            email_error.innerHTML = result.errors?.email?.[0] ?? '';
            password_error.innerHTML = result.errors?.password?.[0] ?? '';
            recovery_question_error.innerHTML = result.errors?.recovery_question?.[0] ?? '';
            recovery_answer_error.innerHTML = result.errors?.recovery_answer?.[0] ?? '';
            blk_n_street_error.innerHTML = result.errors?.blk_n_street?.[0] ?? '';
            purok_dropdown_error.innerHTML = result.errors?.patient_purok_dropdown?.[0] ?? '';

            Swal.fire({
                title: 'Update',
                text: "Invalid input value",
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        } else {

            formDataElement.reset();
            // make the errors gone
            username_error.innerHTML =  '';
            fname_error.innerHTML =  '';
            middle_initial_error.innerHTML = '';
            lname_error.innerHTML = '';
            email_error.innerHTML = '';
            password_error.innerHTML = '';
            recovery_question_error.innerHTML = '';
            recovery_answer_error.innerHTML = '';
            blk_n_street_error.innerHTML = '';
            purok_dropdown_error.innerHTML = '';
            Swal.fire({
                title: 'Creation',
                text: "Patient Account is successfully created",
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }
        
    } catch (error) {
        console.log(error);
    }
})

// import the address function

document.addEventListener('DOMContentLoaded', () => {
    const removeIcons = document.querySelectorAll('.remove-icon-con');

    removeIcons.forEach(icon => {
        icon.addEventListener('click', (e) => {
            e.preventDefault();
            const removeId = icon.dataset.id;
            console.log(removeId);

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently remove the user.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/delete-patient-account/${removeId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire('Deleted!', 'The user has been removed.', 'success');
                            icon.closest('tr').remove(); // remove row from table
                        } else {
                            Swal.fire('Error', 'Failed to delete user.', 'error');
                        }
                    });
                }
            });
        });
    });

    const editIcon = document.querySelectorAll('.edit-icon');
    const popUp = document.getElementById('pop-up');
    const cancelBtn = document.getElementById('cancel-btn');
    const submitBtn = document.getElementById('submit-btn');

    editIcon.forEach(icon => {
        icon.addEventListener('click', (e) =>{
            e.preventDefault();
            const id = icon.dataset.id;
            console.log(id);

            fetch(`/patient-account/get-patient-info/${id}`,{
                method:'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },

            }).then(response =>{
                if(response.ok){
                    return response.json();
                }
            }).then(data => {
                console.log(data);

                // reset first
                submitBtn.dataset.user = data.response.user_id;
                const profileImg = document.getElementById('profile-image');
                const fullname = document.getElementById('full_name');
                const fname = document.getElementById('first_name');
                const lname = document.getElementById('last_name');
                const mInitial = document.getElementById('middle_initial');
                const age = document.getElementById('age');
                const bday = document.getElementById('birthdate');
                const contact = document.getElementById('contact_num');
                const nationality = document.getElementById('nationality');
                const username = document.getElementById('username');
                const email = document.getElementById('email');
                const blkNstreet = document.getElementById('update_blk_n_street');
                const patient_purok_address = document.getElementById('update_patient_purok_dropdown');

                // address
                const region = document.getElementById('region');
                const province = document.getElementById('province');
                const city = document.getElementById('city');
                const brgy = document.getElementById('brgy');

              

                // input values
                const baseUrl = profileImg.dataset.baseUrl; // gets data-base-url
                profileImg.src = baseUrl + data.response.patient.profile_image;
                // profileImg.src = `{{ asset('${data.response.profile_image}') }}`;
                console.log(profileImg);
                fullname.innerHTML = data.response.patient.full_name;
                fname.value = data.response.patient.first_name;
                lname.value = data.response.patient.last_name;
                mInitial.value = data.response.patient.middle_initial;
                age.value = data.response.patient.age;
                bday.value = data.response.patient.date_of_birth;
                contact.value = data.response.patient.contact_number;
                nationality.value = data.response.patient.nationality;

                username.value = data.response.user.username;
                email.value = data.response.user.email;

                console.log(username.value);
                blkNstreet.value = data.response.patient_address.house_number 
                + (data.response.patient_address.street ? ', ' + data.response.patient_address.street : '');
                
                // submit btn data-user value
                submitBtn.dataset.user = data.response.user.id;

                // select purok
                const puroks = async function () {
                    // const dropdown = document.getElementById('update_assigned_area');

                    // const occupiedAreas = JSON.parse(dropdown.dataset.occupiedAreas);
                    
                    try {
                        const response = await fetch('/showBrgyUnit');
                        const brgyData = await response.json();

                
                        brgyData.forEach(element => {
                            let inUse = '';
                            let inUseText = '';

                            const selected = element.brgy_unit == data.response.patient_address.purok? 'selected' : '';
                            // add other option
                            const currentlyUse = element.id == data.response.assigned_area_id ? '(currently)' : ''
                            patient_purok_address.innerHTML += `<option value="${element.brgy_unit}" ${selected} >${element.brgy_unit} </option>`;
                
                        });
                        
                    } catch (error) {
                        console.log('Errors', error);
                    }
                    
                };
                
                puroks();
                
                

            }).catch( error =>{
                console.error('Fetch error: ', error);
            })

            popUp.classList.remove('d-none');
            popUp.classList.add('d-flex');
        })
    });

    cancelBtn.addEventListener('click', (e) =>{
        e.preventDefault();
        popUp.classList.add('d-none');
        popUp.classList.remove('d-flex');
    })


    submitBtn.addEventListener('click', e =>{
        e.preventDefault();
        const userId = submitBtn.dataset.user;

        let form = document.getElementById('profile-form');
        let formData = new FormData(form);

        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        formData.append('_method', 'PUT'); // Laravel will detect this
        
        fetch(`/update-patient-account-information/${userId}`, {
            method: 'POST', // Yes, use POST
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json(); // 👈 parse the body
            return { ok: response.ok, data };   // 👈 pass both ok status and data
        })
        .then(({ ok, data }) => {
            const imgError = document.getElementById('image-error');
            const fnameError = document.getElementById('fname-error');
            const middleError = document.getElementById('middle-initial-error');
            const lnameError = document.getElementById('lname-error');
            const ageError = document.getElementById('age-error');
            const birthdateError = document.getElementById('birthdate-error');
            const sexError = document.getElementById('sex-error');
            const civilStatusError = document.getElementById('civil-status-error');
            const contactError = document.getElementById('contact-error');
            const nationalityError = document.getElementById('nationality-error');
            const usernameError = document.getElementById('username-error');
            const emailError = document.getElementById('email-error');
            const streetError = document.getElementById('street-error');
            const postalError = document.getElementById('postal-error');
            const regionError = document.getElementById('region-error');
            const provinceError = document.getElementById('province-error');
            const cityError = document.getElementById('city-error');
            const brgyError = document.getElementById('brgy-error');
            const imageFile = document.getElementById('fileInput');

            if (ok) {
                Swal.fire({
                    title: 'Update',
                    text: "Health Worker Information is successfully updated",
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });

                // Optional: clear error messages on success
                imgError.innerHTML = '';
                middleError.innerHTML = '';
                // clear others as needed...

            } else {
                Swal.fire({
                    title: 'Update',
                    text: "Invalid input value",
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });

                // Fill error fields
                imgError.innerHTML = data.errors?.profile_image?.[0] ?? '';
                middleError.innerHTML = data.errors?.middle_initial?.[0] ?? '';
                fnameError.innerHTML = data.errors?.first_name?.[0] ?? '';
                lnameError.innerHTML = data.errors?.last_name?.[0] ?? '';
                ageError.innerHTML = data.errors?.age?.[0] ?? '';
                birthdateError.innerHTML = data.errors?.date_of_birth?.[0] ?? '';
                sexError.innerHTML = data.errors?.sex?.[0] ?? '';
                civilStatusError.innerHTML = data.errors?.civil_status?.[0] ?? '';
                contactError.innerHTML = data.errors?.contact_number?.[0] ?? '';
                nationalityError.innerHTML = data.errors?.nationality?.[0] ?? '';
                usernameError.innerHTML = data.errors?.username?.[0] ?? '';
                emailError.innerHTML = data.errors?.email?.[0] ?? '';
                streetError.innerHTML = data.errors?.street?.[0] ?? '';
                postalError.innerHTML = data.errors?.postal_code?.[0] ?? '';
                regionError.innerHTML = data.errors?.regionKey?.[0] ?? '';
                provinceError.innerHTML = data.errors?.provinceKey?.[0] ?? '';
                cityError.innerHTML = data.errors?.cityKey?.[0] ?? '';
                brgyError.innerHTML = data.errors?.barangayKey?.[0] ?? '';

                imageFile.value = '';
                document.getElementById('fileName').innerHTML = 'No choosen File';
            }
        })
        .catch(err => {
            // console.error('Fetch error:', err);
        });



    })
    
});

// for update and delete
document.querySelectorAll('.status-btn').forEach(button => {
    button.addEventListener('click', async  function () {
        const userId = this.getAttribute('data-id');
        const decision = this.getAttribute('data-decision');

       const response = await fetch(`/update/status/${userId}/${decision}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
       })
       if (!response.ok) {
           console.log('Error status:', response.status);
       }
        

        button.closest('tr').remove();
    });
});

