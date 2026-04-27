import { automateAge } from "../automateAge";

let searchTimeout;

async function searchUsers(query) {
    try {
        const response = await fetch(
            `/get-user-list?search=${encodeURIComponent(query)}`,
        );
        if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error("Error fetching search results:", error);
        throw error;
    }
}

function renderSearchResults(users) {
    const resultsContainer = document.getElementById("searchResults");
    resultsContainer.innerHTML = "";

    if (users.length === 0) {
        document.getElementById("noResults").style.display = "block";
        return;
    }

    document.getElementById("resultsContainer").style.display = "block";

    users.forEach((user) => {
        const item = document.createElement("a");
        item.href = "#";
        item.className = "list-group-item list-group-item-action";
        item.innerHTML = `
            <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1">${user.full_name}</h6>
            </div>
            <small class="text-muted">${user.email}</small>
        `;
        item.addEventListener("click", (e) => {
            e.preventDefault();
            document.getElementById("searchInput").value = user.full_name;
            populateFormFields(user);
            document.getElementById("resultsContainer").style.display = "none";
        });
        resultsContainer.appendChild(item);
    });
}

function hideAllSearchUI() {
    document.getElementById("resultsContainer").style.display = "none";
    document.getElementById("noResults").style.display = "none";
    document.getElementById("loadingSpinner").style.display = "none";
}

function clearAutoPopulatedFields() {
    const user_account = document.getElementById("user_account");
    if (user_account) user_account.value = "";

    const email = document.getElementById("email");
    if (email) {
        email.value = "";
        email.disabled = false;
    }

    const fieldsToClean = [
        "first_name",
        "middle_initial",
        "last_name",
        "contact_number",
        "birthdate",
        "age",
        "hiddenAge",
        "nationality",
        "street",
    ];
    fieldsToClean.forEach((fieldId) => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.value = "";
            element.dispatchEvent(new Event("change"));
        }
    });

    const brgy = document.getElementById("brgy");
    if (brgy) {
        brgy.value = "";
        brgy.dispatchEvent(new Event("change"));
    }

    const type_of_patient = document.getElementById("type-of-patient");
    if (type_of_patient) {
        type_of_patient.value = "";
        type_of_patient.dispatchEvent(new Event("change"));
    }

    const add_suffix = document.getElementById("add_suffix");
    if (add_suffix) {
        add_suffix.value = "";
        add_suffix.dispatchEvent(new Event("change"));
    }

    const sexRadios = document.querySelectorAll('input[name="sex"]');
    sexRadios.forEach((radio) => {
        radio.checked = false;
    });
}

document.getElementById("searchInput").addEventListener("input", function (e) {
    const query = e.target.value.trim();

    // Cancel any pending search
    clearTimeout(searchTimeout);

    // Always reset UI on every keystroke
    hideAllSearchUI();

    if (query.length === 0) {
        clearAutoPopulatedFields();
        return;
    }

    // Show spinner immediately so user sees feedback
    document.getElementById("loadingSpinner").style.display = "block";

    searchTimeout = setTimeout(async () => {
        try {
            const users = await searchUsers(query);
            document.getElementById("loadingSpinner").style.display = "none";
            renderSearchResults(users);
        } catch (error) {
            document.getElementById("loadingSpinner").style.display = "none";
            document.getElementById("noResults").style.display = "block";
        }
    }, 300);
});

// On blur: cancel pending search, hide all UI
document.getElementById("searchInput").addEventListener("blur", function () {
    clearTimeout(searchTimeout);
    // Small delay so click on result item can still fire before hiding
    setTimeout(() => {
        hideAllSearchUI();
    }, 150);
});

document.addEventListener("click", (e) => {
    if (
        !e.target.closest("#searchInput") &&
        !e.target.closest("#resultsContainer")
    ) {
        hideAllSearchUI();
    }
});

function populateFormFields(userData) {
    const user_account = document.getElementById("user_account");
    if (user_account) user_account.value = userData.id;

    const email = document.getElementById("email");
    if (email) email.value = userData.email;

    Object.entries(userData).forEach(([key, value]) => {
        const element = document.getElementById(key);

        if (key === "patient_type" && value) {
            const type_of_patient = document.getElementById("type-of-patient");
            if (type_of_patient) {
                type_of_patient.value = value;
                type_of_patient.dispatchEvent(new Event("change"));
            }
        }

        if (key === "contact_number" && value) {
            if (element) element.value = value;
        }

        if (key === "date_of_birth" && value) {
            const birthdateElement = document.getElementById("birthdate");
            if (birthdateElement) {
                const date = new Date(value);
                birthdateElement.value = date.toISOString().split("T")[0];
                birthdateElement.dispatchEvent(new Event("change"));
            }
        }

        if (key === "suffix" && value) {
            const suffixElement = document.getElementById("add_suffix");
            if (suffixElement) suffixElement.value = value;
        }

        if (!element) return;
        element.value = value || "";
        element.dispatchEvent(new Event("change"));
    });

    const street = document.getElementById("street");
    const purok = document.getElementById("brgy");

    if (userData.user_address) {
        const blk_n_street = [
            userData.user_address.house_number,
            userData.user_address.street,
        ]
            .filter(Boolean)
            .join(" ")
            .trim();

        if (street) street.value = blk_n_street;

        if (purok) {
            purok.value = userData.user_address.purok;
            purok.dispatchEvent(new Event("change"));
        }
    }

    window.syncHandledByView();
}
