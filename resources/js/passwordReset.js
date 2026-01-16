export default async function resetPasswordManually(userId, endpoint) {
    const response = await fetch(`${endpoint}${userId}`, {
        headers: {
            method: "GET",
            accept: "application/json",
        },
    });

    const data = await response.json();

    if (response.ok) {
        Swal.fire({
            title: "Password Generated!",
            html: `
                            <div class="password-display">
                                <p><strong>User ID:</strong> ${userId}</p>
                                <p style="margin-top: 10px;">New temporary password:</p>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; border: 2px dashed #dee2e6;">
                                    <code id="newPassword" style="font-size: 20px; color: #e74c3c; font-weight: bold; letter-spacing: 2px;">
                                        ${data.newPassword}
                                    </code>
                                </div>
                                <button class="btn btn-primary btn-sm" id="copyBtn">
                                    <i class="fas fa-copy"></i> Copy Password
                                </button>
                                <div style="background: #fff3cd; padding: 12px; border-radius: 5px; margin-top: 15px; border-left: 4px solid #ffc107;">
                                    <p style="font-size: 13px; color: #856404; margin: 0;">
                                        ⚠️ <strong>Important:</strong> Please share this password securely with the user.
                                    </p>
                                </div>
                                <p style="font-size: 12px; color: #999; margin-top: 10px;">
                                    User must change this password on next login.
                                </p>
                            </div>
                        `,
            icon: "success",
            confirmButtonText: "Done",
            allowOutsideClick: false,
            width: "600px",
            didOpen: () => {
                const btn = document.getElementById("copyBtn");
                btn.addEventListener("click", copyPassword);
            },
        });
    } else {
        Swal.fire("Error", data.message || "Failed to reset password", "error");
    }
}

export function copyPassword() {
    const password = document.getElementById("newPassword").textContent.trim();
    navigator.clipboard.writeText(password).then(() => {
        const btn = document.getElementById("copyBtn");
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.replace("btn-primary", "btn-success");
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-copy"></i> Copy Password';
            btn.classList.replace("btn-success", "btn-primary");
        }, 2000);
    });
}
