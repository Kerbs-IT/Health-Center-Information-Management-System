<div class="min-vh-100 p-5">
    <div class="shadow p-2">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-8 mx-2 mt-2 p-1">
            <!-- Total Medicines Card -->
            <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-blue-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-green-500 p-3 rounded-lg">
                        <i class="bi bi-capsule text-3xl text-white"></i>
                    </div>
                    <h1 class="font-bolder text-blue-700">{{ $this->totalMedicineCount() }}</h1>
                </div>
                <h5 class="text-sm font-bold text-blue-900 uppercase tracking-wide">Total Medicines</h5>
            </div>

            <!-- Total Requests Card (NEW - replaces vaccines) -->
            <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-cyan-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-green-500 p-3 rounded-lg">
                        <i class="bi bi-clipboard-check text-3xl text-white"></i>
                    </div>
                    <h1 class="font-bold text-cyan-700">{{ $this->totalRequests() }}</h1>
                </div>
                <h5 class="text-sm font-bold text-cyan-900 uppercase tracking-wide">Total Requests</h5>
            </div>

            <!-- Total Medicine Given Card -->
            <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-purple-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-green-500 p-3 rounded-lg">
                        <i class="bi bi-box-arrow-right text-3xl text-white"></i>
                    </div>
                    <h1 class="font-bold text-purple-700">{{ $this->totalMedicineDispense() }}</h1>
                </div>
                <h5 class="text-sm font-bold text-purple-900 uppercase tracking-wide">Total Distributed</h5>
            </div>

            <!-- Total Low Stock Card -->
            <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-orange-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-green-500 p-3 rounded-lg">
                        <i class="bi bi-exclamation-triangle text-3xl text-white"></i>
                    </div>
                    <h1 class="font-bold text-orange-700">{{ $this->totalLowStock() }}</h1>
                </div>
                <h5 class="text-sm font-bold text-orange-900 uppercase tracking-wide">Low Stock Alert</h5>
            </div>

            <!-- Expiring Soon Card -->
            <div class="report-card bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-red-200 hover:shadow-lg shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="bg-green-500 p-3 rounded-lg">
                        <i class="bi bi-calendar-x text-3xl text-white"></i>
                    </div>
                    <h1 class="font-bold text-red-700">{{ $this->totalExpSoon() }}</h1>
                </div>
                <h5 class="text-sm font-bold text-red-900 uppercase tracking-wide">Expiring Soon</h5>
            </div>
        </div>

        <!-- ANALYTICS SECTION -->
        <div class="mt-3 px-3 chart-section">
            <div class="flex justify-between px-3 pb-3">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Visual Analytics</h2>
                <button class="btn btn-success text-nowrap" wire:click="generateReport">Generate Report</button>
            </div>
            <div class="w-100 mb-3">
                <!-- MONTHLY USAGE LINE CHART -->
                <div class="bg-white p-6 rounded-xl shadow px-3 pb-3">
                    <h3 class="text-center">Consumption Trends</h3>
                    <select id="lineChartSelector" class="form-select w-auto mb-3">
                        <option value="monthly_given">Monthly Medicine Given</option>
                        <option value="request_trend">Monthly Request Trend</option>
                        <option value="top_medicines">Top 5 Most Dispensed Medicines</option>
                    </select>
                    <div class="w-100 h-[350px]">
                        <canvas id="lineChart" class="w-100"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRID FOR 2 CHARTS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-3">
                <!-- MEDICINE CATEGORIES BAR CHART (NEW - replaces Medicine vs Vaccine) -->
                <div class="bg-white p-6 rounded-xl shadow">
                    <h3 class="text-center">Medicine Categories</h3>
                    <div class="w-100 min-h-[290px]">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <!-- STOCK DISTRIBUTION PIE CHART -->
                <div class="bg-white p-6 rounded-xl shadow d-flex justify-center flex-column">
                    <h3 class="text-center">Stock Level Distribution</h3>
                    <div class="w-80 xl:min-h-[290px] align-self-center">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Pass PHP data to JavaScript
    const categoriesData = @json($categoriesData);
    const pieChartData = @json($pieChartData);
    const monthlyGivenData = @json($monthlyGivenData);
    const requestTrendData = @json($requestTrendData);
    const topMedicinesData = @json($topMedicinesData);

    // Color palette for multiple datasets
    const colors = [
        '#48ec6bff', // Green
        '#c348ecff', // Purple
        '#f472b6',   // Pink
        '#fbbf24',   // Yellow
        '#60a5fa',   // Blue
        '#10b981',   // Emerald
        '#ef4444',   // Red
        '#a855f7'    // Violet
    ];

    // BAR CHART — Medicine Categories (NEW)
    const barChart = new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
            labels: categoriesData.labels,
            datasets: [{
                label: "Count",
                data: categoriesData.data,
                backgroundColor: colors,
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // PIE CHART — Stock Level Distribution
    const pieChart = new Chart(document.getElementById("pieChart"), {
        type: "pie",
        data: {
            labels: pieChartData.labels,
            datasets: [{
                data: pieChartData.data,
                backgroundColor: ["#86efac", "#fcd34d", "#fca5a5"]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // LINE CHART — Dynamic based on selector
    let lineChart = new Chart(document.getElementById("lineChart"), {
        type: "line",
        data: {
            labels: monthlyGivenData.labels,
            datasets: [{
                label: "Medicines Given",
                data: monthlyGivenData.data,
                borderColor: "#f472b6",
                backgroundColor: "rgba(244,114,182,0.2)",
                tension: 0.4,
                borderWidth: 3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Handle line chart selector change
    document.getElementById('lineChartSelector').addEventListener('change', function(e) {
        const value = e.target.value;
        let newData;

        if (value === 'monthly_given') {
            newData = {
                labels: monthlyGivenData.labels,
                datasets: [{
                    label: "Medicines Given",
                    data: monthlyGivenData.data,
                    borderColor: "#f472b6",
                    backgroundColor: "rgba(244,114,182,0.2)",
                    tension: 0.4,
                    borderWidth: 3,
                    fill: true
                }]
            };
        } else if (value === 'request_trend') {
            newData = {
                labels: requestTrendData.labels,
                datasets: [{
                    label: "Medicine Requests",
                    data: requestTrendData.data,
                    borderColor: "#93c5fd",
                    backgroundColor: "rgba(147,197,253,0.2)",
                    tension: 0.4,
                    borderWidth: 3,
                    fill: true
                }]
            };
        } else if (value === 'top_medicines') {
            const datasets = topMedicinesData.datasets.map((dataset, index) => ({
                label: dataset.label,
                data: dataset.data,
                borderColor: colors[index % colors.length],
                backgroundColor: colors[index % colors.length] + '33',
                tension: 0.4,
                borderWidth: 3,
                fill: false
            }));

            newData = {
                labels: topMedicinesData.labels,
                datasets: datasets
            };
        }

        // Update chart
        lineChart.data = newData;
        lineChart.update();
    });
</script>
@endpush