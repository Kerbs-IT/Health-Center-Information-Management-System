<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/css/nurse_dashboard.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/js/chart/chart.js',
    'resources/css/manageInterface.css',
    'resources/js/manageInterface/manageInterface.js'])

    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1 ">
                @include('layout.header')
                <main class=" mt-4 d-flex align-items-center justify-content-center flex-grow-1">
                    <div class="color-pallette  h-[700px] bg-light w-[900px] p-3 d-flex align-items-center flex-column ">
                        <div class="pallet-con w-50">
                            <h2 class="text-center fw-bold"> Change Color Pallete </h2>
                            <div class="colors mt-5">
                                <div class="inputs d-flex flex-column">
                                    <label for="" class="fw-bold mb-1">Choose Primary Color</label>
                                    <div class="color-text-wrap w-100 mb-3">
                                        <input type="color" class="color-box" value="#065A24" id="primary_color">
                                        <input type="text" class="color-code w-100" value="#065A24" id="primary_hex">
                                    </div>
                                </div>
                                <!-- secondary -->
                                <div class="inputs d-flex flex-column">
                                    <label for="" class="fw-bold mb-1">Choose Secondary Color</label>
                                    <div class="color-text-wrap w-100 mb-3">
                                        <input type="color" class="color-box" value="#065A24" id="secondary_color">
                                        <input type="text" class="color-code w-100" value="#065A24" id="secondary_hex">
                                    </div>
                                </div>
                                <!-- 3rd -->
                                <div class="inputs d-flex flex-column">
                                    <label for="" class="fw-bold mb-1">Choose Tertiary Color</label>
                                    <div class="color-text-wrap w-100 mb-3">
                                        <input type="color" class="color-box" value="#065A24" id="tertiary_color">
                                        <input type="text" class="color-code w-100" value="#065A24" id="tertiary_hex">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('manage_interface');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>