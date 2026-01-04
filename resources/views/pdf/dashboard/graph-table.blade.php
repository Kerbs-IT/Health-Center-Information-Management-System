<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Document</title>

    <style>
        /* monthy */
        .charts {
            display: flex;
            gap: 20px;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* FIXED CHART CONTAINER - NO MORE INVISIBLE MARGINS */
        .chart-container {
            width: 600px;
            height: 400px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid black;
            border-collapse: collapse;
            flex: 1 1 auto;
            min-width: 0;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .chart-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            flex-shrink: 0;
        }

        .filter-container {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .filter-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            white-space: nowrap;
        }

        .filter-select {
            padding: 8px 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            background: white;
            color: #495057;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 160px;
        }

        .filter-select:hover {
            border-color: #007bff;
        }

        .filter-select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        /* FIXED CANVAS CONTAINER - NO OVERFLOW */
        .canvas-container {

            height: 250px;
            margin-top: 5px;
        }

        /* PIE CHART CONTAINER */
        .pie-chart-container {
            flex: 1;
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        #myPieChart {
            background-color: white;
            border-radius: 10px;
            max-height: 100%;
            max-width: 100%;
        }

        .pie-chart-container {
            border: 1px solid black;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1200px) {
            .chart-title {
                font-size: 20px;
            }

            .chart-header {
                align-items: stretch;
                gap: 10px;
            }

            .filter-container {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .charts {
                gap: 20px;
            }

            .pie-chart-container {
                height: 300px;
            }

            .canvas-container {
                height: 250px;
            }
        }

        #myChart {
            background-color: white;
            border-radius: 10px;
        }

        #myPieChart {
            background-color: white;
            border-radius: 10px;
            height: 520px;
            width: 520px;
        }
    </style>

</head>

