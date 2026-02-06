const eyeIcon = document.getElementById("eye-icon");
const password = document.getElementById("password");
const retypeEyeIcon = document.getElementById("Retype-eye-icon");
const retypePassword = document.getElementById("re-type-pass");

function passwordToggle(eyeIcon, passwordInput) {
    if (!eyeIcon || !passwordInput) {
        console.warn("passwordToggle: eyeIcon or passwordInput is null");
        return;
    }

    eyeIcon.addEventListener("click", () => {
        const isPassword = passwordInput.type === "password";
        passwordInput.type = isPassword ? "text" : "password";

        // Remove querySelector since eyeIcon IS the <i> element
        if (isPassword) {
            // Password is now visible, show eye-slash
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            // Password is now hidden, show eye
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    });
}

passwordToggle(eyeIcon, password);
passwordToggle(retypeEyeIcon, retypePassword);
