export default function initSignatureCapture(elementIds) {
    const {
        drawBtnId,
        uploadBtnId,
        canvasId,
        canvasSectionId,
        uploadSectionId,
        previewSectionId,
        fileInputId,
        previewImageId,
        errorElementId,
        clearBtnId,
        saveBtnId,
        removeBtnId,
        hiddenInputId = "signature_data", // default name
        maxFileSizeMB = 5,
    } = elementIds;

    // Get all elements
    const drawBtn = document.getElementById(drawBtnId);
    const uploadBtn = document.getElementById(uploadBtnId);
    const canvasSection = document.getElementById(canvasSectionId);
    const uploadSection = document.getElementById(uploadSectionId);
    const previewSection = document.getElementById(previewSectionId);
    const clearBtn = document.getElementById(clearBtnId);
    const saveBtn = document.getElementById(saveBtnId);
    const removeBtn = document.getElementById(removeBtnId);
    const canvas = document.getElementById(canvasId);
    const fileInput = document.getElementById(fileInputId);
    const previewImage = document.getElementById(previewImageId);
    const signatureError = document.getElementById(errorElementId);

    // Check if all required elements exist
    if (!drawBtn || !uploadBtn || !canvas || !fileInput) {
        // console.error("Required signature elements not found:", elementIds);
        return null;
    }

    // Initialize Signature Pad
    let signaturePad = null;

    function initSignaturePad() {
        if (!signaturePad) {
            canvas.width = canvas.offsetWidth;
            canvas.height = 200;
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: "rgb(255, 255, 255)",
                penColor: "rgb(0, 0, 0)",
                minWidth: 1,
                maxWidth: 3,
            });
        }
    }

    // Show draw section
    drawBtn.addEventListener("click", function () {
        canvasSection.classList.remove("d-none");
        uploadSection.classList.add("d-none");
        previewSection.classList.add("d-none");
        signatureError.textContent = "";
        fileInput.value = "";
        initSignaturePad();
    });

    // Show upload section
    uploadBtn.addEventListener("click", function () {
        uploadSection.classList.remove("d-none");
        canvasSection.classList.add("d-none");
        previewSection.classList.add("d-none");
        signatureError.textContent = "";
        if (signaturePad) {
            signaturePad.clear();
        }
    });

    // Clear canvas
    clearBtn.addEventListener("click", function () {
        if (signaturePad) {
            signaturePad.clear();
            signatureError.textContent = "";
        }
    });

    // Save signature from canvas
    saveBtn.addEventListener("click", function () {
        if (!signaturePad) {
            signatureError.textContent = "Signature pad not initialized.";
            return;
        }

        if (signaturePad.isEmpty()) {
            signatureError.textContent = "Please provide a signature first.";
            return;
        }

        const dataURL = signaturePad.toDataURL("image/png");
        previewImage.src = dataURL;
        previewSection.classList.remove("d-none");
        canvasSection.classList.add("d-none");
        signatureError.textContent = "";

        // Store the data URL in a hidden input for form submission
        let hiddenInput = document.getElementById(hiddenInputId);
        if (!hiddenInput) {
            hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.id = hiddenInputId;
            hiddenInput.name = hiddenInputId;
            document.querySelector("form").appendChild(hiddenInput);
        }
        hiddenInput.value = dataURL;
    });

    // Remove signature
    removeBtn.addEventListener("click", function () {
        previewSection.classList.add("d-none");
        previewImage.src = "";
        signatureError.textContent = "";
        fileInput.value = "";

        if (signaturePad) {
            signaturePad.clear();
        }

        const hiddenInput = document.getElementById(hiddenInputId);
        if (hiddenInput) {
            hiddenInput.remove();
        }
    });

    // Handle file upload preview
    fileInput.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!file.type.match("image.*")) {
            signatureError.textContent = "Please select a valid image file.";
            fileInput.value = "";
            return;
        }

        const maxFileSize = maxFileSizeMB * 1024 * 1024;
        if (file.size > maxFileSize) {
            signatureError.textContent = `File size must be less than ${maxFileSizeMB}MB.`;
            fileInput.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            previewImage.src = event.target.result;
            previewSection.classList.remove("d-none");
            uploadSection.classList.add("d-none");
            signatureError.textContent = "";
        };
        reader.onerror = function () {
            signatureError.textContent =
                "Error reading file. Please try again.";
            fileInput.value = "";
        };
        reader.readAsDataURL(file);
    });

    // Handle window resize for canvas
    window.addEventListener("resize", function () {
        if (signaturePad && !canvasSection.classList.contains("d-none")) {
            const data = signaturePad.toData();
            canvas.width = canvas.offsetWidth;
            canvas.height = 200;
            signaturePad.fromData(data);
        }
    });

    // Return API for external control
    return {
        clear: () => {
            if (signaturePad) signaturePad.clear();
            previewSection.classList.add("d-none");
            fileInput.value = "";
            const hiddenInput = document.getElementById(hiddenInputId);
            if (hiddenInput) hiddenInput.remove();
        },
        getSignatureData: () => {
            if (signaturePad && !signaturePad.isEmpty()) {
                return signaturePad.toDataURL("image/png");
            }
            return null;
        },
        isEmpty: () => {
            return !signaturePad || signaturePad.isEmpty();
        },
    };
}
