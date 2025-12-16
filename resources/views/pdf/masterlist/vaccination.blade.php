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

    <div class="vaccination-masterlist-con">

        <div class="mb-3 text-center">
            <h2>MASTER LIST OF {{ $selectedRange }}</h2>
        </div>
        <div class="mb-3 d-flex justify-content-between">
            <h4 class="w-50 text-center">Name of Barangay: <span class="fw-light text-decoration-underline">{{$selectedBrgy == ''?'All Barangays':$selectedBrgy }}</span></h4>
            <h4 class="w-50 text-center">Name of Midwife: <span class="fw-light text-decoration-underline">Nurse Joy</span></h4>
        </div>
        <div class="table-con">
            <table>
                <thead class="table-header ">
                    <tr>
                        <th class="need-space text-no-wrap">Name of Child</th>
                        <th class="need-space">Address</th>
                        <th>sex</th>
                        <th>Age</th>
                        <th class="need-space">Date of Birth</th>
                        <th class="">SE status 1 Months 4 months</th>
                        <th>BCG</th>
                        <th>NEPA w/in 24 hrs</th>
                        <th>PENTA 1</th>
                        <th>PENTA 2</th>
                        <th>PENTA 3</th>
                        <th>OPV 1</th>
                        <th>OPV 2</th>
                        <th>OPV3</th>
                        <th>PCV 1</th>
                        <th>PCV 2</th>
                        <th>PCV 3</th>
                        <th>IPV 1</th>
                        <th>IPV 2</th>
                        <th>MCV 1</th>
                        <th>MCV 2</th>
                        <th>Remarks</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($vaccinationMasterlist as $masterlist)
                    <tr>

                        <td class="need-space">{{optional($masterlist)->name_of_child??''}}</td>
                        <td class="need-space">{{optional($masterlist)-> Address ?? ''}}</td>
                        <td>{{optional($masterlist)-> sex ?? ''}}</td>
                        <td>{{optional($masterlist)-> age ?? ''}}</td>
                        <td class="need-space">{{optional($masterlist)-> date_of_birth?->format('Y-m-d') ?? ''}}</td>
                        <td class="" style="font-size: 15px;">{{optional($masterlist)->SE_status??''}}</td>
                        <td>{{optional($masterlist)->BCG??''}}</td>
                        <td>{{optional($masterlist)->{'Hepatitis B'}??''}}</td>
                        <td>{{optional($masterlist)-> PENTA_1??''}}</td>
                        <td>{{optional($masterlist)-> PENTA_2??''}}</td>
                        <td>{{optional($masterlist)->PENTA_3??''}}</td>
                        <td>{{optional($masterlist)->OPV_1}}</td>
                        <td>{{optional($masterlist)->OPV_2}}</td>
                        <td>{{optional($masterlist)->OPV_3}}</td>
                        <td>{{optional($masterlist)->PCV_1}}</td>
                        <td>{{optional($masterlist)->PCV_2}}</td>
                        <td>{{optional($masterlist)->PCV_3}}</td>
                        <td>{{optional($masterlist)->IPV_1}}</td>
                        <td>{{optional($masterlist)->IPV_2}}</td>
                        <td>{{optional($masterlist)->MCV_1??''}}</td>
                        <td>{{optional($masterlist)->MCV_2}}</td>
                        <td>{{optional($masterlist)->remarks}}</td>


                    </tr>
                    @empty
                    <tr>
                        <td colspan="24" class="text-center ">
                            <i class="fas fa-search mb-2" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mb-1">No records found</p>
                            @if($search || $selectedBrgy || $filterMonth || $filterYear)
                            <small class="text-muted">Try adjusting your filters or search term</small>
                            @else
                            <small class="text-muted">No data available</small>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                    {{-- Fill remaining rows to maintain consistent table height --}}
                    @php
                    $currentCount = $vaccinationMasterlist->count();
                    $emptyRowsNeeded = 20 - $currentCount;
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
                        <td>&nbsp;</td>
                        </tr>
                        @endfor
                        @endif
                </tbody>
            </table>
        </div>
        <h2>Name OF BHW: <span class="text-decoration-underline">
                @if(Auth::user()->role == 'staff')
                {{ Auth::user()->staff->full_name ?? 'N/A' }}
                @else
                {{Auth::user()->nurses->full_name }}
                @endif
        </span></h2>
    </div>
</body>

</html>