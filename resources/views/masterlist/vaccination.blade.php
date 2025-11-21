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
    'resources/css/masterList/masterlist.css',
    'resources/js/masterlist/vaccination.js'])
    <div class="masterList-vaccination vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column w-100 overflow-x-hidden">
            @include('layout.header')
            <div class="d-flex flex-column flex-grow-1 p-3 overflow-y-auto">
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
                    <div class="main-content card shadow d-flex flex-column p-3 w-100  ">
                        <div class="banner">
                            <h5>Vaccination Patient</h5>
                        </div>
                        <div class="mb-3 d-flex justify-content-between w-100 gap-5">
                            <div class="input-group flex-column w-25">
                                <label for="">Show</label>
                                <input type="number" value="10" class="form-control w-100 rounded bg-light">
                                <!-- <label for="">entries</label> -->
                            </div>
                            <div class="input-group flex-column w-25 ">
                                <label for="">Search</label>
                                <input type="number" value="" class="form-control w-100 rounded rounded bg-light" placeholder="Search here.....">
                            </div>
                            <div class="input-group flex-column w-25">
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
                            <div class="iput-group flex-column w-25">
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
                            <h2>MASTER LIST OF 0-59 MONTHS</h2>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <h4 class="w-50 text-center">Name of Barangay: <span class="fw-light text-decoration-underline">Karlaville Park Homes</span></h4>
                            <h4 class="w-50 text-center">Name of Midwife: <span class="fw-light text-decoration-underline">Nurse Joy</span></h4>
                        </div>
                        <div class="table-con">
                            <table>
                                <thead class="table-header ">
                                    <tr>
                                        <th class="need-space">Name of Child</th>
                                        <th class="need-space">Address</th>
                                        <th>sex</th>
                                        <th>Age</th>
                                        <th class="need-space">Date of Birth</th>
                                        <th class="" style="font-size: 15px;">SE status 1 Months 4 months</th>
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
                                        <th>MCV 1</th>
                                        <th>MCV 2</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vaccinationMasterlist as $masterlist)
                                    <tr>

                                        <td class="need-space">{{optional($masterlist)->name_of_child??''}}</td>
                                        <td class="need-space">{{optional($masterlist)-> Address ?? ''}}</td>
                                        <td>{{optional($masterlist)-> sex ?? ''}}</td>
                                        <td>{{optional($masterlist)-> age ?? ''}}</td>
                                        <td class="need-space">{{optional($masterlist)-> date_of_birth->format('Y-m-d') ?? ''}}</td>
                                        <td class="" style="font-size: 15px;">{{optional($masterlist)->SE_status??''}}</td>
                                        <td>{{optional($masterlist)->BCG??''}}</td>
                                        <td>{{optional($masterlist)->{'Hepatitis B'}??''}}</td>
                                        <td>{{optional($masterlist)-> PENTA_1??''}}</td>
                                        <td>{{optional($masterlist)-> PENTA_2??''}}</td>
                                        <td>{{optional($masterlist)->PENTA_3??''}}</td>
                                        <td>{{optional($masterlist)->OPV_1}}</td>
                                        <td>{{optional($masterlist)->OPV_2}}</td>
                                        <td>{{optional($masterlist)->OPV_3}}</td>
                                        <td>{{optional($masterlist)->PCV_1}}</td>
                                        <td>{{optional($masterlist)->PCV_2}}</td>
                                        <td>{{optional($masterlist)->PCV_3}}</td>
                                        <td>{{optional($masterlist)->IPV_1}}</td>
                                        <td>{{optional($masterlist)->IPV_2}}</td>
                                        <td>{{optional($masterlist)->MCV_1??''}}</td>
                                        <td>{{optional($masterlist)->MCV_2}}</td>
                                        <td>{{optional($masterlist)->remarks}}</td>
                                        <td>
                                            <button class="btn btn-success vaccination-masterlist-edit-btn" data-bs-toggle="modal" data-bs-target="#vaccinationMasterListModal" data-masterlist-id="{{$masterlist->id}}">Edit</button>
                                        </td>

                                    </tr>
                                    @endforeach
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

        <div class="modal fade" id="vaccinationMasterListModal" tabindex="-1" aria-labelledby="vaccinationMasterListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="#" class="flex-column" id="edit-vaccination-masterlist-form">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Masterlist Details</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                        </div>

                        <div class="modal-body w-100">
                            <div class="input-group mb-2">
                                <label for="" class="w-100">Name of Child</label>
                                <div class="full-name d-flex gap-2 w-100 flex-grow-1">
                                    <input type="text" name="vaccination_masterlist_fname" id="vaccination_masterlist_fname" placeholder="Enter First Name" class="form-control border">
                                    <input type="text" name="vaccination_masterlist_MI" id="vaccination_masterlist_MI" placeholder="Enter Middle Initial" class="form-control  border">
                                    <input type="text" name="vaccination_masterlist_lname" id="vaccination_masterlist_lname" placeholder="Enter Last Name" class="form-control  border">
                                </div>

                            </div>
                            <div class="input-group mb-2">
                                <h4>Address</h4>
                                <div class="input-field d-flex gap-2 align-items-center w-100">
                                    <div class=" mb-2 w-50">
                                        <label for="street">Street*</label>
                                        <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2 border" name="street" value="">
                                        <small class="text-danger error-text" id="street_error"></small>
                                    </div>
                                    <div class="mb-2 w-50">
                                        <label for="brgy">Barangay*</label>
                                        @php
                                        $brgy = \App\Models\brgy_unit::orderBy('brgy_unit') -> get();
                                        @endphp
                                        <select name="brgy" id="brgy" class="form-select py-2">
                                            <option value="" disabled selected>Select a brgy</option>
                                            @foreach($brgy as $brgy_unit)
                                            <option value="{{ $brgy_unit -> brgy_unit }}">{{$brgy_unit -> brgy_unit}}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-danger error-text" id="brgy_error"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-2 w-100 d-flex flex-grow-1 gap-2 ">
                                <div class="input-field flex-grow-1 ">
                                    <label for="">Sex</label>
                                    <div class="input-field d-flex align-items-center p-2">
                                        <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                            <input type="radio" id="male" class="mb-0" name="sex" value="male" class="mb-0">
                                            <label for="male">Male</label>
                                            <input type="radio" id="female" class="mb-0" name="sex" value="female" class="mb-0">
                                            <label for="female">Female</label>
                                        </div>
                                        <small class="text-danger error-text" id="sex_error"></small>
                                    </div>
                                </div>
                                <div class="input-field flex-grow-1 ">
                                    <label for="age">Age</label>
                                    <input type="number" id="age" placeholder="20" class="form-control" name="age" value="">
                                    <small class="text-danger error-text" id="age_error"></small>
                                </div>
                                <div class="input-field flex-grow-1 ">
                                    <label for="birthdate">Date of Birth</label>
                                    <input type="date" id="birthdate" placeholder="01-02-25" class="form-control w-100 px-5" name="date_of_birth" value="">
                                    <small class="text-danger error-text" id="date_of_birth_error"></small>
                                </div>
                            </div>

                            <div class="d-flex gap-2 w-100">
                                <div class="vaccination-date flex-grow-1">
                                    <label>SE status</label>
                                    <input type="text" name="SE_status" class="form-control">
                                </div>

                                <div class="vaccination-date flex-grow-1">
                                    <label for="BCG_input">BCG</label>
                                    <input type="date" class="form-control" name="BCG" id="BCG_input">
                                </div>

                                <div class="vaccination-date flex-grow-1">
                                    <label for="Hepatitis_w/in_24hrs">Hepatitis w/in 24hrs</label>
                                    <input type="date" class="form-control" name="Hepatitis_B" id="Hepatitis_w/in_24hrs_input">
                                </div>
                            </div>

                            <div class="input-group d-flex gap-2 w-100">
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">PENTA 1</label>
                                    <input type="date" name="PENTA_1" class="form-control w-100 border">
                                </div>
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">PENTA 2</label>
                                    <input type="date" name="PENTA_2" class="form-control w-100 border">
                                </div>
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">PENTA 3</label>
                                    <input type="date" name="PENTA_3" class="form-control w-100 border">
                                </div>
                            </div>
                            <div class="input-group d-flex gap-2">
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">OPV 1</label>
                                    <input type="date" name="OPV_1" class="form-control border w-100">
                                </div>
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">OPV 2</label>
                                    <input type="date" name="OPV_2" class="form-control border w-100">
                                </div>
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">OPV 3</label>
                                    <input type="date" name="OPV_3" class="form-control border w-100">
                                </div>
                            </div>
                            <div class="input-group d-flex gap-2">
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">PCV 1</label>
                                    <input type="date" name="PCV_1" class="form-control border w-100">
                                </div>
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">PCV 2</label>
                                    <input type="date" name="PCV_2" class="form-control border w-100">
                                </div>
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">PCV 3</label>
                                    <input type="date" name="PCV_3" class="form-control border w-100">
                                </div>
                            </div>
                            <!-- ipv -->
                            <div class="input-group d-flex gap-2">
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">IPV 1</label>
                                    <input type="date" name="IPV_1" class="form-control border w-100">
                                </div>
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">IPV 2</label>
                                    <input type="date" name="IPV_2" class="form-control border w-100">
                                </div>
                            </div>
                            <!-- mcv -->
                            <div class="input-group d-flex gap-2">
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">MCV 1</label>
                                    <input type="date" name="MCV_1" class="form-control border w-100" id="MCV_1">
                                </div>
                                <div class="vaccination-date flex-grow-1">
                                    <label for="">MCV 2</label>
                                    <input type="date" name="MCV_2" class="form-control border w-100">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="" class="">Remarks</label>
                                <input type="text" name="remarks" class="form-control">
                            </div>
                        </div>


                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="add-cancel-btn">Cancel</button>
                            <button type="submit" class="btn btn-success" id="update_vaccination_masterlist_save_btn">Update Record</button>
                        </div>
                    </form>
                </div>
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