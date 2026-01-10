// chart.js
import Chart from "chart.js/auto";
import jQuery from "jquery";
import momentLib from "moment";

// ✅ Import daterangepicker directly (no dynamic import needed)
import "daterangepicker";
import "daterangepicker/daterangepicker.css";

// Set globals for daterangepicker
if (typeof window !== "undefined") {
    window.jQuery = jQuery;
    window.$ = jQuery;
    window.moment = momentLib;
}

const moment = momentLib;

// Global state
let patientData = {};
let barChart = null;
let pieChart = null;

// Date range state
let barChartDateRange = {
    start: moment().startOf("year"),
    end: moment().endOf("year"),
};

let pieChartDateRange = {
    start: moment().startOf("year"),
    end: moment().endOf("year"),
};

// ❌ REMOVE THIS - No longer needed
// let daterangepickerLoaded = false;
// async function loadDateRangePicker() {
//     if (!daterangepickerLoaded) {
//         try {
//             await import("daterangepicker");
//             await import("daterangepicker/daterangepicker.css");
//             daterangepickerLoaded = true;
//         } catch (error) {
//             console.error("Failed to load daterangepicker:", error);
//             throw error;
//         }
//     }
// }

// Fetch patient data for charts
async function loadPatientData(startDate, endDate) {
    try {
        const response = await fetch(
            `/dashboard/monthly-stats?start_date=${startDate}&end_date=${endDate}`,
            {
                headers: { Accept: "application/json" },
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Add colors to datasets
        Object.keys(data).forEach((key) => {
            data[key].backgroundColor = "rgba(40, 167, 69, 0.8)";
            data[key].borderColor = "rgba(40, 167, 69, 1)";
        });

        patientData = data;
        return data;
    } catch (error) {
        console.error("Failed to load patient data:", error);
        throw error;
    }
}

// Initialize date range pickers
function initDateRangePickers() {
    const pickerConfig = {
        opens: "left",
        drops: "down",
        showDropdowns: true,
        autoApply: false,
        showCustomRangeLabel: true,
        alwaysShowCalendars: true,
        locale: {
            format: "MMM D, YYYY",
            separator: " - ",
            applyLabel: "Apply",
            cancelLabel: "Cancel",
            fromLabel: "From",
            toLabel: "To",
            customRangeLabel: "Custom Range",
            weekLabel: "W",
            daysOfWeek: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
            monthNames: [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December",
            ],
            firstDay: 0,
        },
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [
                moment().subtract(1, "days"),
                moment().subtract(1, "days"),
            ],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [
                moment().subtract(1, "month").startOf("month"),
                moment().subtract(1, "month").endOf("month"),
            ],
            "Last 3 Months": [
                moment().subtract(3, "months").startOf("month"),
                moment().endOf("month"),
            ],
            "Last 6 Months": [
                moment().subtract(6, "months").startOf("month"),
                moment().endOf("month"),
            ],
            "This Year": [moment().startOf("year"), moment().endOf("year")],
            "Last Year": [
                moment().subtract(1, "year").startOf("year"),
                moment().subtract(1, "year").endOf("year"),
            ],
        },
    };

    // Bar Chart Date Range
    const barRangeInput = $("#dateRange");
    if (barRangeInput.length) {
        barRangeInput.daterangepicker(
            {
                ...pickerConfig,
                startDate: barChartDateRange.start,
                endDate: barChartDateRange.end,
            },
            function (start, end, label) {
                barChartDateRange = { start, end };
                reloadBarChart();
            }
        );
    } else {
        console.warn("⚠ Bar chart date range input not found");
    }

    // Pie Chart Date Range
    const pieRangeInput = $("#pieChartDateRange");
    if (pieRangeInput.length) {
        pieRangeInput.daterangepicker(
            {
                ...pickerConfig,
                startDate: pieChartDateRange.start,
                endDate: pieChartDateRange.end,
            },
            function (start, end, label) {
                pieChartDateRange = { start, end };
                reloadPieChart();
            }
        );
    } else {
        console.warn("⚠ Pie chart date range input not found");
    }
}

// Initialize bar chart
async function initBarChart() {
    const canvas = document.getElementById("patientChart");
    if (!canvas) {
        console.error("✗ Canvas element 'patientChart' not found!");
        return;
    }

    const ctx = canvas.getContext("2d");
    if (!ctx) {
        console.error("✗ Could not get 2D context for patientChart");
        return;
    }

    try {
        const startDate = barChartDateRange.start.format("YYYY-MM-DD");
        const endDate = barChartDateRange.end.format("YYYY-MM-DD");

        await loadPatientData(startDate, endDate);

        if (!patientData.all) {
            console.error("✗ No patient data available");
            return;
        }

        // Destroy existing chart if it exists
        if (barChart) {
            barChart.destroy();
        }

        barChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: patientData.all.months || [],
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
    } catch (error) {
        console.error("✗ Failed to initialize bar chart:", error);
    }
}

// Update chart based on patient type selection
function updateChart(patientType) {
    if (!barChart || !patientData[patientType]) {
        console.warn("Chart or data not available for:", patientType);
        return;
    }

    const selectedData = patientData[patientType];

    barChart.data.datasets[0] = {
        label: selectedData.label,
        data: selectedData.data,
        backgroundColor: selectedData.backgroundColor,
        borderColor: selectedData.borderColor,
        borderWidth: 2,
        borderRadius: 8,
        borderSkipped: false,
    };

    barChart.data.labels = selectedData.months || [];
    barChart.update("active");
    updateStats(patientType);
}

// Update statistics summary
function updateStats(patientType) {
    if (!patientData[patientType]) return;

    const data = patientData[patientType].data;
    const months = patientData[patientType].months || [];

    const total = data.reduce((a, b) => a + b, 0);
    const average = data.length > 0 ? Math.round(total / data.length) : 0;
    const maxValue = Math.max(...data);
    const peakIndex = data.indexOf(maxValue);
    const peakMonth = months[peakIndex] || "N/A";

    const totalEl = document.getElementById("totalPatients");
    const avgEl = document.getElementById("avgMonthly");
    const peakEl = document.getElementById("peakMonth");

    if (totalEl) totalEl.textContent = total.toLocaleString();
    if (avgEl) avgEl.textContent = average.toLocaleString();
    if (peakEl) peakEl.textContent = peakMonth;
}

// Reload bar chart with new date range
async function reloadBarChart() {
    await initBarChart();
}

// Initialize pie chart
async function initPieChart() {
    const canvas = document.getElementById("myPieChart");
    if (!canvas) {
        console.error("✗ Canvas element 'myPieChart' not found!");
        return;
    }

    const ctx = canvas.getContext("2d");
    if (!ctx) {
        console.error("✗ Could not get 2D context for myPieChart");
        return;
    }

    await reloadPieChart();
}

// Reload pie chart with new date range
async function reloadPieChart() {
    const startDate = pieChartDateRange.start.format("YYYY-MM-DD");
    const endDate = pieChartDateRange.end.format("YYYY-MM-DD");

    try {
        const response = await fetch(
            `/dashboard/pie-chart-data?start_date=${startDate}&end_date=${endDate}`,
            {
                headers: {
                    Accept: "application/json",
                },
            }
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const canvas = document.getElementById("myPieChart");
        const ctx = canvas.getContext("2d");

        // Destroy existing chart if it exists
        if (pieChart) {
            pieChart.destroy();
        }

        pieChart = new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: [
                    "Vaccination",
                    "Prenatal",
                    "Senior Citizen",
                    "TB Dots",
                    "Family Planning",
                ],
                datasets: [
                    {
                        label: "Patient Categories",
                        data: [
                            data.vaccinationCount || 0,
                            data.prenatalCount || 0,
                            data.seniorCitizenCount || 0,
                            data.tbDotsCount || 0,
                            data.familyPlanningCount || 0,
                        ],
                        backgroundColor: [
                            "#FFC107",
                            "#DC3545",
                            "#007BFF",
                            "#2E8B57",
                            "#FF8C00",
                        ],
                        borderWidth: 2,
                        borderColor: "#fff",
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12,
                            },
                        },
                    },
                    title: {
                        display: true,
                        text: "Patient Distribution",
                        font: {
                            size: 18,
                            weight: "bold",
                        },
                        color: "#333",
                        align: "start",
                        padding: {
                            top: 10,
                            bottom: 20,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || "";
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce(
                                    (a, b) => a + b,
                                    0
                                );
                                const percentage =
                                    total > 0
                                        ? ((value / total) * 100).toFixed(1)
                                        : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            },
                        },
                    },
                },
            },
        });
    } catch (error) {
        console.error("✗ Failed to load pie chart:", error);
    }
}

