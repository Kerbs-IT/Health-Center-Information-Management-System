
import $ from 'jquery';


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
    document.getElementById("patient_purok_dropdown").removeAttribute("required");
})
document.getElementById("patient").addEventListener('click', () => {
    document.getElementById("staff-fields").classList.replace('d-block', 'd-none');
    document.getElementById("patient_type_con").classList.replace('d-none', 'd-block');
    document.getElementById("assigned_area").removeAttribute("required");
    document.getElementById("patient_purok_dropdown").setAttribute("required", true);
})



document.addEventListener('DOMContentLoaded', function () {
    
    const puroks = async function () {
        const dropdown = document.getElementById('assigned_area');
    
        const occupiedAreas = JSON.parse(dropdown.dataset.occupiedAreas);
        
        try {
            const response = await fetch('/showBrgyUnit');
            const brgyData = await response.json();
    
    
            brgyData.forEach(element => {
                let inUse = '';
                let inUseText = '';
    
               
                inUse = occupiedAreas.includes(Number(element.id)) ? 'disabled' : '';
                inUseText = occupiedAreas.includes(Number(element.id)) ? '(assigned to other)' : '';
                dropdown.innerHTML += `<option value="${element.id}"  ${inUse}>${element.brgy_unit}  ${inUseText}</option>`;
    
            });
            
        } catch (error) {
            console.log('Errors', error);
        }
        
    };
    
    puroks();
    
    fetch('/showBrgyUnit')
        .then(response => response.json())
        .then(data => {
            let dropdown = document.getElementById('patient_purok_dropdown');

            // console.log(data);
            data.forEach(item => {
                let option = document.createElement('option');
                option.value = item.brgy_unit;
                option.text = item.brgy_unit;
                dropdown.appendChild(option);
            });
        });
});

// nurse dept