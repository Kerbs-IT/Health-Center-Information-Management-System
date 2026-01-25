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
    start: moment().subtract(6, "months").startOf("month"),
    end: moment().endOf("month"),
};

let pieChartDateRange = {
    start: moment().subtract(6, "months").startOf("month"),
    end: moment().endOf("month"),
};

let numberPerAreaRange = {
    start: moment().subtract(6, "months").startOf("month"),
    end: moment().endOf("month"),
};

// Age chart state
let ageChart = null;
let ageChartDateRange = {
    start: moment().subtract(6, "months").startOf("month"),
    end: moment().endOf("month"),
};

// initiliaze the age dirtribution table
initAgeChartDatePicker();

// Fetch patient data for charts
async function loadPatientData(startDate, endDate) {
    try {
        const response = await fetch(
            `/dashboard/monthly-stats?start_date=${startDate}&end_date=${endDate}`,
            {
                headers: { Accept: "application/json" },
            },
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
            },
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
            },
        );
    } else {
        console.warn("⚠ Pie chart date range input not found");
    }

    // number per area config
    const numberPerAreaInput = $("#areaDateRange");

    if (numberPerAreaInput.length) {
        numberPerAreaInput.daterangepicker(
            {
                ...pickerConfig,
                startDate: numberPerAreaRange.start,
                endDate: numberPerAreaRange.end,
            },
            function (start, end, label) {
                numberPerAreaRange = { start, end };
                initCountPerArea(
                    numberPerAreaRange.start,
                    numberPerAreaRange.end,
                );
            },
        );
    } else {
        console.warn("⚠ Area date range input not found");
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
            },
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
                                    0,
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
async function initCountPerArea(
    startDate = moment().startOf("year"),
    endDate = moment().endOf("year"),
) {
    try {
        const response = await fetch(
            `/dashboard/patient-count-per-area?startDate=${startDate.format("YYYY-MM-DD")}&endDate=${endDate.format("YYYY-MM-DD")}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const container = document.querySelector(".patient-per-area-con");
        if (!container) {
            console.warn("⚠ Container '.patient-per-area-con' not found");
            return;
        }

        // ✅ Calculate total from the data you already have
        const totalCount = Object.values(data.data).reduce(
            (sum, count) => sum + count,
            0,
        );
        const areaCount = Object.keys(data.data).length;

        // Update the header or display the total
        updateTotalDisplay(totalCount, areaCount);

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

function updateTotalDisplay(total, areas) {
    // Option: Update a display element in your header
    const totalElement = document.querySelector(".total-patient-count");
    if (totalElement) {
        totalElement.innerHTML = `Total: <span class="text-danger fw-bold fs-5">${total}</span> patient${total !== 1 ? "s" : ""} across  <span class="text-danger fw-bold fs-5">${areas}</span> areas`;
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
                "senior-citizen-patient-today",
            ),
            tbDots: document.getElementById("tb-dots-patient-today"),
            familyPlanning: document.getElementById(
                "family-planning-patient-today",
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
            initAgeChart(),
            initOverDueCount(),
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

                //  Show loading indicator on dashboard
                showLoadingIndicator();

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

        // Listen for completion message from the popup
        window.addEventListener("message", function (event) {
            if (event.data && event.data.type === "pdfGenerated") {
                hideLoadingIndicator();

                if (event.data.success) {
                    showNotification("PDF downloaded successfully!", "success");
                } else {
                    showNotification(
                        "Failed to generate PDF. Please try again.",
                        "error",
                    );
                }
            }
        });

        // GENERATE THE PATIENT PER AREA REPORT
        const patientPerAreaReportBtn =
            document.getElementById("patientPerAreaBtn");

        if (patientPerAreaReportBtn) {
            patientPerAreaReportBtn.addEventListener("click", async (e) => {
                e.preventDefault();

                const startDate = numberPerAreaRange.start.format("YYYY-MM-DD");
                const endDate = numberPerAreaRange.end.format("YYYY-MM-DD");
                // loading effect
                showLoadingIndicator();

                window.location.href = `/patient-per-area/detailed-report?startDate=${startDate}&endDate=${endDate}`;
                // after 2sec hide the loading indicator
                setTimeout(() => {
                    hideLoadingIndicator();
                }, 1000);
            });
        }

        // FOR AGE DISTRIBUTION
        // Setup age distribution patient type dropdown listener
        const agePatientTypeSelect = document.getElementById("agePatientType");
        if (agePatientTypeSelect) {
            agePatientTypeSelect.addEventListener("change", function (e) {
                updateAgeChart(e.target.value);
            });
        } else {
            console.warn("⚠ Age patient type dropdown not found");
        }

        const generateTable = document.getElementById("generateTableReport");
        if (generateTable) {
            generateTable.addEventListener("click", async (e) => {
                e.preventDefault();

                const startDate = ageChartDateRange.start.format("YYYY-MM-DD");
                const endDate = ageChartDateRange.end.format("YYYY-MM-DD");
                // loading effect
                showLoadingIndicator();

                window.location.href = `/pdf/generate/dashboard?start_date=${startDate}&end_date=${endDate}`;
                // after 2sec hide the loading indicator
                setTimeout(() => {
                    hideLoadingIndicator();
                }, 1000);
            });
        }
    } catch (error) {
        console.error("❌ Dashboard initialization failed:", error);
    }
});
// Function to show loading indicator
function showLoadingIndicator() {
    // Create loading overlay if it doesn't exist
    let loadingOverlay = document.getElementById("pdfLoadingOverlay");

    if (!loadingOverlay) {
        loadingOverlay = document.createElement("div");
        loadingOverlay.id = "pdfLoadingOverlay";
        loadingOverlay.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                        background: rgba(0,0,0,0.7); z-index: 9999; display: flex; 
                        align-items: center; justify-content: center;">
                <div style="background: white; padding: 40px; border-radius: 10px; 
                            text-align: center; box-shadow: 0 4px 30px rgba(0,0,0,0.3);">
                    <div style="border: 5px solid #f3f3f3; border-top: 5px solid #28a745; 
                                border-radius: 50%; width: 60px; height: 60px; 
                                animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                    <p style="font-size: 18px; font-weight: 600; color: #333; margin-bottom: 8px;">
                        Generating PDF...
                    </p>
                    <p style="font-size: 13px; color: #666;">
                        Please wait while charts are being rendered
                    </p>
                </div>
            </div>
        `;
        document.body.appendChild(loadingOverlay);

        // Add spin animation if not already added
        if (!document.getElementById("spinAnimationStyle")) {
            const style = document.createElement("style");
            style.id = "spinAnimationStyle";
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }
    }

    loadingOverlay.style.display = "block";
}

// Function to hide loading indicator
function hideLoadingIndicator() {
    const loadingOverlay = document.getElementById("pdfLoadingOverlay");
    if (loadingOverlay) {
        loadingOverlay.style.display = "none";
    }
}

// Function to show notifications
function showNotification(message, type) {
    const notification = document.createElement("div");
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px; 
        padding: 15px 25px; border-radius: 5px; 
        color: white; font-weight: 600; z-index: 10000;
        background: ${type === "success" ? "#28a745" : "#dc3545"};
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
    `;
    notification.textContent = message;

    // Add animation styles if not already added
    if (!document.getElementById("notificationAnimationStyle")) {
        const style = document.createElement("style");
        style.id = "notificationAnimationStyle";
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(notification);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = "slideOut 0.3s ease-out";
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Setup PDF download button with BOTH date ranges
const dashboardChartsBtn = document.getElementById("dashboardCharts");
if (dashboardChartsBtn) {
    dashboardChartsBtn.addEventListener("click", function (e) {
        e.preventDefault();

        // Show loading indicator on dashboard
        showLoadingIndicator();

        // Get BAR CHART date range
        const barStartDate = barChartDateRange.start.format("YYYY-MM-DD");
        const barEndDate = barChartDateRange.end.format("YYYY-MM-DD");

        // Get PIE CHART date range
        const pieStartDate = pieChartDateRange.start.format("YYYY-MM-DD");
        const pieEndDate = pieChartDateRange.end.format("YYYY-MM-DD");

        // Get the selected patient type
        const patientType = document.getElementById("patientType").value;

        // Build the URL with BOTH date ranges
        const params = new URLSearchParams({
            bar_start_date: barStartDate,
            bar_end_date: barEndDate,
            pie_start_date: pieStartDate,
            pie_end_date: pieEndDate,
            patient_type: patientType,
        });

        const url = `/pdf/generate/graph?${params.toString()}`;

        // Open in new tab (user will just see white page briefly)
        window.open(url, "_blank");
    });

    // Listen for completion message from the popup
    window.addEventListener("message", function (event) {
        if (event.data && event.data.type === "pdfGenerated") {
            hideLoadingIndicator();

            if (event.data.success) {
                showNotification("PDF downloaded successfully!", "success");
            } else {
                showNotification(
                    "Failed to generate PDF. Please try again.",
                    "error",
                );
            }
        }
    });
} else {
    console.warn("⚠ Dashboard charts button not found");
}

// ============================================
// AGE DISTRIBUTION CHART
// ============================================

// ============================================
// AGE DISTRIBUTION CHART
// ============================================

// Initialize age distribution chart
async function initAgeChart() {
    const canvas = document.getElementById("ageDistributionChart");
    if (!canvas) {
        console.error("✗ Canvas element 'ageDistributionChart' not found!");
        return;
    }

    const ctx = canvas.getContext("2d");
    if (!ctx) {
        console.error("✗ Could not get 2D context for ageDistributionChart");
        return;
    }

    await reloadAgeChart();
}

// Reload age chart with new date range and patient type
async function reloadAgeChart(patientType = "all") {
    const startDate = ageChartDateRange.start.format("YYYY-MM-DD");
    const endDate = ageChartDateRange.end.format("YYYY-MM-DD");

    try {
        const response = await fetch(
            `/dashboard/age-distribution?start_date=${startDate}&end_date=${endDate}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const canvas = document.getElementById("ageDistributionChart");
        const ctx = canvas.getContext("2d");

        // Destroy existing chart if it exists
        if (ageChart) {
            ageChart.destroy();
        }

        // Define patient type configurations
        const patientTypeConfig = {
            all: {
                label: "All Patients",
                data: [
                    (data.vaccination["0-11"] || 0) +
                        (data.prenatal["0-11"] || 0) +
                        (data.seniorCitizen["0-11"] || 0) +
                        (data.tbDots["0-11"] || 0) +
                        (data.familyPlanning["0-11"] || 0),

                    (data.vaccination["1-5"] || 0) +
                        (data.prenatal["1-5"] || 0) +
                        (data.seniorCitizen["1-5"] || 0) +
                        (data.tbDots["1-5"] || 0) +
                        (data.familyPlanning["1-5"] || 0),

                    (data.vaccination["6-17"] || 0) +
                        (data.prenatal["6-17"] || 0) +
                        (data.seniorCitizen["6-17"] || 0) +
                        (data.tbDots["6-17"] || 0) +
                        (data.familyPlanning["6-17"] || 0),

                    (data.vaccination["18-59"] || 0) +
                        (data.prenatal["18-59"] || 0) +
                        (data.seniorCitizen["18-59"] || 0) +
                        (data.tbDots["18-59"] || 0) +
                        (data.familyPlanning["18-59"] || 0),

                    (data.vaccination["60+"] || 0) +
                        (data.prenatal["60+"] || 0) +
                        (data.seniorCitizen["60+"] || 0) +
                        (data.tbDots["60+"] || 0) +
                        (data.familyPlanning["60+"] || 0),
                ],
                backgroundColor: "rgba(40, 167, 69, 0.8)",
                borderColor: "rgba(40, 167, 69, 1)",
            },
            vaccination: {
                label: "Vaccination",
                data: [
                    data.vaccination["0-11"] || 0,
                    data.vaccination["1-5"] || 0,
                    data.vaccination["6-17"] || 0,
                    data.vaccination["18-59"] || 0,
                    data.vaccination["60+"] || 0,
                ],
                backgroundColor: "rgba(255, 193, 7, 0.8)",
                borderColor: "rgba(255, 193, 7, 1)",
            },
            prenatal: {
                label: "Prenatal",
                data: [
                    data.prenatal["0-11"] || 0,
                    data.prenatal["1-5"] || 0,
                    data.prenatal["6-17"] || 0,
                    data.prenatal["18-59"] || 0,
                    data.prenatal["60+"] || 0,
                ],
                backgroundColor: "rgba(220, 53, 69, 0.8)",
                borderColor: "rgba(220, 53, 69, 1)",
            },
            seniorCitizen: {
                label: "Senior Citizen",
                data: [
                    data.seniorCitizen["0-11"] || 0,
                    data.seniorCitizen["1-5"] || 0,
                    data.seniorCitizen["6-17"] || 0,
                    data.seniorCitizen["18-59"] || 0,
                    data.seniorCitizen["60+"] || 0,
                ],
                backgroundColor: "rgba(0, 123, 255, 0.8)",
                borderColor: "rgba(0, 123, 255, 1)",
            },
            tbDots: {
                label: "TB Dots",
                data: [
                    data.tbDots["0-11"] || 0,
                    data.tbDots["1-5"] || 0,
                    data.tbDots["6-17"] || 0,
                    data.tbDots["18-59"] || 0,
                    data.tbDots["60+"] || 0,
                ],
                backgroundColor: "rgba(46, 139, 87, 0.8)",
                borderColor: "rgba(46, 139, 87, 1)",
            },
            familyPlanning: {
                label: "Family Planning",
                data: [
                    data.familyPlanning["0-11"] || 0,
                    data.familyPlanning["1-5"] || 0,
                    data.familyPlanning["6-17"] || 0,
                    data.familyPlanning["18-59"] || 0,
                    data.familyPlanning["60+"] || 0,
                ],
                backgroundColor: "rgba(255, 140, 0, 0.8)",
                borderColor: "rgba(255, 140, 0, 1)",
            },
        };

        const selectedConfig = patientTypeConfig[patientType];

        ageChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["0-11 mos", "1-5", "6-17", "18-59", "60+"],
                datasets: [
                    {
                        label: selectedConfig.label,
                        data: selectedConfig.data,
                        backgroundColor: selectedConfig.backgroundColor,
                        borderColor: selectedConfig.borderColor,
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
                    title: {
                        display: false,
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
                            precision: 0, // Show whole numbers only
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
                            text: "Age Range",
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

        // Update statistics summary
        updateAgeStats(selectedConfig.data, selectedConfig.label);
    } catch (error) {
        console.error("✗ Failed to load age distribution chart:", error);
    }
}

// Update age distribution statistics
function updateAgeStats(data, patientTypeLabel) {
    const total = data.reduce((a, b) => a + b, 0);

    // Find the age group with most patients
    const maxValue = Math.max(...data);
    const maxIndex = data.indexOf(maxValue);
    const ageGroups = [
        "0-11 months",
        "1-5 years",
        "6-17 years",
        "18-59 years",
        "60+ years",
    ];
    const mostCommonAgeGroup = maxValue > 0 ? ageGroups[maxIndex] : "—";

    // Update the summary cards
    const mostCommonEl = document.querySelector(".age-most-common");
    const totalPatientsEl = document.querySelector(".age-total-patients");
    const largestCategoryEl = document.querySelector(".age-largest-category");

    if (mostCommonEl) {
        mostCommonEl.textContent = mostCommonAgeGroup;
    }

    if (totalPatientsEl) {
        totalPatientsEl.textContent = total.toLocaleString();
    }

    if (largestCategoryEl) {
        largestCategoryEl.textContent = patientTypeLabel;
    }
}

// Update age chart based on patient type selection
function updateAgeChart(patientType) {
    reloadAgeChart(patientType);
}

// Initialize age chart date picker
function initAgeChartDatePicker() {
    const ageRangeInput = $("#ageChartDateRange");

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

    if (ageRangeInput.length) {
        ageRangeInput.daterangepicker(
            {
                ...pickerConfig,
                startDate: ageChartDateRange.start,
                endDate: ageChartDateRange.end,
            },
            function (start, end, label) {
                ageChartDateRange = { start, end };
                // Get current patient type selection
                const patientType =
                    document.getElementById("agePatientType")?.value || "all";
                reloadAgeChart(patientType);
            },
        );
    } else {
        console.warn("⚠ Age chart date range input not found");
    }
}

async function initOverDueCount() {
    // Get DOM elements
    const loadingEl = document.getElementById("loading");
    const listEl = document.getElementById("overdue-list");
    const noDataEl = document.getElementById("no-data");

    // Category display names and icons
    const categoryConfig = {
        vaccination: {
            label: "Vaccination",
            icon: "💉",
            color: "#dc3545",
        },
        prenatal: {
            label: "Prenatal Checkup",
            icon: "🤰",
            color: "#e74c3c",
        },
        senior_citizen: {
            label: "Senior Citizen Checkup",
            icon: "👴",
            color: "#c0392b",
        },
        tb_dots: {
            label: "TB DOTS Checkup",
            icon: "🏥",
            color: "#a93226",
        },
        family_planning: {
            label: "Family Planning Follow-up",
            icon: "👨‍👩‍👧‍👦",
            color: "#922b21",
        },
    };

    try {
        const response = await fetch("/daily-overdue-record/count", {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            throw new Error("Failed to fetch data");
        }

        const data = await response.json();

        // Convert object to array and sort by count (highest to lowest)
        const sortedData = Object.entries(data)
            .map(([type, count]) => ({
                type,
                count,
                ...categoryConfig[type],
            }))
            .filter((item) => item.count > 0) // Only show categories with overdue appointments
            .sort((a, b) => b.count - a.count);

        // Hide loading indicator
        loadingEl.style.display = "none";

        if (sortedData.length === 0) {
            noDataEl.style.display = "block";
            listEl.style.display = "none";
        } else {
            noDataEl.style.display = "none";
            listEl.style.display = "block";
            renderOverdueList(sortedData);
        }
    } catch (error) {
        console.error("Error fetching overdue counts:", error);
        loadingEl.innerHTML = `
            <div class="alert alert-danger mx-3" role="alert">
                Failed to load overdue appointments. Please try again later.
            </div>
        `;
    }

    // Render the overdue list
    function renderOverdueList(data) {
        listEl.innerHTML = data
            .map(
                (item) => `
        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" 
           >
            <div>
                <span class="fs-5 me-2">${item.icon}</span>
                <span class="fw-semibold">${item.label}</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-danger rounded-pill me-3 fs-6">${item.count}</span>
                <a href="#" class="link-visit-element text-info text-decoration-none" data-type="${item.type}"><i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    `,
            )
            .join("");

        // Add click event listeners
        listEl.querySelectorAll(".link-visit-element").forEach((item) => {
            item.addEventListener("click", function (e) {
                e.preventDefault();
                const type = this.getAttribute("data-type");
                handleVisitClick(type);
            });
        });
    }

    // Handle visit link click
    function handleVisitClick(type) {
        const routes = {
            vaccination: {
                url: "/patient-record/vaccination",
                menuId: "record_vaccination",
            },
            prenatal: {
                url: "/patient-record/prenatal/view-records",
                menuId: "record_prenatal",
            },
            senior_citizen: {
                url: "/patient-record/senior-citizen/view-records",
                menuId: "record_senior_citizen",
            },
            tb_dots: {
                url: "/patient-record/tb-dots/view-records",
                menuId: "record_tb_dots",
            },
            family_planning: {
                url: "/patient-record/family-planning/view-records",
                menuId: "record_family_planning",
            },
        };

        if (routes[type]) {
            const route = routes[type];

            // Find the index of the "Records" menu-option by its ID
            const recordsMenu = document.getElementById("records-menu");
            const allMenuOptions = Array.from(
                document.querySelectorAll(".menu-option"),
            );
            const recordsIndex = allMenuOptions.indexOf(recordsMenu);

            // Set localStorage
            if (recordsIndex !== -1) {
                localStorage.setItem(
                    "openSubmenus",
                    JSON.stringify([recordsIndex]),
                );
            }
            localStorage.setItem("activeMenuItem", `#${route.menuId}`);

            // Redirect
            window.location.href = route.url;
        }
    }
}