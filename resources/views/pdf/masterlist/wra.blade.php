<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @page {
            size: legal landscape;
            margin: 8mm 5mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }

        .page-break {
            page-break-after: always;
        }

        .header-section {
            text-align: center;
            margin-bottom: 8px;
        }

        .header-section h4 {
            margin: 2px 0;
            font-size: 11px;
            font-weight: bold;
        }

        .header-info-row {
            width: 100%;
            margin: 5px 0;
            font-size: 10px;
        }

        .header-info-row table {
            width: 100%;
            border: none;
        }

        .header-info-row td {
            border: none;
            padding: 2px 5px;
            text-align: left;
            width: 33.33%;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 5px 2px;
            text-align: center;
            font-size: 10px;
            vertical-align: middle;
            overflow: hidden;
        }

        table.main-table th {
            background-color: #e8e8e8;
            font-weight: bold;
            line-height: 1.2;
        }

        /* Precise column widths - total = 100% */
        .col-no {
            width: 1.5%;
        }

        .col-hh {
            width: 2.5%;
        }

        .col-name {
            width: 7%;
        }

        .col-address {
            width: 9%;
        }

        .col-age {
            width: 2%;
        }

        .col-birthday {
            width: 5%;
        }

        .col-se {
            width: 4%;
        }

        .col-plan {
            width: 2.5%;
        }

        .col-fp-type {
            width: 5%;
        }

        .col-check {
            width: 2.5%;
        }

        .col-fp-specify {
            width: 5.5%;
        }

        .col-date {
            width: 5%;
        }

        .text-wrap {
            white-space: normal;
            word-wrap: break-word;
            text-align: left;
        }

        .underline {
            text-decoration: underline;
        }

        p {
            margin: 1px 0;
            padding: 0;
        }

        th p {
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }
    </style>
</head>

