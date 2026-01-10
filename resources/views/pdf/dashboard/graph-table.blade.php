<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Dashboard Report</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #28a745;
            padding-bottom: 15px;
        }

        .date-range {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .chart-container {
            width: 100%;
            background: white;
            border-radius: 12px;
            padding: 30px;
            border: 2px solid #333;
            margin-bottom: 40px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }

        .chart-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .stats {
            font-size: 16px;
            color: #666;
            font-weight: 600;
        }

        .canvas-container {
            height: 400px;
            margin-top: 20px;
        }

        .pie-chart-container {
            width: 100%;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            border-radius: 12px;
            padding: 30px;
        }

        canvas {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>

<body>
    <script>
        window.patientDataFromServer = @json($patientData);
        window.pieChartDataFromServer = @json($pieData);
        window.selectedPatientType = '{{ $selectedType ?? "all" }}';
    </script>

    <div class="header">
        <h2 class="fw-bold">HEALTH CENTER INFORMATION MANAGEMENT SYSTEM</h2>
        <h5 class="fw-light">Brgy. Hugo Perez, Proper</h5>
        <h3 class="fw-bold mt-3">Monthly Patient Statistics Report</h3>
    </div>

    <div class="date-range">
        Bar Chart Period: {{ $barDateRangeText }}
    </div>

    <!-- Single Bar Chart (based on selection) -->
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title" id="chartTitle">Monthly Patient Statistics</h3>
            <div id="stats-display" class="stats"></div>
        </div>
        <div class="canvas-container">
            <canvas id="mainChart" width="1000" height="400"></canvas>
        </div>
    </div>

    <div class="page-break" style="page-break-after: always;"></div>
    <!-- Pie Chart -->
    <div class="date-range">
        Pie Chart Period: {{ $pieDateRangeText }}
    </div>
    <div class="chart-container">
        <h3 class="chart-title text-center mb-4">Patient Distribution Overview</h3>
        <div class="pie-chart-container">
            <canvas id="myPieChart" width="700" height="500"></canvas>
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

        document.addEventListener("DOMContentLoaded", function() {
            async function initChart() {
                await loadPatientData();

                const selectedType = window.selectedPatientType || 'all';

                if (!patientData[selectedType]) {
                    console.error("No data for selected type:", selectedType);
                    return;
                }

                const canvas = document.getElementById('mainChart');
                if (!canvas) {
                    console.error("Canvas not found");
                    return;
                }

                const ctx = canvas.getContext("2d");
                const chartData = patientData[selectedType];
                const data = chartData.data;
                const months = chartData.months || [];
                const dataLabel = chartData.label;

                // Update title
                document.getElementById('chartTitle').textContent = dataLabel;

                // Create the bar chart
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
                                        size: 16,
                                        weight: "600",
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
                                        size: 14,
                                        weight: "500"
                                    },
                                    color: "#666",
                                },
                                title: {
                                    display: true,
                                    text: "Number of Patients",
                                    font: {
                                        size: 16,
                                        weight: "600"
                                    },
                                    color: "#333",
                                },
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 14,
                                        weight: "500"
                                    },
                                    color: "#666",
                                },
                                title: {
                                    display: true,
                                    text: "Month",
                                    font: {
                                        size: 16,
                                        weight: "600"
                                    },
                                    color: "#333",
                                },
                            },
                        },
                    },
                });

                // Update stats
                const total = data.reduce((a, b) => a + b, 0);
                const average = data.length > 0 ? Math.round(total / data.length) : 0;
                const maxValue = Math.max(...data);
                const peakIndex = data.indexOf(maxValue);
                const peakMonth = months[peakIndex] || 'N/A';

                document.getElementById('stats-display').innerHTML =
                    `Total: ${total} | Average: ${average} | Peak: ${peakMonth}`;

                console.log("Chart created successfully");
            }

            async function initPieChart() {
                const data = window.pieChartDataFromServer;

                if (!data) {
                    console.error("No pie chart data!");
                    return;
                }

                const counts = [
                    data.vaccinationCount || 0,
                    data.prenatalCount || 0,
                    data.seniorCitizenCount || 0,
                    data.tbDotsCount || 0,
                    data.familyPlanningCount || 0,
                ];

                const canvas = document.getElementById("myPieChart");
                if (!canvas) {
                    console.error("Pie chart canvas not found!");
                    return;
                }

                const pieChart = canvas.getContext("2d");
                new Chart(pieChart, {
                    type: "doughnut",
                    data: {
                        labels: ["Vaccination", "Prenatal", "Senior Citizen", "TB Dots", "Family Planning"],
                        datasets: [{
                            label: "Patient Categories",
                            data: counts,
                            backgroundColor: [
                                "#FFC107",
                                "#DC3545",
                                "#007BFF",
                                "#2E8B57",
                                "#FF8C00"
                            ],
                            borderWidth: 2,
                            borderColor: "#fff",
                        }],
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        animation: false,
                        plugins: {
                            legend: {
                                position: "bottom",
                                labels: {
                                    font: {
                                        size: 16,
                                        weight: 'bold',
                                    },
                                    padding: 20,
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const value = data.datasets[0].data[i];
                                                return {
                                                    text: `${label}: ${value}`,
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

                console.log("Pie chart created successfully");
            }

            window.addEventListener("load", initChart);
            window.addEventListener("load", initPieChart);
        });
    </script>
</body>

</html>