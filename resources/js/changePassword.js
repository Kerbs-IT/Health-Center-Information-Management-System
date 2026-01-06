// get the submit btn
const btn = document.getElementById("change-pass-submit-btn");

btn.addEventListener("click", async (e) => {
    e.preventDefault();

   try {
       // Clear previous errors
       document.getElementById("current_password_error").textContent = "";
       document.getElementById("new_password_error").textContent = "";

       const form = document.getElementById("change-pass-form");
       const formData = new FormData(form);
       const response = await fetch("/change-pass/submit", {
           method: "POST",
           headers: {
               "X-CSRF-TOKEN": document
                   .querySelector('meta[name="csrf-token"]')
                   .getAttribute("content"),
               Accept: "application/json",
           },
           body: formData,
       });

       const data = await response.json();

       if (!response.ok) {
           // Handle validation errors
           if (data.errors) {
               // Display field-specific errors
               if (data.errors.current_password) {
                   document.getElementById(
                       "current_password_error"
                   ).textContent = data.errors.current_password[0];
               }
               if (data.errors.new_password) {
                   document.getElementById("new_password_error").textContent =
                       data.errors.new_password[0];
               }

             Swal.fire({
                 title: "Validation Error",
                 text: Object.values(data.errors).flat().join("\n"),
                 icon: "error",
                 confirmButtonColor: "#d33",
                 confirmButtonText: "OK",
             });
           } else {
               // General error
               Swal.fire({
                   title: "Error",
                   text:
                       data.message ||
                       "There's an error in updating the password",
                   icon: "error",
                   confirmButtonColor: "#d33",
                   confirmButtonText: "OK",
               });
           }
       } else {
           // Success
           Swal.fire({
               title: "Success!",
               text: data.message || "Password updated successfully",
               icon: "success",
               confirmButtonColor: "#3085d6",
               confirmButtonText: "OK",
           }).then(() => {
               // Optional: Clear the form after success
               form.reset();
           });
       }
   } catch (error) {
       console.error("Error in updating password:", error);
       Swal.fire({
           title: "Error",
           text: "An unexpected error occurred. Please try again.",
           icon: "error",
           confirmButtonColor: "#d33",
           confirmButtonText: "OK",
       });
   }
});

// inputs
const currentPassword= document.getElementById("current_password");
const newPassword= document.getElementById("new_password");
const newPasswordConfirmation= document.getElementById("new_password_confirmation");
// eyeIcon
const currentPasswordEyeIcon = document.getElementById("current_password_eye_icon");
const newPasswordEyeIcon = document.getElementById("new_password_eye_icon");
const newPasswordConfirmationEyeIcon = document.getElementById("new_password_confirmation_eye_icon");

// call the function
passwordToggle(currentPasswordEyeIcon, currentPassword);
passwordToggle(newPasswordEyeIcon, newPassword);
passwordToggle(newPasswordConfirmationEyeIcon, newPasswordConfirmation);

// eye icon toggle
function passwordToggle(eyeIcon, passwordInput) {
    eyeIcon.addEventListener("click", () => {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    });
}


