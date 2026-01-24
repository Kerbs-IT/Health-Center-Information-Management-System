<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TB-DOTS </title>
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
        .tb-dots-con {
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
        <img src="{{public_path('images/hugoperez_logo.png')}}" alt="hugo perez logo">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
    </header>
    <div class="tb-dots-con">
        <h5 class="fw-bold fs-4">Tuberculosis Patient Case Record</h5>
        <div class="patient-info-con clearfix">
            <div class="info-column">
                <div class="info-field">
                    <label class="fw-bold">NAME: </label>
                    <span class="text-decoration-underline">{{ $medicalRecord->patient->full_name ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">DATE OF BIRTH:</label>
                    <span class="text-decoration-underline">
                        {{
                            $medicalRecord->patient->date_of_birth
                                ? \Carbon\Carbon::parse($medicalRecord->patient->date_of_birth)
                                : 'N/A'
                        }}
                    </span>

                </div>
                <div class="info-field">
                    <label class="fw-bold">PLACE OF BIRTH: </label>
                    <span class="text-decoration-underline">{{$medicalRecord->patient->place_of_birth ?? 'N/A' }}</span>
                    <label class="fw-bold">RELIGION: </label>
                    <span class="text-decoration-underline">{{$medicalRecord->tb_dots_medical_record->religion ?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">COMPLETE ADDRESS: </label>
                    <span class="text-decoration-underline">{{ $address?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-column">
                <div class="info-field">
                    <label class="fw-bold">AGE: </label>
                    <span class="text-decoration-underline">{{ $medicalRecord->patient->age?? 'N/A' }}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">SEX:</label>
                    <span class="text-decoration-underline">{{ $medicalRecord->patient->sex?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">CIVIL STATUS:</label>
                    <span class="text-decoration-underline">{{$medicalRecord->patient->civil_status?? 'N/A'}}</span>
                </div>
                <div class="info-field">
                    <label class="fw-bold">PHILHEALTH ID NO:</label>
                    <span class="text-decoration-underline">{{ $medicalRecord->tb_Dots_medical_record->philhealth_id_no ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        <!-- content -->
        <div class="tb-dots card shadow p-4 w-100">
            <table class="table table-bordered">
                <tbody>

                    <tr>
                        <th>Type of Tuberculosis (TB)</th>
                        <td id="view_type_of_tuberculosis">{{$caseRecord->type_of_tuberculosis??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Type of TB Case</th>
                        <td id="view_type_of_tb_case">{{$caseRecord->type_of_tb_case??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Date of Diagnosis</th>
                        <td id="view_date_of_diagnosis">{{$caseRecord->date_of_diagnosis??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Name of Physician</th>
                        <td id="view_name_of_physician">{{$caseRecord->name_of_physician??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Sputum Test Results</th>
                        <td id="view_sputum_test_results">{{$caseRecord->sputum_test_results??'N/A'}}</td>
                    </tr>

                </tbody>
            </table>

            <h4 class="border-bottom mt-4">Medication List</h4>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="table-header">
                        <th>Medicine Name</th>
                        <th>Dosage & Frequency</th>
                        <th>Quality</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody id="view-table-body">
                    @forelse($caseRecord->tb_dots_maintenance_med as $maintenance)
                    <tr>
                        <td>{{$maintenance->medicine_name??''}}</td>
                        <td>{{$maintenance->dosage_n_frequency??''}}</td>
                        <td>{{$maintenance->quantity??''}}</td>
                        <td>{{$maintenance->start_date??''}}</td>
                        <td>{{$maintenance->end_date??''}}</td>
                    </tr>
                    @empty
                    <td colspan="12" class="text-center">No record is available</td>
                    @endforelse
                </tbody>
            </table>

            <table class="table table-bordered mt-4">
                <tbody>
                    <tr>
                        <th>Treatment Category</th>
                        <td id="view_treatment_category">{{$caseRecord->treatment_category??'N/A'}}</td>
                    </tr>
                    <!-- <tr>
                        <th>Assigned Health Worker</th>
                        <td id="view_assigned_health_worker">{{$healthWorker->full_name??'N/A'}}</td>
                    </tr> -->
                </tbody>
            </table>

            <h4 class="border-bottom mt-4">Monitoring & Progress</h4>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Date of Diagnosis</th>
                        <td id="view_date_administered">{{$caseRecord->date_administered??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Side Effects</th>
                        <td id="view_side_effect">{{$caseRecord->side_effect??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td id="view_remarks">{{$caseRecord->remarks??'N/A'}}</td>
                    </tr>
                    <tr>
                        <th>Outcome</th>
                        <td id="view_outcome">{{$caseRecord->outcome??'N/A'}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>