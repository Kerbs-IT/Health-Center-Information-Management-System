<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>
<body>
    @vite(['resources/css/app.css','resources/css/nurse_dashboard.css','resources/js/app.js','resources/js/menudropdown.js','resources/js/header.js','resources/js/chart/chart.js'])

    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside >
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
             <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1"> 
                @include('layout.header')
                <main class="mt-4">
                    <div class="data-over-view d-flex gap-3 flex-wrap justify-content-center mb-3">
                        <div class="item rounded">
                            <div class="d-flex gap-3 justify-content-between px-3 ">
                                <div class="overall-data fs-1 align-self-center text-black p-3">100</div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="dashboard-icons" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M48 0C21.5 0 0 21.5 0 48L0 256l144 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L0 288l0 64 144 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L0 384l0 80c0 26.5 21.5 48 48 48l217.9 0c-6.3-10.2-9.9-22.2-9.9-35.1c0-46.9 25.8-87.8 64-109.2l0-95.9L320 48c0-26.5-21.5-48-48-48L48 0zM152 64l16 0c8.8 0 16 7.2 16 16l0 24 24 0c8.8 0 16 7.2 16 16l0 16c0 8.8-7.2 16-16 16l-24 0 0 24c0 8.8-7.2 16-16 16l-16 0c-8.8 0-16-7.2-16-16l0-24-24 0c-8.8 0-16-7.2-16-16l0-16c0-8.8 7.2-16 16-16l24 0 0-24c0-8.8 7.2-16 16-16zM512 272a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM288 477.1c0 19.3 15.6 34.9 34.9 34.9l218.2 0c19.3 0 34.9-15.6 34.9-34.9c0-51.4-41.7-93.1-93.1-93.1l-101.8 0c-51.4 0-93.1 41.7-93.1 93.1z" fill="#2E8B57"/></svg>
                            </div>
                            <h4 class="text-black text-center mt-1">Overall Patient</h4>
                        </div>
                        <!-- vaccination -->
                        <div class="item rounded">
                            <div class="d-flex gap-3 justify-content-between px-3 ">
                                <div class="overall-data fs-1 align-self-center text-black p-3">1</div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="dashboard-icons"  viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M441 7l32 32 32 32c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-15-15L417.9 128l55 55c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-72-72L295 73c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l55 55L422.1 56 407 41c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0zM210.3 155.7l61.1-61.1c.3 .3 .6 .7 1 1l16 16 56 56 56 56 16 16c.3 .3 .6 .6 1 1l-191 191c-10.5 10.5-24.7 16.4-39.6 16.4l-88.8 0L41 505c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l57-57 0-88.8c0-14.9 5.9-29.1 16.4-39.6l43.3-43.3 57 57c6.2 6.2 16.4 6.2 22.6 0s6.2-16.4 0-22.6l-57-57 41.4-41.4 57 57c6.2 6.2 16.4 6.2 22.6 0s6.2-16.4 0-22.6l-57-57z" fill="#2E8B57"/></svg>
                            </div>
                            <h4 class="text-black text-center mt-1">Vaccination</h4>
                        </div>
                        <!-- prenatal  -->
                        <div class="item rounded">
                            <div class="d-flex gap-3 justify-content-between px-3">
                                <div class="overall-data fs-1 align-self-center text-black p-3">1</div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="dashboard-icons" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M192 0a48 48 0 1 1 0 96 48 48 0 1 1 0-96zM120 383c-13.8-3.6-24-16.1-24-31l0-55.1-4.6 7.6c-9.1 15.1-28.8 20-43.9 10.9s-20-28.8-10.9-43.9l58.3-97c15-24.9 40.3-41.5 68.7-45.6c4.1-.6 8.2-1 12.5-1l1.1 0 12.5 0 2.4 0c1.4 0 2.8 .1 4.1 .3c35.7 2.9 65.4 29.3 72.1 65l6.1 32.5c44.3 8.6 77.7 47.5 77.7 94.3l0 32c0 17.7-14.3 32-32 32l-16 0-40 0 0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-96-8 0-8 0 0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-97z" fill="#2E8B57"/></svg>
                            </div>
                            <h4 class="text-black text-center mt-1">Prenatal</h4>
                        </div>
                        <!-- senior citizen -->
                        <div class="item rounded">
                            <div class="d-flex gap-3 justify-content-between px-3 ">
                                <div class="overall-data fs-1 align-self-center text-black p-3">1</div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="dashboard-icons" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M272 48a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zm-8 187.3l47.4 57.1c11.3 13.6 31.5 15.5 45.1 4.2s15.5-31.5 4.2-45.1l-73.7-88.9c-18.2-22-45.3-34.7-73.9-34.7l-35.9 0c-33.7 0-64.9 17.7-82.3 46.6l-58.3 97c-9.1 15.1-4.2 34.8 10.9 43.9s34.8 4.2 43.9-10.9L120 256.9 120 480c0 17.7 14.3 32 32 32s32-14.3 32-32l0-128 16 0 0 128c0 17.7 14.3 32 32 32s32-14.3 32-32l0-244.7zM352 376c0-4.4 3.6-8 8-8s8 3.6 8 8l0 112c0 13.3 10.7 24 24 24s24-10.7 24-24l0-112c0-30.9-25.1-56-56-56s-56 25.1-56 56l0 8c0 13.3 10.7 24 24 24s24-10.7 24-24l0-8z"fill="#2E8B57"/></svg>
                            </div>
                            <h4 class="text-black text-center mt-1">Senior Citizen</h4>
                        </div> 
                        <!-- TB DOTS -->
                        <div class="item rounded">
                            <div class="d-flex gap-3 justify-content-between px-3 ">
                                <div class="overall-data fs-1 align-self-center text-black p-3">1</div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="dashboard-icons" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M48 0C21.5 0 0 21.5 0 48L0 256l144 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L0 288l0 64 144 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L0 384l0 80c0 26.5 21.5 48 48 48l217.9 0c-6.3-10.2-9.9-22.2-9.9-35.1c0-46.9 25.8-87.8 64-109.2l0-95.9L320 48c0-26.5-21.5-48-48-48L48 0zM152 64l16 0c8.8 0 16 7.2 16 16l0 24 24 0c8.8 0 16 7.2 16 16l0 16c0 8.8-7.2 16-16 16l-24 0 0 24c0 8.8-7.2 16-16 16l-16 0c-8.8 0-16-7.2-16-16l0-24-24 0c-8.8 0-16-7.2-16-16l0-16c0-8.8 7.2-16 16-16l24 0 0-24c0-8.8 7.2-16 16-16zM512 272a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM288 477.1c0 19.3 15.6 34.9 34.9 34.9l218.2 0c19.3 0 34.9-15.6 34.9-34.9c0-51.4-41.7-93.1-93.1-93.1l-101.8 0c-51.4 0-93.1 41.7-93.1 93.1z" fill="#2E8B57"/></svg>
                            </div>
                            <h4 class="text-black text-center mt-1">TB Dots</h4>
                        </div>
                        <!-- WRA -->
                        <div class="item rounded">
                            <div class="d-flex gap-3 justify-content-between px-3 ">
                                <div class="overall-data fs-1 align-self-center text-black p-3">1</div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="dashboard-icons" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M48 0C21.5 0 0 21.5 0 48L0 256l144 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L0 288l0 64 144 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L0 384l0 80c0 26.5 21.5 48 48 48l217.9 0c-6.3-10.2-9.9-22.2-9.9-35.1c0-46.9 25.8-87.8 64-109.2l0-95.9L320 48c0-26.5-21.5-48-48-48L48 0zM152 64l16 0c8.8 0 16 7.2 16 16l0 24 24 0c8.8 0 16 7.2 16 16l0 16c0 8.8-7.2 16-16 16l-24 0 0 24c0 8.8-7.2 16-16 16l-16 0c-8.8 0-16-7.2-16-16l0-24-24 0c-8.8 0-16-7.2-16-16l0-16c0-8.8 7.2-16 16-16l24 0 0-24c0-8.8 7.2-16 16-16zM512 272a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM288 477.1c0 19.3 15.6 34.9 34.9 34.9l218.2 0c19.3 0 34.9-15.6 34.9-34.9c0-51.4-41.7-93.1-93.1-93.1l-101.8 0c-51.4 0-93.1 41.7-93.1 93.1z" fill="#2E8B57"/></svg>
                            </div>
                            <h4 class="text-black text-center mt-1">WRA</h4>
                        </div>
                    </div>
                    <!-- chart n recent patient -->
                    <div class="charts d-flex gap-3 justify-content-center flex-wrap">
                        <div class="w-[610px] h-[350px] bg-white rounded p-3">
                            <canvas id="myChart"></canvas>
                        </div>
                         <div class="w-[450px] h-[350px] d-flex align-items-center justify-content-center bg-white rounded p-3">
                            <canvas id="myPieChart"></canvas>
                        </div>
                        <!-- recent added patient -->
                         <div class="recent-patient w-[500px] h-[350px] bg-white p-3 rounded overflow-auto">
                            <h3 class="">Recent Patient</h3>
                            <table class="table overflow-y">
                                <thead>
                                    <th scope="col">Name</th>
                                    <th scope="col">Gender</th>
                                    <th scope="col">Patient Type</th>
                                    <th scope="col">Date</th>
                                </thead>
                                <tbody class="">
                                    <tr >
                                        <td class="d-flex align-items-center gap-1"> 
                                            <img src="{{ asset('images/profile_images/default_profile.png') }}" alt="profile img" class="w-[40px] h-[40px]">
                                            <p class="recent-post-text mb-0">Kerby G. Buan</p>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <p class="recent-post-text">Male</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">Vaccination</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">O6-1-25</p>
                                        </td>
                                    </tr>
                                    <tr >
                                        <td class="d-flex align-items-center gap-1"> 
                                            <img src="{{ asset('images/profile_images/default_profile.png') }}" alt="profile img" class="w-[40px] h-[40px]">
                                            <p class="recent-post-text mb-0">Kerby G. Buan</p>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <p class="recent-post-text">Male</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">Vaccination</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">O6-1-25</p>
                                        </td>
                                    </tr>
                                    <tr >
                                        <td class="d-flex align-items-center gap-1"> 
                                            <img src="{{ asset('images/profile_images/default_profile.png') }}" alt="profile img" class="w-[40px] h-[40px]">
                                            <p class="recent-post-text mb-0">Kerby G. Buan</p>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <p class="recent-post-text">Male</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">Vaccination</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">O6-1-25</p>
                                        </td>
                                    </tr>
                                    <tr >
                                        <td class="d-flex align-items-center gap-1"> 
                                            <img src="{{ asset('images/profile_images/default_profile.png') }}" alt="profile img" class="w-[40px] h-[40px]">
                                            <p class="recent-post-text mb-0">Kerby G. Buan</p>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <p class="recent-post-text">Male</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">Vaccination</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">O6-1-25</p>
                                        </td>
                                    </tr>
                                    <tr >
                                        <td class="d-flex align-items-center gap-1"> 
                                            <img src="{{ asset('images/profile_images/default_profile.png') }}" alt="profile img" class="w-[40px] h-[40px]">
                                            <p class="recent-post-text mb-0">Kerby G. Buan</p>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <p class="recent-post-text">Male</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">Vaccination</p>
                                        </td>
                                        <td>
                                            <p class="recent-post-text">O6-1-25</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                         </div>
                    </div>
                </main>
            </div>
        </div>
     </div>
     @if($isActive)
        <script>
            // load all of the content first
            document.addEventListener('DOMContentLoaded', () =>{
                const con = document.getElementById('dashboard');

                if(con){
                    con.classList.add('active');
                }
            })
            
        </script>
    @endif
</body>
</html>