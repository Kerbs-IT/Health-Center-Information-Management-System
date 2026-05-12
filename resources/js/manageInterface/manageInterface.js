/* =============================================================
   manageInterface.js
   Handles: color palette, logo, carousel, worker photos, about image
   ============================================================= */

// ── Helpers ──────────────────────────────────────────────────

function showToast(message, type = "success") {
    let toast = document.getElementById("mi-toast");
    if (!toast) {
        toast = document.createElement("div");
        toast.id = "mi-toast";
        toast.className = "mi-toast";
        document.body.appendChild(toast);
    }
    toast.className = `mi-toast mi-toast--${type}`;
    toast.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            ${
                type === "success"
                    ? '<path d="M5 12l5 5l10 -10"/>'
                    : '<path d="M12 9v4m0 4h.01"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>'
            }
        </svg>
        ${message}
    `;
    toast.classList.add("mi-toast--show");
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(
        () => toast.classList.remove("mi-toast--show"),
        3000,
    );
}

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.content ?? "";

// ── 1. COLOR PALETTE ──────────────────────────────────────────
// (your existing logic, kept intact)

const primaryBox = document.getElementById("primary_color");
const primaryHex = document.getElementById("primary_hex");
const secondaryBox = document.getElementById("secondary_color");
const secondaryHex = document.getElementById("secondary_hex");
const tertiaryBox = document.getElementById("tertiary_color");
const tertiaryHex = document.getElementById("tertiary_hex");
const colorPalleteForm = document.getElementById("color-pallete-form");
const root = document.querySelector(":root");

function syncColorInputs(e) {
    const value = e.target.value;

    if (e.target === primaryBox) {
        root.style.setProperty("--primaryColor", value);
        primaryHex.value = value;
    } else if (e.target === primaryHex) {
        primaryBox.value = value;
        root.style.setProperty("--primaryColor", value);
    }

    if (e.target === secondaryBox) {
        root.style.setProperty("--secondaryColor", value);
        secondaryHex.value = value;
    } else if (e.target === secondaryHex) {
        root.style.setProperty("--secondaryColor", value);
        secondaryBox.value = value;
    }

    if (e.target === tertiaryBox) {
        root.style.setProperty("--tertiaryColor", value);
        tertiaryHex.value = value;
    } else if (e.target === tertiaryHex) {
        root.style.setProperty("--tertiaryColor", value);
        tertiaryBox.value = value;
    }
}

async function updateColorPallete(form) {
    const formData = new FormData(form);
    try {
        const response = await fetch("/update-color-pallete", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken(),
                Accept: "application/json",
            },
            body: formData,
        });
        const result = await response.json();
        if (response.ok) {
            showToast("Color palette saved successfully");
        } else {
            showToast(result.message ?? "Failed to save palette", "error");
        }
    } catch (error) {
        console.error("Color palette error:", error);
        showToast("Network error", "error");
    }
}

async function InputcurrentColorPallete() {
    try {
        const response = await fetch("/color-pallete");
        const data = await response.json();

        primaryBox.value = data.primaryColor;
        primaryHex.value = data.primaryColor;
        secondaryBox.value = data.secondaryColor;
        secondaryHex.value = data.secondaryColor;
        tertiaryBox.value = data.tertiaryColor;
        tertiaryHex.value = data.tertiaryColor;

        // apply to CSS vars immediately
        root.style.setProperty("--primaryColor", data.primaryColor);
        root.style.setProperty("--secondaryColor", data.secondaryColor);
        root.style.setProperty("--tertiaryColor", data.tertiaryColor);
    } catch (error) {
        console.error("Fetch palette error:", error);
    }
}

if (primaryBox) {
    [primaryBox, primaryHex].forEach((el) =>
        el.addEventListener("change", (e) => {
            syncColorInputs(e);
            updateColorPallete(colorPalleteForm);
        }),
    );
    [secondaryBox, secondaryHex].forEach((el) =>
        el.addEventListener("change", (e) => {
            syncColorInputs(e);
            updateColorPallete(colorPalleteForm);
        }),
    );
    [tertiaryBox, tertiaryHex].forEach((el) =>
        el.addEventListener("change", (e) => {
            syncColorInputs(e);
            updateColorPallete(colorPalleteForm);
        }),
    );

    InputcurrentColorPallete();
}

// ── 2. LOGO ──────────────────────────────────────────────────

const logoInput = document.getElementById("logo-file-input");
const logoPreviewBox = document.getElementById("logo-preview-box");
const logoRemoveBtn = document.getElementById("logo-remove-btn");

if (logoInput) {
    logoInput.addEventListener("change", async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        // instant local preview
        const reader = new FileReader();
        reader.onload = (ev) => {
            logoPreviewBox.innerHTML = `<img src="${ev.target.result}" alt="Logo preview" class="mi-logo-preview__img">`;
        };
        reader.readAsDataURL(file);

        // upload
        const formData = new FormData();
        formData.append("logo", file);

        try {
            const response = await fetch("/manage-interface/logo", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken(),
                    Accept: "application/json",
                },
                body: formData,
            });
            const result = await response.json();
           if (response.ok) {
               showToast("Logo updated successfully");
               // Refresh the menubar logo in-place
               document
                   .querySelectorAll('img[src*="hugoperez_logo"]')
                   .forEach((img) => {
                       const base = img.src.split("?")[0];
                       img.src = base + "?v=" + Date.now();
                   });
           } else {
               showToast(result.message ?? "Failed to upload logo", "error");
           }
        } catch (err) {
            console.error("Logo upload error:", err);
            showToast("Network error", "error");
        }
    });
}

if (logoRemoveBtn) {
    logoRemoveBtn.addEventListener("click", async () => {
        if (!confirm("Remove the current logo?")) return;
        try {
            const response = await fetch("/manage-interface/logo", {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken(),
                    Accept: "application/json",
                },
            });
            if (response.ok) {
                // Reload the default with a cache-bust timestamp
                const defaultUrl = "/images/hugoperez_logo.png?v=" + Date.now();

                logoPreviewBox.innerHTML = `
                    <img src="${defaultUrl}" alt="Current logo" class="mi-logo-preview__img">`;

                // Also refresh the menubar logo
                document
                    .querySelectorAll('img[src*="hugoperez_logo"]')
                    .forEach((img) => {
                        img.src = "/images/hugoperez_logo.png?v=" + Date.now();
                    });

                showToast("Logo removed");
            }
        } catch (err) {
            showToast("Network error", "error");
        }
    });
}

// ── 3. CAROUSEL ──────────────────────────────────────────────

const carouselInput = document.getElementById("carousel-file-input");
const carouselTrack = document.getElementById("carousel-track");

if (carouselInput) {
    carouselInput.addEventListener("change", async (e) => {
        const files = Array.from(e.target.files);
        if (!files.length) return;

        for (const file of files) {
            const formData = new FormData();
            formData.append("image", file);

            try {
                const response = await fetch("/manage-interface/carousel", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken(),
                        Accept: "application/json",
                    },
                    body: formData,
                });
                const result = await response.json();

                if (response.ok) {
                    // Insert new slide thumbnail before the "Add" button
                    const addSlot =
                        carouselTrack.querySelector(".mi-slide--add");
                    const slideCount = carouselTrack.querySelectorAll(
                        ".mi-slide:not(.mi-slide--add)",
                    ).length;

                    const div = document.createElement("div");
                    div.className = "mi-slide";
                    div.dataset.id = result.id;
                    div.innerHTML = `
                        <img src="${result.url}" alt="Slide ${slideCount + 1}" class="mi-slide__img">
                        <div class="mi-slide__overlay">
                            <button type="button" class="mi-slide__del" data-id="${result.id}" title="Remove slide">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6l-12 12"/><path d="M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <span class="mi-slide__label">Slide ${slideCount + 1}</span>`;
                    carouselTrack.insertBefore(div, addSlot);
                    showToast("Slide added");
                } else {
                    showToast(
                        result.message ?? "Failed to upload slide",
                        "error",
                    );
                }
            } catch (err) {
                console.error("Carousel upload error:", err);
                showToast("Network error", "error");
            }
        }

        carouselInput.value = "";
    });
}

// Delete slide (event delegation)
if (carouselTrack) {
    carouselTrack.addEventListener("click", async (e) => {
        const delBtn = e.target.closest(".mi-slide__del");
        if (!delBtn) return;

        const id = delBtn.dataset.id;
        if (!confirm("Remove this slide?")) return;

        try {
            const response = await fetch(`/manage-interface/carousel/${id}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken(),
                    Accept: "application/json",
                },
            });
            if (response.ok) {
                delBtn.closest(".mi-slide").remove();
                // Re-number slide labels
                carouselTrack
                    .querySelectorAll(".mi-slide:not(.mi-slide--add)")
                    .forEach((el, i) => {
                        const label = el.querySelector(".mi-slide__label");
                        if (label) label.textContent = `Slide ${i + 1}`;
                    });
                showToast("Slide removed");
            }
        } catch (err) {
            showToast("Network error", "error");
        }
    });
}

