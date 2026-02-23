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
        displayPatientId: document.getElementById("displayPatientId"),

        // Form fields to lock
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
            // Search input not on this page — stop silently
            return;
        }

        attachEventListeners();
    }

    /**
     * Attach event listeners
     */
    function attachEventListeners() {
        elements.searchInput.addEventListener("input", handleSearchInput);
        document.addEventListener("click", handleOutsideClick);

        elements.searchInput.addEventListener("focus", function () {
            if (isPatientSelected) {
                this.blur();
            }
        });
    }

    /**
     * Handle search input with debouncing
     */
    function handleSearchInput(e) {
        const query = e.target.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < CONFIG.minSearchLength) {
            hideResults();
            return;
        }

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

            if (!response.ok) throw new Error("Search request failed");

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
        if (!elements.resultsList) return;

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
        const activeCases =
            patient.medical_record_case
                ?.filter((c) => c.status === "Active")
                ?.map((c) => formatCaseType(c.type_of_case))
                ?.join(", ") || "No active cases";

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
        if (!elements.resultsList) return;

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

            if (!response.ok)
                throw new Error("Failed to fetch patient details");

            const patients = await response.json();
            const patient = patients.find((p) => p.id === patientId);

            if (!patient) throw new Error("Patient not found");

            // Populate form
            populatePatientForm(patient);

            // Lock fields
            lockPatientFields();

            // Update UI
            if (elements.selectedPatientId)
                elements.selectedPatientId.value = patient.id;
            if (elements.displayPatientId)
                elements.displayPatientId.textContent = patient.id;
            if (elements.selectedIndicator)
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

            if (elements.searchInput) {
                elements.searchInput.value = `${fullName} (ID: ${patient.id})`;
                elements.searchInput.disabled = true;
                elements.searchInput.style.backgroundColor = "#e9ecef";
            }

            hideResults();
            isPatientSelected = true;

            document.getElementById("type-of-patient")?.focus();
        } catch (error) {
            console.error("Error selecting patient:", error);
            alert("Failed to load patient details. Please try again.");
        }
    };

    /**
     * Populate form with patient data
     */
    function populatePatientForm(patient) {
        const selectedPatientId = document.getElementById("selectedPatientId");
        if (selectedPatientId) selectedPatientId.value = patient.id;

        const email = document.getElementById("email");
        if (email && patient.email) {
            email.value = patient.email;
            email.dispatchEvent(new Event("change"));
        }

        const firstName = document.getElementById("first_name");
        if (firstName && patient.first_name) {
            firstName.value = patient.first_name;
            firstName.dispatchEvent(new Event("change"));
        }

        const middleInitial = document.getElementById("middle_initial");
        if (middleInitial && patient.middle_initial) {
            middleInitial.value = patient.middle_initial;
            middleInitial.dispatchEvent(new Event("change"));
        }

        const lastName = document.getElementById("last_name");
        if (lastName && patient.last_name) {
            lastName.value = patient.last_name;
            lastName.dispatchEvent(new Event("change"));
        }

        const suffix = document.getElementById("add_suffix");
        if (suffix && patient.suffix) {
            suffix.value = patient.suffix;
            suffix.dispatchEvent(new Event("change"));
        }

        const birthdate = document.getElementById("birthdate");
        if (birthdate && patient.date_of_birth) {
            const date = new Date(patient.date_of_birth);
            birthdate.value = date.toISOString().split("T")[0];
            birthdate.dispatchEvent(new Event("change"));
        }

        const placeOfBirth = document.getElementById("place_of_birth");
        if (placeOfBirth && patient.place_of_birth) {
            placeOfBirth.value = patient.place_of_birth;
            placeOfBirth.dispatchEvent(new Event("change"));
        }

        // Age (visible — always disabled, just populate value)
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

        const contactNumber = document.getElementById("contact_number");
        if (contactNumber && patient.contact_number) {
            contactNumber.value = patient.contact_number;
            contactNumber.dispatchEvent(new Event("change"));
        }

        const nationality = document.querySelector('input[name="nationality"]');
        if (nationality && patient.nationality) {
            nationality.value = patient.nationality;
            nationality.dispatchEvent(new Event("change"));
        }

        const dateOfRegistration =
            document.getElementById("dateOfRegistration");
        if (dateOfRegistration && patient.date_of_registration) {
            const regDate = new Date(patient.date_of_registration);
            dateOfRegistration.value = regDate.toISOString().split("T")[0];
            dateOfRegistration.dispatchEvent(new Event("change"));
        }

        if (patient.address) {
            const street = document.getElementById("street");
            if (street) {
                const blk_n_street = [
                    patient.address.house_number,
                    patient.address.street,
                ]
                    .filter(Boolean)
                    .join(" ")
                    .trim();

                if (blk_n_street) {
                    street.value = blk_n_street;
                    street.dispatchEvent(new Event("change"));
                }
            }

            const brgy = document.getElementById("brgy");
            if (brgy && patient.address.purok) {
                brgy.value = patient.address.purok;
                brgy.dispatchEvent(new Event("change"));
            }
        }
    }

    /**
     * Lock patient demographic fields
     */
    function lockPatientFields() {
        const fieldsToLock = [
            "email",
            "firstName",
            "middleInitial",
            "lastName",
            "suffix",
            "birthdate",
            "placeOfBirth",
            "age",
            "maleRadio",
            "femaleRadio",
            "contactNumber",
            "nationality",
            "dateOfRegistration",
            "street",
            "brgy",
        ];

        fieldsToLock.forEach((fieldKey) => {
            const field = elements.formFields[fieldKey];
            if (field) {
                field.disabled = true;
                field.style.backgroundColor = "#f8f9fa";
                field.style.cursor = "not-allowed";
            }
        });

        const handledBySelect = document.getElementById("handled_by");
        const handledByBackup = document.getElementById("handled_by_backup");

        if (handledBySelect && handledByBackup) {
            handledByBackup.value = handledBySelect.value;
            handledBySelect.disabled = true;
        }
    }

    /**
     * Unlock patient fields
     * FIX: Changed Object.values to Object.entries so 'key' is available.
     *      Added key === "age" check to keep age always disabled.
     */
    function unlockPatientFields() {
        Object.entries(elements.formFields).forEach(([key, field]) => {
            if (!field) return;
            if (key === "age") return; // age is always disabled — computed from birthdate

            field.disabled = false;
            field.style.backgroundColor = "";
            field.style.cursor = "";
        });

        const handledBySelect = document.getElementById("handled_by");
        const handledByBackup = document.getElementById("handled_by_backup");

        if (handledBySelect) {
            handledBySelect.disabled = false;
            handledBySelect.style.backgroundColor = "";
            handledBySelect.style.cursor = "";
        }

        if (handledByBackup) {
            handledByBackup.value = "";
        }
    }

    /**
     * Clear patient record selection
     * FIX: Added early return if searchInput doesn't exist on the page.
     */
    window.clearPatientRecordSelection = function () {
        // Fail-safe: if search input doesn't exist on this page, do nothing
        if (!elements.searchInput) return;

        if (elements.selectedPatientId) elements.selectedPatientId.value = "";

        elements.searchInput.value = "";
        elements.searchInput.disabled = false;
        elements.searchInput.style.backgroundColor = "";

        if (elements.selectedIndicator)
            elements.selectedIndicator.style.display = "none";

        unlockPatientFields();
        clearForm();

        isPatientSelected = false;

        elements.searchInput.focus();
    };

    /**
     * Clear form fields
     * FIX: Changed Object.values to Object.entries so 'key' is available.
     *      Added try/catch per field so one bad field doesn't stop the rest.
     */
    function clearForm() {
        Object.entries(elements.formFields).forEach(([key, field]) => {
            if (!field) return; // skip missing elements silently

            try {
                if (field.type === "radio" || field.type === "checkbox") {
                    field.checked = false;
                } else if (field.tagName === "SELECT") {
                    field.selectedIndex = 0;
                } else {
                    field.value = "";
                }
            } catch (e) {
                console.warn(`Could not clear field: ${key}`, e);
            }
        });

        const typeSelect = document.getElementById("type-of-patient");
        if (typeSelect) typeSelect.value = "";
    }

    /**
     * Show/hide results
     */
    function showResults() {
        if (elements.resultsContainer)
            elements.resultsContainer.style.display = "block";
    }

    function hideResults() {
        if (elements.resultsContainer)
            elements.resultsContainer.style.display = "none";
    }

    /**
     * Show/hide loading spinner
     */
    function showLoading() {
        if (elements.loadingSpinner)
            elements.loadingSpinner.style.display = "block";
    }

    function hideLoading() {
        if (elements.loadingSpinner)
            elements.loadingSpinner.style.display = "none";
    }

    /**
     * Handle clicks outside search area
     */
    function handleOutsideClick(e) {
        if (!elements.searchInput || !elements.resultsContainer) return;

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
