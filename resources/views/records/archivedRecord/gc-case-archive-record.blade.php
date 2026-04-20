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

    @php
    $typeOfCase = request('type_of_case');

    $elementIdMap = [
    'general-consultation' => 'record_general_consultation',
    'prenatal' => 'record_prenatal',
    'family-planning' => 'record_family_planning',
    'tb-dots' => 'record_tb_dots',
    'senior-citizen' => 'record_senior_citizen',
    'vaccination' => 'record_vaccination'
    ];

    $elementId = $elementIdMap[$typeOfCase] ?? 'record_general_consultation';
    @endphp

    <div class="vaccination min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column" style="min-width: 0;">
            @include('layout.header')
            <main class="flex-column p-2 w-100 overflow-y-auto flex-grow-1">
                <div class="mb-3 w-100 px-md-3 px-1 px-lg-5">
                    <livewire:archive.general-consultation-case-archive />
                </div>
            </main>
        </div>
    </div>

    @if($isActive)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const subMenuElement = document.querySelectorAll(".sub-menu-bar-item");
            subMenuElement.forEach(element => element.classList.remove('active'));

            const con = document.getElementById('record_general_consultation');
            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif

</body>

</html>