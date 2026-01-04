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
    const response = await fetch("/vaccines");

    if (!response.ok) {
        return "Error in fetching";
    }
    const data = await response.json();

    return data;
}

// show the puroks for the assigned area
export async function puroks(dropdown, purok) {
    try {
        const response = await fetch("/showBrgyUnit");
        const brgyData = await response.json();

        brgyData.forEach((element) => {
            dropdown.innerHTML += `<option value="${element.brgy_unit}" ${
                element.brgy_unit == purok ? "selected" : ""
            }  >${element.brgy_unit}</option>`;
        });
    } catch (error) {
        console.log("Errors", error);
    }
}

// function that allow adding the selected vaccine inside the container
export function addVaccineInteraction(
    btn,
    vaccineInput,
    vaccinesContainer,
    selectedVaccinesCon,
    selectedVaccines
) {
    if (!btn) return;
    btn.addEventListener("click", (e) => {
        //  const vaccineInput = document.getElementById("vaccine_input");
        // console.log("current selected vaccine:", selectedVaccines);
        const selectedText =
            vaccineInput.options[vaccineInput.selectedIndex].text;
        const selectedId = Number(
            vaccineInput.options[vaccineInput.selectedIndex].value
        );

        if (!vaccineInput.value) {
            Swal.fire({
                title: "Vaccine Type",
                text: "The input field is empty.Please provide a valid value.", // this will make the text capitalize each word
                icon: "error",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "OK",
            });
            return;
        }
        if (!selectedVaccines.includes(selectedId) && selectedId != "") {
            vaccinesContainer.innerHTML += ` <div class="vaccine d-flex justify-content-between bg-white align-items-center p-1 w-25 rounded" data-bs-id=${selectedId}>
                    <p class="mb-0">${selectedText}</p>
                    <div class="delete-icon d-flex align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="delete-icon-svg" viewBox="0 0 448 512">
                            <path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z" />
                        </svg>
                    </div>
                </div>`;
            // push the id to the selectedVaccines array
            selectedVaccines.push(selectedId);
            selectedVaccinesCon.value = selectedVaccines.join(",");
        } else {
            if (selectedVaccines.includes(selectedId)) {
                Swal.fire({
                    title: "Vaccine Type",
                    text: "The selected vaccine is already added.Please select another type of vaccine.", // this will make the text capitalize each word
                    icon: "error",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK",
                });
                 vaccineInput.value = "";
                return;
            }
                
        }
         setTimeout(() => {
             updateDoseDropdown(selectedVaccines);
         }, 100);

        
        // console.log("updated vaccine:", selectedVaccinesCon.value);
        vaccineInput.value = '';
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

const vaccineDoseConfig = {
    1: { maxDoses: 1, description: "at birth", name: "BCG Vaccine" },
    2: { maxDoses: 1, description: "at birth", name: "Hepatitis B Vaccine" },
    3: {
        maxDoses: 3,
        description: "doses 1-3",
        name: "Pentavalent Vaccine (DPT-HEP B-HIB)",
    },
    4: {
        maxDoses: 3,
        description: "doses 1-3",
        name: "Oral Polio Vaccine (OPV)",
    },
    5: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Inactived Polio Vaccine (IPV)",
    },
    6: {
        maxDoses: 3,
        description: "doses 1-3",
        name: "Pnueumococcal Conjugate Vaccine (PCV)",
    },
    7: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Measles, Mumps, Rubella Vaccine (MMR)",
    },
    8: {
        maxDoses: 1,
        description: "dose 1",
        name: "Measles Containing Vaccine (MCV) MR/MMR (Grade 1)",
    },
    9: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Measles Containing Vaccine (MCV) MR/MMR (Grade 7)",
    },
    10: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Tetanus Diphtheria (TD)",
    },
    11: {
        maxDoses: 2,
        description: "doses 1-2",
        name: "Human Papiliomavirus Vaccine",
    },
    12: { maxDoses: 3, description: "doses 1-3", name: "Influenza Vaccine" },
    13: { maxDoses: 3, description: "doses 1-3", name: "Pnuemococcal Vaccine" },
};

function updateDoseDropdown(selectedVaccines) {
    const doseDropdown = document.getElementById("dose");

    if (!doseDropdown) return;

    // Find maximum dose from selected vaccines
    let maxDose = 1;
    selectedVaccines.forEach((id) => {
        if (vaccineDoseConfig[id]) {
            maxDose = Math.max(maxDose, vaccineDoseConfig[id].maxDoses);
        }
    });

    // Store current selection
    const currentValue = doseDropdown.value;

    // Clear and rebuild options
    doseDropdown.innerHTML = '<option value="">Select Dose</option>';

    for (let i = 1; i <= maxDose; i++) {
        const option = document.createElement("option");
        option.value = `${i}`;
        option.textContent = `Dose ${i}`;
        doseDropdown.appendChild(option);
    }

    // Restore selection if still valid
    if (currentValue && currentValue <= maxDose) {
        doseDropdown.value = currentValue;
    }
}
