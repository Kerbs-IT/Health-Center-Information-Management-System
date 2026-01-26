<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Citizen Case Record</title>

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            width: 100%;
            padding: 10px;
        }

        /* Header */
        header {
            width: 80%;
            margin: 0 auto 10px auto;
        }

        header img {
            height: 120px;
            width: 120px;
            float: left;
            margin-right: 10px;
        }

        .header-text {
            float: left;
            padding-top: 35px;
            width: calc(100% - 200px);
            text-align: center;
            text-transform: uppercase;
        }

        .header-text h4 {
            margin: 5px 0;
            font-size: 14px;
        }

        /* Main Container */
        .senior-citizen-con {
            width: 80%;
            margin: 0 auto;
            padding: 0px 10px;
        }

        h5 {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            text-transform: uppercase;
        }

        /* Patient Info Section */
        .patient-info-con {
            margin-bottom: 20px;
            width: 100%;
        }

        .info-column {
            width: 49%;
            float: left;
            vertical-align: top;
        }

        .info-column:first-child {
            margin-right: 2%;
        }

        .info-field {
            margin-bottom: 10px;
            font-size: 11px;
            line-height: 1.8;
        }

        .info-field label {
            font-weight: bold;
        }

        /* Tables */
        table {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }

        tr,
        td,
        th {
            border: 1px solid black;
            padding: 8px;
            font-size: 11px;
        }

        th {
            text-align: left;
            background-color: #d3d3d3;
            font-weight: bold;
        }

        td {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .fw-bold {
            font-weight: bold;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .bg-light {
            background-color: #d3d3d3;
        }

        .bg-white {
            background-color: #ffffff;
        }

        .w-25 {
            width: 25%;
        }

        .w-75 {
            width: 75%;
        }

        /* Clear floats */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .text-decoration-underline {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header class="clearfix">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/hugoperez_logo.png'))) }}" alt="hugo perez logo">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
    </header>

    <div class="senior-citizen-con">
        <h5>SENIOR CITIZEN CASE RECORD</h5>

        <div class="patient-info-con clearfix">
            <div class="info-column">
                <div class="info-field">
                    <label>NAME: </label>
                    <span class="text-decoration-underline">{{ $medicalRecord->patient->full_name ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>DATE OF BIRTH: </label>
                    <span class="text-decoration-underline">
                        {{
                            $medicalRecord->patient->date_of_birth
                                ? \Carbon\Carbon::parse($medicalRecord->patient->date_of_birth)->format('M d, Y')
                                : 'N/A'
                        }}
                    </span>
                </div>
                <div class="info-field">
                    <label>PLACE OF BIRTH: </label>
                    <span class="text-decoration-underline">{{$medicalRecord->patient->place_of_birth ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>RELIGION: </label>
                    <span class="text-decoration-underline">{{$medicalRecord->senior_citizen_medical_record->religion ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>COMPLETE ADDRESS: </label>
                    <span class="text-decoration-underline">{{ $address ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="info-column">
                <div class="info-field">
                    <label>AGE: </label>
                    <span class="text-decoration-underline">{{ $medicalRecord->patient->age ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label>SEX: </label>
                    <span class="text-decoration-underline">{{ $medicalRecord->patient->sex ?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label>CIVIL STATUS: </label>
                    <span class="text-decoration-underline">{{$medicalRecord->patient->civil_status ?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label>SSS MEMBER: </label>
                    <span class="text-decoration-underline">{{ $medicalRecord->senior_citizen_medical_record->SSS ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="contents">
            <table>
                <tbody>
                    <!-- Section Header -->
                    <tr>
                        <th colspan="7" class="text-uppercase fw-bold bg-light" style="background-color: light gray; text-align:center">
                            Medical Information
                        </th>
                    </tr>

                    <!-- Existing Medical Condition -->
                    <tr>
                        <td colspan="2" class="w-25 fw-semibold bg-light" style="background-color: light gray; text-align:center">Existing Medical Condition:</td>
                        <td colspan="5" class="w-75 bg-white text-center">{{$seniorCaseRecord->existing_medical_condition ?? 'None'}}</td>
                    </tr>

                    <!-- Allergies -->
                    <tr>
                        <td colspan="2" class="w-25 fw-semibold bg-light" style="background-color: light gray; text-align:center">Allergies:</td>
                        <td colspan="5" class="w-75 bg-white text-center">{{$seniorCaseRecord->alergies ?? 'None'}}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Maintenance Medication Table -->
            <table>
                <thead>
                    <tr>
                        <th>Maintenance Medication</th>
                        <th>Dosage & Frequency</th>
                        <th>Duration</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($seniorCaseRecord->senior_citizen_maintenance_med as $record)
                    <tr>
                        <td>{{$record->maintenance_medication ?? ''}}</td>
                        <td>{{$record->dosage_n_frequency ?? ''}}</td>
                        <td>{{$record->quantity ?? ''}}</td>
                        <td>{{$record->start_date ?? ''}}</td>
                        <td>{{$record->end_date ?? ''}}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No record available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Prescriber and Remarks -->
            <table>
                <tbody>
                    @php
                    use App\Models\User; // or whatever your User model namespace is
                    use App\Models\nurses; // adjust to your actual namespace

                    $nurse = User::where('role','nurse')->first();
                    $nurseInfo = nurses::where("user_id",$nurse->id)->first();

                    $middle = substr($nurseInfo ->middle_initial ?? '', 0, 1);
                    $middle = $middle ? strtoupper($middle) . '.' : null;
                    $parts = [
                    $nurseInfo->first_name,
                    $middle,
                    $nurseInfo->last_name

                    ];
                    $fullName = ucwords(trim(implode(' ', array_filter($parts))));
                    @endphp
                    <tr>
                        <th colspan="2" class="w-25 fw-semibold bg-light" style="background-color: light gray; text-align:center">Prescribe by:</th>
                        <td colspan="5" class="w-75 bg-white">{{ $seniorCaseRecord->prescribe_by_nurse ?? 'Nurse ' . $fullName }}</td>
                    </tr>
                    <tr>
                        <th colspan="2" class="w-25 fw-semibold bg-light" style="background-color: light gray; text-align:center">Remarks:</th>
                        <td colspan="5" class="w-75 bg-white">{{$seniorCaseRecord->remarks ?? 'None'}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>