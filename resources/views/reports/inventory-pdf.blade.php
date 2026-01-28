<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report - {{ $generatedDate }}</title>
    @vite(['app.css']);
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('images/hugo_perez_logo.png')}}">
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-width: 1200px; /* Force minimum width */
        }

        #pdf-content {
            background: white;
            padding: 40px;
            width: 1200px; /* Fixed width for consistent PDF */
            margin: 0 auto;
        }

        /* Rest of your existing styles remain the same */

        .report-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
        }

        .report-header h1 {
            color: #1f2937;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .report-header .subtitle {
            color: #6b7280;
            font-size: 14px;
        }

        .report-body{
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* Fixed 5 columns */
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }

        .stat-card p {
            font-size: 12px;
            margin: 0;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }

        .chart-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .chart-subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        canvas {
            max-width: 100%;
            height: auto !important;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }

        .two-column-charts {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Always 2 columns */
            gap: 30px;
            margin-bottom: 40px;
        }

        .controls {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .btn-generate {
            background: #10b981;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-generate:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.5);
        }

        .btn-generate:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            text-align: center;
        }

        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #10b981;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Remove all responsive breakpoints for PDF */
        /* The layout will always be fixed at 1200px width */
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h5>Generating PDF...</h5>
            <p class="text-muted mb-0">Please wait while we process your report</p>
        </div>
    </div>

    <!-- Generate Button -->
    <div class="controls no-print">
        <button id="generatePdfBtn" class="btn-generate">
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" height="30" width="30"  viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path d="M352 96C352 78.3 337.7 64 320 64C302.3 64 288 78.3 288 96L288 306.7L246.6 265.3C234.1 252.8 213.8 252.8 201.3 265.3C188.8 277.8 188.8 298.1 201.3 310.6L297.3 406.6C309.8 419.1 330.1 419.1 342.6 406.6L438.6 310.6C451.1 298.1 451.1 277.8 438.6 265.3C426.1 252.8 405.8 252.8 393.3 265.3L352 306.7L352 96zM160 384C124.7 384 96 412.7 96 448L96 480C96 515.3 124.7 544 160 544L480 544C515.3 544 544 515.3 544 480L544 448C544 412.7 515.3 384 480 384L433.1 384L376.5 440.6C345.3 471.8 294.6 471.8 263.4 440.6L206.9 384L160 384zM464 440C477.3 440 488 450.7 488 464C488 477.3 477.3 488 464 488C450.7 488 440 477.3 440 464C440 450.7 450.7 440 464 440z"/></svg> Generate PDF Report
        </button>
    </div>

    <!-- PDF Content -->
    <div id="pdf-content">
        <!-- Header -->
        <div class="report-header">
            <div class="report-body">
                <img src="{{ asset('images/hugo_perez_logo.png') }}" height="100" width="100" alt="">
                <h1>Inventory Analytics Report</h1>
            </div>
            <p class="subtitle">Generated on {{ $generatedDate }}</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>{{ $totalMedicines }}</h3>
                <p>Total Medicines</p>
            </div>
            <div class="stat-card">
                <h3>{{ $totalRequests }}</h3>
                <p>Total Requests</p>
            </div>
            <div class="stat-card">
                <h3>{{ $totalDispensed }}</h3>
                <p>Total Distributed</p>
            </div>
            <div class="stat-card">
                <h3>{{ $lowStock }}</h3>
                <p>Low Stock Alert</p>
            </div>
            <div class="stat-card">
                <h3>{{ $expiringSoon }}</h3>
                <p>Expiring Soon</p>
            </div>
        </div>

        <!-- Medicine Given Trend -->
        <div class="chart-section">
            <div class="chart-container">
                <h3 class="chart-title">Medicine Distribution Trend</h3>
                <p class="chart-subtitle">Period: {{ $startDate }} - {{ $endDate }}</p>
                <canvas id="givenChart" height="80"></canvas>
            </div>
        </div>

        <!-- Request Trend -->
        <div class="chart-section">
            <div class="chart-container">
                <h3 class="chart-title">Medicine Request Trend</h3>
                <p class="chart-subtitle">Period: {{ $startDate }} - {{ $endDate }}</p>
                <canvas id="requestChart" height="80"></canvas>
            </div>
        </div>

        <!-- Top 5 Medicines -->
        @if(count($topMedicinesData['datasets']) > 0)
        <div class="chart-section">
            <div class="chart-container">
                <h3 class="chart-title">Top 5 Most Dispensed Medicines</h3>
                <p class="chart-subtitle">Period: {{ $startDate }} - {{ $endDate }}</p>
                <canvas id="topMedicinesChart" height="80"></canvas>
            </div>
        </div>
        @endif

        <!-- Two Column Charts -->
        <div class="two-column-charts">
            <!-- Categories Bar Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Medicine Categories</h3>
                <p class="chart-subtitle">Period: {{ $barChartStartDate }} - {{ $barChartEndDate }}</p>
                <canvas id="categoriesChart" height="150"></canvas>
            </div>

            <!-- Stock Distribution Pie Chart -->
            <div class="chart-container">
                <h3 class="chart-title">Stock Level Distribution</h3>
                <p class="chart-subtitle">Period: {{ $pieChartStartDate }} - {{ $pieChartEndDate }}</p>
                <canvas id="stockChart" height="150"></canvas>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Medicine Inventory Management System</strong></p>
            <p>This report is automatically generated and contains confidential information.</p>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- html2canvas and jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        // Chart color palette
        const colors = [
            '#10b981', '#8b5cf6', '#f472b6', '#fbbf24',
            '#60a5fa', '#ef4444', '#a855f7', '#06b6d4'
        ];

        // Initialize all charts
        document.addEventListener('DOMContentLoaded', function() {
            // Medicine Given Chart
            new Chart(document.getElementById('givenChart'), {
                type: 'line',
                data: {
                    labels: @json($dateRangeGivenData['labels']),
                    datasets: [{
                        label: 'Medicines Given',
                        data: @json($dateRangeGivenData['data']),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        borderWidth: 3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });

            // Request Trend Chart
            new Chart(document.getElementById('requestChart'), {
                type: 'line',
                data: {
                    labels: @json($requestTrendData['labels']),
                    datasets: [{
                        label: 'Medicine Requests',
                        data: @json($requestTrendData['data']),
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4,
                        borderWidth: 3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });

            // Top Medicines Chart
            @if(count($topMedicinesData['datasets']) > 0)
            const topMedicinesDatasets = @json($topMedicinesData['datasets']).map((dataset, index) => {
                const color = colors[index % colors.length];
                return {
                    label: dataset.label,
                    data: dataset.data,
                    borderColor: color,
                    backgroundColor: color + '20',
                    tension: 0.4,
                    borderWidth: 3,
                    fill: false
                };
            });

            new Chart(document.getElementById('topMedicinesChart'), {
                type: 'line',
                data: {
                    labels: @json($topMedicinesData['labels']),
                    datasets: topMedicinesDatasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
            @endif

            // Categories Bar Chart
            new Chart(document.getElementById('categoriesChart'), {
                type: 'bar',
                data: {
                    labels: @json($categoriesData['labels']),
                    datasets: [{
                        label: 'Count',
                        data: @json($categoriesData['data']),
                        backgroundColor: colors,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });

            // Stock Pie Chart
            new Chart(document.getElementById('stockChart'), {
                type: 'pie',
                data: {
                    labels: @json($pieChartData['labels']),
                    datasets: [{
                        data: @json($pieChartData['data']),
                        backgroundColor: ['#86efac', '#fcd34d', '#fca5a5']
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
        });

        // PDF Generation
        document.getElementById('generatePdfBtn').addEventListener('click', async function() {
            const button = this;
            const overlay = document.getElementById('loadingOverlay');

            // Disable button and show loading
            button.disabled = true;
            overlay.classList.add('active');

            try {
                const content = document.getElementById('pdf-content');

                // Use html2canvas to capture the content
                const canvas = await html2canvas(content, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                });

                // Calculate PDF dimensions
                const imgWidth = 210; // A4 width in mm
                const pageHeight = 297; // A4 height in mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                let heightLeft = imgHeight;

                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                let position = 0;

                // Add image to PDF (handle multiple pages if needed)
                const imgData = canvas.toDataURL('image/png');
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                // Save the PDF
                const filename = `Inventory_Report_${new Date().toISOString().split('T')[0]}.pdf`;
                pdf.save(filename);

            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Failed to generate PDF. Please try again.');
            } finally {
                // Re-enable button and hide loading
                button.disabled = false;
                overlay.classList.remove('active');
            }
        });
    </script>
</body>
</html>