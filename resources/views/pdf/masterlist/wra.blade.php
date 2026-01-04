<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .header-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header-item {
            width: 50%;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .need-space {
            min-width: 150px;
        }

        .text-decoration-underline {
            text-decoration: underline;
        }

        .fw-light {
            font-weight: 300;
        }

        .text-no-wrap {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="wra-masterlist-con">
        <div class="table-con">
            <table>
                <thead class="bg-light">
                    <tr>
                        <th colspan="21">
                            <div class="mb-3 text-center">
                                <h4 class="mb-1">Master List of Women of Reproductive Age for Family Planning Services</h4>
                                <h4 class="mb-0">For the Quarter/Year: <span class="text-decoration-underline">{{$monthName}} - {{$selectedYear??'2025'}}</span> </h4>
                            </div>
                            <div class="mb-3 d-flex w-100 gap-5">
                                <h4 class="mb-0 ">Barangay: <span class="fw-light text-decoration-underline">{{$selectedBrgy == ''?'All Barangays':$selectedBrgy }}</span></h4>
                                <h4 class="mb-0 ">Name of BHS Midwife: <span class="fw-light text-decoration-underline">Nurse Joy</span></h4>
                                <h4 class="mb-0 ">Date Prepared: <span class="fw-light text-decoration-underline">06 - 01 - 2025</span></h4>
                            </div>
                        </th>

                    </tr>
                    <tr>
                        <th rowspan="3">No.</th>
                        <th rowspan="3" class="h-100">
                            <div class="mb-0 d-flex flex-column justify-content-between h-100">
                                <p class="">HH No.</p>
                                <p>(1)</p>
                            </div>

                        </th>
                        <th class="need-space" rowspan="3">
                            <p>Name of WRA(FN,MI,LN)</p>
                            <p>(2)</p>
                        </th>
                        <th class="need-space" rowspan="3">
                            <p>Address</p>
                            <p>(3)</p>
                        </th>
                        <th colspan="3">

                            <p>Age in Years</p>
                            <p>(4)</p>
                        </th>
                        <th class="need-space" rowspan="3">
                            <p>Birthday</p>
                            <p>(MM/DD/YY)</p>
                            <p>(5)</p>

                        </th>
                        <th>
                            <p>SE Status </p>
                            <p>(6)</p>
                        </th>
                        <th class="need-space" colspan="3">
                            <p>Do you plan to have more children?</p>
                            <p>(Place a check)</p>
                            <p>(7)</p>
                        </th>
                        <th class="need-space" colspan="3">
                            <p>If col. 7b & 7c is (✓),are you currently using any FP method</p>
                            <p>(8)</p>
                        </th>
                        <th class="need-space" colspan="2">
                            <p>if col 7b or 7c is ✓ and using col 8b or 8c, would you like to shift to modern method? place(✓)</p>
                            <p>(9)</p>
                        </th>
                        <th>
                            <p>WRA with MFP Unmet Need</p>
                            <p>(10)</p>
                        </th>
                        <th colspan="3">
                            <p>Based on TCL on FP, did WRA accept any modern FP method?</p>
                            <p>(11)</p>
                        </th>

                    </tr>
                    <tr>
                        <th rowspan="2" class="text-nowrap px-1">10-14</th>
                        <th rowspan="2" class="text-nowrap px-1">15-19</th>
                        <th rowspan="2" class="text-nowrap px-1">20-49</th>
                        <th rowspan="2">
                            <p>1.NHTS</p>
                            <p>2.NON-NHTS</p>
                        </th>
                        <th colspan="2">if Yes,when?</th>
                        <th>No</th>
                        <th colspan="2">If Yes, what type?</th>
                        <th rowspan="2">
                            Not using any FP method(place a ✓)
                            <p>(8c)</p>
                        </th>
                        <th rowspan="2">
                            <p>Yes</p>
                            <p>(9a)</p>
                        </th>
                        <th rowspan="2">
                            <p>No</p>
                            <p>(9b)</p>
                        </th>
                        <th rowspan="2">(Put ✓ if col (a is checked))</th>
                        <th rowspan="2">
                            <p>No (11a) (Put a ✓)</p>
                        </th>
                        <th colspan="2">
                            <p>Yes</p>
                            <p>(11b)</p>
                        </th>

                    </tr>
                    <tr>

                        <th>
                            <p>Now</p>
                            <p>(7a)</p>
                        </th>
                        <th>
                            <p>Spacing</p>
                            <p>(7b)</p>
                        </th>
                        <th>
                            <p>Limiting</p>
                            <p>(7c)</p>
                        </th>
                        <th>
                            <p>modern</p>
                            <p>(8a)</p>
                        </th>
                        <th>
                            <p>traditional</p>
                            <p>(8b)</p>
                        </th>
                        <th>specify modern FP method</th>
                        <th>
                            Date when FP method accepted
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($masterlistRecords as $record)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $record['house_hold_number'] ?? '' }}</td>
                        <td>{{ $record['name_of_wra'] ?? '' }}</td>
                        <td class="text-wrap min-w-75">{{ $record['address'] ?? '' }}</td>

                        <td>{{ isset($record['age']) && $record['age'] >= 10 && $record['age'] <= 14 ? '✓' : '' }}</td>
                        <td>{{ isset($record['age']) && $record['age'] >= 15 && $record['age'] <= 19 ? '✓' : '' }}</td>
                        <td>{{ isset($record['age']) && $record['age'] >= 20 && $record['age'] <= 49 ? '✓' : '' }}</td>

                        <td>{{ $record['date_of_birth'] ?? '' }}</td>

                        <td>{{ ($record['SE_status'] ?? '') === 'Yes' ? 'NHTS' : 'Non-NHTS' }}</td>

                        <td>{{ ($record['plan_to_have_more_children_yes'] ?? '') === 'now' ? '✓' : '' }}</td>
                        <td>{{ ($record['plan_to_have_more_children_yes'] ?? '') === 'spacing' ? '✓' : '' }}</td>
                        <td>{{ ($record['plan_to_have_more_children_no'] ?? '') === 'limiting' ? '✓' : '' }}</td>

                        <td>{{ $record['modern_FP'] ?? '' }}</td>
                        <td>{{ $record['traditional_FP'] ?? '' }}</td>

                        <td>{{ ($record['currently_using_any_FP_method_no'] ?? '') === 'yes' ? '✓' : '' }}</td>

                        <td>{{ ($record['shift_to_modern_method'] ?? '') === 'Yes' ? '✓' : '' }}</td>
                        <td>{{ ($record['shift_to_modern_method'] ?? '') === 'No' ? '✓' : '' }}</td>

                        <td>{{ ($record['wra_with_MFP_unmet_need'] ?? '') !== 'no' ? '✓' : '' }}</td>

                        <td>{{ ($record['wra_accepte_any_modern_FP_method'] ?? '') === 'no' ? '✓' : '' }}</td>

                        <td>{{ $record['selected_modern_FP_method'] ?? '' }}</td>
                        <td>{{ $record['date_when_FP_method_accepted'] ?? '' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="24" class="text-center ">
                            <i class="fas fa-search mb-2" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mb-1">No records found</p>
                            @if($search || $selectedBrgy || $selectedMonth || $selectedYear)
                            <small class="text-muted">Try adjusting your filters or search term</small>
                            @else
                            <small class="text-muted">No data available</small>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                    {{-- Fill remaining rows to maintain consistent table height --}}
                    @php
                    $currentCount = $masterlistRecords->count();
                    $emptyRowsNeeded = 15 - $currentCount;
                    @endphp

                    @if($currentCount > 0 && $emptyRowsNeeded > 0)
                    @for($i = 0; $i < $emptyRowsNeeded; $i++)
                        <tr>
                        <td class="need-space">&nbsp;</td>
                        <td class="need-space">&nbsp;</td>
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

        </div>
    </div>
</body>

</html>