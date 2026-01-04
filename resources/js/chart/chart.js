import Chart from "chart.js/auto";

let patientData = {};

async function loadPatientData() {
    const response = await fetch("/dashboard/monthly-stats", {
        headers: { Accept: "application/json" },
    });

    if (!response.ok) {
        console.error("Failed to load chart data");
        return;
    }

    const data = await response.json();

    // Add colors (since backend only sends data + label)
    Object.keys(data).forEach((key) => {
        data[key].backgroundColor = "rgba(40, 167, 69, 0.8)";
        data[key].borderColor = "rgba(40, 167, 69, 1)";
    });

    patientData = data;
}
// get the current year
const year = new Date().getFullYear();

document.addEventListener("DOMContentLoaded", function (e) {


    const months = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
    ];

    let chart;

    // Initialize chart
   async function initChart() {
        await loadPatientData();
        const ctx = document.getElementById("patientChart").getContext("2d");

        chart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: months,
                datasets: [
                    {
                        label: patientData.all.label,
                        data: patientData.all.data,
                        backgroundColor: patientData.all.backgroundColor,
                        borderColor: patientData.all.borderColor,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 14,
                                weight: "600",
                            },
                        },
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        titleColor: "white",
                        bodyColor: "white",
                        borderColor: "rgba(255, 255, 255, 0.2)",
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            title: function (context) {
                                return `${context[0].label} ${year}`;
                            },
                            label: function (context) {
                                return `${context.dataset.label}: ${context.parsed.y} patients`;
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.1)",
                            lineWidth: 1,
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: "500",
                            },
                            color: "#666",
                        },
                        title: {
                            display: true,
                            text: "Number of Patients",
                            font: {
                                size: 14,
                                weight: "600",
                            },
                            color: "#333",
                        },
                        max:100
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: "500",
                            },
                            color: "#666",
                        },
                        title: {
                            display: true,
                            text: "Month",
                            font: {
                                size: 14,
                                weight: "600",
                            },
                            color: "#333",
                        },
                    },
                },
                animation: {
                    duration: 800,
                    easing: "easeInOutQuart",
                },
            },
        });

        updateStats("all");
    }

    // Update chart based on selected patient type
    function updateChart(patientType) {
        const selectedData = patientData[patientType];

        chart.data.datasets[0] = {
            label: selectedData.label,
            data: selectedData.data,
            backgroundColor: selectedData.backgroundColor,
            borderColor: selectedData.borderColor,
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        };

        chart.update("active");
        updateStats(patientType);
    }

    // Update statistics summary
    function updateStats(patientType) {
        const data = patientData[patientType].data;
        const total = data.reduce((a, b) => a + b, 0);
        const average = Math.round(total / data.length);
        const peakIndex = data.indexOf(Math.max(...data));
        const peakMonth = months[peakIndex];

        if (document.getElementById("totalPatients")) {
            document.getElementById("totalPatients").textContent =
                total.toLocaleString();
            document.getElementById("avgMonthly").textContent =
                average.toLocaleString();
            document.getElementById("peakMonth").textContent = peakMonth;
        }
    }

    // Event listener for dropdown change
    document
        .getElementById("patientType")
        .addEventListener("change", function (e) {
            updateChart(e.target.value);
        });

    // Initialize chart when page loads
    window.addEventListener("load", initChart);
    window.addEventListener("load", initPieChart);
    window.addEventListener("load", initCountPerArea);
    window.addEventListener("load", initPatientToday);
});

async function initPieChart(params) {
    try {
        const response = await fetch("/dashboard/info", {
            headers: {
                accept: "application/json",
            },
        });

        if (!response.ok) {
            console.log("Errors:", response.status);
            return;
        }

        const data = await response.json();

        const pieChart = document.getElementById("myPieChart").getContext("2d");
        const myPieChart = new Chart(pieChart, {
            type: "doughnut",
            data: {
                labels: [
                    "Vaccination",
                    "Prenatal",
                    "Senior Citizen",
                    "TB Dots",
                    "Family-planning",
                ],
                datasets: [
                    {
                        label: "Patient Categories",
                        data: [
                            data.vaccinationCount,
                            data.prenatalCount,
                            data.tbDotsCount,
                            data.seniorCitizenCount,
                            data.familyPlanningCount,
                        ],
                        backgroundColor: [
                            "yellow",
                            "red",
                            "blue",
                            "rgba(46, 139, 87, 1)",
                            "orange",
                        ],
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: "bottom",
                    },
                    title: {
                        display: true,
                        text: "Patient Distribution",
                        font: {
                            size: 20,
                            weight: "bold",
                        },
                        color: "rgba(0,0,0)",
                        align: "start",
                    },
                },
                maintainAspectRatio: false,
            },
        });
    } catch (error) {
        console.log("error:", error);
    }
}
async function initCountPerArea() {
    try {
        const response = await fetch('/dashboard/patient-count-per-area', {
            headers: {
                accept: "application/json",
            },
        });

        if (!response.ok) return;

        const data = await response.json();

        // console.log(data);

        const patientPerAreaCon = document.querySelector(
            ".patient-per-area-con"
        );
        Object.entries(data.data).forEach(([key, value]) => {
            patientPerAreaCon.innerHTML += `
             <div class="service-item">
                <div class="service-label">${key}</div>
                <div class="service-count" style="background-color: #065A24;">${value??0}</div>
            </div>
            `;
        });
    } catch (error) {
        
    }
}
async function initPatientToday() {
     try {
         const response = await fetch("/dashboard/today/added-patient", {
             headers: {
                 accept: "application/json",
             },
         });

         if (!response.ok) return;

         const data = await response.json();
         

         //  get the id of element
         const vaccinationElement = document.getElementById(
             "vaccination-patient-today"
         );
         const prenatalElement = document.getElementById(
             "prenatal-patient-today"
         );
         const seniorCitizenElement = document.getElementById(
             "senior-citizen-patient-today"
         );
         const tbDotsElement = document.getElementById("tb-dots-patient-today");
         const familyPlanningElement = document.getElementById(
             "family-planning-patient-today"
         );

         //  populate 
         vaccinationElement.innerHTML = data.vaccinationCount;
         prenatalElement.innerHTML = data.prenatalCount;
         seniorCitizenElement.innerHTML = data.seniorCitizenCount;
         tbDotsElement.innerHTML = data.tbDotsCount;
         familyPlanningElement.innerHTML = data.familyPlanningCount;

     } catch (error) {
         console.log("error:", error);
     }
}