<body>
    <script>
        // prettier-ignore
        window.patientDataFromServer = @json($patientData);
        window.pieChartDataFromServer = @json($pieData);
    </script>

    <div class="container mt-5">
        <h3 class="fw-bold text-center ">HEALTH CENTER INFORMATION MANAGEMENT SYSTEM</h3>
        <h5 class="fw-light text-center">Brgy.Hugo Perez,Proper</h5>
        <h1 class="chart-title mb-3">Monthly Patient Statistics</h1>
        <!-- by two -->
        <div class="mb-3 d-flex gap-3 flex-column">
            <div class="chart-container flex-fill  card w-100">
                <div class="chart-header d-flex justify-content-between">
                    <h3 class="mb-0">Vaccination</h3>
                    <div id="stats-vaccination" class="stats"></div>
                </div>

                <div class="canvas-container w-100 mb-2">
                    <canvas id="vaccinationChart" width="600" height="300"></canvas>
                </div>

            </div>
            <div class="chart-container flex-fill  card w-100">
                <div class="chart-header d-flex justify-content-between">
                    <h3>Prenatal</h3>
                    <div id="stats-prenatal" class="stats"></div>
                </div>

                <div class="canvas-container w-100">
                    <canvas width="600" height="300" id="prenatalChart"></canvas>
                </div>

            </div>
        </div>
        <div style="page-break-before: always;"></div>
        <div class="mb-3 d-flex gap-3 flex-column mt-5">
            <div class="chart-container flex-fill  card w-100">
                <div class="chart-header d-flex justify-content-between">
                    <h3>Tb-dots</h3>
                    <div id="stats-tb" class="stats"></div>
                </div>

                <div class="canvas-container w-100">
                    <canvas width="600" height="300" id="tbChart"></canvas>
                </div>

            </div>
            <div class="chart-container flex-fill  card w-100">
                <div class="chart-header d-flex justify-content-between">
                    <h3>Senior Citizen</h3>
                    <div id="stats-senior" class="stats"></div>
                </div>

                <div class="canvas-container w-100">
                    <canvas width="600" height="300" id="seniorChart"></canvas>
                </div>

            </div>
        </div>
        <div style="page-break-before: always;"></div>
        <div class="d-flex gap-2 flex-column mt-5 mb-3">
            <div class="chart-container flex-fill card w-100">
                <div class="chart-header d-flex justify-content-between">
                    <h3>Family Planning</h3>
                    <div id="stats-family_planning" class="stats-family_planning"></div>
                </div>

                <div class="canvas-container w-100">
                    <canvas width="600" height="300" id="family_planningChart"></canvas>
                </div>

            </div>
        </div>
        <div class="pie-chart">
            <h3 class="fw-bold">Patient Count Distribution</h3>
            <div class="flex-grow-1 flex-shrink-1 xl:max-w-[520px] xl:min-h-[520px] d-flex align-items-center chart-canvas justify-content-center bg-white rounded p-3 pie-chart-container">
                <canvas id="myPieChart"></canvas>
            </div>
        </div>
    </div>
    <script>
        let patientData = {};

        async function loadPatientData() {
            if (window.patientDataFromServer) {
                const data = window.patientDataFromServer;
                console.log("Using data from server:", data);

                if (data && typeof data === "object") {
                    Object.keys(data).forEach((key) => {
                        if (data[key] && typeof data[key] === "object") {
                            data[key].backgroundColor = "rgba(40, 167, 69, 0.8)";
                            data[key].borderColor = "rgba(40, 167, 69, 1)";
                        }
                    });
                }

                patientData = data;
                return;
            }
        }

        const year = new Date().getFullYear();

        document.addEventListener("DOMContentLoaded", function() {
            const months = [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
            ];

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
                    if (!patientData[type]) {
                        console.error("No data for:", type);
                        return;
                    }

                    const canvas = document.getElementById(`${type}Chart`);
                    if (!canvas) {
                        console.error("Canvas not found:", type);
                        return;
                    }

                    const ctx = canvas.getContext("2d");
                    const data = patientData[type].data;
                    const dataLabel = patientData[type].label;

                    new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: months,
                            datasets: [{
                                label: dataLabel,
                                data: data,
                                backgroundColor: "rgba(40, 167, 69, 0.8)",
                                borderColor: "rgba(40, 167, 69, 1)",
                                borderWidth: 2,
                                borderRadius: 8,
                                borderSkipped: false,
                            }],
                        },
                        options: {
                            responsive: false,
                            maintainAspectRatio: false,
                            animation: false,
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
                                        title: function(context) {
                                            return `${context[0].label} ${year}`;
                                        },
                                        label: function(context) {
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
                                            weight: "500"
                                        },
                                        color: "#666",
                                    },
                                    title: {
                                        display: true,
                                        text: "Number of Patients",
                                        font: {
                                            size: 14,
                                            weight: "600"
                                        },
                                        color: "#333",
                                    },
                                    max: 100,
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 12,
                                            weight: "500"
                                        },
                                        color: "#666",
                                    },
                                    title: {
                                        display: true,
                                        text: "Month",
                                        font: {
                                            size: 14,
                                            weight: "600"
                                        },
                                        color: "#333",
                                    },
                                },
                            },
                        },
                    });

                    updateStats(type);
                    console.log("Chart created for:", type);
                });
            }

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

                const counts = [
                    data.vaccinationCount,
                    data.prenatalCount,
                    data.seniorCitizenCount,
                    data.tbDotsCount,
                    data.familyPlanningCount,
                ];

                const pieChart = document.getElementById("myPieChart").getContext("2d");
                new Chart(pieChart, {
                    type: "doughnut",
                    data: {
                        labels: ["Vaccination", "Prenatal", "Senior Citizen", "TB Dots", "Family-planning"],
                        datasets: [{
                            label: "Patient Categories",
                            data: counts,
                            backgroundColor: ["yellow", "red", "blue", "rgba(46, 139, 87, 1)", "orange"],
                            borderWidth: 1,
                        }],
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        animation: false,
                        plugins: {
                            legend: {
                                position: "right", // ✅ Changed to right
                                labels: {
                                    font: {
                                        size: 16, // ✅ Increase font size
                                        weight: 'bold',
                                        color: 'black' // ✅ Make it bold
                                    },
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const value = data.datasets[0].data[i];
                                                return {
                                                    text: `${label}: ${value}`, // ✅ Shows "Label: Number"
                                                    fillStyle: data.datasets[0].backgroundColor[i],
                                                    hidden: false,
                                                    index: i
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                }
                            },

                        },
                    },
                });
            }


            window.addEventListener("load", initChart);
            window.addEventListener("load", initPieChart);
        });
    </script>
</body>

</html>