const eyeIcon = document.getElementById('eye-icon');
        const password = document.getElementById('password');

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

passwordToggle(eyeIcon,password);        