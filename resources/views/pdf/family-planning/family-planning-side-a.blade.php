<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Family Planning Client Assessment Record</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-size: 11px;
            font-family: Arial, sans-serif;
            /* Reduce from default (usually 10-12px) */
            line-height: 1.2;
        }

        table {
            font-size: 12px;
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            /* Reduce cell padding */
            line-height: 1.2;
        }

        h3,
        h4,
        .section-title {
            font-size: 8px;
            margin: 3px 0;
        }

        /* Reduce spacing between sections */
        .section {
            margin-bottom: 3px;
        }

        .whole-table {
            border: 2px solid #000;
            width: 100%;
        }

        .boxes {
            display: flex;
            width: 100%;
        }

        .d-flex {
            display: flex;
        }

        .w-100 {
            width: 100%;
        }

        .w-50 {
            width: 50%;
        }

        .border-dark {
            border-color: #000;
        }

        .border-2 {
            border-width: 2px;
        }

        .border-r-2 {
            border-right: 2px solid #000;
        }

        .box {
            border: 1px solid #000;
            /* padding: 10px; */
        }

        .p-3 {
            padding: 2px;
        }

        .text-center {
            text-align: center;
        }

        p {
            font-size: 11px;
        }

        /* Add any other CSS from your modal here */
    </style>
</head>

<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js'])
    <div class="d-flex justify-content-between">
        <h4 class="fw-bold">SIDE A</h4>
        <h4 class="fw-bold">FP FORM 1</h4>
    </div>
    <div class="main-container">
        <div class="whole-table w-100 border-dark border-2">
            <div class="boxes">
                <div class="box w-100">
                    @include('pdf.family-planning.side-a-components.step1')
                </div>
            </div>
            <div class="boxes d-flex w-100 ">
                <div class="col  w-50 border-r-2 border-dark">
                    <div class="box w-100 ">
                        @include('pdf.family-planning.side-a-components.step2')
                    </div>
                    <div class="box w-100 ">
                        @include('pdf.family-planning.side-a-components.step3')
                    </div>
                    <div class="box w-100">
                        @include('pdf.family-planning.side-a-components.step4')
                    </div>
                    <div class="box w-100 p-3 ">
                        <p class="text-center">Implant=Progestin subdermal Implant,IUD= Intrauterine device, BTL= Bilateral tubal ligation, Nsy=No sceptal vasedomy,
                            COC= Combined ora; contraceptives, POP= Progestin only pills, LAM=Lactational amenorhes method, SOM= Standard days method,
                            ABT=Based body temperature, BOM= Billage ovulation method, CMMI= Cervical mucus method, STM= Symptothermal method
                        </p>
                    </div>
                </div>
                <div class="col w-50 ">
                    <div class="box w-100">
                        @include('pdf.family-planning.side-a-components.step5')
                    </div>
                    <div class="box w-100">
                        @include('pdf.family-planning.side-a-components.step6')
                    </div>
                </div>

            </div>

        </div>
    </div>
</body>

</html>