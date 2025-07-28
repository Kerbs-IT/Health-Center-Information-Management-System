const eyeIcon = document.getElementById('eye-icon');
const password = document.getElementById('password');
const retypeEyeIcon = document.getElementById("Retype-eye-icon");
const retypePassword = document.getElementById("re-type-pass");

 function passwordToggle(eyeIcon,passwordInput){
    eyeIcon.addEventListener('mousedown', () =>{
        passwordInput.type = 'text';
    })
    eyeIcon.addEventListener('mouseup', () =>{
        passwordInput.type = 'password';
    })

    eyeIcon.addEventListener('mouseout',() =>{
        passwordInput.type = 'password';
    })

}

passwordToggle(eyeIcon, password);       
passwordToggle(retypeEyeIcon,retypePassword); 