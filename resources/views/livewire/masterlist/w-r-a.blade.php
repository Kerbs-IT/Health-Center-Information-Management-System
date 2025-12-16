<main class="flex-column">
    <div class="head-part d-flex justify-content-between align-items-center mb-3">
        <h2 class="main-header w-100">{{ $page ?? 'none'}}</h2>
        <div class="direction d-flex gap-2 align-items-center">
            <a href="#" class="text-decoration-none text-black">
                <h5 class="fw-light text-nowrap mb-0">Master List</h5>
            </a>

            <svg xmlns="http://www.w3.org/2000/svg" class="arrow-right" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                <path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z" />
            </svg>
            <a href="#" class="text-decoration-none text-black">
                <h5 class="fw-light text-nowrap mb-0">WRA</h5>
            </a>
        </div>
    </div>
    <div class="main-content card shadow d-flex flex-column p-3 w-100  ">
        <div class="mb-3 d-flex justify-content-between w-100 gap-3 ">
            {{-- Show Entries --}}
            <div class="input-group flex-column w-50">
                <label for="entries" class="mb-1">Show</label>
                <select wire:model.live="entries" id="entries" class="form-select rounded bg-light w-100">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            {{-- Search --}}
            <div class="input-group flex-column w-100">
                <label for="search" class="mb-1">Search</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    id="search"
                    class="form-control rounded bg-light w-100"
                    placeholder="Search by name...">
            </div>

            {{-- Barangay Filter (Only for Nurses) --}}
            @if(Auth::user()->role == 'nurse')
            <div class="input-group flex-column w-75">
                <label for="brgy" class="mb-1">Barangay</label>
                <select wire:model.live="selectedBrgy" id="brgy" class="form-select rounded bg-light w-100">
                    <option value="">All Barangays</option>
                    @foreach($brgyList as $brgy)
                    <option value="{{ $brgy->brgy_unit }}">{{ $brgy->brgy_unit }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Month Filter --}}
            <div class="input-group flex-column w-50">
                <label for="month" class="mb-1">Month</label>
                <select wire:model.live="selectedMonth" id="month" class="form-select rounded bg-light w-100">
                    <option value="">All Months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>

            {{-- Year Filter --}}
            <div class="input-group flex-column w-50">
                <label for="year" class="mb-1">Year</label>
                <select wire:model.live="selectedYear" id="year" class="form-select rounded bg-light w-100">
                    <option value="">All Years</option>
                    @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-group flex-column w-75">
                <label for="unmetNeed">WRA with MFP Unmet Need</label>
                <select wire:model.live="withUnmetNeed" id="unmetNeed" class="form-select rounded bg-light w-100">
                    <option value="" selected>Select an option</option>
                    <option value="no">WRA without MFP Unmet Need</option>
                    <option value="yes">WRA with MFP Unmet Need</option>
                </select>
            </div>

            {{-- Download Button --}}
            <div class=" d-flex align-items-end ms-auto">
                <button
                    type="button"
                    class="btn btn-success d-flex justify-content-center align-items-center gap-2 px-3 py-2"
                    style="height: 38px;"
                    wire:click="exportPdf()">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height:20px" viewBox="0 0 512 512">
                        <path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" fill="white" />
                    </svg>
                    <span style="font-size: 0.875rem;">Download</span>
                </button>
            </div>
        </div>
        <!-- <div class="mb-3 text-center">
                            <h4>MASTER LIST OF 0-59 MONTHS</h4>
                        </div>
                        <div class="mb-3 d-flex w-100">
                            <h5 class="w-50 ">Barangay: <span class="fw-light text-decoration-underline">Karlaville Park Homes</span></h5>
                            <h5 class="w-50 ">Name of BHS Midwife: <span class="fw-light text-decoration-underline">Nurse Joy</span></h5>
                            <h5 class="w-50 text-center">Date Prepared: <span class="fw-light text-decoration-underline">06 - 01 - 2025</span></h5>
                        </div> -->
        <div class="table-con">
            <table>
                <thead class="bg-light">
                    <tr>
                        <th colspan="21">
                            <div class="mb-3 text-center">
                                <h6 class="mb-1">Master List of Women of Reproductive Age for Family Planning Services</h6>
                                <h6 class="mb-0">For the Quarter/Year: <span class="text-decoration-underline">{{$this->monthName($this->selectedMonth)}} - {{$this->selectedYear??'2025'}}</span> </h6>
                            </div>
                            <div class="mb-3 d-flex w-100 gap-5">
                                <h6 class="mb-0 ">Barangay: <span class="fw-light text-decoration-underline">{{$this->selectedBrgy == ''?'All Barangays':$this->selectedBrgy }}</span></h6>
                                <h6 class="mb-0 ">Name of BHS Midwife: <span class="fw-light text-decoration-underline">Nurse Joy</span></h6>
                                <h6 class="mb-0 ">Date Prepared: <span class="fw-light text-decoration-underline">06 - 01 - 2025</span></h6>
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
                        <th rowspan="3">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($masterlistRecords as $record)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{optional($record)->house_hold_number??''}}</td>
                        <td>{{optional($record)->name_of_wra??''}}</td>
                        <td class="text-wrap min-w-75 ">{{optional($record)->address??''}}</td>
                        <td>{{ optional($record, fn($r) => $r->age >= 10 && $r->age <= 14) ? '✓' : '' }}</td>
                        <td>{{ optional($record, fn($r) => $r->age >= 15 && $r->age <= 19) ? '✓' : '' }}</td>
                        <td>{{ optional($record, fn($r) => $r->age >= 20 && $r->age <= 49) ? '✓' : '' }}</td>
                        <td>{{optional($record)->date_of_birth??''}}</td>
                        <td>{{ optional($record, fn($r) => $r->SE_status == 'Yes' ) ? 'NHTS' : 'Non-NHTS' }}</td>
                        <td>{{ optional($record, fn($r) => $r->plan_to_have_more_children_yes != null && $r->plan_to_have_more_children_yes == 'now' ) ? '✓' : '' }}</td>
                        <td>{{ optional($record, fn($r) => $r->plan_to_have_more_children_yes != null && $r->plan_to_have_more_children_yes == 'spacing' ) ? '✓' : '' }}</td>
                        <td>{{ optional($record, fn($r) => $r->plan_to_have_more_children_no != null && $r->plan_to_have_more_children_no == 'limiting' ) ? '✓' : '' }}</td>
                        <td>{{optional($record)->modern_FP ?? ''}}</td>
                        <td>{{optional($record)->traditional_FP ?? ''}}</td>
                        <td>{{ optional($record, fn($r) => $r->currently_using_any_FP_method_no == 'yes') ? '✓' : '' }}</td>
                        <td>{{ optional($record, fn($r) => $r->shift_to_modern_method == 'Yes') ? '✓' : '' }}</td>
                        <td>{{ optional($record, fn($r) => $r->shift_to_modern_method == 'No') ? '✓' : '' }}</td>
                        <td>{{ optional($record, fn($r) => $r->wra_with_MFP_unmet_need != 'no') ? '✓' : '' }}</td>
                        <td>{{ optional($record, fn($r) => $r->wra_accepte_any_modern_FP_method == 'no') ? '✓' : '' }}</td>
                        <td>{{optional($record)->selected_modern_FP_method ?? ''}}</td>
                        <td>{{optional($record)->date_when_FP_method_accepted ?? ''}}</td>
                        <td class="">
                            <button class="btn btn-success wra-masterlist-edit-btn" data-wra-masterlist-id="{{$record->id}}" data-bs-toggle="modal" data-bs-target="#wraMasterListModal">Edit</button>
                        </td>
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
                    $emptyRowsNeeded = $entries - $currentCount;
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
                        <td>&nbsp;</td>
                        </tr>
                        @endfor
                        @endif

                </tbody>
            </table>

        </div>
        {{-- Pagination --}}
        @if($masterlistRecords->total() > 0)
        <div class="mt-1 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $masterlistRecords->firstItem() }} to {{ $masterlistRecords->lastItem() }}
                of {{ $masterlistRecords->total() }} results
            </div>
            <div>
                {{ $masterlistRecords->links() }}
            </div>
        </div>
        @endif


        @if(Auth::user() -> role == 'staff')
        <div class="mb-3">
            <h2>Name of BHM:<span>{{Auth::user() -> staff -> fullName}}</span></h2>
        </div>
        @endif
    </div>
</main>