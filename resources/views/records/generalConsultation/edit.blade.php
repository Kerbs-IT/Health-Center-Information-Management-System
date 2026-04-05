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
    'resources/js/general_consultation/editPatientDetails.js',
    'resources/js/brgy_sync/brgy-&-healthWorker-sync.js'])
    <div class="patient-details vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <div class="d-flex flex-grow-1 flex-column overflow-auto">
                <main class="flex-column p-2">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center border-bottom mx-md-3 mx-2">
                        <h2>Update Patient Details</h2>
                        <div class="sequence-links d-md-flex d-none justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512">
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="{{ route('record.general.consultation') }}" class="text-decoration-none fs-5 text-muted">General Consultation</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512">
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="/patient-record/general-consultation/edit-details/{{ $gcRecord->id }}" class="text-decoration-none fs-5 text-black">Edit Patient Info</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-lg-5 mx-md-3 mx-2 px-2 shadow-lg">
                        @php
                        $backUrl = route('record.general.consultation') . '?' . http_build_query(request()->only(['patient_id', 'search', 'entries', 'sortField', 'sortDirection']));
                        @endphp

                        <a href="{{ $backUrl }}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>

                        <form action="" method="post" class="d-flex flex-column align-items-center justify-content-center rounded overflow-hidden bg-white py-2" id="edit-general-consultation-form">
                            @method('PUT')
                            @csrf
                            <div class="step d-flex flex-column w-100 rounded px-2">
                                <div class="info">
                                    <div class="bg-light border-start border-primary px-3 py-2 mb-4 rounded w-100">
                                        <span class="fs-6">
                                            <strong>Note:</strong>
                                            <span class="text-danger">*</span>
                                            <span class="fw-light"> indicates a required field.</span>
                                        </span>
                                    </div>

                                    <h4>Personal Info</h4>
                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="first_name">First Name<span class="text-danger">*</span></label>
                                            <input type="text" id="first_name" placeholder="Enter First Name" class="form-control" name="first_name" value="{{ optional($gcRecord->patient)->first_name ?? '' }}">
                                            <small class="text-danger error-text" id="first_name_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="middle_initial">Middle Name</label>
                                            <input type="text" id="middle_initial" placeholder="Enter Middle Name" class="form-control" name="middle_initial" value="{{ optional($gcRecord->patient)->middle_initial ?? '' }}">
                                            <small class="text-danger error-text" id="middle_initial_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                            <input type="text" id="last_name" placeholder="Enter Last Name" class="form-control" name="last_name" value="{{ optional($gcRecord->patient)->last_name ?? '' }}">
                                            <small class="text-danger error-text" id="last_name_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="suffix">Suffix</label>
                                            <select name="suffix" id="suffix" class="form-select py-2">
                                                <option value="" disabled {{ !optional($gcRecord)->patient?->suffix ? 'selected' : '' }}>Select Suffix</option>
                                                <option value="Jr." {{ optional($gcRecord)->patient?->suffix == 'Jr.' ? 'selected' : '' }}>Jr</option>
                                                <option value="Sr." {{ optional($gcRecord)->patient?->suffix == 'Sr.' ? 'selected' : '' }}>Sr</option>
                                                <option value="II." {{ optional($gcRecord)->patient?->suffix == 'II.' ? 'selected' : '' }}>II</option>
                                                <option value="III." {{ optional($gcRecord)->patient?->suffix == 'III.' ? 'selected' : '' }}>III</option>
                                                <option value="IV." {{ optional($gcRecord)->patient?->suffix == 'IV.' ? 'selected' : '' }}>IV</option>
                                                <option value="V." {{ optional($gcRecord)->patient?->suffix == 'V.' ? 'selected' : '' }}>V</option>
                                            </select>
                                            <small class="text-danger" id="suffix_error"></small>
                                        </div>
                                    </div>

                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="birthdate">Date of Birth<span class="text-danger">*</span></label>
                                            <input type="date" id="birthdate" class="form-control w-100 px-5" min="1950-01-01" max="{{ date('Y-m-d') }}" name="date_of_birth" value="{{ optional($gcRecord->patient)->date_of_birth?->format('Y-m-d') ?? '' }}">
                                            <small class="text-danger error-text" id="date_of_birth_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="place_of_birth">Place of Birth</label>
                                            <input type="text" id="place_of_birth" placeholder="Enter your place of birth" class="form-control" name="place_of_birth" value="{{ optional($gcRecord->patient)->place_of_birth ?? '' }}">
                                            <small class="text-danger error-text" id="place_of_birth_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="age">Age</label>
                                            <input type="number" id="age" class="form-control" disabled value="{{ optional($gcRecord->patient)->age ?? '' }}">
                                            <input type="hidden" id="hiddenAge" name="age" value="{{ optional($gcRecord->patient)->age ?? '' }}">
                                            <small class="text-danger error-text" id="age_error"></small>
                                        </div>
                                    </div>

                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="sex">Sex</label>
                                            <div class="input-field d-flex align-items-center justify-content-center flex-column p-2">
                                                <div class="sex-input d-flex align-items-center justify-content-center w-100 gap-1">
                                                    <input type="radio" id="male" name="sex" value="Male" {{ optional($gcRecord->patient)->sex == 'Male' ? 'checked' : '' }}>
                                                    <label for="male">Male</label>
                                                    <input type="radio" id="female" name="sex" value="Female" {{ optional($gcRecord->patient)->sex == 'Female' ? 'checked' : '' }}>
                                                    <label for="female">Female</label>
                                                </div>
                                                <small class="text-danger error-text" id="sex_error"></small>
                                            </div>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="contact_number">Contact Number<span class="text-danger">*</span></label>
                                            <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="{{ optional($gcRecord->patient)->contact_number ?? '' }}">
                                            <small class="text-danger error-text" id="contact_number_error"></small>
                                        </div>
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="nationality">Nationality</label>
                                            <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="{{ optional($gcRecord->patient)->nationality ?? '' }}">
                                            <small class="text-danger error-text" id="nationality_error"></small>
                                        </div>
                                    </div>

                                    <div class="mb-2 d-flex gap-1 flex-wrap flex-md-nowrap">
                                        <div class="input-field w-full md:w-[50%]">
                                            <label for="date_of_registration">Date of Registration</label>
                                            <input type="date" id="date_of_registration" class="form-control text-center w-100 px-5" name="date_of_registration" min="1950-01-01" max="{{ date('Y-m-d') }}" value="{{ optional($gcRecord)->date_of_registration?->format('Y-m-d') ?? '' }}">
                                            <small class="text-danger error-text" id="date_of_registration_error"></small>
                                        </div>
                                        <div class="mb-2 w-full md:w-[50%]">
                                            <label for="handled_by">Administered by<span class="text-danger">*</span></label>
                                            <select name="handled_by" id="handled_by" class="form-select"
                                                data-bs-health-worker-id="{{ optional($gcRecord->gc_medical_record)->health_worker_id ?? '' }}"
                                                data-staff-id="{{ Auth::user()->role == 'staff' ? Auth::user()->id : null }}">
                                                <option value="">Select a person</option>
                                            </select>

                                            <small class="text-danger error-text" id="handled_by_error"></small>
                                        </div>
                                    </div>

                                    <div class="mb-2 d-flex gap-1 flex-xl-nowrap flex-wrap">
                                        <div class="input-field flex-fill xl:w-[50%]">
                                            <label for="civil_status">Civil Status</label>
                                            <select name="civil_status" id="civil_status" class="form-select">
                                                <option value="Single" {{ optional($gcRecord->patient)->civil_status == 'Single' ? 'selected' : '' }}>Single</option>
                                                <option value="Married" {{ optional($gcRecord->patient)->civil_status == 'Married' ? 'selected' : '' }}>Married</option>
                                                <option value="Divorce" {{ optional($gcRecord->patient)->civil_status == 'Divorce' ? 'selected' : '' }}>Divorce</option>
                                            </select>
                                            <small class="text-danger error-text" id="civil_status_error"></small>
                                        </div>
                                    </div>

                                    <!-- Address -->
                                    <div class="mb-2 d-flex gap-1 flex-column">
                                        <h4>Address</h4>
                                        <div class="input-field d-flex gap-2 align-items-center flex-wrap flex-md-nowrap">
                                            <div class="mb-2 w-full md:w-[50%]">
                                                <label for="street">Street<span class="text-danger">*</span></label>
                                                <input type="text" id="street" placeholder="Blk & Lot n Street" class="form-control py-2" name="street" value="{{ trim($address->house_number . ' ' . optional($address->street)->name) }}">
                                                <small class="text-danger error-text" id="street_error"></small>
                                            </div>
                                            <div class="mb-2 w-full md:w-[50%]">
                                                <label for="brgy">Purok<span class="text-danger">*</span></label>
                                                <select name="brgy" id="brgy" class="form-select py-2"
                                                    data-bs-selected-brgy="{{ $address->purok }}"
                                                    data-health-worker-assigned-area-id="{{ optional(Auth::user())->staff?->assigned_area_id }}">
                                                    <option value="" disabled>Select a brgy</option>
                                                </select>
                                                <small class="text-danger error-text" id="brgy_error"></small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- save btn -->
                                <div class="save-record align-self-end mt-5">
                                    <button type="button" class="btn btn-success px-4 fs-5" id="edit-save-btn" data-bs-medical-id="{{ $gcRecord->id }}">
                                        Save Record
                                    </button>
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
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('record_general_consultation');
            if (con) con.classList.add('active');
        })
    </script>
    @endif
</body>

</html>