const eyeIcon = document.getElementById('eye-icon');
const password = document.getElementById('password');
const retypeEyeIcon = document.getElementById("Retype-eye-icon");
const retypePassword = document.getElementById("re-type-pass");

function passwordToggle(eyeIcon, passwordInput) {
    if (!eyeIcon || !passwordInput) {
        // console.warn("passwordToggle: eyeIcon or passwordInput is null");
        return;
    }

    eyeIcon.addEventListener("click", () => {
        const isPassword = passwordInput.type === "password";
        passwordInput.type = isPassword ? "text" : "password";

        const icon = eyeIcon.querySelector("i");
        if (icon) {
            icon.classList.toggle("fa-eye");
            icon.classList.toggle("fa-eye-slash");
        }
    });
}

passwordToggle(eyeIcon, password);       
passwordToggle(retypeEyeIcon,retypePassword); 