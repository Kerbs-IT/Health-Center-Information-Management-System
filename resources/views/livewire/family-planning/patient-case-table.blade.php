<div>
    <div class="tables d-flex flex-column  p-md-3 p-0">
        <div class="add-btn mb-3 d-flex justify-content-between">
            <div>
                <a href="{{route('record.family.planning')}}" class="btn btn-danger px-4 fs-5 ">Back</a>
            </div>
            <div class="add-btn  d-flex justify-content-end text-nowrap flex-sm-row flex-column gap-2">
                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#side-a-add-record" data-patient-info='@json($patientInfo)' data-patient-address='@json($address)' data-medical-case-record-id="{{$patientInfo->id}}" id="side-a-add-record-btn">Add Side A Record</button>
                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#side-b-add-record" data-patient-info='@json($patientInfo)' id="side-b-add-record-btn">Add Side B Record</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="w-100 table ">
                <thead class="table-header">
                    <tr>
                        <th>Case No.</th>
                        <th>Type of Record</th>
                        <th>Nurse</th>
                        <th style="cursor:pointer;" wire:click="sortBy('created_at')">
                            Date
                            @if ($sortField === 'created_at')
                            {{ $sortDirection === 'asc' ? '▼' : '▲' }}
                            @endif
                        </th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <!-- data of patient -->
                <tbody>
                    @forelse($allRecords as $record)
                    <tr class="px-">
                        <td>{{ $record['id'] }}</td>
                        <td>{{ $record['type_of_record'] }}</td>
                        <td>Nurse Joy</td>
                        <td>{{ optional($record['created_at'])->format('M d, Y') }}</td>
                        <td>{{ $record['status'] }}</td>
                        <td>
                            <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                @if($record['record_type'] == 'family_planning_side_a')
                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewdetailsModal" class="side-A-view-family-plan-info" id="view-family-plan-info" data-case-id="{{$record['id']}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                    </svg>
                                </button>
                                <button type="button" class="btn btn-info text-white fw-bold px-3 side-A-edit-family-plan-info" data-bs-toggle="modal" data-bs-target="#editfamilyPlanningCaseModal" id="edit-family-plan-info" data-case-id="{{$record['id']}}">Edit</button>
                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3 archive-family-plan-side-A" data-case-id="{{$record['id']}}">Archive</button>
                                <!-- export btn -->
                                <button type="button" data-case-id="{{$record['id']}}" wire:click="exportPdf({{$record['id']}},'a')">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                    </svg>
                                </button>
                                @elseif($record['record_type'] == 'family_planning_side_b')
                                <button type="button" data-bs-toggle="modal" data-bs-target="#viewSideBmodal" class="view-side-b-record" data-case-id="{{$record['id']}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                    </svg>
                                </button>
                                <button type="button" class="btn btn-info text-white fw-bold px-3 edit-side-b-record" data-bs-toggle="modal" data-bs-target="#editSideBcaseModal" data-case-id="{{$record['id']}}">Edit</button>
                                <button type="button" class="btn btn-danger delete-record-icon text-white fw-bold px-3 delete-side-b-record" data-case-id="{{$record['id']}}">Archive</button>
                                <button type="button" wire:click="exportPdf({{$record['id']}},'b')">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                        <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No records available.
                        </td>
                    </tr>
                    @endforelse


                </tbody>

            </table>
        </div>
    </div>
</div>