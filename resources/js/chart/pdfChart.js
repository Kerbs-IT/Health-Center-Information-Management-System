import Chart from "chart.js/auto";

let patientData = {};
async function loadPatientData() {
    // Check if data was passed from server (for PDF)
    if (window.patientDataFromServer) {
        const data = window.patientDataFromServer;
        console.log("Using data from server:", data);

        // ✅ ADD NULL CHECK BEFORE SETTING COLORS
        if (data && typeof data === "object") {
            Object.keys(data).forEach((key) => {
                if (data[key] && typeof data[key] === "object") {
                    // ✅ Check if not null
                    data[key].backgroundColor = "rgba(40, 167, 69, 0.8)";
                    data[key].borderColor = "rgba(40, 167, 69, 1)";
                }
            });
        }

        patientData = data;
        return;
    }

    // Fallback to fetch for regular page
    const response = await fetch("/dashboard/monthly-stats", {
        headers: { Accept: "application/json" },
    });

    if (!response.ok) {
        console.error("Failed to load chart data");
        return;
    }

    const data = await response.json();
    console.log("chart data:", data);

    Object.keys(data).forEach((key) => {
        if (data[key] && typeof data[key] === "object") {
            data[key].backgroundColor = "rgba(40, 167, 69, 0.8)";
            data[key].borderColor = "rgba(40, 167, 69, 1)";
        }
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

       const chartTypes = [
           "vaccination",
           "prenatal",
           "tb",
           "senior",
           "family_planning",
       ];
       chartTypes.forEach(type => {
           const ctx = document.getElementById(`${type}Chart`).getContext("2d");
           const data = patientData[type].data;
           const dataLabel = patientData[type].label;
           chart = new Chart(ctx, {
               type: "bar",
               data: {
                   labels: months,
                   datasets: [
                       {
                           label: dataLabel,
                           data: data,
                           backgroundColor: patientData.all.backgroundColor,
                           borderColor: patientData.all.borderColor,
                           borderWidth: 2,
                           borderRadius: 8,
                           borderSkipped: false,
                       },
                   ],
               },
               options: {
                   responsive: false,
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
                           max: 100,
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
                   animation:false
               },
           });
           updateStats(type);
       });
       
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

       document.getElementById(`stats-${patientType}`).innerHTML = `
        Total: ${total} | Average: ${average} | Peak: ${peakMonth}
    `;
    }
     async function initPieChart() {
        const data = window.pieChartDataFromServer;
        
        if (!data) {
            console.error("No pie chart data!");
            return;
        }
        
        const pieChart = document.getElementById("myPieChart").getContext("2d");
        new Chart(pieChart, {
            type: "doughnut",
            data: {
                labels: ["Vaccination", "Prenatal", "Senior Citizen", "TB Dots", "Family-planning"],
                datasets: [{
                    label: "Patient Categories",
                    data: [
                        data.vaccinationCount,
                        data.prenatalCount,
                        data.seniorCitizenCount,
                        data.tbDotsCount,
                        data.familyPlanningCount,
                    ],
                    backgroundColor: ["yellow", "red", "blue", "rgba(46, 139, 87, 1)", "orange"],
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: false, // ✅ Set to false for PDF
                maintainAspectRatio: false,
                animation: false, // ✅ Disable animation for PDF
                plugins: {
                    legend: { position: "bottom" },
                    title: {
                        display: true,
                        text: "Patient Distribution",
                        font: { size: 20, weight: "bold" },
                        color: "rgba(0,0,0)",
                        align: "start",
                    },
                },
            },
        });
    }


    // Initialize chart when page loads
    window.addEventListener("load", initChart);
});