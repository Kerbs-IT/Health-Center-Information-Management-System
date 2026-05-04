import $ from "jquery";

document.addEventListener("DOMContentLoaded", function () {
    fetch("/showBrgyUnit")
        .then((response) => response.json())
        .then((data) => {
            let dropdown = document.getElementById("brgy");

            // console.log(data);
            data.forEach((item) => {
                let option = document.createElement("option");
                option.value = item.brgy_unit;
                option.text = item.brgy_unit;
                dropdown.appendChild(option);
            });
        });


});

document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById("password");
    const strengthBar = document.getElementById("strength-bar");
    const strengthLabel = document.getElementById("strength-label");
    const requirementsList = document.getElementById("password-requirements");

    const requirements = [
        {
            id: "req-length",
            label: "At least 8 characters",
            test: (p) => p.length >= 8,
        },
        {
            id: "req-lower",
            label: "At least one lowercase letter (a-z)",
            test: (p) => /[a-z]/.test(p),
        },
        {
            id: "req-upper",
            label: "At least one uppercase letter (A-Z)",
            test: (p) => /[A-Z]/.test(p),
        },
        {
            id: "req-number",
            label: "Atleast one number (0-9)",
            test: (p) => /[0-9]/.test(p),
        },
        {
            id: "req-symbol",
            label: "Atleast one special character (!@#$...)",
            test: (p) => /[^a-zA-Z0-9]/.test(p),
        },
    ];

    const levels = [
        { label: "Weak",   color: "#dc3545", width: "25%"  },
        { label: "Fair",   color: "#fd7e14", width: "50%"  },
        { label: "Good",   color: "#ffc107", width: "75%"  },
        { label: "Strong", color: "#198754", width: "100%" },
    ];

    function buildRequirementsUI() {
        requirements.forEach((req) => {
            const li = document.createElement("li");
            li.id = req.id;
            li.className = "req-item";
            li.innerHTML = `<span class="req-icon">✗</span> ${req.label}`;
            requirementsList.appendChild(li);
        });
    }

    function updateRequirements(password) {
        requirements.forEach((req) => {
            const li = document.getElementById(req.id);
            const icon = li.querySelector(".req-icon");
            const passed = req.test(password);
            li.classList.toggle("passed", passed);
            icon.textContent = passed ? "✓" : "✗";
        });
    }

    function getScore(password) {
        return requirements.filter((req) => req.test(password)).length;
    }

    function updateStrengthBar(password) {
        if (!password) {
            strengthBar.style.width = "0%";
            strengthBar.style.backgroundColor = "transparent";
            strengthLabel.textContent = "";
            strengthLabel.style.color = "";
            return;
        }

        const score = getScore(password);
        let levelIndex;

        if (score === 5)         levelIndex = 3; // Strong — all requirements met
        else if (score <= 2)     levelIndex = 0; // Weak
        else if (score === 3)    levelIndex = 1; // Fair
        else                     levelIndex = 2; // Good (score === 4)

        const level = levels[levelIndex];
        strengthBar.style.width = level.width;
        strengthBar.style.backgroundColor = level.color;
        strengthLabel.textContent = level.label;
        strengthLabel.style.color = level.color;
    }

    buildRequirementsUI();

    passwordInput.addEventListener("input", function () {
        const password = this.value;
        updateRequirements(password);
        updateStrengthBar(password);
    });
});