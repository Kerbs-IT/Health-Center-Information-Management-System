/**
 * patientModeToggle.js
 *
 * Handles:
 * 1. New / Existing patient radio toggle (show/hide sections)
 * 2. Notification mode toggle (new account / guardian)
 * 3. Guardian account search + selection
 *
 * Only included in add_patient blade.
 * All DOM references are null-checked for safety.
 */

(function () {
    "use strict";

    // =========================================================================
    // CONFIG
    // =========================================================================
    const GUARDIAN_SEARCH_ENDPOINT = "/get-guardian-account-list";
    const MIN_SEARCH_LENGTH = 2;
    const DEBOUNCE_DELAY = 300;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    // =========================================================================
    // DOM REFERENCES
    // =========================================================================
    const modeNewRadio = document.getElementById("mode-new");
    const modeExistingRadio = document.getElementById("mode-existing");
    const sectionNew = document.getElementById("section-new-patient");
    const sectionExisting = document.getElementById("section-existing-patient");

    const notifNewAccount = document.getElementById("notif-new-account");
    const notifGuardian = document.getElementById("notif-guardian");
    const sectionGuardian = document.getElementById("section-guardian-search");
    const sectionNotifSetup = document.getElementById(
        "section-notification-setup",
    );

    const guardianInput = document.getElementById("guardianSearchInput");
    const guardianResults = document.getElementById("guardianResultsContainer");
    const guardianResultsList = document.getElementById(
        "guardianSearchResults",
    );
    const guardianSpinner = document.getElementById("guardianLoadingSpinner");
    const guardianNoResults = document.getElementById("guardianNoResults");
    const guardianIndicator = document.getElementById(
        "selectedGuardianIndicator",
    );
    const guardianIdInput = document.getElementById("guardian_account_id");
    const displayGuardianName = document.getElementById("displayGuardianName");
    const displayGuardianEmail = document.getElementById(
        "displayGuardianEmail",
    );
    const clearGuardianBtn = document.getElementById("clearGuardianBtn");

    // Patient account search input (from searchUser.js)
    const patientAccountInput = document.getElementById("searchInput");

    let guardianSearchTimeout = null;

    // =========================================================================
    // 1. PATIENT MODE TOGGLE (New / Existing)
    // =========================================================================
    function handleModeToggle() {
        // Fail-safe: if core elements don't exist, do nothing
        if (!modeNewRadio || !modeExistingRadio) return;

        const isNew = modeNewRadio.checked;

        // Show/hide sections
        if (sectionNew) sectionNew.style.display = isNew ? "block" : "none";
        if (sectionExisting)
            sectionExisting.style.display = isNew ? "none" : "block";

        // Reset notification mode to default when switching to new
        if (isNew) {
            if (notifNewAccount) notifNewAccount.checked = true;
            hideSectionGuardian();
        }

        // Clear existing patient selection if switching back to new
        if (isNew) {
            const clearBtn = document.querySelector(
                '[onclick="clearPatientRecordSelection()"]',
            );
            if (clearBtn) clearBtn.click();
        }

        // Clear patient account search if switching to existing
        if (!isNew && patientAccountInput) {
            patientAccountInput.value = "";

            const resultsContainer =
                document.getElementById("resultsContainer");
            if (resultsContainer) resultsContainer.style.display = "none";

            const userAccount = document.getElementById("user_account");
            if (userAccount) userAccount.value = "";
        }
    }

    modeNewRadio?.addEventListener("change", handleModeToggle);
    modeExistingRadio?.addEventListener("change", handleModeToggle);

    // =========================================================================
    // 2. NOTIFICATION MODE TOGGLE (New Account / Guardian)
    // =========================================================================
    function handleNotifToggle() {
        if (!notifGuardian) return;

        if (notifGuardian.checked) {
            showSectionGuardian();
        } else {
            hideSectionGuardian();
            clearGuardianSelection();
        }
    }

    notifNewAccount?.addEventListener("change", handleNotifToggle);
    notifGuardian?.addEventListener("change", handleNotifToggle);

    function showSectionGuardian() {
        if (sectionGuardian) sectionGuardian.style.display = "block";
    }

    function hideSectionGuardian() {
        if (sectionGuardian) sectionGuardian.style.display = "none";
    }

    // =========================================================================
    // 3. HIDE NOTIFICATION SETUP when patient account is linked
    // =========================================================================
    patientAccountInput?.addEventListener("input", function () {
        if (this.value.trim().length === 0) {
            if (sectionNotifSetup) sectionNotifSetup.style.display = "block";
        }
    });

    // Watch for user_account being set by searchUser.js
    const userAccountInput = document.getElementById("user_account");
    if (userAccountInput) {
        const observer = new MutationObserver(function () {
            if (userAccountInput.value) {
                if (sectionNotifSetup) sectionNotifSetup.style.display = "none";
                hideSectionGuardian();
                clearGuardianSelection();
                if (notifNewAccount) notifNewAccount.checked = true;
            } else {
                if (sectionNotifSetup)
                    sectionNotifSetup.style.display = "block";
            }
        });

        observer.observe(userAccountInput, {
            attributes: true,
            attributeFilter: ["value"],
        });

        // Patch: MutationObserver doesn't catch direct .value assignments
        let lastUserAccountValue = "";
        setInterval(() => {
            const current = userAccountInput.value;
            if (current !== lastUserAccountValue) {
                lastUserAccountValue = current;
                if (current) {
                    if (sectionNotifSetup)
                        sectionNotifSetup.style.display = "none";
                    hideSectionGuardian();
                    clearGuardianSelection();
                    if (notifNewAccount) notifNewAccount.checked = true;
                } else {
                    if (sectionNotifSetup)
                        sectionNotifSetup.style.display = "block";
                }
            }
        }, 300);
    }

    // =========================================================================
    // 4. GUARDIAN SEARCH
    // =========================================================================
    guardianInput?.addEventListener("input", function () {
        const query = this.value.trim();

        clearTimeout(guardianSearchTimeout);
        hideGuardianResults();

        if (guardianNoResults) guardianNoResults.style.display = "none";

        if (query.length < MIN_SEARCH_LENGTH) return;

        if (guardianSpinner) guardianSpinner.style.display = "block";

        guardianSearchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(
                    `${GUARDIAN_SEARCH_ENDPOINT}?search=${encodeURIComponent(query)}`,
                    {
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                        },
                    },
                );

                if (!response.ok) throw new Error("Search failed");

                const users = await response.json();
                renderGuardianResults(users);
            } catch (err) {
                console.error("Guardian search error:", err);
                if (guardianNoResults)
                    guardianNoResults.style.display = "block";
            } finally {
                if (guardianSpinner) guardianSpinner.style.display = "none";
            }
        }, DEBOUNCE_DELAY);
    });

    function renderGuardianResults(users) {
        if (!guardianResultsList) return;

        guardianResultsList.innerHTML = "";

        if (!users || users.length === 0) {
            guardianResultsList.innerHTML = `
                <div class="p-3 text-center text-muted">
                    <small>No accounts found</small>
                </div>`;
            if (guardianResults) guardianResults.style.display = "block";
            return;
        }

        users.forEach((user) => {
            const item = document.createElement("div");
            item.className = "list-group-item list-group-item-action p-3";
            item.style.cursor = "pointer";
            item.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">${user.full_name}</div>
                        <small class="text-muted">${user.email}</small>
                    </div>
                    <div>
                        <span class="badge border-1 border-dark text-dark">Guardian</span>
                        <span class="badge bg-info-subtle text-dark border-1 border-info">${user.patient_type}</span>
                    </div>
                </div>`;
            item.addEventListener(
                "mouseenter",
                () => (item.style.backgroundColor = "#f8f9fa"),
            );
            item.addEventListener(
                "mouseleave",
                () => (item.style.backgroundColor = "white"),
            );
            item.addEventListener("click", () => selectGuardian(user));
            guardianResultsList.appendChild(item);
        });

        if (guardianResults) guardianResults.style.display = "block";
    }

    function selectGuardian(user) {
        // Set hidden field
        if (guardianIdInput) guardianIdInput.value = user.id;

        // Show indicator
        if (displayGuardianName)
            displayGuardianName.textContent = user.full_name;
        if (displayGuardianEmail) displayGuardianEmail.textContent = user.email;
        if (guardianIndicator) guardianIndicator.style.display = "block";

        // Update input
        if (guardianInput) {
            guardianInput.value = user.full_name;
            guardianInput.disabled = true;
            guardianInput.style.backgroundColor = "#e9ecef";
        }

        hideGuardianResults();

        // Disable patient account search — not needed when guardian is linked
        if (patientAccountInput) {
            patientAccountInput.value = "";
            patientAccountInput.disabled = true;
            patientAccountInput.style.backgroundColor = "#e9ecef";
            patientAccountInput.placeholder =
                "Disabled — guardian account is linked";

            const userAccount = document.getElementById("user_account");
            if (userAccount) userAccount.value = "";
        }

        // Hide email field — notifications go to guardian's account
        const emailWrapper = document.getElementById("email")?.closest(".mb-2");
        if (emailWrapper) emailWrapper.style.display = "none";
    }

    function clearGuardianSelection() {
        if (guardianIdInput) guardianIdInput.value = "";
        if (guardianIndicator) guardianIndicator.style.display = "none";

        if (guardianInput) {
            guardianInput.value = "";
            guardianInput.disabled = false;
            guardianInput.style.backgroundColor = "";
        }

        // Re-enable patient account search
        if (patientAccountInput) {
            patientAccountInput.disabled = false;
            patientAccountInput.style.backgroundColor = "";
            patientAccountInput.placeholder = "Search by full name...";
        }

        // Show email field again
        const emailWrapper = document.getElementById("email")?.closest(".mb-2");
        if (emailWrapper) emailWrapper.style.display = "block";
    }

    // Expose globally so it can be called externally if needed
    window.clearGuardianSelection = clearGuardianSelection;

    clearGuardianBtn?.addEventListener("click", function () {
        clearGuardianSelection();
        if (guardianInput) guardianInput.focus();
    });

    // Close guardian results when clicking outside
    document.addEventListener("click", function (e) {
        if (
            !guardianInput?.contains(e.target) &&
            !guardianResults?.contains(e.target)
        ) {
            hideGuardianResults();
        }
    });

    function hideGuardianResults() {
        if (guardianResults) guardianResults.style.display = "none";
    }

    // =========================================================================
    // INIT — run on load
    // =========================================================================
    if (modeNewRadio && modeExistingRadio) {
        handleModeToggle();
    }
})();
