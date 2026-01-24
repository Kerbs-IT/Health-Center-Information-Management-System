<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccintion Case Record</title>
</head>
<style>
    * {
        font-family: 'Arial', sans-serif;
    }

    tr th {
        background-color: lightgray !important;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table tr,
    tr td,
    tr th {
        font-size: 15px;
        border: 1px solid black;
    }

    tr th {
        text-align: start;
    }

    tr th,
    tr td {
        padding: 10px 10px;
    }

    h4 {
        text-align: center;
    }

    header {
        width: 100%;
    }

    header img {
        height: 120px;
        width: 120px;
        float: left;
        margin-right: 20px;
        margin-top: 20px;
    }

    .header-text {
        width: 70%;
        text-align: center;
        text-transform: uppercase;
        float: left;
        padding-top: 20px;
        /* Adjust to vertically center text */
    }
</style>

<body>

    <header>
        <img src="{{public_path('images/hugoperez_logo.png')}}" alt="">
        <div class="header-text">
            <h4>Barangay Hugo Perez Proper -</h4>
            <h4>Health Center Information Management System</h4>
        </div>
        <div style="clear: both;"></div>
    </header>
    <h4 class="text-center fw-bold fs-2">VACCINATION RECORD</h4>
    <div class="">
        <div class="card-body">

            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Patient Name</th>
                        <td id="view-patient-name">{{$vaccinationCase->patient_name}}</td>
                    </tr>
                    <!-- <tr>
                    <th>Administered By</th>
                    <td>Nurse Joy</td>
                </tr> -->
                    <!-- <tr>
                    <th>Handled By</th>
                    <td id="view-handled-by">Nurse Joy</td>
                </tr> -->
                    <tr>
                        <th>Date of Vaccination</th>
                        <td id="view-date-of-vaccination">{{$vaccinationCase->date_of_vaccination}}</td> <!-- Replace with dynamic value -->
                    </tr>
                    <tr>
                        <th>Time of Vaccination</th>
                        <td id="view-time-of-vaccination">{{$vaccinationCase->time}}</td> <!-- Replace with dynamic value -->
                    </tr>
                    <tr>
                        <th>Height</th>
                        <td id="view-height">{{$vaccinationCase->height ?? '0'}} cm</td> <!-- Replace with dynamic value -->
                    </tr>
                    <tr>
                        <th>Weight</th>
                        <td id="view-weight">{{$vaccinationCase->weight?? '0'}} kg</td> <!-- Replace with dynamic value -->
                    </tr>
                    <tr>
                        <th>Temperature</th>
                        <td id="view-temperature">{{$vaccinationCase->temperature ?? '0'}} °C</td> <!-- Replace with dynamic value -->
                    </tr>
                    <tr>
                        <th>Vaccine Type</th>
                        <td id="view-vaccine-type">
                            {{$vaccinationCase->vaccine_type}}
                        </td>
                    </tr>
                    <tr>
                        <th>Vaccine Dose Number</th>
                        <td id="view-dose-number">{{
                            $vaccinationCase->dose_number == 1 ? '1st dose' :
                            ($vaccinationCase->dose_number == 2 ? '2nd dose' :
                            ($vaccinationCase->dose_number == 3 ? '3rd dose' : 'N/A'))
                        }}
                        </td>
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td id="view-case-remarks"> {{$vaccinationCase->remarks}}</td>
                    </tr>
                    <tr>
                        <th>Date of comeback</th>
                        <td id="view-date-of-comeback" class="bg-light"> {{$vaccinationCase->date_of_comeback}}</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

</body>

</html>