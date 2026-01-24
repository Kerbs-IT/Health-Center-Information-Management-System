<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Report</title>

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- HTML2Canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            /* Changed from #e5e5e5 to white */
            padding: 20px;
        }

        /* HIDE EVERYTHING FROM USER VIEW */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 99999;
        }

        /* Main container - hidden behind white overlay */
        .pdf-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            position: relative;
            z-index: 1;
        }

        /* Each page - exact A4 proportions */
        .pdf-page {
            width: 100%;
            max-width: 800px;
            background: white;
            padding: 30px 40px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 4px solid #28a745;
            padding-bottom: 15px;
        }

        .header h2 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #000;
        }

        .header h5 {
            font-size: 13px;
            color: #666;
            font-weight: normal;
            margin-bottom: 10px;
        }

        .header h3 {
            font-size: 17px;
            font-weight: bold;
            margin-top: 8px;
            color: #000;
        }

        .date-range {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin: 18px 0;
            font-weight: 600;
        }

        .chart-section {
            margin-bottom: 25px;
        }

        .chart-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }

        .chart-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .stats {
            font-size: 13px;
            color: #666;
            font-weight: 600;
        }

        .canvas-wrapper {
            margin: 20px 0;
            background: white;
            padding: 10px;
        }

        #mainChart {
            width: 100% !important;
            height: 300px !important;
        }

        #myPieChart {
            width: 100% !important;
            height: 450px !important;
        }

        /* Table styling */
        .breakdown-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: white;
            border: 2px solid #28a745;
        }

        .breakdown-table th {
            background: #28a745;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
        }

        .breakdown-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            font-size: 13px;
        }

        .breakdown-table tr:last-child td {
            border-bottom: none;
        }

        .breakdown-table .label-col {
            font-weight: 600;
            color: #333;
        }

        .breakdown-table .value-col {
            text-align: center;
            font-weight: bold;
            color: #28a745;
            font-size: 16px;
        }

        .breakdown-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        /* ⭐ LOADING INDICATOR - SHOW ON TOP OF WHITE OVERLAY */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            z-index: 999999;
            /* Above the white overlay */
        }

        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #28a745;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ⭐ OPTIONAL: Add a subtle message */
        .loading-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 999999;
            color: #28a745;
            font-size: 16px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- Loading Indicator -->
    <div class="loading" id="loadingIndicator">
        <div class="spinner"></div>
        <p style="font-size: 18px; font-weight: 600; color: #333;">Generating PDF...</p>
        <p style="font-size: 13px; color: #666; margin-top: 8px;">Please wait while charts are being rendered</p>
    </div>

    <!-- PDF Content Container -->
    <div class="pdf-container">
        <!-- PAGE 1: BAR CHART -->
        <div class="pdf-page" id="page1">
            <!-- Header -->
            <div class="header">
                <h2>HEALTH CENTER INFORMATION MANAGEMENT SYSTEM</h2>
                <h5>Brgy. Hugo Perez, Proper</h5>
                <h3>Monthly Patient Statistics Report</h3>
            </div>

            <!-- Bar Chart Date Range -->
            <div class="date-range">Bar Chart Period: {{ $barDateRangeText }}</div>

            <!-- Bar Chart Section -->
            <div class="chart-section">
                <div class="chart-header">
                    <div class="chart-title" id="chartTitle">All Patients</div>
                    <div id="stats-display" class="stats"></div>
                </div>

                <div class="canvas-wrapper">
                    <canvas id="mainChart"></canvas>
                </div>

                <!-- Patient Type Breakdown Table -->
                <table class="breakdown-table">
                    <thead>
                        <tr>
                            <th>Patient Type</th>
                            <th style="text-align: center;">Total Patients ({{ $barDateRangeText }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="label-col">Vaccination</td>
                            <td class="value-col">{{ $patientTypeTotals['vaccination'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="label-col">Prenatal Care</td>
                            <td class="value-col">{{ $patientTypeTotals['prenatal'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="label-col">Senior Citizen</td>
                            <td class="value-col">{{ $patientTypeTotals['senior'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="label-col">TB Treatment</td>
                            <td class="value-col">{{ $patientTypeTotals['tb'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="label-col">Family Planning</td>
                            <td class="value-col">{{ $patientTypeTotals['family_planning'] ?? 0 }}</td>
                        </tr>
                        <tr style="background: #e9ecef;">
                            <td class="label-col" style="font-weight: bold; font-size: 15px;">TOTAL</td>
                            <td class="value-col" style="font-size: 18px;">
                                {{
                                    ($patientTypeTotals['vaccination'] ?? 0) + 
                                    ($patientTypeTotals['prenatal'] ?? 0) + 
                                    ($patientTypeTotals['senior'] ?? 0) + 
                                    ($patientTypeTotals['tb'] ?? 0) + 
                                    ($patientTypeTotals['family_planning'] ?? 0) 
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Page 1 of 2</p>
            </div>
        </div>

        <!-- PAGE 2: PIE CHART -->
        <div class="pdf-page" id="page2">
            <!-- Header -->
            <div class="header">
                <h2>HEALTH CENTER INFORMATION MANAGEMENT SYSTEM</h2>
                <h5>Brgy. Hugo Perez, Proper</h5>
                <h3>Monthly Patient Statistics Report</h3>
            </div>

            <!-- Pie Chart Date Range -->
            <div class="date-range">Pie Chart Period: {{ $pieDateRangeText }}</div>

            <!-- Pie Chart Section -->
            <div class="chart-section">
                <div class="chart-title" style="text-align: center; margin-bottom: 20px;">
                    Patient Distribution Overview
                </div>

                <div class="canvas-wrapper">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Generated on: <span id="currentDate"></span></p>
                <p>Health Center Information Management System © {{ date('Y') }}</p>
                <p>Page 2 of 2</p>
            </div>
        </div>
    </div>

    <script>
        // Pass data from Laravel to JavaScript
        window.patientDataFromServer = @json($patientData);
        window.pieChartDataFromServer = @json($pieData);
        window.selectedPatientType = '{{ $selectedType ?? "all" }}';

        // Set current date
        document.getElementById('currentDate').textContent = new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        let barChartInstance = null;
        let pieChartInstance = null;

        // Initialize Bar Chart
        function initBarChart() {
            const patientData = window.patientDataFromServer;
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
            barChartInstance = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: months,
                    datasets: [{
                        label: dataLabel,
                        data: data,
                        backgroundColor: "rgba(40, 167, 69, 0.8)",
                        borderColor: "rgba(40, 167, 69, 1)",
                        borderWidth: 2,
                        borderRadius: 5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 0
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: "top",
                            labels: {
                                padding: 15,
                                font: {
                                    size: 13,
                                    weight: "600",
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: "rgba(0, 0, 0, 0.08)",
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                padding: 8
                            },
                            title: {
                                display: true,
                                text: "Number of Patients",
                                font: {
                                    size: 13,
                                    weight: "600"
                                },
                                padding: 10
                            },
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                padding: 5
                            },
                            title: {
                                display: true,
                                text: "Month",
                                font: {
                                    size: 13,
                                    weight: "600"
                                },
                                padding: 10
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
                `Total: <strong>${total}</strong> | Average: <strong>${average}</strong> | Peak: <strong>${peakMonth}</strong>`;
        }

        // Initialize Pie Chart
        function initPieChart() {
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
            pieChartInstance = new Chart(pieChart, {
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
                            "#28A745",
                            "#FF8C00"
                        ],
                        borderWidth: 3,
                        borderColor: "#fff",
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 0
                    },
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                font: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                padding: 15,
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
        }

        // Generate PDF with proper settings
        async function generatePDF() {
            try {
                const {
                    jsPDF
                } = window.jspdf;

                // Create PDF in portrait mode, A4 size
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });

                const pageWidth = 210; // A4 width in mm
                const pageHeight = 297; // A4 height in mm

                // Capture Page 1
                const page1Element = document.getElementById('page1');
                const canvas1 = await html2canvas(page1Element, {
                    scale: 2.5,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff',
                    windowWidth: 800,
                    onclone: function(clonedDoc) {
                        const page = clonedDoc.getElementById('page1');
                        page.style.boxShadow = 'none';
                        page.style.margin = '0';
                    }
                });

                const imgData1 = canvas1.toDataURL('image/png', 1.0);
                const imgWidth1 = pageWidth;
                const imgHeight1 = (canvas1.height * pageWidth) / canvas1.width;

                // Add Page 1 to PDF
                pdf.addImage(imgData1, 'PNG', 0, 0, imgWidth1, imgHeight1);

                // Add new page for Page 2
                pdf.addPage();

                // Capture Page 2
                const page2Element = document.getElementById('page2');
                const canvas2 = await html2canvas(page2Element, {
                    scale: 2.5,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff',
                    windowWidth: 800,
                    onclone: function(clonedDoc) {
                        const page = clonedDoc.getElementById('page2');
                        page.style.boxShadow = 'none';
                        page.style.margin = '0';
                    }
                });

                const imgData2 = canvas2.toDataURL('image/png', 1.0);
                const imgWidth2 = pageWidth;
                const imgHeight2 = (canvas2.height * pageWidth) / canvas2.width;

                // Add Page 2 to PDF
                pdf.addImage(imgData2, 'PNG', 0, 0, imgWidth2, imgHeight2);

                // Save PDF
                const filename = 'patient-statistics-report-' + new Date().toISOString().split('T')[0] + '.pdf';
                pdf.save(filename);

                // Hide loading indicator
                document.getElementById('loadingIndicator').style.display = 'none';

                // console.log('PDF generated successfully!');
                // ⭐ Notify parent window
                if (window.opener && !window.opener.closed) {
                    window.opener.postMessage({
                        type: 'pdfGenerated',
                        success: true
                    }, '*');
                }

                // ⭐ Close this window automatically after a short delay
                setTimeout(function() {
                    window.close();
                }, 500);
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Failed to generate PDF. Please try again.');
                document.getElementById('loadingIndicator').style.display = 'none';
            }
        }

        // Initialize everything when page loads
        window.addEventListener('load', function() {
            // Show loading indicator
            document.getElementById('loadingIndicator').style.display = 'block';

            // Initialize charts
            initBarChart();
            initPieChart();

            // Wait for charts to render completely, then generate PDF
            setTimeout(function() {
                generatePDF();
            }, 3000); // 3 seconds to ensure everything is loaded
        });
    </script>
</body>

</html>