// ── 4. WORKER HOMEPAGE PHOTOS ─────────────────────────────────

document.querySelectorAll(".worker-photo-input").forEach((input) => {
    input.addEventListener("change", async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const workerId = e.target.dataset.workerId;
        const formData = new FormData();
        formData.append("photo", file);
        formData.append("worker_id", workerId);

        try {
            const response = await fetch(
                `/manage-interface/worker-photo/${workerId}`,
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken(),
                        Accept: "application/json",
                    },
                    body: formData,
                },
            );
            const result = await response.json();

            if (response.ok) {
                // Update badge in the row
                const row = document.querySelector(
                    `.mi-worker-row[data-worker-id="${workerId}"]`,
                );
                if (row) {
                    const statusDiv = row.querySelector(
                        ".mi-worker-photo-status",
                    );
                    statusDiv.innerHTML = `
                        <span class="mi-photo-badge mi-photo-badge--set">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12l5 5l10 -10"/>
                            </svg>
                            Homepage photo set
                        </span>`;

                    // Swap upload button to replace + delete buttons
                    const actionsDiv = row.querySelector(".mi-worker-actions");
                    actionsDiv.innerHTML = `
                        <label class="mi-btn-sm" for="worker-photo-${workerId}" title="Replace">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                            </svg>
                            Replace
                        </label>
                        <button type="button" class="mi-btn-sm mi-btn-sm--danger worker-photo-remove"
                            data-worker-id="${workerId}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/>
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                            </svg>
                        </button>`;
                }

                const previewBox = document.getElementById(
                    `worker-preview-${workerId}`,
                );
                if (previewBox) {
                    previewBox.innerHTML = `<img src="${result.url}" alt="Preview" class="mi-worker-preview__img">`;
                }
                
                showToast("Homepage photo updated");
            } else {
                showToast(result.message ?? "Upload failed", "error");
            }
        } catch (err) {
            console.error("Worker photo error:", err);
            showToast("Network error", "error");
        }
    });
});

