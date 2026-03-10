<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Archived Health Workers - HCIMS</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css'])

    <div class="vaccination min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column" style="min-width: 0;">
            @include('layout.header')
            <main class="flex-column p-2 w-100 overflow-y-auto flex-grow-1">
                <div class="mb-3 w-100 px-md-3 px-1 px-lg-5">
                    <livewire:archived-health-worker />
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const subMenuElement = document.querySelectorAll(".sub-menu-bar-item");
            subMenuElement.forEach(element => element.classList.remove('active'));

            // Highlight the manage health worker menu item
            const manageHealthWorkerMenu = document.getElementById('manage_health_worker'); // ✅ Use your actual menu ID
            if (manageHealthWorkerMenu) {
                manageHealthWorkerMenu.classList.add('active');
            }
        });
    </script>

</body>

</html>