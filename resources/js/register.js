window.showField = function(role) {
      // Hide all role-specific sections first
    document.getElementById("nurse-fields").style.display = "none";
    document.getElementById("staff-fields").style.display = "none";
    
    switch(role){
        case 'nurse':
            document.getElementById("nurse-fields").style.display = "block";
            break;
        case 'staff':
            document.getElementById("staff-fields").style.display = "block";
            break;

        default:
            document.getElementById("nurse-fields").style.display = "none";
            document.getElementById("staff-fields").style.display = "none";
    }
};

// handle the show of staff field
document.getElementById("healthWorker").addEventListener('click', () => {
    document.getElementById("staff-fields").classList.replace('d-none', 'd-block');
    document.getElementById("patient_type_con").classList.replace('d-block', 'd-none');
    document.getElementById("assigned_area").setAttribute("required", true);
    document.getElementById("patient_type").removeAttribute("required");
})
document.getElementById("patient").addEventListener('click', () => {
    document.getElementById("staff-fields").classList.replace('d-block', 'd-none');
    document.getElementById("patient_type_con").classList.replace('d-none', 'd-block');
    document.getElementById("assigned_area").removeAttribute("required");
    document.getElementById("patient_type").setAttribute("required", true);
})



document.addEventListener('DOMContentLoaded', function (){
    fetch('/showBrgyUnit')
        .then(response => response.json())
        .then(data => {
            let dropdown = document.getElementById('assigned_area');

            console.log(data);
            data.forEach(item => {
                let option = document.createElement('option');
                option.value = item.id;
                option.text = item.brgy_unit;
                dropdown.appendChild(option);
            });
        });
});

// nurse dept