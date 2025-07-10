const primaryBox = document.getElementById('primary_color');
const primaryHex = document.getElementById('primary_hex');
// secondary
const secondaryBox = document.getElementById('secondary_color');
const secondaryHex = document.getElementById('secondary_hex');
// tertiary
const tertiaryBox = document.getElementById('tertiary_color');
const tertiaryHex = document.getElementById('tertiary_hex');

primaryBox.addEventListener('change', () => {
    primaryHex.value = primaryBox.value;
})
// hex color input
primaryHex.addEventListener('change', () => {
    primaryBox.value =  primaryHex.value;
})

// secondary color
secondaryBox.addEventListener('change', () => {
    secondaryHex.value = secondaryBox.value;
})
secondaryHex.addEventListener('change', () => {
    secondaryBox.value =  secondaryHex.value;
})
// tertiary
tertiaryBox.addEventListener('change', () => {
    tertiaryHex.value = tertiaryBox.value;
})
tertiaryHex.addEventListener('change', () => {
    tertiaryBox.value =  tertiaryHex.value;
})