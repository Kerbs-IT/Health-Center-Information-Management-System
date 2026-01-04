<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <h3 class="fw-bold text-center ">HEALTH CENTER INFORMATION MANAGEMENT SYSTEM</h3>
            <h5 class="fw-light text-center">Brgy.Hugo Perez,Proper</h5>
            <h3 class="fw-bold ">Patient Overall Count</h3>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>Type of Patient</th>
                            <th>Total Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Prenatal</td>
                            <td>{{$patientCount['prenatalCount']??'0'}}</td>
                        </tr>
                        <tr>
                            <td>Senior Citizen</td>
                            <td>{{$patientCount['seniorCitizenCount']??'0'}}</td>
                        </tr>
                        <tr>
                            <td>TB Dots</td>
                            <td>{{$patientCount['tbDotsCount']??'0'}}</td>
                        </tr>
                        <tr>
                            <td>Vaccination</td>
                            <td>{{$patientCount['vaccinationCount']??'0'}}</td>
                        </tr>
                        <tr>
                            <td>Family Planning</td>
                            <td>{{$patientCount['familyPlanningCount']??'0'}}</td>
                        </tr>
                        <tr class="table-secondary fw-bold">
                            <td>Overall Patient</td>
                            <td>{{$patientCount['overallPatients']??'0'}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- patient per day -->
        <div class="row justify-content-center">
            <h3 class="fw-bold ">Patient Added Today </h3>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>Type of Patient</th>
                            <th>Total Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Prenatal</td>
                            <td>{{$patientPerDay['prenatalCount']??'0'}}</td>
                        </tr>
                        <tr>
                            <td>Senior Citizen</td>
                            <td>{{$patientPerDay['seniorCitizenCount']??'0'}}</td>
                        </tr>
                        <tr>
                            <td>TB Dots</td>
                            <td>{{$patientPerDay['tbDotsCount']??'0'}}</td>
                        </tr>
                        <tr>
                            <td>Vaccination</td>
                            <td>{{$patientPerDay['vaccinationCount']??'0'}}</td>
                        </tr>
                        <tr>
                            <td>Family Planning</td>
                            <td>{{$patientPerDay['familyPlanningCount']??'0'}}</td>
                        </tr>
                        <tr class="table-secondary fw-bold">
                            <td>Overall Patient</td>
                            <td>{{$patientPerDay['overallPatients']??'0'}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-end mt-3">
            <small class="text-muted">Generated Date: {{ $generatedDate }}</small>
        </div>
        <!-- patient per area -->
        <div style="page-break-before: always;"></div>

        <div class="row justify-content-center">
            <h3 class="fw-bold ">Patient Per Area Count</h3>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>Purok/Area</th>
                            <th>Patient Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patientCountPerArea as $purok => $count)
                        <tr>
                            <td>{{ $purok }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-secondary fw-bold">
                            <td>Total</td>
                            <td>{{ array_sum($patientCountPerArea) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-end mt-3">
            <small class="text-muted">Generated Date: {{ $generatedDate }}</small>
        </div>
    </div>
</body>

</html>