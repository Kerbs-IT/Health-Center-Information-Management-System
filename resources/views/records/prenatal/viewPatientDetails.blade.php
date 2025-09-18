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
            <div class="d-flex flex-grow-1 flex-column overflow-y-auto">
                <main class="flex-column p-2 ">
                    <div class="top-part d-flex justify-content-between px-2 align-items-center">
                        <h2>View Patient Details</h2>
                        <div class="sequence-links d-flex justify-content-center align-items-center">
                            <h5 class="mb-0 text-muted cursor-pointer fw-normal">Records</h5>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-muted">Prenatal</a>
                            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
                            </svg>
                            <a href="#" class="text-decoration-none fs-5 text-black">View Patient</a>
                        </div>
                    </div>
                    <!-- main content -->
                    <div class="flex-grow-1 py-3 px-5">
                        <a href="{{route('records.prenatal')}}" class="btn btn-danger px-4 fs-5 mb-3">Back</a>
                        <!-- patient Info -->
                        <div class="info bg-white rounded overflow-hidden">
                            <div class="patient-info">
                                <h2 class="fs-3 fw-normal patient-info-header p-3 text-center">Patient's Information Details</h2>
                            </div>
                            <!-- basic info header -->
                            <h3 class="text-start mb-3 fs-2 w-100">Basic Information</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <!-- first row -->
                                    <!-- <div>{{$prenatalRecord}}</div> -->
                                    <tr>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Head of the Family:</h6> <span class="fw-light">{{optional($prenatalRecord)->prenatal_medical_record?->family_head_name??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Family Serial No:</h6> <span class="fw-light">{{optional($prenatalRecord)->prenatal_medical_record?->family_serial_no??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Plan to have Family Planning method:</h6><span>{{optional($prenatalRecord)->prenatal_medical_record?->family_planning_decision??'N/A'}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 2nd row -->
                                    <tr>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Name of Patient:</h6> <span class="fw-light">{{optional($prenatalRecord)->patient?->full_name??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Sex:</h6> <span class="fw-light">{{optional($prenatalRecord)->patient?->sex??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Age:</h6> <span class="fw-light">{{optional($prenatalRecord)->patient?->age??'N/A'}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 3rd row -->
                                    <tr>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">BirthDay:</h6> <span class="fw-light">{{optional($prenatalRecord)->patient?->date_of_birth->format('M d Y')??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Blood Type:</h6> <span class="fw-light">{{optional($prenatalRecord)->prenatal_medical_record?->blood_type??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Nationality:</h6> <span class="fw-light">{{optional($prenatalRecord)->patient?->nationality??'N/A'}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 4th -->
                                    <tr>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Contact No.:</h6> <span class="fw-light">{{optional($prenatalRecord)->patient?->contact_number??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Religion:</h6> <span class="fw-light">{{optional($prenatalRecord)->prenatal_medical_record?->religion??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Civil Status:</h6> <span class="fw-light">{{optional($prenatalRecord)->patient?->civil_status??'N/A'}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 class="text-start mb-3 fs-2 w-100">Address</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="p-4 text-start">Blk 25 lot 11, Green Forbes, Hugo Perez, Trece Martires City, Cavite, Philippines</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- vital header  -->
                            <h3 class="text-start mb-3 fs-2 w-100">Vital Sign</h3>
                            <table class="table table-bordered table-light">
                                <tbody>
                                    <tr>
                                        <td colspan="7">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Date:</h6> <span>{{optional($prenatalRecord)->prenatal_case_record?->first()->created_at->format('M d Y')}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 2nd row -->
                                    <tr>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Height(cm):</h6> <span>{{optional($prenatalRecord)->prenatal_case_record?->first()->height}} cm</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Weight(kg):</h6> <span>{{optional($prenatalRecord)->prenatal_case_record?->first()->weight}} kg</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Blood Pressure:</h6> <span class="fw-light">{{optional($prenatalRecord)->prenatal_case_record?->first()->blood_pressure??'N/A'}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- 3rd -->
                                    <tr>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Temperature(C):</h6> <span>{{optional($prenatalRecord)->prenatal_case_record?->first()->temperature??'N/A'}}°C</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Respiratory Rate(breaths/min):</h6> <span>{{optional($prenatalRecord)->prenatal_case_record?->first()->respiratory_rate??'N/A'}}</span>
                                            </div>
                                        </td>
                                        <td class="w-25 ">
                                            <div class="info d-flex gap-2 align-items-center">
                                                <h6 class="mb-0">Pulse Rate:</h6> <span class="fw-light">{{optional($prenatalRecord)->prenatal_case_record?->first()->pulse_rate?? 'N/A'}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <h3 class="text-start mb-3 fs-2 w-100">History</h3>
                            <div class="history-con d-flex w-100 gap-2">
                                <!-- table 1 -->
                                <table class="w-50 table table-bordered table-light">
                                    <thead>
                                        <tr class="border-bottom table-header">
                                            <th colspan="7">Kasaysayan ng Pagbubuntis</th>
                                        </tr>
                                        <tr class="border-bottom">
                                            <th class="w-75"></th>
                                            <th>Oo</th>
                                            <th>Hindi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>

                                            <td class="text-start">Nanganak ng sasarin:</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->answer_1 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->answer_1 == 'no' ? '✔' : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">3 beses nakuhanan magkasuyesd:</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->answer_2 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->answer_2 == 'no' ? '✔' : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Ipinanganak ng patay:</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->answer_3 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->answer_3 == 'no' ? '✔' : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Labis na pagdurogo matapos manganak:</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->answer_4 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->answer_4 == 'no' ? '✔' : '' }}</td>
                                        </tr>

                                        <tr class="bg-light">
                                            <td class="text-start">Bilang ng nakaraang pagbubuntis</td>
                                            <td colspan="2">{{optional($prenatalRecord)->prenatal_case_record?->first()->pregnancy_history_questions[0]->number_of_children??'0'}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- table 2 -->
                                <table class="w-50 table table-bordered table-light">
                                    <thead>
                                        <tr class="border-bottom table-header">
                                            <th colspan="7">Kasalukuyang Problemang Pangkalusugan</th>
                                        </tr>
                                        <tr class="border-bottom">
                                            <th class="w-75"></th>
                                            <th>Oo</th>
                                            <th>Hindi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-start">Tuberculosis(ubong labis sa 14 na araw):</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer2 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer2 == 'no' ?'✔' : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">sakit sa Puso:</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer2 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer2 == 'no' ? '✔' : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Diabetes:</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer3 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer3 == 'no' ? '✔' : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Hika:</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer4 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer4 == 'no' ? '✔' : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start">Bisyo:</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer5 == 'yes'?"✔":""}}</td>
                                            <td class="text-black">{{ optional($prenatalCaseRecord->pregnancy_history_questions->first())->q2_answer5 == 'no' ? '✔' : '' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- table 3 -->

                            </div>
                            <table class="table table-bordered">
                                <tr>
                                    <td class="fw-bold bg-light">Hatol sa Pasyente:</td>
                                    <td id="decision_con">
                                        @php
                                        $decision = optional($prenatalRecord)->prenatal_case_record->first()->decision ?? '';
                                        @endphp

                                        @if ($decision == 1)
                                        Papuntahin sa Doktor/RHU Alamin? Sundan ang kalagayan
                                        @elseif ($decision == 2)
                                        Masusing pagsusuri at aksyon ng kumadrona / Nurse
                                        @elseif ($decision == 3)
                                        Ipinayong manganak sa Ospital
                                        @else
                                        {{-- optional: show nothing or a default --}}
                                        @endif
                                    </td>

                                </tr>
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