<form method="GET" class="filters w-100 d-flex gap-3 mb-3 ">

    {{-- Show Entries --}}
    <div class="flex-1">
        <small>Show Entries</small>
        <select name="per_page" class="form-select">
            @foreach([5,10,25,50] as $size)
            <option value="{{ $size }}"
                {{ request('per_page',10) == $size ? 'selected' : '' }}>
                {{ $size }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Search --}}
    <div class="flex-1">
        <small>Search</small>
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control"
            placeholder="Record ID">
    </div>
    {{-- type of record --}}
    <div class="flex-1">
        <small>Type of Record</small>
        <select name="record_type_filter" class="form-select">
            <option value="all" {{ request('record_type_filter') === 'all' || !request('record_type_filter') ? 'selected' : '' }}>
                All Records
            </option>
            <option value="prenatal_case" {{ request('record_type_filter') === 'prenatal_case' ? 'selected' : '' }}>
                Case Record
            </option>
            <option value="pregnancy_plan" {{ request('record_type_filter') === 'pregnancy_plan' ? 'selected' : '' }}>
                Pregnancy Plan
            </option>
            <option value="family_plan_side_a" {{ request('record_type_filter') === 'family_plan_side_a' ? 'selected' : '' }}>
                Family Plan - Side A
            </option>
            <option value="family_plan_side_b" {{ request('record_type_filter') === 'family_plan_side_b' ? 'selected' : '' }}>
                Family Plan - Side B
            </option>
            <option value="prenatal_checkup" {{ request('record_type_filter') === 'prenatal_checkup' ? 'selected' : '' }}>
                Follow-up Check-up
            </option>
        </select>
    </div>

    {{-- Sort --}}
    <div class="flex-1">
        <small>Sort by Date</small>
        <select name="date_sort" class="form-select flex-1">
            <option value="desc" {{ request('date_sort') === 'desc' ? 'selected' : '' }}>
                Newest first
            </option>
            <option value="asc" {{ request('date_sort') === 'asc' ? 'selected' : '' }}>
                Oldest first
            </option>
        </select>
    </div>

    {{-- Apply --}}
    <div class="align-self-end">
        <button type="submit" class="btn btn-success px-5">
            Apply
        </button>
    </div>

</form>