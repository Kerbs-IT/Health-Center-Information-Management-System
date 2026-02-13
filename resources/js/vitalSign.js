const blood_pressure = document.getElementById("blood_pressure");

const temperature = document.getElementById(
    "temperature",
);
const pulse_rate = document.getElementById(
    "pulse_rate",
);
const respiratory_rate = document.getElementById(
    "respiratory_rate",
);
const height = document.getElementById("height");
const weight = document.getElementById("weight");

if (
    blood_pressure &&
    height &&
    weight &&
    pulse_rate &&
    respiratory_rate &&
    temperature
) {
    Inputmask({
        mask: "99[9]/99[9]",
        placeholder:"",
        clearIncomplete:false
    }).mask(blood_pressure);
    // Temperature (e.g., 36.5)
    Inputmask({
        alias: "decimal",
        digits: 2,
        placeholder: "",
        min: 0,
        max: 59.99,
        rightAlign: false,
    }).mask(temperature);

    // Pulse Rate (e.g., 60-100 or just 72)
    Inputmask({
        mask: "999", 
        placeholder: "",
        clearIncomplete: false,
    }).mask(pulse_rate);

    // Respiratory Rate (e.g., 16)
    Inputmask({
        mask: "99",
        placeholder: "",
        min: 0,
        max: 60,
    }).mask(respiratory_rate);

    // Height in cm (e.g., 175.5)
    Inputmask({
        alias: "decimal",
        digits: 2,
        min: 0,
        max: 300,
        rightAlign: false,
    }).mask(height);

    // Weight in kg (e.g., 65.5)
    Inputmask({
        alias: "decimal",
        digits: 2,
        min: 0.1,
        max: 500,
        rightAlign: false,
    }).mask(weight);
}

export function vitalSignInputMask(blood_pressure,temperature,pulse_rate,respiratory_rate,height,weight) {
    Inputmask({
        mask: "99[9]/99[9]",
        placeholder: " ",
    }).mask(blood_pressure);
    // Temperature (e.g., 36.5)
    Inputmask({
        alias: "decimal",
        digits: 2,
        min: 0,
        max:59.99,
        rightAlign: false,
    }).mask(temperature);

    // Pulse Rate (e.g., 60-100 or just 72)
    Inputmask({
        mask: "999", // allows "72" or "60-100"
        placeholder: "",
        clearIncomplete: false,
    }).mask(pulse_rate);

    // Respiratory Rate (e.g., 16)
    Inputmask({
        mask: "99",
        placeholder: "",
        min: 0,
        max: 60,
    }).mask(respiratory_rate);

    // Height in cm (e.g., 175.5)
    Inputmask({
        alias: "decimal",
        digits: 2,
        min: 0,
        max: 250,
        rightAlign: false,
    }).mask(height);

    // Weight in kg (e.g., 65.5)
    Inputmask({
        alias: "decimal",
        digits: 2,
        min: 0,
        max: 250,
        rightAlign: false,
    }).mask(weight);
}