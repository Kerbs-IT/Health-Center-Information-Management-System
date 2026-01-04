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
                    @case('tb_dots_case')
                    <button type="button" data-bs-toggle="modal" data-bs-target="#viewdetailsModal"
                        class="viewCaseBtn" data-case-id="{{ $record['id'] }}">
                        @include('patient-info.partials.view-icon')
                    </button>
                    <a href="{{ route('tb-dots-case.pdf', ['caseId' => $record['id']]) }}"
                        class="btn btn-link p-0" target="_blank">
                        @include('patient-info.partials.print-icon')
                    </a>
                    @break

                    @case('tb_dots_checkup')
                    <button type="button" data-bs-toggle="modal" data-bs-target="#viewCheckUpModal"
                        class="tb-dots-view-check-up" data-case-id="{{ $record['id'] }}">
                        @include('patient-info.partials.view-icon')
                    </button>
                    <a href="{{ route('tb-dots-checkup.pdf', ['checkupId' => $record['id']]) }}"
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