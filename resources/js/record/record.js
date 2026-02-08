import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    console.log("Delete handler loaded"); // Debug log

    // Universal delete handler using event delegation
    document.addEventListener("click", async (e) => {
        // Find the closest delete-record-icon (works even if SVG/path is clicked)
        const deleteIcon = e.target.closest(".delete-record-icon");

        if (!deleteIcon) return;

        e.preventDefault();
        e.stopPropagation();

        console.log("Delete icon clicked"); // Debug log

        const patientId = deleteIcon.dataset.bsPatientId;
        const recordType = deleteIcon.dataset.recordType;

        console.log("Patient ID:", patientId, "Record Type:", recordType); // Debug log

        if (!recordType || !patientId) {
            console.error("Missing recordType or patientId");
            return;
        }

        // Format record type for display
        const displayType = recordType
            .split("-")
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" ");

        // Map database type to route type
        const routeTypeMap = {
            vaccination: "vaccination",
            prenatal: "prenatal",
            "senior-citizen": "seniorCitizen",
            "tb-dots": "tbDots",
            "family-planning": "family-planning",
            seniorCitizen: "seniorCitizen",
            tbDots: "tbDots",
            familyPlanning: "family-planning",
        };

        const routeType = routeTypeMap[recordType] || recordType;

        console.log("Showing SweetAlert..."); // Debug log

        const result = await Swal.fire({
            title: "Are you sure?",
            html: `This <strong>${displayType}</strong> record will be moved to archived status.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!",
            cancelButtonText: "Cancel",
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(
                    `/patient-record/${routeType}/delete/${patientId}`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]',
                            ).content,
                        },
                    },
                );

                const data = await response.json();

                if (response.ok) {
                    deleteIcon.closest("tr").remove();

                    await Swal.fire({
                        title: "Archived!",
                        text:
                            data.message ||
                            "Record has been archived successfully.",
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK",
                        timer: 2000,
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data.message || "Failed to archive record.",
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK",
                    });
                }
            } catch (error) {
                console.error("Delete error:", error);
                Swal.fire({
                    title: "Error",
                    text: "Something went wrong. Please try again.",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });
            }
        }
    });

    // Keep your existing handlers for specific sections
    const recordTypes = [
        "vaccination",
        "prenatal",
        "seniorCitizen",
        "tbDots",
        "familyPlanning",
    ];

    recordTypes.forEach((recordType) => {
        const deleteIcons = document.querySelectorAll(
            `.delete-record-icon-${recordType}`,
        );

        deleteIcons.forEach((icon) => {
            icon.addEventListener("click", async (e) => {
                e.preventDefault();
                const patientId = icon.dataset.bsPatientId;

                const result = await Swal.fire({
                    title: "Are you sure?",
                    text: "This patient record will be moved to archived status.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, archive it!",
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(
                            `/patient-record/${recordType}/delete/${patientId}`,
                            {
                                method: "POST",
                                headers: {
                                    "Content-type": "Application/json",
                                    "X-CSRF-TOKEN": document.querySelector(
                                        'meta[name="csrf-token"]',
                                    ).content,
                                },
                            },
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
                    } catch (error) {
                        console.error("Error:", error);
                    }
                }
            });
        });
    });
});
