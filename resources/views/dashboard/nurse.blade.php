<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>
<body>
    @vite(['resources/css/app.css','resources/js/app.js','resources/js/menudropdown.js','resources/js/header.js'])

    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside >
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
             <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <main class="flex-grow-1"> 
                @include('layout.header')
            </main>
        </div>
     </div>
</body>
</html>