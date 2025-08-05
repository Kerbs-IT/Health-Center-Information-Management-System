export async function fetchHealthworkers() {
    const response = await fetch("/health-worker-list");

    if (!response.ok) {
        return 'Error in fetching';
    }

    const data = await response.json();

    return data;
}

// show the puroks for the assigned area
 export async function puroks (dropdown,purok) {
    
    try {
        const response = await fetch('/showBrgyUnit');
        const brgyData = await response.json();


        brgyData.forEach(element => {         
            dropdown.innerHTML += `<option value="${element.brgy_unit}" ${element.brgy_unit == purok?'selected':''}  >${element.brgy_unit}</option>`;

        });
        
    } catch (error) {
        console.log('Errors', error);
    }
    
};