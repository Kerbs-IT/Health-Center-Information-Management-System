
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/js/patient/add-patient.js',
    'resources/css/patient/record.css',
    'resources/js/record/record.js',
    'resources/js/inventory_system/category.js'
    ])
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="icon" type="image/x-icon" href="{{ asset('images/hugo_perez_logo.png')}}">


    @livewireStyles

    <style>

    .page-item.active .page-link {
    background-color: #ff8fb1;
    border-color: #ff8fb1;
}
.page-link {
    color: #d6336c;
}
.page-link:hover {
    background-color: #ffd6e0;
}
    </style>
</head>
<body>
    <div class="min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="flex-grow-1">
                <header class=" d-flex align-items-center pe-3 ">
                    <button class="btn  d-lg-block fs-6 mx-1" id="toggleSidebar" style="z-index: 100;">
                        <i class="fa-solid fa-bars fs-2"></i>
                    </button>
                    <nav class="d-flex justify-content-between align-items-center w-100 ">
                        <h1 class="mb-0">INVENTORY</span></h1>
                        <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2" style="min-width: 150px;">
                            <img src="{{ optional(Auth::user()->nurses)->profile_image
                        ? asset(optional(Auth::user()->nurses)->profile_image)
                        : (optional(Auth::user()->staff)->profile_image
                            ? asset(optional(Auth::user()->staff)->profile_image)
                            : asset('images/default_profile.png')) }}" alt="profile picture" class="profile-img" id="profile_img">
                            <div class="username-n-role">
                                <h5 class="mb-0">{{ optional(Auth::user()->nurses)->full_name
                                                    ?? optional(Auth::user()->staff)->full_name
                                                    ?? 'none'  }}</h5>
                                <h6 class="mb-0 text-muted fw-light">{{Auth::user() -> role ?? 'none';}}</h6>
                            </div>
                            <div class="links position-absolute flex-column top-20 w-100 bg-white" id="links">
                                <a href="{{ route('page.profile') }}" class="text-decoration-none text-black">view profile</a>
                                <a href="{{route('logout')}}" class="text-decoration-none text-black">Logout</a>
                            </div>
                        </div>
                    </nav>
                </header>
                {{ $slot }}
        </div>
    </div>

    @livewireScripts
        <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // BAR CHART — Medicine vs Vaccine
        const barChart = new Chart(document.getElementById("barChart"), {
            type: "bar",
            data: {
                labels: ["Medicines", "Vaccines", "TB Dots Medicine"],
                datasets: [{
                    label: "Count",
                    data: [125, 45, 80],
                    backgroundColor: ["#f472b6", "#93c5fd", 'orange'],
                    borderRadius: 10
                }]
            },
            options: { responsive: true }
        });

        // LINE CHART — Monthly Consumption Trend
        const lineChart = new Chart(document.getElementById("lineChart"), {
            type: "line",
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                datasets: [{
                    label: "Paracetamol",
                    data: [0, 140, 150, 135, 165, 180],
                    borderColor: "#48ec6bff",
                    backgroundColor: "rgba(236,72,153,0.2)",
                    tension: 0.4,
                    borderWidth: 3
                },{
                    label: "Amlodiphene",
                    data: [0, 100, 120, 110, 140, 160],
                    borderColor: "#c348ecff",
                    backgroundColor: "rgba(236,72,153,0.2)",
                    tension: 0.4,
                    borderWidth: 3
                },]
            },
            options: { responsive: true }
        });

        // PIE CHART — Stock Level Distribution
        const pieChart = new Chart(document.getElementById("pieChart"), {
            type: "pie",
            data: {
                labels: ["In Stock", "Low Stock", "Out of Stock"],
                datasets: [{
                    data: [260, 12, 6],
                    backgroundColor: ["#f9a8d4", "#fcd34d", "#fca5a5"]
                }]
            },
            options: { responsive: true }
        });

        // DOUGHNUT — Expiring Soon vs Safe
        const doughnutChart = new Chart(document.getElementById("doughnutChart"), {
            type: "doughnut",
            data: {
                labels: ["Expiring Soon", "Not Expiring"],
                datasets: [{
                    data: [8, 162],
                    backgroundColor: ["#fca5a5", "#86efac"],
                    cutout: "60%"
                }]
            },
            options: { responsive: true }
        });


    </script>
    <script>
window.addEventListener('close-vaccine-modal', () => {
    var modal = bootstrap.Modal.getInstance(document.getElementById('addVaccineModal'));
    modal.hide();
});
</script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>