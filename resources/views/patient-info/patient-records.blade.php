<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body class="bg-white">
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/profile.css',
    'resources/js/patient/add-patient.js',
    'resources/css/patient/record.css',
    'resources/js/record/record.js'])

    @if(isset($typeOfPatient) && $typeOfPatient == 'vaccination')
    @vite('resources/js/patient-case-info/vaccination_case.js')
    @elseif(isset($typeOfPatient) && $typeOfPatient == 'prenatal')
    @vite('resources/js/patient-case-info/prenatal_case.js')
    @vite('resources/js/patient-case-info/family_plan_side_a.js')
    @vite('resources/js/patient-case-info/family_plan_side_b.js')
    @elseif(isset($typeOfPatient) && $typeOfPatient == 'senior-citizen')
    @vite('resources/js/patient-case-info/senior_citizen_case.js')
    @elseif(isset($typeOfPatient) && $typeOfPatient == 'tb-dots')
    @vite('resources/js/patient-case-info/tb_dots_case.js')
    @elseif(isset($typeOfPatient) && $typeOfPatient == 'family-planning')
    @vite('resources/js/patient-case-info/family_plan_side_a.js')
    @vite('resources/js/patient-case-info/family_plan_side_b.js')
    @else
    @endif

    <div class="vaccination vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column overflow-x-auto">
            @include('layout.header')
            <main class="flex-column p-md-3 p-2 overflow-y-auto">
                <h1>PATIENT CASES</h1>
                <!-- body part -->
                <div class="mb-3 w-100 px-lg-5 px-md-3 px-2 min-h-[700px] record-con">
                    <!-- <a href="{{route('all.record')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a> -->
                    <div class="filters d-flex justify-content-between w-100">
                        @if(($typeOfPatient ?? null) === 'vaccination')
                        @include('patient-info.filters.vaccination')
                        @elseif(($typeOfPatient ?? null) === 'prenatal')
                        @include('patient-info.filters.prenatal')
                        @elseif(($typeOfPatient ?? null) === 'tb-dots')
                        @include('patient-info.filters.tb-dots')
                        @elseif(($typeOfPatient ?? null) === 'senior-citizen')
                        @include('patient-info.filters.senior-citizen')
                        @elseif(($typeOfPatient ?? null) === 'family-planning')
                        @include('patient-info.filters.family-planning')
                        @else
                        @endif
                    </div>
                    <div class="tables">
                        @if(($typeOfPatient ?? null) === 'vaccination')
                        @include('patient-info.partials.vaccination-table')
                        @elseif(($typeOfPatient ?? null) === 'prenatal')
                        @include('patient-info.partials.prenatal-table')
                        @elseif(($typeOfPatient ?? null) === 'tb-dots')
                        @include('patient-info.partials.tb-dots-table')
                        @elseif(($typeOfPatient ?? null) === 'senior-citizen')
                        @include('patient-info.partials.senior-citizen-table')
                        @elseif(($typeOfPatient ?? null) === 'family-planning')
                        @include('patient-info.partials.family-planning-table')
                        @else
                        <div class="alert alert-info text-center">
                            <p>{{ $message ?? 'No patient records available' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

            </main>
        </div>
    </div>
    <!-- Vaccination view -->
    @if(($typeOfPatient ?? null) === 'vaccination')
    @include('patient-info.modals.vaccination.vaccination-case-modal')
    @elseif(($typeOfPatient ?? null) === 'prenatal')
    @include('patient-info.modals.prenatal.prenatal-case-modal')
    @include('patient-info.modals.prenatal.pregnancy-plan-modal')
    @include('patient-info.modals.prenatal.prenatal-checkup-modal')
    @include('patient-info.modals.family-planning.side-a-modal')
    @include('patient-info.modals.family-planning.side-b-modal')
    @elseif(($typeOfPatient ?? null) === 'senior-citizen')
    @include('patient-info.modals.senior-citizen.senior-citizen-case-modal')
    @elseif(($typeOfPatient ?? null) === 'tb-dots')
    @include('patient-info.modals.tb-dots.tb-dots-case-modal')
    @elseif(($typeOfPatient ?? null) === 'family-planning')
    @include('patient-info.modals.family-planning.side-a-modal')
    @include('patient-info.modals.family-planning.side-b-modal')
    @else
    @endif

    @if(isset($typeOfPatient) && $typeOfPatient == 'vaccination')

    @endif

    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const con = document.getElementById('patient_medical_record');

            if (con) {
                con.classList.add('active');
            }
        })
    </script>
    @endif

</body>

</html>