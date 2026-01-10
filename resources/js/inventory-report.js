// Wait for DOM before initializing
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCharts);
} else {
    initializeCharts();
}

function initializeCharts() {
    // Check if we're on the right page
    if (!document.getElementById('dateRangePicker')) {
        console.log('Not on inventory report page, skipping initialization');
        return;
    }

    // Get data from Livewire
    const categoriesData = window.chartData.categoriesData;
    const pieChartData = window.chartData.pieChartData;
    let dateRangeGivenData = window.chartData.dateRangeGivenData;
    let dateRangeRequestData = window.chartData.dateRangeRequestData;
    let topMedicinesData = window.chartData.topMedicinesData;

    // Color palette
    const colors = [
        '#48ec6bff', '#c348ecff', '#f472b6', '#fbbf24',
        '#60a5fa', '#10b981', '#ef4444', '#a855f7'
    ];

    // Initialize Date Range Picker for Line Chart
    const start = moment().startOf('year');
    const end = moment().endOf('year');

    const dateRangeConfig = {
        startDate: start,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        },
        locale: {
            format: 'MMMM D, YYYY'
        },
        alwaysShowCalendars: true,
        autoUpdateInput: true
    };

    // Line Chart Date Picker
    $('#dateRangePicker').daterangepicker(dateRangeConfig, function(start, end, label) {
        console.log('Line chart date range: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        $('#dateRangePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

        const livewireElement = document.querySelector('[wire\\:id]');
        if (livewireElement && window.Livewire) {
            window.Livewire.find(livewireElement.getAttribute('wire:id'))
                .call('updateDateRange', start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }
    });
    $('#dateRangePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    // Bar Chart Date Picker
    $('#barChartDatePicker').daterangepicker(dateRangeConfig, function(start, end, label) {
        console.log('Bar chart date range: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        $('#barChartDatePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

        const livewireElement = document.querySelector('[wire\\:id]');
        if (livewireElement && window.Livewire) {
            window.Livewire.find(livewireElement.getAttribute('wire:id'))
                .call('updateBarChartDateRange', start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }
    });
    $('#barChartDatePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    // Pie Chart Date Picker
    $('#pieChartDatePicker').daterangepicker(dateRangeConfig, function(start, end, label) {
        console.log('Pie chart date range: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        $('#pieChartDatePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

        const livewireElement = document.querySelector('[wire\\:id]');
        if (livewireElement && window.Livewire) {
            window.Livewire.find(livewireElement.getAttribute('wire:id'))
                .call('updatePieChartDateRange', start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }
    });
    $('#pieChartDatePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    // Listen for line chart date range updates
    window.addEventListener('dateRangeUpdated', (event) => {
        const data = event.detail;
        dateRangeGivenData = data.dateRangeGivenData;
        dateRangeRequestData = data.dateRangeRequestData;
        topMedicinesData = data.topMedicinesData;

        const currentChart = document.getElementById('lineChartSelector').value;
        updateLineChart(currentChart);
    });

    // Listen for bar chart updates
    window.addEventListener('barChartUpdated', (event) => {
        const data = event.detail;
        barChart.data = {
            labels: data.categoriesData.labels,
            datasets: [{
                label: "Count",
                data: data.categoriesData.data,
                backgroundColor: colors,
                borderRadius: 10
            }]
        };
        barChart.update();
    });

    // Listen for pie chart updates
    window.addEventListener('pieChartUpdated', (event) => {
        const data = event.detail;
        pieChart.data = {
            labels: data.pieChartData.labels,
            datasets: [{
                data: data.pieChartData.data,
                backgroundColor: ["#86efac", "#fcd34d", "#fca5a5"]
            }]
        };
        pieChart.update();
    });

    // Function to update line chart
    function updateLineChart(chartType) {
        let newData;
        let tooltipCallbacks = {};

        if (chartType === 'given') {
            // Store fullLabels for tooltip use
            const fullLabels = dateRangeGivenData.fullLabels || dateRangeGivenData.labels;

            newData = {
                labels: dateRangeGivenData.labels,
                datasets: [{
                    label: "Medicines Given",
                    data: dateRangeGivenData.data,
                    borderColor: "#f472b6",
                    backgroundColor: "rgba(244,114,182,0.2)",
                    tension: 0.4,
                    borderWidth: 3,
                    fill: true
                }]
            };

            // Custom tooltip title to show full labels
            tooltipCallbacks = {
                title: function(context) {
                    return fullLabels[context[0].dataIndex];
                }
            };
        } else if (chartType === 'request_trend') {
            const fullLabels = dateRangeRequestData.fullLabels || dateRangeRequestData.labels;

            newData = {
                labels: dateRangeRequestData.labels,
                datasets: [{
                    label: "Medicine Requests",
                    data: dateRangeRequestData.data,
                    borderColor: "#93c5fd",
                    backgroundColor: "rgba(147,197,253,0.2)",
                    tension: 0.4,
                    borderWidth: 3,
                    fill: true
                }]
            };

            tooltipCallbacks = {
                title: function(context) {
                    return fullLabels[context[0].dataIndex];
                }
            };
        } else if (chartType === 'top_medicines') {
            const fullLabels = topMedicinesData.fullLabels || topMedicinesData.labels;

            if (!topMedicinesData.datasets || topMedicinesData.datasets.length === 0) {
                newData = {
                    labels: topMedicinesData.labels,
                    datasets: []
                };
            } else {
                const datasets = topMedicinesData.datasets.map((dataset, index) => {
                    const color = colors[index % colors.length];
                    const rgbaMatch = color.match(/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i);
                    const rgba = rgbaMatch
                        ? `rgba(${parseInt(rgbaMatch[1], 16)},${parseInt(rgbaMatch[2], 16)},${parseInt(rgbaMatch[3], 16)},0.2)`
                        : color + '33';

                    return {
                        label: dataset.label,
                        data: dataset.data,
                        borderColor: color,
                        backgroundColor: rgba,
                        tension: 0.4,
                        borderWidth: 3,
                        fill: false
                    };
                });

                newData = {
                    labels: topMedicinesData.labels,
                    datasets: datasets
                };
            }

            tooltipCallbacks = {
                title: function(context) {
                    return fullLabels[context[0].dataIndex];
                }
            };
        }

        lineChart.data = newData;

        // Update tooltip options
        lineChart.options.plugins.tooltip = {
            callbacks: tooltipCallbacks
        };

        lineChart.update();
    }

    // Initialize Charts
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
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

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
                legend: { position: 'bottom' }
            }
        }
    });

    const lineChart = new Chart(document.getElementById("lineChart"), {
        type: "line",
        data: {
            labels: dateRangeGivenData.labels,
            datasets: [{
                label: "Medicines Given",
                data: dateRangeGivenData.data,
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
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            const fullLabels = dateRangeGivenData.fullLabels || dateRangeGivenData.labels;
                            return fullLabels[context[0].dataIndex];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });

    // Handle line chart selector change
    document.getElementById('lineChartSelector')?.addEventListener('change', function(e) {
        updateLineChart(e.target.value);
    });
}