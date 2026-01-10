
export function automateAge(dateOfBirthInput, ageInput,hiddenInput) {
    // if (!dateOfBirthInput|| !dateOfBirthInput.value) return;

    dateOfBirthInput.addEventListener("change", () => {
        
        const dob = new Date(dateOfBirthInput.value);
        const today = new Date();

        let calculatedAge = today.getFullYear() - dob.getFullYear();
        let monthDiff = today.getMonth() - dob.getMonth(); 

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            calculatedAge--;
        }
        

        ageInput.value = calculatedAge >= 0 ? calculatedAge : "";
        hiddenInput.value = ageInput.value??0; 

    });
}