<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    @vite(['resources/css/app.css',
        'resources/js/app.js',
        'resources/js/menudropdown.js',
        'resources/js/header.js',
        'resources/css/profile.css',
        'resources/js/patient/add-patient.js',
        'resources/css/patient/record.css',
        'resources/js/record/record.js',
        'resources/js/inventory_system/category.js',
        'resources/js/inventory_system/medicineRequest.js',
        'resources\css\patient\patient-request-medicine.css'
    ])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugo_perez_logo.png')}}">

    @livewireStyles

<!-- Add this style section right before the closing </div> of your main container -->
<style>
    /* Pagination Container */
    nav[role="navigation"] {
        margin-top: 1.5rem !important;
    }

    /* Pagination Links - Default State */
    .pagination .page-link {
        color: #198754 !important;
        border: 1px solid #198754 !important;
        background-color: white !important;
        padding: 0.5rem 0.75rem !important;
        margin: 0 2px !important;
        border-radius: 0.375rem !important;
        transition: all 0.3s ease !important;
        font-weight: 500 !important;
    }

    /* Hover State */
    .pagination .page-link:hover {
        background-color: #198754 !important;
        color: white !important;
        border-color: #198754 !important;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(25, 135, 84, 0.3) !important;
    }

    /* Focus State */
    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
        z-index: 3;
    }

    /* Active Page */
    .pagination .page-item.active .page-link {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: white !important;
        font-weight: bold !important;
    }

    /* Active Page - Remove hover transform */
    .pagination .page-item.active .page-link:hover {
        transform: none !important;
    }

    /* Disabled State */
    .pagination .page-item.disabled .page-link {
        color: #6c757d !important;
        border-color: #dee2e6 !important;
        background-color: #f8f9fa !important;
        cursor: not-allowed !important;
    }

    .pagination .page-item.disabled .page-link:hover {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        transform: none !important;
        box-shadow: none !important;
    }

    /* Previous and Next Text */
    .pagination .page-link span {
        color: inherit !important;
    }
</style>
</head>
<body>
    <div class="min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="flex-grow-1 overflow-x-auto">
            <header class="d-flex align-items-center pe-3">
                <button class="btn d-lg-block fs-6 mx-1" id="toggleSidebar" style="z-index: 100;">
                    <i class="fa-solid fa-bars fs-2"></i>
                </button>
                <nav class="d-flex justify-content-between align-items-center w-100">
                    <h1 class="mb-0">Request</h1>
                    <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2" style="min-width: 150px;">
                        <img src="{{ optional(Auth::user()->nurses)->profile_image
                            ? asset(optional(Auth::user()->nurses)->profile_image)
                            : (optional(Auth::user()->staff)->profile_image
                                ? asset(optional(Auth::user()->staff)->profile_image)
                                : asset('images/default_profile.png')) }}"
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

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <!-- Page-specific scripts -->
    @stack('scripts')

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