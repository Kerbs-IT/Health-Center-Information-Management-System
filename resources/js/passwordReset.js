export default async function resetPasswordManually(userId, endpoint) {
    // Show loading
    Swal.fire({
        title: "Processing...",
        text: "Resetting password and sending email",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    try {
        const response = await fetch(`${endpoint}${userId}`, {
            method: "GET",
            headers: {
                Accept: "application/json",
            },
        });

        const data = await response.json();

        if (response.ok && data.success) {
            Swal.fire({
                title: "Password Reset Successful!",
                html: `
                    <div style="text-align: left; padding: 20px;">
                        <p><i class="fas fa-check-circle" style="color: #198754;"></i> Password has been reset successfully</p>
                        <p><i class="fas fa-envelope" style="color: #0d6efd;"></i> New password sent to patient email account</p>
                        ${data.email_sent === false ? '<p style="color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Note: Email delivery failed. Please contact user directly.</p>' : ""}
                        <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 15px; border-left: 4px solid #0d6efd;">
                            <p style="font-size: 13px; color: #004085; margin: 0;">
                                📧 The patient will receive their new temporary password via email and should change it upon next login.
                            </p>
                        </div>
                    </div>
                `,
                icon: "success",
                confirmButtonText: "Done",
                confirmButtonColor: "#198754",
            });
        } else {
            throw new Error(data.message || "Failed to reset password");
        }
    } catch (error) {
        Swal.fire({
            title: "Error",
            text:
                error.message || "Failed to reset password. Please try again.",
            icon: "error",
            confirmButtonText: "Ok",
        });
    }
}

// export function copyPassword() {
//     const password = document.getElementById("newPassword").textContent.trim();
//     navigator.clipboard.writeText(password).then(() => {
//         const btn = document.getElementById("copyBtn");
//         btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
//         btn.classList.replace("btn-primary", "btn-success");
//         setTimeout(() => {
//             btn.innerHTML = '<i class="fas fa-copy"></i> Copy Password';
//             btn.classList.replace("btn-success", "btn-primary");
//         }, 2000);
//     });
// }
