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
    'resources/css/patient/record.css'])
    <div class="patient-details vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-auto">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center border-bottom">
                        <h2>Update Patient Details</h2>
                        <div class="sequence-links d-flex justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('records.prenatal') }}" class="text-decoration-none fs-5 text-muted">Prenatal</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">Edit Patient Info</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-5">
                        <a href="{{route('records.prenatal')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <form action="" method="post" class="d-flex flex-column align-items-center  justify-content-center rounded overflow-hidden bg-white py-2">
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded  px-2">
                                <div class="info">
                                    <h4>Personal Info</h4>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="">
                                            @error('first_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="">
                                            @error('middle_initial')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror

                                        </div>
                                        <div class="input-field w-50">
                                            <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="">
                                            @error('last_name')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <!-- date of birth -->
                                        <div class="input-field w-50">
                                            <label for="birthdate">Date of Birth</label>
                                            <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- place of birth -->
                                        <div class="input-field w-50">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="20" class="form-control" name="place_of_birth" value="">
                                            @error('place_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>

                                        <!-- age -->
                                        <div class="input-field w-50">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" placeholder="20" class="form-control" name="age" value="">
                                            @error('age')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center p-2">
                                                @php
                                                $selectedSex = optional(Auth::user() -> staff) -> sex ?? optional(Auth::user() -> nurses) -> sex ?? 'none';
                                                @endphp
                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" class="mb-0" name="sex" value="" class="mb-0">Male</label>
                                                    <input type="radio" id="female" class="mb-0" name="sex" value="" class="mb-0">Female</label>
                                                </div>
                                                @error('sex')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- contact -->
                                        <div class="input-field w-50">
                                            <label for="contact_number" class="">Contact Number</label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="">
                                            @error('contact_number')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <div class="input-field w-50">
                                            <label for="nationality" class="">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="">
                                            @error('nationality')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2 d-flex gap-1">
                                        <div class="input-field w-50">
                                            <label for="dateOfRegistration">Date of Registration</label>
                                            <input type="date" id="dateOfRegistration" placeholder="20" class="form-control text-center w-100 px-5 " name="date_of_registration" value="">
                                            @error('date_of_birth')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                        <!-- administered by -->
                                        <div class="mb-2 w-50">
                                            <label for="brgy">Administered by*</label>
                                            <select name="brgy" id="brgy" class="form-select ">
                                                <option value="">Select a person</option>
                                            </select>
                                            @error('brgy')
                                            <small class="text-danger">{{$message}}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- mother name and father -->
                                    <div class="prenatal-inputs mb-2 d-flex flex-column gap-1">
                                        <div class="mb-2 w-100 d-flex gap-2">
                                            <div class="input-field w-50">
                                                <label for="motherName">Head of the Family</label>
                                                <input type="text" id="head_of_the_family" placeholder="Enter the Name" class="form-control" name="mother_name" value="">
                                                @error('mother_name')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror

                                            </div>
                                            <div class="input-field w-50">
                                                <label for="civil_status" class="">Civil Status</label>
                                                <select name="civil_status" id="civil_status" class="form-select">
                                                    <option value="Single">Single</option>
                                                    <option value="Married">Married</option>
                                                    <option value="Divorce">Divorce</option>
                                                </select>
                                                @error('civil_status')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                            <div class="input-field w-50">
                                                <label for="blood_type">Blood Type</label>
                                                <select name="blood_type" id="blood_type" class="form-select" required>
                                                    <option value="" disabled selected>Select Blood Type</option>
                                                    <option value="A+">A+</option>
                                                    <option value="A-">A-</option>
                                                    <option value="B+">B+</option>
                                                    <option value="B-">B-</option>
                                                    <option value="AB+">AB+</option>
                                                    <option value="AB-">AB-</option>
                                                    <option value="O+">O+</option>
                                                    <option value="O-">O-</option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="mb-3 w-100 d-flex gap-3">

                                            <!-- Religion -->
                                            <div class="input-field w-25">
                                                <label for="religion" class="form-label">Religion</label>
                                                <input type="text" id="religion" placeholder="Enter the Religion" class="form-control" name="religion" value="">
                                                @error('religion')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>

                                            <!-- PhilHealth -->
                                            <div class="input-field w-50">
                                                <label class="form-label">PhilHealth</label>
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="philhealth" id="philhealth_yes" value="yes">
                                                        <label class="form-check-label" for="philhealth_yes">(Yes)</label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <label class="form-label mb-0">Number:</label>
                                                        <input type="text" class="form-control form-control-sm" name="philhealth_number" style="width: 120px;">
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="philhealth" id="philhealth_no" value="no">
                                                        <label class="form-check-label" for="philhealth_no">(No)</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Family Planning -->
                                            <div class="input-field w-50">
                                                <label class="form-label fw-normal">Would you like to use a family planning method?</label>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="family_planning" id="planning_yes" value="yes">
                                                        <label class="form-check-label" for="planning_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="family_planning" id="planning_no" value="no">
                                                        <label class="form-check-label" for="planning_no">No</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="family_planning" id="planning_undecided" value="undecided">
                                                        <label class="form-check-label" for="planning_undecided">Undecided</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- address -->
                                    <div class="mb-2 d-flex gap-1 flex-column border-top border-bottom">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center">
                                            <div class=" mb-2 w-50">
                                                <label for="street">Street*</label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2" name="street" value="">
                                                @error('street')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="brgy">Barangay*</label>
                                                <select name="brgy" id="brgy" class="form-select py-2">
                                                    <option value="">Select a brgy</option>
                                                </select>
                                                @error('brgy')
                                                <small class="text-danger">{{$message}}</small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <!-- vital sign -->
                                    <div class="vital-sign w-100 border-bottom">
                                        <h5>Vital Sign</h5>
                                        <div class="mb-2 input-field d-flex gap-3 w-100 first-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Blood Pressure:</label>
                                                <input type="text" class="form-control w-100" placeholder="ex. 120/80">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Temperature:</label>
                                                <input type="number" class="form-control w-100" placeholder="00 C">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Pulse Rate(Bpm):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 60-100">
                                            </div>

                                        </div>
                                        <!-- 2nd row -->
                                        <div class="mb-2 input-field d-flex gap-3 w-100 second-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Respiratory Rate (breaths/min):</label>
                                                <input type="text" class="form-control w-100" placeholder="ex. 25">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="height">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Weight(kg):</label>
                                                <input type="number" class="form-control w-100" placeholder=" 00.00" name="weight">
                                            </div>
                                        </div>
                                        <!-- 3rd row -->
                                        <div class="mb-2 input-field d-none gap-3 w-100 third-row">
                                            <div class="mb-2 w-50">
                                                <label for="BP">Birth Height(cm):</label>
                                                <input type="number" class="form-control w-100" placeholder="00.00" name="height">
                                            </div>
                                            <div class="mb-2 w-50">
                                                <label for="BP">Birth Weight(kg):</label>
                                                <input type="text" class="form-control w-100" placeholder=" 00.00" name="weight">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="survey-questionare w-100 ">
                                        <div class="current-prenancy w-100 d-flex gap-3 mb-3 border-bottom">

                                            <div class="questions w-100">
                                                <h3 class="w-100 bg-success text-white text-center">Kasaysayan ng Pagbubuntis</h3>
                                                <div class="mb-4 d-flex">
                                                    <label for="number_of" class="w-100 fs-5" class="w-50">Bilang ng Pagbubuntis:</label>
                                                    <select name="number_if_children" id="number_of_children" class="form-select w-50 text-center">
                                                        <option value="" disabled selected>Select the number</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4+">4+</option>
                                                    </select>
                                                </div>
                                                <div class="mb-4 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Nanganak ng sasarin:</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter gap-3">
                                                        <input type="radio" id="yes" name="answer_1">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer_1">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 2nd -->
                                                <div class="mb-4 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">3 beses nakuhanan magkasunod:</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                                                        <input type="radio" id="yes" name="answer_2">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer_2">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 3rd -->
                                                <div class="mb-4 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Ipinanganak ng patay:</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                                                        <input type="radio" id="yes" name="answer_3">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer_3">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 4th -->
                                                <div class="mb-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Labis na pagdurogo matapos manganak:</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                                                        <input type="radio" id="yes" name="answer_4">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer_4">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- 2nd question-->
                                            <div class="questions w-100">
                                                <h3 class="w-100 bg-success text-white text-center">Kasalukuyang Problemang Pang Kalusugan</h3>
                                                <div class="mb-4 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Tuberculosis(ubong labis 14 araaw):</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                                                        <input type="radio" id="yes" name="answer">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 2nd -->
                                                <div class="mb-4 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Sakit sa Puso:</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                                                        <input type="radio" id="yes" name="answer">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 3rd -->
                                                <div class="mb-4 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Diabetis:</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                                                        <input type="radio" id="yes" name="answer">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 4th -->
                                                <div class="mb-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Hika:</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                                                        <input type="radio" id="yes" name="answer">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                                <!-- 4th -->
                                                <div class="mb-2 d-flex justify-content-between w-100">
                                                    <label for="sasarin" class="w-75">Bisyo:</label>
                                                    <div class="radio-input w-50 d-fles align-items-center justify-content-cetter">
                                                        <input type="radio" id="yes" name="answer">
                                                        <label for="yes">Oo</label>
                                                        <input type="radio" name="answer">
                                                        <label for="no">HIndi</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- save btn -->
                                    <div class="save-record d-flex justify-content-end  w-100">
                                        <input type="submit" class="btn btn-success px-4 fs-5" value="Save Record">
                                    </div>
                                </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>

    </div>
    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('record_prenatal');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif
</body>

</html>