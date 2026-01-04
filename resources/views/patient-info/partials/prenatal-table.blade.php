<table class="w-100 table">
    <thead class="table-header">
        <tr>
            <th>Record Id.</th>
            <th>Service Type</th>
            <th>Type of Record</th>
            <th>Date Registered</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($allRecords as $record)
        <tr>
            <td>{{ $record['id'] }}</td>
            <td>{{ $record['service_type'] }}</td>
            <td>{{ $record['type_of_record'] }}</td>
            <td>{{ $record['date_registered']?->format('Y-m-d') ?? 'N/A' }}</td>
            <td>
                <div class="actions d-flex gap-2 justify-content-center align-items-center">
                    @switch($record['record_type'])
                        @case('prenatal_case')
                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewPrenatalMedicalRecordModal" 
                                    class="viewCaseBtn" data-bs-medical-id="{{ $record['id'] }}">
                                @include('patient-info.partials.view-icon')
                            </button>
                            <a href="{{ route('prenatal-case.pdf', ['caseId' => $record['id']]) }}" 
                               class="btn btn-link p-0" target="_blank">
                                @include('patient-info.partials.print-icon')
                            </a>
                            @break

                        @case('pregnancy_plan')
                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewPregnancyPlanRecordModal" 
                                    class="pregnancy-plan-view-btn" data-bs-id="{{ $record['id'] }}">
                                @include('patient-info.partials.view-icon')
                            </button>
                            <a href="{{ route('pregnancy-plan.pdf', ['planId' => $record['id']]) }}" 
                               class="btn btn-link p-0" target="_blank">
                                @include('patient-info.partials.print-icon')
                            </a>
                            @break

                        @case('family_plan_side_a')
                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewdetailsModal" 
                                    id="view-family-plan-info" data-case-id="{{ $record['id'] }}">
                                @include('patient-info.partials.view-icon')
                            </button>
                            <a href="{{ route('family-planning-side-a.pdf', ['caseId' => $record['id']]) }}" 
                               class="btn btn-link p-0" target="_blank">
                                @include('patient-info.partials.print-icon')
                            </a>
                            @break

                        @case('family_plan_side_b')
                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewSideBmodal" 
                                    class="view-side-b-record" data-case-id="{{ $record['id'] }}">
                                @include('patient-info.partials.view-icon')
                            </button>
                            <a href="{{ route('family-planning-side-b.pdf', ['caseId' => $record['id']]) }}" 
                               class="btn btn-link p-0" target="_blank">
                                @include('patient-info.partials.print-icon')
                            </a>
                            @break

                        @case('prenatal_checkup')
                            <button type="button" data-bs-toggle="modal" data-bs-target="#pregnancyCheckUpModal" 
                                    class="viewPregnancyCheckupBtn" data-checkup-id="{{ $record['id'] }}">
                                @include('patient-info.partials.view-icon')
                            </button>
                            <a href="{{ route('prenatal-checkup.pdf', ['caseId' => $record['id']]) }}" 
                               class="btn btn-link p-0" target="_blank">
                                @include('patient-info.partials.print-icon')
                            </a>
                            @break
                    @endswitch
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No records found</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Pagination Links --}}
<div class="d-flex justify-content-center mt-3">
    {{ $allRecords->links() }}
</div>