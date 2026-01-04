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