// Initialize patient count per area
async function initCountPerArea() {
    try {
        const response = await fetch("/dashboard/patient-count-per-area", {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const container = document.querySelector(".patient-per-area-con");
        if (!container) {
            console.warn("⚠ Container '.patient-per-area-con' not found");
            return;
        }

        container.innerHTML = "";

        Object.entries(data.data).forEach(([key, value]) => {
            container.innerHTML += `
                <div class="service-item">
                    <div class="service-label">${key}</div>
                    <div class="service-count" style="background-color: #065A24;">${
                        value ?? 0
                    }</div>
                </div>
            `;
        });
    } catch (error) {
        console.error("✗ Failed to load count per area:", error);
    }
}

// Initialize today's patient count
async function initPatientToday() {
    try {
        const response = await fetch("/dashboard/today/added-patient", {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const elements = {
            vaccination: document.getElementById("vaccination-patient-today"),
            prenatal: document.getElementById("prenatal-patient-today"),
            seniorCitizen: document.getElementById(
                "senior-citizen-patient-today"
            ),
            tbDots: document.getElementById("tb-dots-patient-today"),
            familyPlanning: document.getElementById(
                "family-planning-patient-today"
            ),
        };

        if (elements.vaccination)
            elements.vaccination.textContent = data.vaccinationCount || 0;
        if (elements.prenatal)
            elements.prenatal.textContent = data.prenatalCount || 0;
        if (elements.seniorCitizen)
            elements.seniorCitizen.textContent = data.seniorCitizenCount || 0;
        if (elements.tbDots)
            elements.tbDots.textContent = data.tbDotsCount || 0;
        if (elements.familyPlanning)
            elements.familyPlanning.textContent = data.familyPlanningCount || 0;
    } catch (error) {
        console.error("✗ Failed to load today's patient count:", error);
    }
}

// Main initialization
document.addEventListener("DOMContentLoaded", async function () {
    try {
        // ✅ No need to load daterangepicker - it's already imported at the top

        // Initialize date range pickers
        initDateRangePickers();

        // Initialize all dashboard components
        await Promise.all([
            initBarChart(),
            initPieChart(),
            initCountPerArea(),
            initPatientToday(),
        ]);

        // Setup patient type dropdown listener
        const patientTypeSelect = document.getElementById("patientType");
        if (patientTypeSelect) {
            patientTypeSelect.addEventListener("change", function (e) {
                updateChart(e.target.value);
            });
        } else {
            console.warn("⚠ Patient type dropdown not found");
        }

        // Setup PDF download button with BOTH date ranges
        const dashboardChartsBtn = document.getElementById("dashboardCharts");
        if (dashboardChartsBtn) {
            dashboardChartsBtn.addEventListener("click", function (e) {
                e.preventDefault();

                // Get BAR CHART date range
                const barStartDate =
                    barChartDateRange.start.format("YYYY-MM-DD");
                const barEndDate = barChartDateRange.end.format("YYYY-MM-DD");

                // Get PIE CHART date range
                const pieStartDate =
                    pieChartDateRange.start.format("YYYY-MM-DD");
                const pieEndDate = pieChartDateRange.end.format("YYYY-MM-DD");

                // Get the selected patient type
                const patientType =
                    document.getElementById("patientType").value;

                // Build the URL with BOTH date ranges
                const params = new URLSearchParams({
                    bar_start_date: barStartDate,
                    bar_end_date: barEndDate,
                    pie_start_date: pieStartDate,
                    pie_end_date: pieEndDate,
                    patient_type: patientType,
                });

                const url = `/pdf/generate/graph?${params.toString()}`;

                // Open in new tab
                window.open(url, "_blank");
            });
        } else {
            console.warn("⚠ Dashboard charts button not found");
        }

        console.log("✅ Dashboard initialization complete");
    } catch (error) {
        console.error("❌ Dashboard initialization failed:", error);
    }
});
