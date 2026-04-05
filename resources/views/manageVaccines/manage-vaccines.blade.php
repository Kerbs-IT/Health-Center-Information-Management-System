<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png') }}">
    <title>Manage Vaccines — Health Center IMS</title>
</head>

<body>
    @vite([
    'resources/css/app.css',
    'resources/js/app.js',
    'resources/css/patient/record.css',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/js/manageVaccines/manage-vaccines.js'
    ])

    <div class="d-flex vh-100">
        <aside>
            @include('layout.menuBar')
        </aside>

        <div class="d-flex flex-grow-1 flex-column" style="min-width: 0;">
            @include('layout.header')

            <main class="flex-grow-1 py-3 px-4" style="overflow-y: auto; min-height: 0;">
                <livewire:manageVaccines.manage-vaccines />
            </main>
        </div>
    </div>

    {{-- Add / Edit Modal --}}
    <div class="modal fade" id="vaccineModal" tabindex="-1" aria-labelledby="vaccineModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">

                <div class="modal-header text-white rounded-top-4" style="background-color: #0b8433;">
                    <h5 class="modal-title fw-semibold" id="vaccineModalLabel">
                        <i class="fa-solid fa-syringe me-2"></i>
                        <span id="modalTitleText">Add Vaccine</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <input type="hidden" id="vaccineId">

                    {{-- Vaccine Name --}}
                    <div class="mb-3">
                        <label for="typeOfVaccine" class="form-label fw-semibold text-secondary small">
                            Vaccine Name <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="typeOfVaccine"
                            placeholder="e.g. BCG Vaccine"
                            autocomplete="off">
                        <div class="text-danger small mt-1 d-none" id="typeOfVaccineError"></div>
                    </div>

                    {{-- Acronym --}}
                    <div class="mb-3">
                        <label for="vaccineAcronym" class="form-label fw-semibold text-secondary small">
                            Acronym <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control text-uppercase"
                            id="vaccineAcronym"
                            placeholder="e.g. BCG"
                            autocomplete="off"
                            style="text-transform: uppercase;">
                        <div class="text-danger small mt-1 d-none" id="vaccineAcronymError"></div>
                    </div>

                    {{-- Max Doses --}}
                    <div class="mb-1">
                        <label for="maxDoses" class="form-label fw-semibold text-secondary small">
                            Maximum Doses <span class="text-danger">*</span>
                            <span class="text-muted fw-normal">(1–3)</span>
                        </label>
                        <div class="d-flex gap-3">
                            @foreach([1, 2, 3] as $dose)
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="maxDoses"
                                    id="dose{{ $dose }}"
                                    value="{{ $dose }}">
                                <label class="form-check-label" for="dose{{ $dose }}">{{ $dose }}</label>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-danger small mt-1 d-none" id="maxDosesError"></div>
                    </div>
                </div>

                <div class="modal-footer px-4 pb-4 border-0">
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn text-white px-4" id="saveVaccineBtn" style="background-color: #0b8433;">
                        <span id="saveBtnText">Save Vaccine</span>
                        <span id="saveBtnSpinner" class="spinner-border spinner-border-sm ms-1 d-none" role="status"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</body>

</html>