<body>
    @php
    $recordsPerPage = 10;
    $totalRecords = $masterlistRecords->count();
    $totalPages = $totalRecords > 0 ? ceil($totalRecords / $recordsPerPage) : 1;
    $recordChunks = $totalRecords > 0 ? $masterlistRecords->chunk($recordsPerPage) : collect([collect()]);
    @endphp

    @foreach($recordChunks as $pageIndex => $pageRecords)
    <div class="header-section">
        <h4 style="font-size:15px !important;">Master List of Women of Reproductive Age for Family Planning Services</h4>
        <h4 style="font-size:15px !important;">For the Quarter/Year: <span class="underline">{{$monthName ?? 'Jan-Dec'}} - {{$selectedYear ?? '2025'}}</span></h4>
    </div>

    <div class="header-info-row">
        <table>
            <tr>
                <td style="font-size:15px !important;"><strong>Barangay:</strong> <span class="underline">{{$selectedBrgy == '' ? 'All Barangays' : $selectedBrgy}}</span></td>
                <td style="font-size:15px !important;"><strong>Name of BHS Midwife:</strong> <span class="underline">{{$midwifeName ?? ''}}</span></td>
                <td style="font-size:15px !important;"><strong>Date Prepared:</strong> <span class="underline">{{date('Y-m-d')}}</span></td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <!-- Row 1: Main headers -->
            <tr>
                <th rowspan="3" class="col-no">No.</th>
                <th rowspan="3" class="col-hh">
                    <p>HH No.</p>
                    <p>(1)</p>
                </th>
                <th rowspan="3" class="col-name">
                    <p>Name of WRA</p>
                    <p>(FN,MI,LN)</p>
                    <p>(2)</p>
                </th>
                <th rowspan="3" class="col-address">
                    <p>Address</p>
                    <p>(3)</p>
                </th>
                <th colspan="3">
                    <p>Age in Years</p>
                    <p>(4)</p>
                </th>
                <th rowspan="3" class="col-birthday">
                    <p>Birthday</p>
                    <p>(MM/DD/YY)</p>
                    <p>(5)</p>
                </th>
                <th rowspan="3" class="col-se">
                    <p>SE Status</p>
                    <p>(6)</p>
                </th>
                <th colspan="3">
                    <p>Do you plan to have more children?</p>
                    <p>(Place a check)</p>
                    <p>(7)</p>
                </th>
                <th colspan="3">
                    <p>If col. 7b & 7c is (✓),are you</p>
                    <p>currently using any FP method</p>
                    <p>(8)</p>
                </th>
                <th colspan="2">
                    <p>if col 7b or 7c is / and using col 8b</p>
                    <p>or 8c, would you like to shift to</p>
                    <p>modern method? place(/)</p>
                    <p>(9)</p>
                </th>
                <th rowspan="3" class="col-check">
                    <p>WRA with</p>
                    <p>MFP Unmet</p>
                    <p>Need</p>
                    <p>(10)</p>
                </th>
                <th colspan="3">
                    <p>Based on TCL on FP, did WRA accept</p>
                    <p>any modern FP method?</p>
                    <p>(11)</p>
                </th>
            </tr>

            <!-- Row 2: Sub-headers -->
            <tr>
                <th rowspan="2" class="col-age">10-14</th>
                <th rowspan="2" class="col-age">15-19</th>
                <th rowspan="2" class="col-age">20-49</th>
                <th colspan="2">if Yes,when?</th>
                <th rowspan="2" class="col-plan">No</th>
                <th colspan="2">If Yes, what type?</th>
                <th rowspan="2" class="col-check">
                    <p>Not using</p>
                    <p>any FP</p>
                    <p>method</p>
                    <p>(place a /)</p>
                    <p>(8c)</p>
                </th>
                <th rowspan="2" class="col-check">
                    <p>Yes</p>
                    <p>(9a)</p>
                </th>
                <th rowspan="2" class="col-check">
                    <p>No</p>
                    <p>(9b)</p>
                </th>
                <th rowspan="2" class="col-check">
                    <p>No</p>
                    <p>(11a)</p>
                    <p>(Put a /)</p>
                </th>
                <th colspan="2">
                    <p>Yes</p>
                    <p>(11b)</p>
                </th>
            </tr>

            <!-- Row 3: Final sub-headers -->
            <tr>
                <th class="col-plan">
                    <p>Now</p>
                    <p>(7a)</p>
                </th>
                <th class="col-plan">
                    <p>Spacing</p>
                    <p>(7b)</p>
                </th>
                <th class="col-fp-type">
                    <p>modern</p>
                    <p>(8a)</p>
                </th>
                <th class="col-fp-type">
                    <p>traditional</p>
                    <p>(8b)</p>
                </th>
                <th class="col-fp-specify">
                    <p>specify modern</p>
                    <p>FP method</p>
                </th>
                <th class="col-date">
                    <p>Date when FP</p>
                    <p>method accepted</p>
                </th>
            </tr>
        </thead>

        <tbody>
            @php
            $startIndex = $pageIndex * $recordsPerPage;
            @endphp

            @if($pageRecords->count() > 0)
            @foreach($pageRecords as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record['house_hold_number'] ?? '' }}</td>
                <td class="text-wrap">{{ $record['name_of_wra'] ?? '' }}</td>
                <td class="text-wrap">{{ $record['address'] ?? '' }}</td>
                <td>{{ isset($record['age']) && $record['age'] >= 10 && $record['age'] <= 14 ? '/' : '' }}</td>
                <td>{{ isset($record['age']) && $record['age'] >= 15 && $record['age'] <= 19 ? '/' : '' }}</td>
                <td>{{ isset($record['age']) && $record['age'] >= 20 && $record['age'] <= 49 ? '/' : '' }}</td>
                <td>{{ $record['date_of_birth'] ?? '' }}</td>
                <td>{{ ($record['SE_status'] ?? '') === 'Yes' ? 'NHTS' : 'Non-NHTS' }}</td>
                <td>{{ ($record['plan_to_have_more_children_yes'] ?? '') === 'now' ? '/' : '' }}</td>
                <td>{{ ($record['plan_to_have_more_children_yes'] ?? '') === 'spacing' ? '/' : '' }}</td>
                <td>{{ ($record['plan_to_have_more_children_no'] ?? '') === 'limiting' ? '/' : '' }}</td>
                <td>{{ $record['modern_FP'] ?? '' }}</td>
                <td>{{ $record['traditional_FP'] ?? '' }}</td>
                <td>{{ ($record['currently_using_any_FP_method_no'] ?? '') === 'yes' ? '/' : '' }}</td>
                <td>{{ ($record['shift_to_modern_method'] ?? '') === 'Yes' ? '/' : '' }}</td>
                <td>{{ ($record['shift_to_modern_method'] ?? '') === 'No' ? '/' : '' }}</td>
                <td>{{ ($record['wra_with_MFP_unmet_need'] ?? '') !== 'no' ? '/' : '' }}</td>
                <td>{{ ($record['wra_accepte_any_modern_FP_method'] ?? '') === 'no' ? '/' : '' }}</td>
                <td>{{ $record['selected_modern_FP_method'] ?? '' }}</td>
                <td>{{ $record['date_when_FP_method_accepted'] ?? '' }}</td>
            </tr>
            @endforeach

            @php
            $emptyRowsNeeded = $recordsPerPage - $pageRecords->count();
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
                </tr>
                @endfor
                @else
                <tr>
                    <td colspan="21" style="padding: 15px;">
                        No records found
                        @if($search || $selectedBrgy || $selectedMonth || $selectedYear)
                        <br><small>Try adjusting your filters or search term</small>
                        @else
                        <br><small>No data available</small>
                        @endif
                    </td>
                </tr>
                @for($i = 1; $i < $recordsPerPage; $i++)
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
                    </tr>
                    @endfor
                    @endif
        </tbody>
    </table>

    @if($pageIndex < $totalPages - 1)
        <div class="page-break">
        </div>
        @endif
        @endforeach
</body>

</html>