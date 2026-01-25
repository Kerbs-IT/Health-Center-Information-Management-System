<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugo_perez_logo.png')}}">

    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- jQuery (required by daterangepicker) - MUST load first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Moment.js (required by daterangepicker) - MUST load second -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

    <!-- Daterangepicker JS - MUST load after jQuery and Moment -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    @vite(['resources/css/app.css',
        'resources/js/app.js',
        'resources/js/menudropdown.js',
        'resources/js/header.js',
        'resources/css/profile.css',
        'resources/css/patient/record.css',
        'resources/js/record/record.js',
        'resources/js/inventory_system/category.js',
        'resources/js/inventory_system/medicineRequest.js',
        'resources/css/inventory_system/inventory-report.css',
        'resources/js/inventory-report-pdf.js'
    ])

    @livewireStyles

    <style>
    .page-item.active .page-link {
        background-color: var(--secondaryColor) !important;
        border-color: var(--primaryColor) !important;
        color: white !important; /* Add text color for active state */
    }
    .page-link {
        color: var(--secondaryColor) !important;
    }
    .page-link:hover {
        background-color: var(--primaryColor) !important;
    }

    </style>
</head>
<body>
    <div class="vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="flex-grow-1 overflow-x-auto">
            <header class="d-flex align-items-center pe-3">
                <button class="btn d-lg-block fs-6 mx-1" id="toggleSidebar" style="z-index: 100;">
                    <i class="fa-solid fa-bars fs-2"></i>
                </button>
                <nav class="d-flex justify-content-between align-items-center w-100">
                    <h1 class="mb-0">INVENTORY</h1>
                    <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2" style="min-width: 150px;">
                        <img src="{{ optional(Auth::user()->nurses)->profile_image
                            ? asset(optional(Auth::user()->nurses)->profile_image)
                            : (optional(Auth::user()->staff)->profile_image
                                ? asset(optional(Auth::user()->staff)->profile_image)
                                : asset('images/default-profile.png')) }}"
                            alt="profile picture" class="profile-img" id="profile_img">
                        <div class="username-n-role">
                            <h5 class="mb-0">{{ optional(Auth::user()->nurses)->full_name
                                ?? optional(Auth::user()->staff)->full_name
                                ?? 'none' }}</h5>
                            <h6 class="mb-0 text-muted fw-light">{{ Auth::user()->role ?? 'none' }}</h6>
                        </div>
                        <div class="links position-absolute flex-column top-20 w-100 bg-white" id="links">
                            <a href="{{ route('page.profile') }}" class="text-decoration-none text-black">view profile</a>
                            <a href="{{ route('logout') }}" class="text-decoration-none text-black">Logout</a>
                        </div>
                    </div>
                </nav>
            </header>
            {{ $slot }}
        </div>
    </div>

    @livewireScripts

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <!-- CRITICAL: Load chart data BEFORE page-specific scripts -->
    @stack('data')

    <!-- Page-specific scripts - MUST load AFTER all CDN dependencies -->
    @stack('scripts')

    <!-- Verify dependencies are loaded -->
    <script>
        if (typeof $ === 'undefined') console.error('jQuery not loaded!');
        if (typeof moment === 'undefined') console.error('Moment.js not loaded!');
        if (typeof Chart === 'undefined') console.error('Chart.js not loaded!');
        if (typeof $.fn.daterangepicker === 'undefined') console.error('Daterangepicker not loaded!');
    </script>

    <script>
        window.addEventListener('close-vaccine-modal', () => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('addVaccineModal'));
            if (modal) {
                modal.hide();
            }
        });
    </script>
</body>
</html>