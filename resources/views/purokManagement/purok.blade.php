<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
    <style>
        .swap-icon-con {
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .swap-icon-con:hover {
            background-color: rgba(23, 162, 184, 0.1);
        }

        .action-icon {
            width: 18px;
            height: 18px;
        }
    </style>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/css/patient/record.css',
    'resources/js/header.js'])
    @include('sweetalert::alert')
    <div class="ms-0 ps-0 d-flex w-100  min-vh-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-lg-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1">
                @include('layout.header')
                <main class="m-3 overflow-auto max-h-[calc(100vh-100px)] p-0 p-md-3">

                    <livewire:purok-management>

                </main>
              
            </div>
        </div>
    </div>


    <!-- eye icon -->

</body>

</html>