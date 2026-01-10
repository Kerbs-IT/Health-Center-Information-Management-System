
export function automateAge(dateOfBirthInput, ageInput, hiddenInput) {
    dateOfBirthInput.addEventListener("change", () => {
        const dob = new Date(dateOfBirthInput.value);
        const today = new Date();

        // Calculate age in years
        let calculatedAge = today.getFullYear() - dob.getFullYear();
        let monthDiff = today.getMonth() - dob.getMonth();

        if (
            monthDiff < 0 ||
            (monthDiff === 0 && today.getDate() < dob.getDate())
        ) {
            calculatedAge--;
        }

        // If less than 1 year old, calculate age in months for display
        if (calculatedAge < 1) {
            let totalMonths = (today.getFullYear() - dob.getFullYear()) * 12;
            totalMonths += today.getMonth() - dob.getMonth();

            if (today.getDate() < dob.getDate()) {
                totalMonths--;
            }

            totalMonths = totalMonths >= 0 ? totalMonths : 0;

            // Display in months
            ageInput.value = totalMonths + " months";
            // But store 0 in hidden input (years)
            hiddenInput.value = 0;
        } else {
            // 1 year or older - show and store in years
            ageInput.value = calculatedAge >= 0 ? calculatedAge : "";
            hiddenInput.value = calculatedAge >= 0 ? calculatedAge : 0;
        }
    });
}