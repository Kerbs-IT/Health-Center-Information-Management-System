<div>
    <div class="tables d-flex flex-column p-3">
        <div class="add-btn mb-3 d-flex justify-content-between">
            <a href="{{route('records.prenatal')}}" class="btn btn-danger px-4 fs-5 ">Back</a>
            <div class="add-buttons">
                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#addPrenatalCaseRecordModal" data-patient-info='@json($patientInfo)' id="add_case_record_add_btn">Add Case Record</button>
                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#addPregnancyPlanModal" data-patient-info='@json($patientInfo)' id="add_pregnancy_plan_add_btn">Add Check-up Record</button>
                <button type="button" class="btn btn-success px-3 py-2" data-bs-toggle="modal" data-bs-target="#prenatalCheckupModal" data-bs-medical-record-id="{{$prenatalCaseRecords->id}}" id="prenatal_check_up_add_btn">Add Check-up Record</button>

            </div>

        </div>
        <table class="w-100 table overflow-y-scroll">
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

            <tbody>
                @foreach($prenatal_case_record as $record)

                <tr class="px-">
                    <td>{{$record-> id}}</td>
                    <td>{{$record-> type_of_record}}</td>
                    <td>Nurse Joy</td>
                    <td>{{ optional($record->created_at)->format('M j, Y') }}</td>
                    <td>{{$record->status}}</td>

                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewPrenatalMedicalRecordModal" id="viewCaseBtn" class="viewCaseBtn" data-bs-medical-id="{{$record->id}}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-info text-white fw-bold px-3 case-edit-icon" data-bs-toggle="modal" data-bs-target="#editPrenatalCaseModal" data-bs-medical-id="{{$record->id}}">Edit</button>
                            <button type="button" class="btn btn-danger case-archive-record-icon text-white fw-bold px-3" data-case-id="{{$record->id}}">Archive</button>
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                            </svg>
                            <!-- <p class="mb-0">None</p> -->
                        </div>
                    </td>
                </tr>
                @endforeach

                @if($pregnancy_plan)
                <tr>

                    <td>{{optional($pregnancy_plan)->id}}</td>
                    <td>{{optional($pregnancy_plan)->type_of_record}}</td>
                    <td>Nurse Joy</td>
                    <td>{{optional($pregnancy_plan)->created_at->format('M j, Y')}}</td>
                    <td>{{optional($pregnancy_plan)->status}}</td>
                    <td>
                        <!-- <div class="actions d-flex gap-2 justify-content-center align-items-center">
                                                        <button class="btn btn-success text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#updateVaccinationModal">Update</button>
                                                    </div> -->
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewPregnancyPlanRecordModal" class="pregnancy-plan-view-btn" id="pregnancy-plan-view-btn" data-bs-id="{{$pregnancy_plan->id}}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-info text-white fw-bold px-3 pregnancy_plan_edit_btn" data-bs-toggle="modal" data-bs-target="#case2PrenatalModal" id="pregnancy_plan_edit_btn" data-bs-id="{{$pregnancy_plan->id}}">Edit</button>
                            <button type="button" class="btn btn-danger pregnancy-plan-archive-record-icon text-white fw-bold px-3" data-case-id="{{$pregnancy_plan->id}}">Archive</button>
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                            </svg>
                            <!-- <p class="mb-0">None</p> -->
                        </div>
                    </td>
                </tr>
                @endif
                @if($familyPlanningRecord)

                <tr class="px-">
                    <td>{{optional($familyPlanningRecord)->id??'00'}}</td>
                    <td>{{optional($familyPlanningRecord)->type_of_record??'N/A'}}</td>
                    <td>Nurse Joy</td>
                    <td>{{optional($familyPlanningRecord)->created_at->format('M j, Y')??'N/A' }}</td>
                    <td>{{optional($familyPlanningRecord)->status??'pending'}}</td>

                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewdetailsModal" id="view-family-plan-info" data-case-id="{{$familyPlanningRecord->id}}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-info text-white fw-bold px-3" data-bs-toggle="modal" data-bs-target="#editfamilyPlanningCaseModal" id="edit-family-plan-info" data-case-id="{{$familyPlanningRecord->id}}">Edit</button>
                            <button type="button" class="btn btn-danger archive-family-plan-side-A text-white fw-bold px-3" data-case-id="{{$familyPlanningRecord->id}}">Archive</button>
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                            </svg>
                            <!-- <p class="mb-0">None</p> -->
                        </div>
                    </td>
                </tr>
                @endif
                <!-- side b record -->
                @if($familyPlanSidebRecord)

                <tr class="px-">
                    <td>C-01</td>
                    <td>{{optional($familyPlanSidebRecord)->type_of_record??'N/A'}}</td>
                    <td>Nurse Joy</td>
                    <td>{{optional($familyPlanSidebRecord->created_at)->format('M j, Y') }}</td>
                    <td>{{optional($familyPlanSidebRecord)->status??'pending'}}</td>

                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewSideBmodal" class="view-side-b-record" data-case-id="{{$familyPlanSidebRecord->id}}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-info text-white fw-bold px-3 edit-side-b-record" data-bs-toggle="modal" data-bs-target="#editSideBcaseModal" data-case-id="{{$familyPlanSidebRecord->id}}">Edit</button>
                            <button type="button" class="btn btn-danger text-white fw-bold px-3 delete-side-b-record" data-case-id="{{$familyPlanSidebRecord->id}}">Archive</button>
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                            </svg>
                            <!-- <p class="mb-0">None</p> -->
                        </div>
                    </td>
                </tr>
                @endif

                @foreach($prenatalCheckupRecords as $checkup)

                <tr class="px-">
                    <td>{{$checkup-> id}}</td>
                    <td>{{$checkup-> type_of_record}}</td>
                    <td>Nurse Joy</td>
                    <td>{{ optional($checkup->created_at)->format('M j, Y') }}</td>
                    <td>{{$checkup->status}}</td>

                    <td>
                        <div class="actions d-flex gap-2 justify-content-center align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#pregnancyCheckUpModal" class="viewPregnancyCheckupBtn" data-checkup-id="{{$checkup->id}}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="view-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-info text-white fw-bold px-3 editPregnancyCheckupBtn" data-bs-toggle="modal" data-bs-target="#checkUpModal" data-checkup-id="{{$checkup->id}}">Edit</button>
                            <button type="button" class="btn btn-danger pregnancy-checkup-archieve-btn  text-white fw-bold px-3" data-case-id="{{$checkup->id}}">Archive</button>
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 30px; height:30px; fill:green" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path d="M128 0C92.7 0 64 28.7 64 64l0 96 64 0 0-96 226.7 0L384 93.3l0 66.7 64 0 0-66.7c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0L128 0zM384 352l0 32 0 64-256 0 0-64 0-16 0-16 256 0zm64 32l32 0c17.7 0 32-14.3 32-32l0-96c0-35.3-28.7-64-64-64L64 192c-35.3 0-64 28.7-64 64l0 96c0 17.7 14.3 32 32 32l32 0 0 64c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-64zM432 248a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
                            </svg>
                            <!-- <p class="mb-0">None</p> -->
                        </div>
                    </td>
                </tr>
                @endforeach

            </tbody>

        </table>
        <div class="mt-3">
            {{ $prenatalCheckupRecords->links() }}
        </div>
    </div>
</div>