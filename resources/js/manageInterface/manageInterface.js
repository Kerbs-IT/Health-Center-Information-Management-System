const primaryBox = document.getElementById('primary_color');
const primaryHex = document.getElementById('primary_hex');
// secondary
const secondaryBox = document.getElementById('secondary_color');
const secondaryHex = document.getElementById('secondary_hex');
// tertiary
const tertiaryBox = document.getElementById('tertiary_color');
const tertiaryHex = document.getElementById('tertiary_hex');



// form
const colorPalleteForm = document.getElementById("color-pallete-form");

// change color value
const root = document.querySelector(":root");
const rootStyle = getComputedStyle(root);

primaryBox.addEventListener("change", (e) => {
    syncColorInputs(e);
    updateColorPallete(colorPalleteForm);
} );
// hex color input
primaryHex.addEventListener("change", (e) => {
    syncColorInputs(e);
    updateColorPallete(colorPalleteForm);
});

// secondary color
secondaryBox.addEventListener("change", (e) => {
    syncColorInputs(e);
    updateColorPallete(colorPalleteForm);
});
secondaryHex.addEventListener("change", (e) => {
    syncColorInputs(e);
    updateColorPallete(colorPalleteForm);
});;
// tertiary
tertiaryBox.addEventListener("change", (e) => {
    syncColorInputs(e);
    updateColorPallete(colorPalleteForm);
});
tertiaryHex.addEventListener("change", (e) => {
    syncColorInputs(e);
    updateColorPallete(colorPalleteForm);
});


// change the value of the 

function syncColorInputs(e) {
    const value = e.target.value;
    if (e.target === primaryBox) {
        root.style.setProperty("--primaryColor", e.target.value);
        primaryHex.value = value;
    } else if (e.target === primaryHex) {
        primaryBox.value = value;
    }

    if (e.target === secondaryBox) {
        root.style.setProperty('--secondaryColor', e.target.value);
        secondaryHex.value = value;
    } else if (e.target === secondaryHex) {
        root.style.setProperty("--secondaryColor", e.target.value);
        secondaryBox.value = value;
    }

    if (e.target === tertiaryBox) {
        root.style.setProperty("--tertiaryColor", e.target.value);
        tertiaryHex.value = value;
    } else if (e.target === tertiaryHex) {
        root.style.setProperty("--tertiaryColor", e.target.value);
        tertiaryBox.value = value;
    }
    
}

// function hexToRgb(hex, rootElement) {
//     const root = document.querySelector(":root");
//     hex = hex.replace(/^#/, '');

//     if (hex.length === 3) {
//         hex = hex.split('').map(c => c + c).join('');
//     }

//     const r = parseInt(hex.substring(0, 2), 16);
//     const g = parseInt(hex.substring(2, 4), 16);
//     const b = parseInt(hex.substring(4, 6), 16);

//     const luminance = 0.299 * r + 0.587 * g + 0.114 * b;

//     if (luminance > 186) {
//         console.log('true bg is light');
//         root.style.setProperty(rootElement, 'white');
//     } else {
//         console.log("true bg dark");
//         root.style.setProperty(rootElement, 'black');
//     }


    
// }

// // fetch the current values

async function InputcurrentColorPallete() {
    try {
        const response = await fetch("/color-pallete");
        const data = await response.json();

        primaryBox.value = data.primaryColor;
        primaryHex.value = data.primaryColor;
        // secondary
        secondaryBox.value = data.secondaryColor;4
        secondaryHex.value = data.secondaryColor;
        // tertiary
        tertiaryBox.value = data.tertiaryColor;  
        tertiaryHex.value = data.tertiaryColor;
        
    } catch (error) {
        console.log('erros:', error);
    }
}


// console.log(primaryBox);

async function updateColorPallete(form) {

    const formData = new FormData(form);

    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    try {
        const response = await fetch("/update-color-pallete", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: formData,
        });

        const result = await response.json();
        if (response.ok) {
            console.log('color pallete has been updated successfully');
        }
        
    } catch (error) {
        console.log('Error status:', error);
    }
}

InputcurrentColorPallete();

