import Swal from "sweetalert2";

const saveBtn = document.getElementById("add-check-up-save-btn");


saveBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    const id = saveBtn.dataset.medicalId;

    const form = document.getElementById("add-check-up-form");
    const formData = new FormData(form);

    const response = await fetch(`/patient-record/add/check-up/tb-dots/${id}`, {
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
            text: errorMessage,
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        Swal.fire({
            title: "Update",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

// -------------------------------- view the checkup information ------------------------------------

const viewCheckupBtn = document.querySelectorAll(".view-check-up");


viewCheckupBtn.forEach(btn => {
    btn.addEventListener('click', async () => {
        const id = btn.dataset.caseId;

        const response = await fetch(`/patient-record/view-check-up/tb-dots/${id}`);

        const data = await response.json();
        if (!response.ok) {
            console.log(data.errors);
        } else {
            
            Object.entries(data.checkUpInfo).forEach(([key, value]) => {
                if (document.getElementById(`view_checkup_${key}`)) {
                    document.getElementById(`view_checkup_${key}`).innerHTML = value??'N/A';
                }
            });
        }
    })
})


const editCheckupBtn = document.querySelectorAll(".edit-check-up");
const editSaveBtn = document.getElementById("edit-checkup-save-btn");


editCheckupBtn.forEach((btn) => {
    btn.addEventListener("click", async () => {
        console.log('working');
        const id = btn.dataset.caseId;
        editSaveBtn.dataset.caseId = id;

        const response = await fetch(
            `/patient-record/view-check-up/tb-dots/${id}`
        );

        const data = await response.json();
        if (!response.ok) {
            console.log(data.errors);
        } else {
            Object.entries(data.checkUpInfo).forEach(([key, value]) => {
                if (document.getElementById(`edit_checkup_${key}`)) {
                    document.getElementById(`edit_checkup_${key}`).value =
                        value ?? "";
                }
            });
        }
    });
});

// update the data

editSaveBtn.addEventListener('click', async (e) => {
    e.preventDefault();

    const id = editSaveBtn.dataset.caseId;
    const form = document.getElementById("edit-checkup-form");
    const formData = new FormData(form);

    const response = await fetch(`/patient-record/tb-dots/update-checkup/${id}`, {
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
            text: errorMessage,
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    } else {
        Swal.fire({
            title: "Update",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
});

