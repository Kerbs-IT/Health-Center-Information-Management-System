// inventory-report.js

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCharts);
    document.addEventListener('DOMContentLoaded', initializeLogsPicker);
} else {
    initializeCharts();
    initializeLogsPicker();
}

// ── Charts (Inventory Report page) ───────────────────────────────────────────
function initializeCharts() {
    if (!document.getElementById('dateRangePicker')) return;

    const categoriesData     = window.chartData.categoriesData;
    const pieChartData       = window.chartData.pieChartData;
    let dateRangeGivenData   = window.chartData.dateRangeGivenData;
    let dateRangeRequestData = window.chartData.dateRangeRequestData;
    let topMedicinesData     = window.chartData.topMedicinesData;

    const colors = [
        '#48ec6bff', '#c348ecff', '#f472b6', '#fbbf24',
        '#60a5fa', '#10b981', '#ef4444', '#a855f7'
    ];

    const start = moment().startOf('year');
    const end   = moment().endOf('year');

    const dateRangeConfig = {
        startDate: start,
        endDate: end,
        ranges: {
            'Today'      : [moment(), moment()],
            'Yesterday'  : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month' : [moment().startOf('month'), moment().endOf('month')],
            'Last Month' : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year'  : [moment().startOf('year'), moment().endOf('year')],
            'Last Year'  : [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        },
        locale: { format: 'MMMM D, YYYY' },
        alwaysShowCalendars: true,
        autoUpdateInput: true
    };

    window.currentDateRanges = {
        line: { start, end },
        bar:  { start, end },
        pie:  { start, end }
    };

    // Line Chart picker
    $('#dateRangePicker').daterangepicker(dateRangeConfig, function(start, end) {
        $('#dateRangePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        window.currentDateRanges.line = { start, end };
        const livewireElement = document.querySelector('[wire\\:id]');
        if (livewireElement && window.Livewire) {
            window.Livewire.find(livewireElement.getAttribute('wire:id'))
                .call('updateDateRange', start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }
    });
    $('#dateRangePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    // Bar Chart picker
    $('#barChartDatePicker').daterangepicker(dateRangeConfig, function(start, end) {
        $('#barChartDatePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        window.currentDateRanges.bar = { start, end };
        const livewireElement = document.querySelector('[wire\\:id]');
        if (livewireElement && window.Livewire) {
            window.Livewire.find(livewireElement.getAttribute('wire:id'))
                .call('updateBarChartDateRange', start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }
    });
    $('#barChartDatePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    // Pie Chart picker
    $('#pieChartDatePicker').daterangepicker(dateRangeConfig, function(start, end) {
        $('#pieChartDatePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        window.currentDateRanges.pie = { start, end };
        const livewireElement = document.querySelector('[wire\\:id]');
        if (livewireElement && window.Livewire) {
            window.Livewire.find(livewireElement.getAttribute('wire:id'))
                .call('updatePieChartDateRange', start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }
    });
    $('#pieChartDatePicker').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    // Event listeners
    window.addEventListener('dateRangeUpdated', (event) => {
        const data = event.detail;
        dateRangeGivenData   = data.dateRangeGivenData;
        dateRangeRequestData = data.dateRangeRequestData;
        topMedicinesData     = data.topMedicinesData;
        updateLineChart(document.getElementById('lineChartSelector').value);
    });

    window.addEventListener('barChartUpdated', (event) => {
        barChart.data = {
            labels: event.detail.categoriesData.labels,
            datasets: [{ label: "Count", data: event.detail.categoriesData.data, backgroundColor: colors, borderRadius: 10 }]
        };
        barChart.update();
    });

    window.addEventListener('pieChartUpdated', (event) => {
        pieChart.data = {
            labels: event.detail.pieChartData.labels,
            datasets: [{ data: event.detail.pieChartData.data, backgroundColor: ["#86efac", "#fcd34d", "#fca5a5"] }]
        };
        pieChart.update();
    });

    function updateLineChart(chartType) {
        let newData;
        let tooltipCallbacks = {};

        if (chartType === 'given') {
            const fullLabels = dateRangeGivenData.fullLabels || dateRangeGivenData.labels;
            newData = {
                labels: dateRangeGivenData.labels,
                datasets: [{
                    label: "Medicines Given",
                    data: dateRangeGivenData.data,
                    borderColor: "#f472b6",
                    backgroundColor: "rgba(244,114,182,0.2)",
                    tension: 0.4, borderWidth: 3, fill: true
                }]
            };
            tooltipCallbacks = { title: ctx => fullLabels[ctx[0].dataIndex] };

        } else if (chartType === 'request_trend') {
            const fullLabels = dateRangeRequestData.fullLabels || dateRangeRequestData.labels;
            newData = {
                labels: dateRangeRequestData.labels,
                datasets: [{
                    label: "Medicine Requests",
                    data: dateRangeRequestData.data,
                    borderColor: "#93c5fd",
                    backgroundColor: "rgba(147,197,253,0.2)",
                    tension: 0.4, borderWidth: 3, fill: true
                }]
            };
            tooltipCallbacks = { title: ctx => fullLabels[ctx[0].dataIndex] };

        } else if (chartType === 'top_medicines') {
            const fullLabels = topMedicinesData.fullLabels || topMedicinesData.labels;
            const datasets = (topMedicinesData.datasets || []).map((dataset, index) => {
                const color     = colors[index % colors.length];
                const rgbaMatch = color.match(/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i);
                const rgba      = rgbaMatch
                    ? `rgba(${parseInt(rgbaMatch[1],16)},${parseInt(rgbaMatch[2],16)},${parseInt(rgbaMatch[3],16)},0.2)`
                    : color + '33';
                return { label: dataset.label, data: dataset.data, borderColor: color, backgroundColor: rgba, tension: 0.4, borderWidth: 3, fill: false };
            });
            newData = { labels: topMedicinesData.labels, datasets };
            tooltipCallbacks = { title: ctx => fullLabels[ctx[0].dataIndex] };
        }

        lineChart.data = newData;
        lineChart.options.plugins.tooltip = { callbacks: tooltipCallbacks };
        lineChart.update();
    }

    // Charts
    const barChart = new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
            labels: categoriesData.labels,
            datasets: [{ label: "Count", data: categoriesData.data, backgroundColor: colors, borderRadius: 10 }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    const pieChart = new Chart(document.getElementById("pieChart"), {
        type: "pie",
        data: {
            labels: pieChartData.labels,
            datasets: [{ data: pieChartData.data, backgroundColor: ["#86efac", "#fcd34d", "#fca5a5"] }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { position: 'bottom' } }
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
                tension: 0.4, borderWidth: 3, fill: true
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        title: ctx => {
                            const fullLabels = dateRangeGivenData.fullLabels || dateRangeGivenData.labels;
                            return fullLabels[ctx[0].dataIndex];
                        }
                    }
                }
            },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    document.getElementById('lineChartSelector')?.addEventListener('change', e => updateLineChart(e.target.value));

    // Modal date pickers
    const medicineModalDateConfig = {
        ...dateRangeConfig,
        startDate: moment().startOf('year'),
        endDate:   moment().endOf('year'),
    };

    const modalPickers = [
        { id: '#medicinesDatePicker',    method: 'updateMedicinesDateRange'    },
        { id: '#requestsDatePicker',     method: 'updateRequestsDateRange'     },
        { id: '#distributedDatePicker',  method: 'updateDistributedDateRange'  },
        { id: '#lowStockDatePicker',     method: 'updateLowStockDateRange'     },
        { id: '#expiringSoonDatePicker', method: 'updateExpiringSoonDateRange' },
    ];

    modalPickers.forEach(({ id, method }) => {
        $(id).daterangepicker(medicineModalDateConfig, function(start, end) {
            $(id).val(start.format('MMM D, YYYY') + ' – ' + end.format('MMM D, YYYY'));
            const livewireEl = document.querySelector('[wire\\:id]');
            if (livewireEl && window.Livewire) {
                window.Livewire.find(livewireEl.getAttribute('wire:id'))
                    .call(method, start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            }
        });
        $(id).val(moment().startOf('year').format('MMM D, YYYY') + ' – ' + moment().endOf('year').format('MMM D, YYYY'));
    });
}

// ── Medicine Request Logs page ────────────────────────────────────────────────
function initializeLogsPicker() {
    if (!document.getElementById('logsDateRangePicker')) return;

    const logsPickerConfig = {
        autoUpdateInput    : false,
        alwaysShowCalendars: true,
        linkedCalendars    : false,
        showDropdowns      : true,
        ranges: {
            'Today'       : [moment(), moment()],
            'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month'  : [moment().startOf('month'), moment().endOf('month')],
            'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'This Year'   : [moment().startOf('year'), moment().endOf('year')],
        },
        locale: {
            format     : 'MMM D, YYYY',
            cancelLabel: 'Clear',
        },
    };

    $('#logsDateRangePicker').daterangepicker(logsPickerConfig);

    $('#logsDateRangePicker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MMM D, YYYY') + ' – ' + picker.endDate.format('MMM D, YYYY'));
        const livewireEl = document.querySelector('[wire\\:id]');
        if (livewireEl && window.Livewire) {
            window.Livewire.find(livewireEl.getAttribute('wire:id'))
                .call('updateLogsDateRange',
                    picker.startDate.format('YYYY-MM-DD'),
                    picker.endDate.format('YYYY-MM-DD')
                );
        }
    });

    $('#logsDateRangePicker').on('cancel.daterangepicker', function() {
        $(this).val('');
        const livewireEl = document.querySelector('[wire\\:id]');
        if (livewireEl && window.Livewire) {
            window.Livewire.find(livewireEl.getAttribute('wire:id'))
                .call('clearLogsDateRange');
        }
    });

    // Sync display text if Livewire clears dates server-side (✕ button)
    document.addEventListener('livewire:updated', function() {
        const el = document.getElementById('logsDateRangePicker');
        if (!el) return;
        const livewireEl = document.querySelector('[wire\\:id]');
        if (livewireEl && window.Livewire) {
            const comp = window.Livewire.find(livewireEl.getAttribute('wire:id'));
            if (comp && !comp.get('startDate') && !comp.get('endDate')) {
                $(el).val('');
            }
        }
    });
}

// ── PDF export ────────────────────────────────────────────────────────────────
window.addEventListener('generate-pdf', () => {
    const dateRanges = window.currentDateRanges;
    const params = new URLSearchParams({
        start_date    : dateRanges.line.start.format('YYYY-MM-DD'),
        end_date      : dateRanges.line.end.format('YYYY-MM-DD'),
        bar_start_date: dateRanges.bar.start.format('YYYY-MM-DD'),
        bar_end_date  : dateRanges.bar.end.format('YYYY-MM-DD'),
        pie_start_date: dateRanges.pie.start.format('YYYY-MM-DD'),
        pie_end_date  : dateRanges.pie.end.format('YYYY-MM-DD'),
    });
    window.open(`/generate-report-pdf?${params.toString()}`, '_blank');
});