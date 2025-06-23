import Chart from 'chart.js/auto';




document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('myChart');
    const ctx = canvas.getContext('2d');

    // Create a vertical gradient (top to bottom)
    const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
    gradient.addColorStop(0, 'rgba(46, 139, 87, 1)');
    gradient.addColorStop(0.5, 'rgba(46, 139, 87, 0.5)');
    gradient.addColorStop(1, 'rgba(46, 139, 87, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'April', 'May', 'June','July','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'Number of Patient',
                data: [10, 20, 30, 40, 50, 60, 70, 90, 50, 40, 30, 10],
                backgroundColor: gradient,
                fill:true,
                tension: 0.1,
                borderColor: '#2E8B57'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                    title:{
                    display: true,
                    align: 'start',
                    text: 'Patient Administered Montly',
                    font:{
                        size:20,
                        weight: 'bold',
                        opacity:1
                    },
                    color: 'rgba(0, 0, 0, 1)'
                }
            }
        }
    });

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

});
