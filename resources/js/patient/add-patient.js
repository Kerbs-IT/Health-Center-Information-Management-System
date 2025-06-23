import Swal from "sweetalert2";

document.addEventListener('DOMContentLoaded', () =>{
    let currentStep = 1;

    function showStep(step){
        const selected = document.getElementById('type-of-patient').value;
        if(currentStep == 1){
            document.getElementById('head-text').innerHTML = 'Basic Information';

        }else if( currentStep == 2){
            document.getElementById('head-text').innerHTML = 'Medical Service Record';
        }else if(currentStep == 3){
            document.getElementById('head-text').innerHTML = 'Additional Information';
        }
        document.querySelectorAll('.step').forEach( div =>{
                if (div && div.classList) {
                    div.classList.remove('d-flex');
                    div.classList.add('d-none');
                }
            } 
        );
        if (currentStep == 2) {
            document.querySelectorAll('.patient-type').forEach(box => {
                box.classList.add('d-none');
            });
            const selectedDiv = document.getElementById(selected + '-con');
            if (selectedDiv) {
                selectedDiv.classList.remove('d-none');
                selectedDiv.classList.add('d-flex', 'flex-column');
            }
        }
        if (currentStep == 3) {
            if(selected == 'prenatal'){
                document.getElementById('step' + step).classList.remove('d-none');
                document.getElementById('step' + step).classList.add('d-flex');
                document.getElementById('step' + step).classList.add('flex-column');
                // target the specific div
                
                 document.getElementById('prenatal-step3').classList.remove('d-none');
            } else if (selected == 'family-planning') {
                console.log('taena gumana kaya boy');
                document.getElementById('step' + step).classList.remove('d-none');
                document.getElementById('step' + step).classList.add('d-flex');
                document.getElementById('step' + step).classList.add('flex-column');

                document.getElementById('family-planning-step3').classList.replace('d-none','d-flex');
                console.log(document.querySelectorAll('#family-planning-step3'));
                
            }
        }else{
            document.getElementById('step' + step).classList.remove('d-none');
            document.getElementById('step' + step).classList.add('d-flex');
            document.getElementById('step' + step).classList.add('flex-column');
        }
    }
    window.nextStep = function () {

        const typeSelect = document.getElementById('type-of-patient');
    
        if (typeSelect.value === "") {
            Swal.fire({
                // title: 'Type of Patient',
                text: "Select the Type of Patient",
                icon: 'warning',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ok'
            });
            typeSelect.focus();
            return; // stop the function here
        }
        currentStep++;
        showStep(currentStep);
    };

    window.prevStep = function () {
        currentStep--;
        showStep(currentStep);
    };

    window.showAdditional = function (){
        let dropdown= document.getElementById('type-of-patient');
        let dropdownValue = dropdown.value ;
        if(dropdownValue == 'vaccination'){
            // hide the prenatal
            document.querySelector('.prenatal-inputs').classList.replace('d-flex','d-none');
            document.querySelector('.vaccination-inputs').classList.replace('d-none','d-flex');
            // vital sign
            document.querySelector('.first-row').classList.replace('d-flex','d-none');
            document.querySelector('.second-row').classList.replace('d-flex','d-none');
            document.querySelector('.third-row').classList.replace('d-none','d-flex');
        }else if(dropdownValue == 'prenatal'){
            // hide the vaccination
            document.querySelector('.vaccination-inputs').classList.replace('d-flex','d-none');

            document.querySelector('.prenatal-inputs').classList.replace('d-none','d-flex');
            // vital
            document.querySelector('.first-row').classList.replace('d-none','d-flex');
            document.querySelector('.second-row').classList.replace('d-none','d-flex');
            document.querySelector('.third-row').classList.replace('d-flex','d-none');
        }else if(dropdownValue == 'family-planning'){
            document.querySelector('.vaccination-inputs').classList.replace('d-flex','d-none');

            document.querySelector('.prenatal-inputs').classList.replace('d-flex','d-none');
            // vital
            document.querySelector('.first-row').classList.replace('d-none','d-flex');
            document.querySelector('.second-row').classList.replace('d-none','d-flex');
            document.querySelector('.third-row').classList.replace('d-flex','d-none');
            // show the family planning
            document.querySelector('.family-planning-inputs').classList.replace('d-none','d-flex');

        }else{
            document.querySelector('.vaccination-inputs').classList.replace('d-flex','d-none');

            document.querySelector('.prenatal-inputs').classList.replace('d-flex','d-none');
            // vital
            document.querySelector('.first-row').classList.replace('d-none','d-flex');
            document.querySelector('.second-row').classList.replace('d-none','d-flex');
            document.querySelector('.third-row').classList.replace('d-flex','d-none');
        }
    }
});