import Swal from "sweetalert2";

export async function fetchHealthworkers() {
    const response = await fetch("/health-worker-list");

    if (!response.ok) {
        return "Error in fetching";
    }

    const data = await response.json();

    return data;
}
// get the list of the vaccine
export async function getVaccines() {
    const response = await fetch("/api/vaccines/active");

    if (!response.ok) {
        return "Error in fetching";
    }
    const data = await response.json();

    return data;
}

// show the puroks for the assigned area
export async function puroks(
    dropdown,
    purok,
    userRole = null,
    assignedPurok = null,
) {
    try {
        const response = await fetch("/showBrgyUnit");
        const brgyData = await response.json();

        const isRestrictedStaff =
            userRole === "staff" && assignedPurok !== null;

        brgyData.forEach((element) => {
            const isSelected = element.brgy_unit == purok;
            const isAssigned = element.id == assignedPurok;

            if (isRestrictedStaff) {
                // Only add the assigned purok to the dropdown
                if (isAssigned) {
                    dropdown.innerHTML += `<option value="${element.brgy_unit}" selected>${element.brgy_unit}</option>`;
                }
            } else {
                // Nurse or admin - show all options
                dropdown.innerHTML += `<option value="${element.brgy_unit}" ${
                    isSelected ? "selected" : ""
                }>${element.brgy_unit}</option>`;
            }
        });
    } catch (error) {
        console.log("Errors", error);
    }
}

// function that allow adding the selected vaccine inside the container
// function that allow adding the selected vaccine inside the container
export function addVaccineInteraction(
    btn,
    vaccineInput,
    vaccinesContainer,
    selectedVaccinesCon,
    selectedVaccines,
    onVaccineChange, // ✅ callback from caller
) {
    if (!btn) return;
    btn.addEventListener("click", (e) => {
        const selectedText =
            vaccineInput.options[vaccineInput.selectedIndex].text;
        const selectedId = Number(
            vaccineInput.options[vaccineInput.selectedIndex].value,
        );

        if (!vaccineInput.value) {
            Swal.fire({
                title: "Vaccine Type",
                text: "The input field is empty. Please provide a valid value.",
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            return;
        }

        if (!selectedVaccines.includes(selectedId) && selectedId != "") {
            vaccinesContainer.innerHTML += `
                <div class="vaccine d-flex justify-content-between bg-white align-items-center p-1 w-25 rounded" data-bs-id=${selectedId}>
                    <p class="mb-0">${selectedText}</p>
                    <div class="delete-icon d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/>
                        </svg>
                    </div>
                </div>`;

            selectedVaccines.push(selectedId);
            selectedVaccinesCon.value = selectedVaccines.join(",");
            
            // ✅ CALL CALLBACK IMMEDIATELY AFTER UPDATING THE ARRAY
            if (typeof onVaccineChange === "function") {
                onVaccineChange(selectedVaccines);
            }
            
        } else {
            if (selectedVaccines.includes(selectedId)) {
                Swal.fire({
                    title: "Vaccine Type",
                    text: "The selected vaccine is already added. Please select another type of vaccine.",
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });
                vaccineInput.value = "";
                return;
            }
        }

        vaccineInput.value = "";
    });
}
export function removeVaccine(allContainers, selectedVaccines) {
    allContainers.forEach((container) => {
        container.addEventListener("click", (e) => {
            if (e.target.closest(".delete-icon")) {
                const parentDiv = e.target.closest(".vaccine");
                const vaccineId = parentDiv.dataset.bsId;

                const index = selectedVaccines.indexOf(vaccineId);
                if (index !== -1) {
                    selectedVaccines.splice(index, 1);
                }

                parentDiv.remove();
                // console.log(selectedVaccines);
            }
        });
    });
}


