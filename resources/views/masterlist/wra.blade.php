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
    <div class="masterList-vaccination min-vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column w-100 overflow-x-hidden min-vh-100">
            @include('layout.header')
            <div class="d-flex flex-column flex-grow-1 p-md-3 p-1 overflow-y-visible">
                <main class="flex-column">
                    <div class="head-part d-flex justify-content-between align-items-center mb-3">
                        <h2 class="main-header w-100">{{ $page ?? 'none'}}</h2>
                        <div class="direction d-flex gap-2 align-items-center">
                            <a href="#" class="text-decoration-none text-black">
                                <h5 class="fw-light text-nowrap mb-0 d-none d-sm-block">Master List</h5>
                            </a>

                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right d-none d-sm-block" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none text-black">
                                <h5 class="fw-light text-nowrap mb-0 d-none d-sm-block">WRA</h5>
                            </a>
                        </div>
                    </div>
                    <div class="main-content card shadow d-flex flex-column p-md-3 p-1 w-100  ">
                        <div class="banner  ">
                            <h5>Vaccination Patient</h5>
                        </div>
                        <div class="mb-md-3 mb-1 d-flex justify-content-between w-100  gap-lg-5 gap-md-3 gap-1 flex-wrap">
                            <div class="flex-column flex-fill">
                                <label for="">Show</label>
                                <input type="number" value="10" class="form-control w-100 rounded bg-light">
                                <!-- <label for="">entries</label> -->
                            </div>
                            <div class="flex-column flex-fill ">
                                <label for="">Search</label>
                                <input type="number" value="" class="form-control w-100 rounded rounded bg-light" placeholder="Search here.....">
                            </div>
                            <div class="flex-column flex-fill">
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
                            <div class="flex-column flex-fill">
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
                            <div class="button-con d-flex align-items-center mt-4">
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
                        <div class="mb-3 d-flex justify-content-start justify-content-md-between  flex-wrap">
                            <h4 class="flex-fill text-center sub-title">Name of Barangay: <span class="fw-bold text-decoration-underline">Karlaville Park Homes</span></h4>
                            <h4 class="flex-fill text-center sub-title">Name of Midwife: <span class="fw-bold text-decoration-underline">Nurse Joy</span></h4>
                            <h4 class="flex-fill text-center sub-title">Date Prepared: <span class="fw-bold text-decoration-underline">06 - 01 - 2025</span></h4>
                        </div>
                        <div class="table-con table-responsive">
                            <table class="table ">
                                <thead class="table-header" >
                                    <tr>
                                        <th rowspan="3">No.</th>
                                        <th rowspan="3">
                                            <p>HH No.</p>
                                            <p>(1)</p>
                                        </th>
                                        <th class="need-space" rowspan="3">
                                            <p>Name of WRA(FN,MI,LN)</p>
                                            <p>(2)</p>
                                        </th>
                                        <th class="need-space" rowspan="3">
                                            <p>Address</p>
                                            <p>(3)</p>
                                        </th>
                                        <th colspan="3">

                                            <p>Age in Years</p>
                                            <p>(4)</p>
                                        </th>
                                        <th class="need-space" rowspan="3">
                                            <p>Birthday</p>
                                            <p>(MM/DD/YY)</p>
                                            <p>(5)</p>

                                        </th>
                                        <th>
                                            <p>SE Status </p>
                                            <p>(6)</p>
                                        </th>
                                        <th class="need-space" colspan="3" >
                                            <p>Do you plan to have more children?</p>
                                            <p>(Place a check)</p>
                                            <p>(7)</p>
                                        </th>
                                        <th class="need-space" colspan="3">
                                            <p>If col. 7b & 7c is (✓),are you currently using any FP method</p>
                                            <p>(8)</p>
                                        </th>
                                        <th class="need-space" colspan="2">
                                            <p>if col 7b or 7c is ✓ and using col 8b or 8c, would you like to shift to modern method? place(✓)</p>
                                            <p>(9)</p>
                                        </th>
                                        <th>
                                            <p>WRA with MFP Unmet Need</p>
                                            <p>(10)</p>
                                        </th>
                                        <th colspan="3">
                                            <p>Based on TCL on FP, did WRA accept any modern FP method?</p>
                                            <p>(11)</p>
                                        </th>

                                    </tr>
                                    <tr>
                                        <th rowspan="2">10-14</th>
                                        <th rowspan="2">15-19</th>
                                        <th rowspan="2">20-49</th>
                                        <th rowspan="2">
                                            <p>1.NHTS</p>
                                            <p>2.NON-NHTS</p>
                                        </th>
                                        <th colspan="2">if Yes,when?</th>
                                        <th>No</th>
                                        <th colspan="2">If Yes, what type?</th>
                                        <th rowspan="2">
                                            Not using any FP method(place a ✓)
                                            <p>(8c)</p>
                                        </th>
                                        <th rowspan="2">
                                            <p>Yes</p>
                                            <p>(9a)</p>
                                        </th>
                                        <th rowspan="2">
                                            <p>No</p>
                                            <p>(9b)</p>
                                        </th>
                                        <th rowspan="2">(Put ✓ if col (a is checked))</th>
                                        <th rowspan="2">
                                            <p>No (11a) (Put a ✓)</p>
                                        </th>
                                        <th colspan="2">
                                            <p>Yes</p>
                                            <p>(11b)</p>
                                        </th>

                                    </tr>
                                    <tr>

                                        <th>
                                            <p>Now</p>
                                            <p>(7a)</p>
                                        </th>
                                        <th>
                                            <p>Spacing</p>
                                            <p>(7b)</p>
                                        </th>
                                        <th>
                                            <p>Limiting</p>
                                            <p>(7c)</p>
                                        </th>
                                        <th>
                                            <p>modern</p>
                                            <p>(8a)</p>
                                        </th>
                                        <th>
                                            <p>traditional</p>
                                            <p>(8b)</p>
                                        </th>
                                        <th>specify modern FP method</th>
                                        <th>
                                            Date when FP method accepted
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td></td>
                                        <td>Ann V. Santos </td>
                                        <td>Blk 64 Lot 10 Sampaguita village</td>
                                        <td></td>
                                        <td>✓</td>
                                        <td></td>
                                        <td>02-01-2025</td>
                                        <td></td>
                                        <td>✓</td>
                                        <td></td>
                                        <td></td>
                                        <td>✓</td>
                                        <td></td>
                                        <td></td>
                                        <td>✓</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
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
            const con = document.getElementById('masterlist_wra');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>