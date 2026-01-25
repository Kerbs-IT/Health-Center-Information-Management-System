<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            margin: 20px;
        }

        h2,
        h4 {
            margin: 10px 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            /* This ensures width percentages are respected */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 10px;
            word-wrap: break-word;
            /* Allow text to wrap */
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }

        .signature-section {
            margin-top: 30px;
        }
    </style>
</head>

<body>

    @php
    $recordsPerPage = 15;
    $chunks = $vaccinationMasterlist->chunk($recordsPerPage);
    $totalPages = $chunks->count();

    if ($totalPages === 0) {
    $chunks = collect([collect(array_fill(0, 15, null))]);
    $totalPages = 1;
    }
    @endphp

    @foreach($chunks as $pageIndex => $pageRecords)
    <div @if($pageIndex < $totalPages - 1) class="page-break" @endif>

        <h2>MASTER LIST OF {{ $selectedRange }}</h2>

        <table style="margin-bottom: 10px; border: none;">
            <tr>
                <td style="width: 50%; text-align: center; border: none;">
                    <h4 style="margin: 0;font-size:20px">Name of Barangay: <span style="font-weight: 300; text-decoration: underline;">
                            @if(Auth::user()->role == 'staff')
                            {{$assignedArea}}
                            @else
                            {{$selectedBrgy == ''?'All Barangays':$selectedBrgy }}
                            @endif
                        </span></h4>
                </td>
                <td style="width: 50%; text-align: center; border: none;">
                    <h4 style="margin: 0;font-size:20px">Name of Midwife: <span style="font-weight: 300; text-decoration: underline;">{{ $midwifeName ?? 'N/A'}}</span></h4>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <th width="12%">Name of Child</th>
                <th width="15%">Address</th>
                <th width="3%">Sex</th>
                <th width="3%">Age</th>
                <th width="5%">Date of Birth</th>
                <th width="4%">SE status</th>
                <th width="3%">BCG</th>
                <th width="3%">HEPA B</th>
                <th width="3%">PENTA 1</th>
                <th width="3%">PENTA 2</th>
                <th width="3%">PENTA 3</th>
                <th width="3%">OPV 1</th>
                <th width="3%">OPV 2</th>
                <th width="3%">OPV 3</th>
                <th width="3%">PCV 1</th>
                <th width="3%">PCV 2</th>
                <th width="3%">PCV 3</th>
                <th width="3%">IPV 1</th>
                <th width="3%">IPV 2</th>
                <th width="3%">MCV 1</th>
                <th width="3%">MCV 2</th>
                <th width="5%">Remarks</th>
            </tr>
            @if($pageRecords->count() > 0)
            @foreach($pageRecords as $masterlist)
            <tr>
                <td style="text-align: left; padding-left: 3px;">{{optional($masterlist)->name_of_child??''}}</td>
                <td style="font-size: 9px; text-align: left; padding-left: 3px;">{{optional($masterlist)->Address ?? ''}}</td>
                <td>{{optional($masterlist)->sex ?? ''}}</td>
                <td>{{optional($masterlist)->age_display ?? ''}}</td>
                <td style="font-size: 9px;">{{optional($masterlist)->date_of_birth?->format('Y-m-d') ?? ''}}</td>
                <td style="font-size: 9px;">{{optional($masterlist)->SE_status??''}}</td>
                <td>{{optional($masterlist)->BCG??''}}</td>
                <td>{{optional($masterlist)->{'Hepatitis B'}??''}}</td>
                <td>{{optional($masterlist)->PENTA_1??''}}</td>
                <td>{{optional($masterlist)->PENTA_2??''}}</td>
                <td>{{optional($masterlist)->PENTA_3??''}}</td>
                <td>{{optional($masterlist)->OPV_1??''}}</td>
                <td>{{optional($masterlist)->OPV_2??''}}</td>
                <td>{{optional($masterlist)->OPV_3??''}}</td>
                <td>{{optional($masterlist)->PCV_1??''}}</td>
                <td>{{optional($masterlist)->PCV_2??''}}</td>
                <td>{{optional($masterlist)->PCV_3??''}}</td>
                <td>{{optional($masterlist)->IPV_1??''}}</td>
                <td>{{optional($masterlist)->IPV_2??''}}</td>
                <td>{{optional($masterlist)->MCV_1??''}}</td>
                <td>{{optional($masterlist)->MCV_2??''}}</td>
                <td style="font-size: 9px;">{{optional($masterlist)->remarks??''}}</td>
            </tr>
            @endforeach

            @php
            $currentCount = $pageRecords->count();
            $emptyRowsNeeded = $recordsPerPage - $currentCount;
            @endphp

            @for($i = 0; $i < $emptyRowsNeeded; $i++)
                <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>
                @endfor
                @else
                <tr>
                    <td colspan="22">No records found</td>
                </tr>
                @endif
        </table>

        <div class="signature-section">
            <h2 style="text-align:left">Name OF BHW: <span style="text-decoration: underline;">
                    @if(Auth::user()->role == 'staff')
                    {{ Auth::user()->staff->full_name ?? 'N/A' }}
                    @else
                    {{$healthWorkerFullName ?? $midwifeName }}
                    @endif
                </span></h2>
        </div>
    </div>
    @endforeach

</body>

</html>