import Swal from "sweetalert2";

document.addEventListener("click", async (e) => {
    const markAsReadBtn = e.target.closest(".notification-mark-as-read-btn");
    if (!markAsReadBtn) return;

    const notificationId = Number(markAsReadBtn.dataset.notificationId);

    if (!notificationId || isNaN(notificationId)) {
        Swal.fire({
            title: "Invalid Notification",
            text: "Invalid or missing notification ID.",
            icon: "error",
        });
        return;
    }

    try {
        const response = await fetch(
            `/notifications/${notificationId}/mark-read`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
            }
        );

        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));

            Swal.fire({
                title: "Failed",
                text:
                    errorData.message || "Failed to mark notification as read.",
                icon: "error",
            });
            return;
        }

        // ✅ Success
        Swal.fire({
            title: "Success",
            text: "Notification marked as read.",
            icon: "success",
            timer: 1200,
            showConfirmButton: false,
        });

    
    } catch (error) {
        // ❌ Network error / server down
        Swal.fire({
            title: "Network Error",
            text: "Unable to connect to the server.",
            icon: "error",
        });

        console.error(error);
    }
});

// delete notification
document.addEventListener("click", async (e) => {
    const markAsReadBtn = e.target.closest(".notification-delete-btn");
    if (!markAsReadBtn) return;

    const notificationId = Number(markAsReadBtn.dataset.notificationId);

    if (!notificationId || isNaN(notificationId)) {
        Swal.fire({
            title: "Invalid Notification",
            text: "Invalid or missing notification ID.",
            icon: "error",
        });
        return;
    }

    const result = await Swal.fire({
        title: "Are you sure?",
        text: "Are you sure to delete the notification?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it",
        cancelButtonText: "Cancel",
        reverseButtons: true,
    });

    // ❌ User cancelled
    if (!result.isConfirmed) return;


    try {
        const response = await fetch(`/notifications/${notificationId}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));

            Swal.fire({
                title: "Failed",
                text:
                    errorData.message || "Failed to delete notification.",
                icon: "error",
            });
            return;
        }

        // ✅ Success
        Swal.fire({
            title: "Success",
            text: "Notification Deleted Successfully.",
            icon: "success",
            timer: 1200,
            showConfirmButton: false,
        }).then(()=>{
            window.location.reload();
        });
    } catch (error) {
        // ❌ Network error / server down
        Swal.fire({
            title: "Network Error",
            text: "Unable to connect to the server.",
            icon: "error",
        });

        console.error(error);
    }
});

document.addEventListener("click", async (e) => {
    const markAsReadBtn = e.target.closest(".mark-all-as-read-btn");
    if (!markAsReadBtn) return;

    try {
        const response = await fetch(
            `/notifications/mark-all-read`,
            {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
            }
        );

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));

            Swal.fire({
                title: "Failed",
                text:
                    errorData.message || "Failed to mark notification as read.",
                icon: "error",
            });
            return;
        }

        // ✅ Success
        Swal.fire({
            title: "Success",
            text: "All Notification marked as read.",
            icon: "success",
            timer: 1200,
            showConfirmButton: false,
        }).then(() => {
             window.location.reload();
        });
    } catch (error) {
        // ❌ Network error / server down
        Swal.fire({
            title: "Network Error",
            text: "Unable to connect to the server.",
            icon: "error",
        });

        console.error(error);
    }
});

// delete all the notificaiton
document.addEventListener("click", async (e) => {
    const markAsReadBtn = e.target.closest(".delete-all-notification-record");
    if (!markAsReadBtn) return;

   
    const result = await Swal.fire({
        title: "Are you sure?",
        text: "Are you sure to delete all the notification?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it",
        cancelButtonText: "Cancel",
        reverseButtons: true,
    });

    // ❌ User cancelled
    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/notifications/delete-all-read`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));

            Swal.fire({
                title: "Failed",
                text: errorData.message || "Failed to delete notification.",
                icon: "error",
            });
            return;
        }

        // ✅ Success
        Swal.fire({
            title: "Success",
            text: "All Notification Deleted Successfully.",
            icon: "success",
            timer: 1200,
            showConfirmButton: false,
        }).then(() => {
            window.location.reload();
        });
    } catch (error) {
        // ❌ Network error / server down
        Swal.fire({
            title: "Network Error",
            text: "Unable to connect to the server.",
            icon: "error",
        });

        console.error(error);
    }
});

