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

document.addEventListener('DOMContentLoaded', function (){
    fetch('/nurseDept')
        .then(response => response.json())
        .then(data => {
            let dropdown = document.getElementById('role');

            console.log(data);
            data.forEach(item => {
                let option = document.createElement('option');
                option.value = item.id;
                option.text = item.department;
                dropdown.appendChild(option);
            });
        });
});