import Chart from 'chart.js/auto';




document.addEventListener('DOMContentLoaded', function () {
    // const canvas = document.getElementById('myChart');
    // const ctx = canvas.getContext('2d');

    // // Create a vertical gradient (top to bottom)
    // const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
    // gradient.addColorStop(0, 'rgba(46, 139, 87, 1)');
    // gradient.addColorStop(0.5, 'rgba(46, 139, 87, 0.5)');
    // gradient.addColorStop(1, 'rgba(46, 139, 87, 0)');

    // new Chart(ctx, {
    //     type: 'line',
    //     data: {
    //         labels: ['Jan', 'Feb', 'Mar', 'April', 'May', 'June','July','Aug','Sep','Oct','Nov','Dec'],
    //         datasets: [{
    //             label: 'Number of Patient',
    //             data: [10, 20, 30, 40, 50, 60, 70, 90, 50, 40, 30, 10],
    //             backgroundColor: gradient,
    //             fill:true,
    //             tension: 0.1,
    //             borderColor: '#2E8B57'
    //         }]
    //     },
    //     options: {
    //         responsive: true,
    //         scales: {
    //             y: {
    //                 beginAtZero: true
    //             }
    //         },
    //         plugins: {
    //                 title:{
    //                 display: true,
    //                 align: 'start',
    //                 text: 'Patient Administered Montly',
    //                 font:{
    //                     size:20,
    //                     weight: 'bold',
    //                     opacity:1
    //                 },
    //                 color: 'rgba(0, 0, 0, 1)'
    //             }
    //         }
    //     }
    // });

    const pieChart = document.getElementById('myPieChart').getContext('2d');
    const myPieChart = new Chart(pieChart, {
        type: 'doughnut',
        data: {
        labels: ['Vaccination', 'Prenatal', 'Senior Citizen', 'TB Dots'],
        datasets: [{
            label: 'Patient Categories',
            data: [25, 15, 35, 25],
            backgroundColor: [
            'yellow',
            'red',
            'blue',
            'rgba(46, 139, 87, 1)'
            ],
            borderWidth: 1
        }]
        },
        options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            title: {
                display: true,
                text: 'Patient Distribution',
                font:{
                    size:20,
                    weight: 'bold',
                },
                color: 'rgba(0,0,0)',
                align: 'start'
            }
        },
        maintainAspectRatio: false,
        }
    });
     // Sample data for different patient types - all using success green color
     const patientData = {
        all: {
            label: 'All Patients',
            data: [245, 289, 312, 298, 267, 334, 356, 298, 312, 289, 267, 298],
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)'
        },
        vaccination: {
            label: 'Vaccination',
            data: [89, 92, 105, 98, 87, 112, 125, 98, 105, 92, 87, 98],
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)'
        },
        prenatal: {
            label: 'Prenatal Care',
            data: [45, 52, 48, 56, 51, 58, 62, 54, 48, 52, 45, 56],
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)'
        },
        senior: {
            label: 'Senior Citizen',
            data: [67, 71, 78, 74, 69, 82, 85, 76, 78, 71, 69, 74],
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)'
        },
        tb: {
            label: 'TB Treatment',
            data: [23, 28, 31, 26, 24, 29, 32, 27, 31, 28, 24, 26],
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)'
        },
        family_planning: {
            label: 'Family Planning',
            data: [21, 26, 29, 24, 22, 28, 31, 25, 29, 26, 22, 24],
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)'
        }
    };

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                   'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    let chart;

    // Initialize chart
    function initChart() {
        const ctx = document.getElementById('patientChart').getContext('2d');
        
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: patientData.all.label,
                    data: patientData.all.data,
                    backgroundColor: patientData.all.backgroundColor,
                    borderColor: patientData.all.borderColor,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 14,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                return `${context[0].label} 2024`;
                            },
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y} patients`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            lineWidth: 1
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        },
                        title: {
                            display: true,
                            text: 'Number of Patients',
                            font: {
                                size: 14,
                                weight: '600'
                            },
                            color: '#333'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#666'
                        },
                        title: {
                            display: true,
                            text: 'Month',
                            font: {
                                size: 14,
                                weight: '600'
                            },
                            color: '#333'
                        }
                    }
                },
                animation: {
                    duration: 800,
                    easing: 'easeInOutQuart'
                }
            }
        });
        
        updateStats('all');
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
        
        chart.update('active');
        updateStats(patientType);
    }

    // Update statistics summary
    function updateStats(patientType) {
        const data = patientData[patientType].data;
        const total = data.reduce((a, b) => a + b, 0);
        const average = Math.round(total / data.length);
        const peakIndex = data.indexOf(Math.max(...data));
        const peakMonth = months[peakIndex];
        
        document.getElementById('totalPatients').textContent = total.toLocaleString();
        document.getElementById('avgMonthly').textContent = average.toLocaleString();
        document.getElementById('peakMonth').textContent = peakMonth;
    }

    // Event listener for dropdown change
    document.getElementById('patientType').addEventListener('change', function(e) {
        updateChart(e.target.value);
    });

    // Initialize chart when page loads
    window.addEventListener('load', initChart);

});
