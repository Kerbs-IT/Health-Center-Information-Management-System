/**
 * Patient Record Search Module
 * Handles searching and linking existing patient records
 */

(function () {
    "use strict";

    // Configuration
    const CONFIG = {
        searchEndpoint: "/patient-record/link",
        minSearchLength: 2,
        debounceDelay: 300,
        csrfToken: document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content"),
    };

    // DOM Elements
    const elements = {
        searchInput: document.getElementById("patientRecordSearch"),
        resultsContainer: document.getElementById("patientRecordResults"),
        resultsList: document.getElementById("patientRecordResultsList"),
        loadingSpinner: document.getElementById("patientRecordLoading"),
        selectedPatientId: document.getElementById("selectedPatientId"),
        selectedIndicator: document.getElementById("selectedPatientIndicator"),
        displayPatientName: document.getElementById("displayPatientId"),

        // Form fields - NO LOCKING, just for population
        formFields: {
            email: document.getElementById("email"),
            firstName: document.getElementById("first_name"),
            middleInitial: document.getElementById("middle_initial"),
            lastName: document.getElementById("last_name"),
            suffix: document.getElementById("add_suffix"),
            birthdate: document.getElementById("birthdate"),
            placeOfBirth: document.getElementById("place_of_birth"),
            age: document.getElementById("age"),
            maleRadio: document.getElementById("male"),
            femaleRadio: document.getElementById("female"),
            contactNumber: document.getElementById("contact_number"),
            nationality: document.querySelector('input[name="nationality"]'),
            dateOfRegistration: document.getElementById("dateOfRegistration"),
            street: document.getElementById("street"),
            brgy: document.getElementById("brgy"),
        },
    };

    // State
    let searchTimeout = null;
    let isPatientSelected = false;

    /**
     * Initialize the module
     */
    function init() {
        if (!elements.searchInput) {
            console.error("Patient record search input not found");
            return;
        }

        attachEventListeners();
    }

    /**
     * Attach event listeners
     */
   function attachEventListeners() {
       elements.searchInput.addEventListener("input", handleSearchInput);

       // Cancel search and hide spinner on blur
       elements.searchInput.addEventListener("blur", function () {
           clearTimeout(searchTimeout);
           setTimeout(() => {
               hideResults();
               hideLoading();
           }, 150);
       });

       document.addEventListener("click", handleOutsideClick);
   }

   function handleSearchInput(e) {
       const query = e.target.value.trim();

       clearTimeout(searchTimeout);

       // Always hide spinner + results on every keystroke first
       hideLoading();
       hideResults();

       if (query.length < CONFIG.minSearchLength) {
           return;
       }

       // Show spinner immediately
       showLoading();

       searchTimeout = setTimeout(() => {
           searchPatients(query);
       }, CONFIG.debounceDelay);
   }

    /**
     * Search for patients via API
     */
    async function searchPatients(query) {
        try {
            const response = await fetch(
                `${CONFIG.searchEndpoint}?search=${encodeURIComponent(query)}`,
                {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": CONFIG.csrfToken,
                        Accept: "application/json",
                    },
                },
            );

            if (!response.ok) {
                throw new Error("Search request failed");
            }

            const patients = await response.json();
            displayResults(patients);
        } catch (error) {
            console.error("Search error:", error);
            displayError("Failed to search patients. Please try again.");
        } finally {
            hideLoading();
        }
    }

    /**
     * Display search results
     */
    function displayResults(patients) {
        if (!patients || patients.length === 0) {
            elements.resultsList.innerHTML = `
                <div class="p-3 text-center text-muted">
                    <small>No existing patient records found</small>
                    <p class="mb-0 mt-1" style="font-size: 0.75rem;">
                        Fill in the form below to create a new record
                    </p>
                </div>
            `;
            showResults();
            return;
        }

        const resultsHtml = patients
            .map((patient) => createPatientResultItem(patient))
            .join("");
        elements.resultsList.innerHTML = resultsHtml;
        showResults();
    }

    /**
     * Create HTML for a single patient result item
     */
    function createPatientResultItem(patient) {
        // Get active case types
        const activeCases =
            patient.medical_record_case
                ?.filter((c) => c.status === "Active")
                ?.map((c) => formatCaseType(c.type_of_case))
                ?.join(", ") || "No active cases";

        // Format name with suffix
        const fullName = [
            patient.first_name,
            patient.middle_initial,
            patient.last_name,
            patient.suffix,
        ]
            .filter(Boolean)
            .join(" ");

        return `
        <div class="patient-result-item border-bottom p-3 cursor-pointer d-flex justify-content-between align-items-center" 
             onclick="window.selectPatientRecord(${patient.id})"
             style="cursor: pointer; transition: background-color 0.2s;"
             onmouseenter="this.style.backgroundColor='#f8f9fa'"
             onmouseleave="this.style.backgroundColor='white'">
            <div class="fw-normal text-dark">${fullName}</div>
            <div class="text-muted">${activeCases}</div>
        </div>
    `;
    }

    /**
     * Format case type for display
     */
    function formatCaseType(type) {
        const types = {
            vaccination: "Vaccination",
            prenatal: "Prenatal",
            "tb-dots": "TB DOTS",
            "senior-citizen": "Senior Citizen",
            "family-planning": "Family Planning",
        };
        return types[type] || type;
    }

    /**
     * Display error message
     */
    function displayError(message) {
        elements.resultsList.innerHTML = `
            <div class="p-3 text-center text-danger">
                <small>${message}</small>
            </div>
        `;
        showResults();
    }

    /**
     * Select a patient record
     */
    window.selectPatientRecord = async function (patientId) {
        try {
            // Fetch full patient details
            const response = await fetch(
                `${CONFIG.searchEndpoint}?patient_id=${patientId}`,
                {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": CONFIG.csrfToken,
                        Accept: "application/json",
                    },
                },
            );

            if (!response.ok) {
                throw new Error("Failed to fetch patient details");
            }

            const patients = await response.json();
            const patient = patients.find((p) => p.id === patientId);

            if (!patient) {
                throw new Error("Patient not found");
            }

            // Populate form (NO LOCKING)
            populatePatientForm(patient);

            // Update UI
            elements.selectedPatientId.value = patient.id;
            elements.displayPatientName.textContent = patient.full_name;
            elements.selectedIndicator.style.display = "block";

            // Update search input
            const fullName = [
                patient.first_name,
                patient.middle_initial,
                patient.last_name,
                patient.suffix,
            ]
                .filter(Boolean)
                .join(" ");
            elements.searchInput.value = `${fullName}`;

            // Hide results
            hideResults();

            // Set state
            isPatientSelected = true;

            // Focus on type of patient
            document.getElementById("type-of-patient")?.focus();
        } catch (error) {
            console.error("Error selecting patient:", error);
            alert("Failed to load patient details. Please try again.");
        }
    };

    /**
     * Populate form with patient data (NO LOCKING - fields remain editable)
     */
    function populatePatientForm(patient) {
        // Set hidden patient ID
        const selectedPatientId = document.getElementById("selectedPatientId");
        if (selectedPatientId) {
            selectedPatientId.value = patient.id;
        }

        // Email
       const email = document.getElementById("email");
       if (email) {
           const emailValue = patient?.user?.email ?? "";
        //    console.log(emailValue);
           if (typeof emailValue === "string" && emailValue.trim()) {
               email.value = emailValue.trim();
               email.dispatchEvent(new Event("change"));
           }
       }

        // First Name
        const firstName = document.getElementById("first_name");
        if (firstName && patient.first_name) {
            firstName.value = patient.first_name;
            firstName.dispatchEvent(new Event("change"));
        }

        // Middle Initial/Name
        const middleInitial = document.getElementById("middle_initial");
        if (middleInitial && patient.middle_initial) {
            middleInitial.value = patient.middle_initial;
            middleInitial.dispatchEvent(new Event("change"));
        }

        // Last Name
        const lastName = document.getElementById("last_name");
        if (lastName && patient.last_name) {
            lastName.value = patient.last_name;
            lastName.dispatchEvent(new Event("change"));
        }

        // Suffix
        const suffix = document.getElementById("add_suffix");
        if (suffix && patient.suffix) {
            suffix.value = patient.suffix;
            suffix.dispatchEvent(new Event("change"));
        }

        // Date of Birth
        const birthdate = document.getElementById("birthdate");
        if (birthdate && patient.date_of_birth) {
            const date = new Date(patient.date_of_birth);
            birthdate.value = date.toISOString().split("T")[0];
            birthdate.dispatchEvent(new Event("change"));
        }

        // Place of Birth
        const placeOfBirth = document.getElementById("place_of_birth");
        if (placeOfBirth && patient.place_of_birth) {
            placeOfBirth.value = patient.place_of_birth;
            placeOfBirth.dispatchEvent(new Event("change"));
        }

        // Age (visible)
        const age = document.getElementById("age");
        if (age && patient.age) {
            age.value = patient.age;
            age.dispatchEvent(new Event("change"));
        }

        // Hidden Age
        const hiddenAge = document.getElementById("hiddenAge");
        if (hiddenAge && patient.age) {
            hiddenAge.value = patient.age;
        }

        // Sex
        if (patient.sex) {
            const maleRadio = document.getElementById("male");
            const femaleRadio = document.getElementById("female");

            if (patient.sex.toLowerCase() === "male" && maleRadio) {
                maleRadio.checked = true;
                maleRadio.dispatchEvent(new Event("change"));
            } else if (patient.sex.toLowerCase() === "female" && femaleRadio) {
                femaleRadio.checked = true;
                femaleRadio.dispatchEvent(new Event("change"));
            }
        }

        // Contact Number
        const contactNumber = document.getElementById("contact_number");
        if (contactNumber && patient.contact_number) {
            contactNumber.value = patient.contact_number;
            contactNumber.dispatchEvent(new Event("change"));
        }

        // Nationality
        const nationality = document.querySelector('input[name="nationality"]');
        if (nationality && patient.nationality) {
            nationality.value = patient.nationality;
            nationality.dispatchEvent(new Event("change"));
        }

        // Date of Registration
        const dateOfRegistration =
            document.getElementById("dateOfRegistration");
        if (dateOfRegistration && patient.date_of_registration) {
            const regDate = new Date(patient.date_of_registration);
            dateOfRegistration.value = regDate.toISOString().split("T")[0];
            dateOfRegistration.dispatchEvent(new Event("change"));
        }

        // Address - Handle properly
        if (patient.address) {
            // Street (House Number + Street combined)
            const street = document.getElementById("street");
            if (street) {
                const blk_n_street = [
                    patient.address.house_number,
                    patient.address.street,
                ]
                    .filter(Boolean)
                    .join(", ") // Changed to comma for consistency with form input
                    .trim();

                if (blk_n_street) {
                    street.value = blk_n_street;
                    street.dispatchEvent(new Event("change"));
                }
            }

            // Barangay/Purok
            const brgy = document.getElementById("brgy");
            if (brgy && patient.address.purok) {
                brgy.value = patient.address.purok;
                brgy.dispatchEvent(new Event("change"));
            }
        }

        window.syncHandledByView();
    }

    /**
     * Clear patient record selection
     */
    window.clearPatientRecordSelection = function () {
        // Clear hidden field
        elements.selectedPatientId.value = "";

        // Clear and re-enable search
        elements.searchInput.value = "";

        // Hide indicator
        elements.selectedIndicator.style.display = "none";

        // Clear form
        clearForm();

        // Reset state
        isPatientSelected = false;

        // Focus on search
        elements.searchInput.focus();
    };

    /**
     * Clear form fields
     */
    function clearForm() {
        Object.values(elements.formFields).forEach((field) => {
            if (field) {
                if (field.type === "radio" || field.type === "checkbox") {
                    field.checked = false;
                } else if (field.tagName === "SELECT") {
                    field.selectedIndex = 0;
                } else {
                    field.value = "";
                }
            }
        });

        // Clear type of patient
        const typeSelect = document.getElementById("type-of-patient");
        if (typeSelect) typeSelect.value = "";
    }

    /**
     * Show/hide results
     */
    function showResults() {
        elements.resultsContainer.style.display = "block";
    }

    function hideResults() {
        elements.resultsContainer.style.display = "none";
    }

    /**
     * Show/hide loading spinner
     */
    function showLoading() {
        elements.loadingSpinner.style.display = "block";
    }

    function hideLoading() {
        elements.loadingSpinner.style.display = "none";
    }

    /**
     * Handle clicks outside search area
     */
    function handleOutsideClick(e) {
        if (
            !elements.searchInput.contains(e.target) &&
            !elements.resultsContainer.contains(e.target)
        ) {
            hideResults();
        }
    }

    // Initialize on DOM ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
