<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/css/patient/record.css',
    'resources/js/record/record.js',
    'resources/js/datePicker/record.js'])

    <div class="vaccination min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <main class="flex-column p-2 px-md-4 px-1">
                <h1>Prenatal</h1>
                <!-- body part -->
                <livewire:prenatal.records-table>

            </main>
        </div>
    </div>

    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            // ✅ Clear old localStorage so record_all doesn't stay active
            localStorage.removeItem('activeMenuItem');

            // Remove active from all items first
            document.querySelectorAll('.menu-items').forEach(el => {
                el.classList.remove('active');
            });
            const con = document.getElementById('record_prenatal');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif

</body>

</html>