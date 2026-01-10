import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    // record delete
    const vaccinationRecords = document.querySelectorAll(
        ".delete-record-icon-vaccination"
    );

    vaccinationRecords.forEach((icon) => {
        icon.addEventListener("click", async (e) => {
            e.preventDefault();
            const patientId = icon.dataset.bsPatientId;
            // console.log(patientId);
            Swal.fire({
                title: "Are you sure?",
                text: "This patient record will be moved to archived status.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, archive it!",
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

    // prenatal
    const prenatalRecords = document.querySelectorAll(
        ".delete-record-icon-prenatal"
    );

    prenatalRecords.forEach((icon) => {
        icon.addEventListener("click", async (e) => {
            e.preventDefault();
            const patientId = icon.dataset.bsPatientId;
            // console.log(patientId);
            Swal.fire({
                title: "Are you sure?",
                text: "This patient record will be moved to archived status.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, archive it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const response = await fetch(
                        `/patient-record/prenatal/delete/${patientId}`,
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

    // seniorCitizen
    const seniorCitizenRecords = document.querySelectorAll(
        ".delete-record-icon-seniorCitizen"
    );

    seniorCitizenRecords.forEach((icon) => {
        icon.addEventListener("click", async (e) => {
            e.preventDefault();
            const patientId = icon.dataset.bsPatientId;
            // console.log(patientId);
            Swal.fire({
                title: "Are you sure?",
                text: "This patient record will be moved to archived status.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, archive it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const response = await fetch(
                        `/patient-record/seniorCitizen/delete/${patientId}`,
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

    // tbDots
    const tbDotsRecords = document.querySelectorAll(
        ".delete-record-icon-tbDots"
    );

    tbDotsRecords.forEach((icon) => {
        icon.addEventListener("click", async (e) => {
            e.preventDefault();
            const patientId = icon.dataset.bsPatientId;
            // console.log(patientId);
            Swal.fire({
                title: "Are you sure?",
                text: "This patient record will be moved to archived status.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, archived it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const response = await fetch(
                        `/patient-record/tbDots/delete/${patientId}`,
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

    // familyPlanning
    const familyPlanningRecords = document.querySelectorAll(
        ".delete-record-icon-familyPlanning"
    );

    familyPlanningRecords.forEach((icon) => {
        icon.addEventListener("click", async (e) => {
            e.preventDefault();
            const patientId = icon.dataset.bsPatientId;
            // console.log(patientId);
            Swal.fire({
                title: "Are you sure?",
                text: "This patient record will be moved to archived status.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, archive it!",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const response = await fetch(
                        `/patient-record/family-planning/delete/${patientId}`,
                        {
                            method: "POST",
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
