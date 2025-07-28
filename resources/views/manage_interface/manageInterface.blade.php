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
    'resources/css/nurse_dashboard.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
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
                    <div class="color-pallette  h-[700px] bg-light w-[900px] p-3 d-flex align-items-center flex-column  rounded">
                        <div class="pallet-con w-50">
                            <h2 class="text-center fw-bold text-dark"> Change Color Pallete </h2>
                            <div class="colors mt-5">
                                <form action="" method="post" id="color-pallete-form">
                                    @method('PUT')
                                    @csrf
                                    <div class="inputs d-flex flex-column">
                                        <label for="" class="fw-bold mb-1">Choose Primary Color</label>
                                        <div class="color-text-wrap w-100 mb-3">
                                            <input type="color" class="color-box" id="primary_color" name="primaryColor">
                                            <input type="text" class="color-code w-100" id="primary_hex" value="#FFFFFF">
                                        </div>
                                    </div>
                                    <!-- secondary -->
                                    <div class="inputs d-flex flex-column">
                                        <label for="" class="fw-bold mb-1">Choose Secondary Color</label>
                                        <div class="color-text-wrap w-100 mb-3">
                                            <input type="color" class="color-box" id="secondary_color" name="secondaryColor">
                                            <input type="text" class="color-code w-100" id="secondary_hex" value="">
                                        </div>
                                    </div>
                                    <!-- 3rd -->
                                    <div class="inputs d-flex flex-column">
                                        <label for="" class="fw-bold mb-1">Choose Tertiary Color</label>
                                        <div class="color-text-wrap w-100 mb-3">
                                            <input type="color" class="color-box" id="tertiary_color" name="tertiaryColor">
                                            <input type="text" class="color-code w-100" id="tertiary_hex" value="#2E8B57">
                                        </div>
                                    </div>
                                </form>
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
            const con = document.getElementById('manage-interface');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>