// Remove worker homepage photo (event delegation)
document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".worker-photo-remove");
    if (!btn) return;

    const workerId = btn.dataset.workerId;
    if (!confirm("Remove this worker's homepage photo?")) return;

    try {
        const response = await fetch(
            `/manage-interface/worker-photo/${workerId}`,
            {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken(),
                    Accept: "application/json",
                },
            },
        );
        if (response.ok) {
            const row = document.querySelector(
                `.mi-worker-row[data-worker-id="${workerId}"]`,
            );
            if (row) {
                row.querySelector(".mi-worker-photo-status").innerHTML =
                    `<span class="mi-photo-badge mi-photo-badge--none">No homepage photo</span>`;
                row.querySelector(".mi-worker-actions").innerHTML = `
                    <label class="mi-btn-sm mi-btn-sm--upload" for="worker-photo-${workerId}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                            <path d="M7 9l5 -5l5 5"/><path d="M12 4l0 12"/>
                        </svg>
                        Upload photo
                    </label>`;
            }
            showToast("Homepage photo removed");
        }
    } catch (err) {
        showToast("Network error", "error");
    }
});

// ── 5. ABOUT US IMAGE ─────────────────────────────────────────

const aboutInput = document.getElementById("about-file-input");
const aboutPreviewBox = document.getElementById("about-preview-box");
const aboutFname = document.getElementById("about-fname");
const aboutRemoveBtn = document.getElementById("about-remove-btn");

if (aboutInput) {
    aboutInput.addEventListener("change", async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        // instant local preview
        const reader = new FileReader();
        reader.onload = (ev) => {
            aboutPreviewBox.innerHTML = `<img src="${ev.target.result}" alt="About Us banner" class="mi-about-preview__img">`;
        };
        reader.readAsDataURL(file);

        if (aboutFname) aboutFname.textContent = file.name;

        const formData = new FormData();
        formData.append("image", file);

        try {
            const response = await fetch("/manage-interface/about-image", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken(),
                    Accept: "application/json",
                },
                body: formData,
            });
            const result = await response.json();
            if (response.ok) {
                showToast("About Us image updated");
            } else {
                showToast(result.message ?? "Upload failed", "error");
            }
        } catch (err) {
            console.error("About image error:", err);
            showToast("Network error", "error");
        }
    });
}

if (aboutRemoveBtn) {
    aboutRemoveBtn.addEventListener("click", async () => {
        if (!confirm("Remove the About Us image?")) return;
        try {
            const response = await fetch("/manage-interface/about-image", {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken(),
                    Accept: "application/json",
                },
            });
            if (response.ok) {
                aboutPreviewBox.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" style="color:#9ca3af">
                        <path d="M15 8h.01"/>
                        <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"/>
                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4"/>
                        <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"/>
                    </svg>
                    <span style="font-size:11px;color:#9ca3af">No image set</span>`;
                if (aboutFname) aboutFname.textContent = "No file selected";
                showToast("About Us image removed");
            }
        } catch (err) {
            showToast("Network error", "error");
        }
    });
}
