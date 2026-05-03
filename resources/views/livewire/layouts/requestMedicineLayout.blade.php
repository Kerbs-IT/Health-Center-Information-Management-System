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
    'resources/css/patient/record.css',
    'resources/js/record/record.js',
    'resources/js/inventory_system/medicineRequest.js',
    ])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugo_perez_logo.png')}}">

    @livewireStyles

    <!-- Add this style section right before the closing </div> of your main container -->

</head>

<body>
    <div class="min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="flex-grow-1 overflow-x-auto">
              @include('layout.header')
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

</body>

</html>