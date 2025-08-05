import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    // record delete
    const records = document.querySelectorAll(".delete-record-icon");

    records.forEach((icon) => {
        icon.addEventListener("click", async (e) => {
            e.preventDefault();
            const patientId = icon.dataset.bsPatientId;
            console.log(patientId);
            Swal.fire({
                title: "Are you sure?",
                text: "This will permanently remove the user.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const response = await fetch(
                        `/patient-record/vaccination/delete/${patientId}`,
                        {
                            method: "DELETE",
                            headers: {
                                "Content-type": "Application/json",
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content,
                            },
                        }
                    );
                    const data = await response.json();

                    if (response.ok) {
                        icon.closest("tr").remove();
                        Swal.fire({
                            title: "Deletion",
                            text: data.message,
                            icon: "success",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "OK",
                        });
                    } else {
                         Swal.fire({
                             title: "Deletion",
                             text: data.message,
                             icon: "error",
                             confirmButtonColor: "#3085d6",
                             confirmButtonText: "OK",
                         });
                    }
                }
            });
        });
    });
});
