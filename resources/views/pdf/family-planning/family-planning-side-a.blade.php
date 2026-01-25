<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Family Planning Client Assessment Record</title>
    <style>
        
        body {
            margin: 0;
            padding: 0;
            font-size: 10px;
            font-family: Arial, sans-serif;
            line-height: 1.0;
        }

        table {
            font-size: 10px;
            border-collapse: collapse;
            width: 100%;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th,
        td {
            padding: 1px 2px;
            line-height: 1.0;
            border: 1px solid #000;
            vertical-align: top;
        }

        th {
            font-weight: bold;
            text-align: left;
        }

        h3,
        h4,
        .section-title {
            font-size: 10px;
            margin: 1px 0;
            font-weight: bold;
        }

        .section {
            margin-bottom: 1px;
        }

        .whole-table {
            border: 2px solid #000;
            width: 100%;
        }

        .boxes {
            display: block;
            width: 100%;
        }

        .d-flex {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .w-100 {
            width: 100%;
        }

        .w-50 {
            width: 50%;
            display: table-cell;
            vertical-align: top;
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
        }

        .p-3 {
            padding: 2px;
        }

        .text-center {
            text-align: center;
        }

        p {
            font-size: 10px;
            margin: 2px 0;
            line-height: 1.1;
        }

        /* Bootstrap table classes converted */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000;
            padding: 1px 2px;
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: transparent;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-start {
            text-align: left;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        .table-secondary {
            background-color: #e9ecef;
        }

        .seperator {
            background-color: #e9ecef;
            border-bottom: 2px solid #000;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-5 {
            margin-top: 5px;
        }

        .px-2 {
            padding-left: 4px;
            padding-right: 4px;
        }

        .px-3 {
            padding-left: 6px;
            padding-right: 6px;
        }

        .text-underline {
            text-decoration: underline;
        }

        .text-muted {
            color: #6c757d;
        }

        .ackknowledgement .border {
            border: 1px solid #000;
            min-height: 35px;
        }

        /* Scale content to fit A4 portrait */
        /* .main-container {
            transform: scale(1);
            transform-origin: top left;
            width: 108.7%;
        } */

        /* Remove extra spacing */
        .table tbody tr td {
            padding: 2px 2px;
            font-size: 10px;
        }

        .table thead th {
            padding: 1px 2px;
            font-size: 10px;
        }

        /* Compact header section */
        .justify-content-between {
            display: table;
            width: 100%;
        }

        .justify-content-between h4 {
            display: table-cell;
            width: 50%;
            margin: 0;
            padding: 1px 0;
        }

        .justify-content-between h4:last-child {
            text-align: right;
        }

        /* Reduce spacing in instruction section */
        .instruction-text {
            font-size: 10px;
            line-height: 1.1;
            margin: 0;
        }

        /* Compact acknowledgement section */
        .ack-signature-box {
            border: 1px solid #000;
            padding: 15px 5px;
            text-align: center;
            margin-bottom: 2px;
            font-size: 10px;
        }

        .ack-text {
            font-size: 10px;
            margin: 3px 0;
        }

        .ack-label {
            font-size: 10px;
            font-weight: bold;
            margin: 0 0 1px 0;
        }
    </style>
</head>

<body>

    <div class="justify-content-between">
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
                <div class="col w-50 border-r-2 border-dark">
                    <div class="box w-100 ">
                        @include('pdf.family-planning.side-a-components.step2')
                    </div>
                    <div class="box w-100 ">
                        @include('pdf.family-planning.side-a-components.step3')
                    </div>
                    <div class="box w-100">
                        @include('pdf.family-planning.side-a-components.step4')
                    </div>
                    <div class="box p-3 " style="width: 97%;">
                        <p class="text-center" style="font-size: 10px; line-height: 1;">Implant=Progestin subdermal Implant, IUD= Intrauterine device, BTL= Bilateral tubal ligation, Nsy=No sceptal vasedomy, COC= Combined oral contraceptives, POP= Progestin only pills, LAM=Lactational amenorrhea method, SOM= Standard days method, ABT=Based body temperature, BOM= Billage ovulation method, CMMI= Cervical mucus method, STM= Symptothermal method
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