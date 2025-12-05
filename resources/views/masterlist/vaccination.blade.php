<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body >

    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/js/patient/add-patient.js',
    'resources/css/patient/record.css',
    'resources/css/masterList/masterlist.css'])
    <div class="masterList-vaccination vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column w-100 overflow-x-hidden">
            @include('layout.header')
            <div class="d-flex flex-column flex-grow-1 p-3 overflow-y-visible">
                <main class="flex-column">
                    <div class="head-part d-flex justify-content-between align-items-center mb-3">
                        <h2 class="main-header w-100">{{ $page ?? 'none'}}</h2>
                        <div class="direction d-flex gap-2 align-items-center">
                            <a href="#" class="text-decoration-none text-black">
                                <h5 class="fw-light text-nowrap mb-0">Master List</h5>
                            </a>

                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none text-black">
                                <h5 class="fw-light text-nowrap mb-0">Vaccination</h5>
                            </a>
                        </div>
                    </div>
                    <div class="main-content card shadow d-flex flex-column p-1 p-md-3 w-100  ">
                        <div class="banner">
                            <h5>Vaccination Patient</h5>
                        </div>
                        <div class="mb-3 d-flex justify-content-between flex-wrap w-100 gap-0 gap-md-5">
                            <div class="flex-column flex-fill ms-2 ms-md-0">
                                <label for="">Show</label>
                                <input type="number" value="10" class="form-control w-100 rounded bg-light">
                                <!-- <label for="">entries</label> -->
                            </div>
                            <div class="flex-column flex-fill ms-2 ms-md-0 ">
                                <label for="">Search</label>
                                <input type="number" value="" class="form-control w-100 rounded rounded bg-light" placeholder="Search here.....">
                            </div>
                            <div class="flex-column flex-fill ms-2 ms-md-0">
                                <label for="">Filter (age)</label>
                                <select name="age_range" class="form-select w-100 bg-light rounded">
                                    <option value="" selected>All Ages</option>
                                    <option value="0-6">0–6 months</option>
                                    <option value="7-11">7–11 months</option>
                                    <option value="12-23">12–23 months</option>
                                    <option value="24-35">24–35 months (2–3 years)</option>
                                    <option value="36-47">36–47 months (3–4 years)</option>
                                    <option value="48-59">48–59 months (4–5 years)</option>
                                </select>
                            </div>
                            @if((Auth::user() -> role) == 'nurse')
                            <div class="flex-column flex-fill ms-2 ms-md-0">
                                <label for="">Brgy*</label>
                                <select name="month" class="form-select">
                                    <option value="">Brgy</option>
                                    @php
                                    $brgy = \App\Models\brgy_unit::orderBy('brgy_unit')->get();
                                    @endphp

                                    @foreach($brgy as $brgy_unit)
                                    <option value="{{ $brgy_unit->id }}">{{ $brgy_unit->brgy_unit }}</option>
                                    @endforeach

                                </select>

                            </div>
                            @endif
                            <div class="button-con d-flex align-items-center mt-4 flex-fill ms-2 justify-content-end justify-content-md-start">
                                <button type="button" class="btn btn-success d-flex  justify-content-center align-items-center gap-2 px-3 py-2" style="height: auto;">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px" viewBox="0 0 512 512">
                                        <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" fill="white" />
                                    </svg>
                                    <p class="mb-0" style="font-size: 0.875rem;">Download</p>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3 text-center">
                            <h2 class="title">MASTER LIST OF 0-59 MONTHS</h2>
                        </div>
                        <div class="mb-3 d-flex justify-content-md-between flex-column flex-md-row">
                            <h4 class="w-100 w-md-50 text-center sub-title">Name of Barangay: <span class="fw-light text-decoration-underline">Karlaville Park Homes</span></h4>
                            <h4 class="w-100 w-md-50 text-center sub-title">Name of Midwife: <span class="fw-light text-decoration-underline">Nurse Joy</span></h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle table-hover">
                                <thead class="table-header">
                                    <tr>
                                        <th class="need-space sticky-col">Name of Child</th>
                                        <th class="need-space">Address</th>
                                        <th>sex</th>
                                        <th>Age</th>
                                        <th class="need-space">Date of Birth</th>
                                        <th class="">SE status 1 Months 4 months</th>
                                        <th>BCG</th>
                                        <th>NEPA w/in 24 hrs</th>
                                        <th>PENTA 1</th>
                                        <th>PENTA 2</th>
                                        <th>PENTA 3</th>
                                        <th>OPV 1</th>
                                        <th>OPV 2</th>
                                        <th>OPV3</th>
                                        <th>PCV 1</th>
                                        <th>PCV 2</th>
                                        <th>PCV 3</th>
                                        <th>IPV 1</th>
                                        <th>IPV 2</th>
                                        <th>IPV 3</th>
                                        <th>MCV 1</th>
                                        <th>MCV 2</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="need-space sticky-col">Jan louie Salimbago</td>
                                        <td class="need-space">Blk 64 Lot 10 Sampaguita village</td>
                                        <td>male</td>
                                        <td>1 week</td>
                                        <td class="need-space">01-20-2025</td>
                                        <td class=""></td>
                                        <td>BCG</td>
                                        <td>NEPA w/in 24 hrs</td>
                                        <td>02-10-2025</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Healthy Baby</td>
                                    </tr>
                                    <tr>
                                        <td class="need-space sticky-col">Christine Dacera</td>
                                        <td class="need-space">Blk 25 lot 11 Golden Horizon</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- row 3 -->
                                    <tr>
                                        <td class="need-space"></td>
                                        <td class="need-space"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- 4 -->
                                    <tr>
                                        <td class="need-space"></td>
                                        <td class="need-space"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- 5 -->
                                    <tr>
                                        <td class="need-space"></td>
                                        <td class="need-space"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- 6 -->
                                    <tr>
                                        <td class="need-space"></td>
                                        <td class="need-space"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- 7 -->
                                    <tr>
                                        <td class="need-space"></td>
                                        <td class="need-space"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- 8 -->
                                    <tr>
                                        <td class="need-space"></td>
                                        <td class="need-space"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- 9 -->
                                    <tr>
                                        <td class="need-space"></td>
                                        <td class="need-space"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <!-- 10 -->
                                    <tr>
                                        <td class="need-space"></td>
                                        <td class="need-space"></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if(Auth::user() -> role == 'staff')
                        <div class="mb-3">
                            <h2>Name of BHM:<span>{{Auth::user() -> staff -> fullName}}</span></h2>
                        </div>
                        @endif
                    </div>
                </main>
            </div>
        </div>
    </div>
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('masterlist_vaccination');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>