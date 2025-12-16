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
    @endif

    <div class="vaccination vh-100 d-flex">
        <aside>
            @include('layout.menuBar')
        </aside>
        <div class="d-flex flex-grow-1 flex-column">
            @include('layout.header')
            <main class="flex-column p-3 overflow-y-auto">
                <h1>PATIENT CASES</h1>
                <!-- body part -->
                <div class="mb-3 w-100 px-5 h-[700px] record-con">
                    <!-- <a href="{{route('all.record')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a> -->
                    <div class="filters d-flex justify-content-between">
                        <div class="mb-3 w-25 ">
                            <small>Show Entries</small>
                            <input type="number" class="form-control bg-light" value="10">
                        </div>
                        <div class="mb-3 w-25">
                            <small>Search</small>
                            <input type="text" class="form-control bg-light" value="Search the record num">
                        </div>
                        <div class="mb-3 w-25">
                            <small>Filter</small>
                            <select name="filter_option" id="" class="form-select bg-light">
                                <option value="" disabled selected>Filter by date</option>
                                <option value="">0-10 weeks</option>
                            </select>
                        </div>
                    </div>
                    <div class="tables">
                        @if(isset($typeOfPatient) && $typeOfPatient == 'vaccination')
                        <table class="w-100 table">
                            <thead class="table-header">
                                <tr>
                                    <th>Patient No.</th>
                                    <th>Service Type</th>
                                    <th>Type of Record</th>
                                    <th>Date Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vaccination_case_record ?? [] as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>Vaccination</td>
                                    <td>Case Record</td>
                                    <td>{{ $record->created_at?->format('Y-m-d') ?? 'N/A' }}</td>
                                    <td>
                                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewVaccinationRecordModal" class="view-case-info" data-bs-case-id="{{$record->id}}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No vaccination records found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @elseif(!isset($typeOfPatient))
                        <div class="alert alert-info text-center">
                            <p>No patient records available</p>
                        </div>
                        @endif
                    </div>
                </div>

            </main>
        </div>
    </div>
    <!-- Vaccination view -->
    <div class="modal fade" id="viewVaccinationRecordModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="vaccinationModalLabel">Vaccination Details</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                </div>

                <div class="modal-body">
                    @include('records.vaccination.viewComponent.viewCase')
                </div>
            </div>
        </div>
    </div>

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