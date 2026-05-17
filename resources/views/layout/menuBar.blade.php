<script>
    (function () {
        if (localStorage.getItem('sidebarState') === 'collapsed' && window.innerWidth > 992) {
            document.documentElement.classList.add('sidebar-pre-collapsed');
        }
    })();
</script>
@php
    $outOfStock   = \App\Models\Medicine::where('stock', '<=', 0)->count();
    $lowStock     = \App\Models\Medicine::where('stock', '>', 0)->where('stock', '<=', 10)->count();
    $expiringSoon = \App\Models\Medicine::whereHas('allBatches', fn($b) =>
                        $b->whereNull('deleted_at')->where('expiry_status', 'Expiring Soon')
                    )->count();
    $expired      = \App\Models\Medicine::whereHas('allBatches', fn($b) =>
                        $b->whereNull('deleted_at')->where('expiry_status', 'Expired')
                    )->count();
    $totalAlerts  = $outOfStock + $lowStock + $expiringSoon + $expired;
@endphp

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
<div class="menu-bar min-vh-100 overflow-y-hidden ">
    <!-- Close button for mobile/tablet -->
    <div class="w-100 d-flex justify-content-end mt-3">
        <button id="closeSidebar" class="close-btn d-lg-none text-red-500 font-black  pe-3"><i class="fa-solid fs-5 fa-x" style="font-weight: 900;"></i></button>
    </div>

    <div class="logo-con d-flex justify-content-center mb-1 px-3 ">
        <a href="{{route('homepage')}}">
            <img src="{{asset(path: 'images/hugoperez_logo.png')}}" alt="logo" class="logo">
        </a>
    </div>
    <div id="side-bar" class="menu-bar-content d-flex flex-column align-items-center w-100">
        <!-- Dashboard -->
        @if(Auth::user()->role != 'patient')
        <div class="wrapper w-100 d-flex justify-content-center ">
            @php
            $role = Auth::user()->role; // assuming 'role' contains a value like 'admin', 'customer', etc.
            $routeName = 'dashboard.' . $role; // e.g., 'dashboard.admin'
            @endphp
            <a href="{{ route($routeName)}}" class="menu-option menu-items d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="dashboard">
                <i class="fa-solid fs-5 fa-house"></i>
                <h4 class="mb-0 fs-5">Dashboard</h4>
            </a>
        </div>
        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{ route('notifications.index')}}" class="menu-option menu-items d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="notification">
                <i class="fa-solid fa-bell"></i>
                <h4 class="mb-0 fs-5">Notification</h4>
            </a>
        </div>
        @if(Auth::user() -> role == 'nurse')
        <!-- manage interface -->
        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{route('manage.interface')}}" class="menu-option menu-items d-flex align-items-center gap-3 text-decoration-none w-100 px-3 py-2" id="manage-interface">
                <i class="fa-solid fs-5 fa-table"></i>
                <h4 class="mb-0 fs-5">Manage Interface</h4>
            </a>
        </div>
        @endif
        <!-- Heatmap -->
        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{route('health-map.index')}}" class="menu-option menu-items d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="heatmap">
                <i class="fa-solid fs-5 fa-map"></i>
                <h4 class="mb-0 fs-5">Heatmap</h4>
            </a>
        </div>
        <!-- Health workers -->
        @if(Auth::user() -> role == 'nurse')
        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{ route('health.worker') }}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="healthWorker">
                <i class="fa-solid fs-5 fa-user-nurse"></i>
                <h4 class="mb-0 fs-5">Health workers</h4>
            </a>

        </div>
        @endif
        @if(Auth::user() -> role == 'nurse' || Auth::user() -> role == 'staff' )
        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{ route('manager.users') }}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="manage_user">
                <i class="fa-solid fs-5 fa-users"></i>
                <h4 class="mb-0 fs-5">Manage users</h4>
            </a>

        </div>
        @endif

        <!-- profile -->
        <div class="wrapper w-100 d-flex justify-content-center d-flex justify-content-center flex-column">
            <a href="{{ route('page.profile') }}" class="menu-option menu-items  d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="profile">
                <i class="fa-solid fs-5 fa-user"></i>
                <h4 class="mb-0 fs-5">Profile</h4>
            </a>

        </div>
        <!-- patient -->
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="{{ route('add-patient')}}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none   w-100 px-3 py-2" id="add-patient">
                <i class="fa-solid fs-5 fa-hospital-user"></i>
                <h4 class="mb-0 fs-5">Add Patients</h4>
            </a>

        </div>
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="{{route('patient-list')}}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none   w-100 px-3 py-2" id="patient-list">
                    <i class="fa-solid fs-5 fa-users"></i>
                    <h4 class="mb-0 fs-5">Patients List</h4>
            </a>

        </div>

        <!-- Records -->
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="#" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="records-menu">
                <div class="menu-text d-flex align-items-center my-auto gap-3 ">
                    <i class="fa-solid fs-5 fa-folder-open"></i>
                    <h4 class="mb-0 fs-6">Records</h4>
                </div>
                <i class="fa-solid fs-5 fa-chevron-right ms-auto dropdown-arrow"></i>
            </a>
            <div class="sub-menu  patient-menu w-75 align-self-end">
                <a href="{{ route('record.all')}}" class="menu-items sub-menu-bar-item  d-flex  gap-3 text-decoration-none  w-100 px-3 py-2" id="record_all">
                    <i class="fa-solid fs-5 fa-list"></i>
                    <h5 class="mb-0">All records</h5>
                </a>
                <a href="{{ route('record.general.consultation')}}" class="menu-items sub-menu-bar-item d-flex  gap-3 text-decoration-none  w-100 px-3 py-2" id="record_general_consultation">
                    <i class="fa-solid fa-stethoscope"></i>
                    <h5 class="mb-0">General Consultation</h5>
                </a>

                <a href="{{ route('record.vaccination')}}" class="menu-items sub-menu-bar-item d-flex  gap-3 text-decoration-none  w-100 px-3 py-2" id="record_vaccination">
                    <i class="fa-solid fs-5 fa-syringe"></i>
                    <h5 class="mb-0">Vaccination</h5>
                </a>
                <!-- prenatal -->
                <a href=" {{ route('records.prenatal') }}" class="menu-items sub-menu-bar-item d-flex  gap-3 text-decoration-none  w-100 px-3 py-2" id="record_prenatal">
                    <i class="fa-solid fs-5 fa-person-pregnant"></i>
                    <h5 class="mb-0">Prenatal</h5>
                </a>
                <!-- senior citizen -->
                <a href="{{ route('record.senior.citizen')}}" class="menu-items sub-menu-bar-item d-flex gap-3 text-decoration-none   w-100 px-3 py-2" id="record_senior_citizen">
                    <i class="fa-solid fs-5 fa-person-cane"></i>
                    <h5 class="mb-0">Senior Citizen</h5>
                </a>
                <!-- TB dots -->
                <a href=" {{ route('record.tb-dots')}}" class="menu-items sub-menu-bar-item d-flex gap-3 text-decoration-none  w-100 px-3 py-2" id="record_tb_dots">
                    <i class="fa-solid fs-5 fa-lungs"></i>
                    <h5 class="mb-0">TB Dots</h5>
                </a>
                <!-- family planning -->
                <a href="{{ route('record.family.planning') }}" class="menu-items sub-menu-bar-item d-flex gap-3 text-decoration-none  w-100 px-3 py-2" id="record_family_planning">
                    <i class="fa-solid fs-5 fa-people-roof"></i>
                    <h5 class="mb-0">Family Planning</h5>
                </a>
            </div>

        </div>
        <!-- master list -->
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="#" class="menu-items d-flex align-items-center gap-3 text-decoration-none  menu-option w-100 px-3 py-2">
                <div class="menu-text d-flex align-items-center my-auto gap-3 ">
                    <i class="fa-solid fs-5 fa-folder-open"></i>
                    <h4 class="mb-0 fs-5">Master list</h4>
                </div>
                <i class="fa-solid fs-5 fa-chevron-right ms-auto dropdown-arrow"></i>


            </a>
            <div class="sub-menu  patient-menu w-75 align-self-end">
                <a href="{{ route('masterlist.vaccination') }}" class="menu-items d-flex  gap-3 text-decoration-none  w-100 px-3 py-2" id="masterlist_vaccination">
                    <i class="fa-solid fs-5 fa-syringe"></i>
                    <h5 class="mb-0">Vaccination</h5>
                </a>
                <!-- prenatal -->
                <!-- <a href="#" class="menu-items d-flex  gap-3 text-decoration-none  w-100 px-3 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px;height:20px;" viewBox="0 0 384 512">
                            <path fill="currentColor" d="M192 0a48 48 0 1 1 0 96 48 48 0 1 1 0-96zM120 383c-13.8-3.6-24-16.1-24-31l0-55.1-4.6 7.6c-9.1 15.1-28.8 20-43.9 10.9s-20-28.8-10.9-43.9l58.3-97c15-24.9 40.3-41.5 68.7-45.6c4.1-.6 8.2-1 12.5-1l1.1 0 12.5 0 2.4 0c1.4 0 2.8 .1 4.1 .3c35.7 2.9 65.4 29.3 72.1 65l6.1 32.5c44.3 8.6 77.7 47.5 77.7 94.3l0 32c0 17.7-14.3 32-32 32l-16 0-40 0 0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-96-8 0-8 0 0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-97z"  />
                        </svg>
                        <h5 class="mb-0">Prenatal</h5>
                    </a> -->
                <!-- senior citizen -->
                <!-- <a href="#" class="menu-items d-flex gap-3 text-decoration-none   w-100 px-3 py-2">
                    <i class="fa-solid fa-person-cane"></i>
                    <h5 class="mb-0">Senior Citizen</h5>
                </a> -->
                <!-- TB dots -->
                <!-- <a href="#" class="menu-items d-flex gap-3 text-decoration-none  w-100 px-3 py-2">
                    <i class="fa-solid fa-lungs"></i>
                    <h5 class="mb-0">TB Dots</h5>
                </a> -->
                <!-- family planning -->
                <a href=" {{ route('masterlist.wra')}}" class="menu-items d-flex gap-3 text-decoration-none  w-100 px-3 py-2" id="masterlist_wra">
                    <i class="fa-solid fa-people-roof"></i>
                    <h5 class="mb-0">WRA</h5>
                </a>
            </div>

        </div>
        <!-- Inventory System -->
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="#" class="menu-items d-flex align-items-center gap-3 text-decoration-none  menu-option w-100 px-3 py-2">
                <div class="menu-text d-flex align-items-center my-auto gap-3 ">
                    <i class="fa-solid fa-warehouse"></i>
                    <h4 class="mb-0 fs-5">Inventory</h4>
                </div>
                <i class="fa-solid fs-5 fa-chevron-right ms-auto dropdown-arrow"></i>
            </a>
            <div class="sub-menu  patient-menu w-75 align-self-end">
                <a href="{{ route('categories') }}" class="menu-items d-flex  gap-3 text-decoration-none align-items-center  w-100 px-3 py-2" id="inventory_category">
                    <i class="fa-solid fa-layer-group"></i>
                    <h5 class="mb-0">Category</h5>
                </a>
                <div class="medicine-item-wrap position-relative">
                    <a href="{{ route('medicines') }}" class="menu-items medicines d-flex  text-decoration-none align-items-center w-100 px-3 py-2" id="inventory_medicine">
                        <i class="fa-solid fa-plus"></i>
                        <h5 class="mb-0 ms-3">Medicine</h5>

                        @php
                            $outOfStock   = \App\Models\Medicine::where('stock', '<=', 0)->count();
                            $lowStock     = \App\Models\Medicine::where('stock', '>', 0)->where('stock', '<=', 10)->count();
                            $expiringSoon = \App\Models\Medicine::whereHas('allBatches', fn($b) =>
                                                $b->whereNull('deleted_at')->where('expiry_status', 'Expiring Soon')
                                            )->count();
                            $expired      = \App\Models\Medicine::whereHas('allBatches', fn($b) =>
                                                $b->whereNull('deleted_at')->where('expiry_status', 'Expired')
                                            )->count();
                            $totalAlerts  = $outOfStock + $lowStock + $expiringSoon + $expired;
                        @endphp

                        @if($totalAlerts > 0)
                            <span class="medicine-alert-badge">{{ $totalAlerts }}</span>
                        @endif
                    </a>

                    @if($totalAlerts > 0)
                    <div class="badge-bar">
                        <div class="badge-bar-header">⚠ Inventory Alerts</div>

                        @if($outOfStock > 0)
                        <a href="{{ route('medicines') }}?filter=out_of_stock"
                        class="badge-filter-btn"
                        style="background: rgba(239,68,68,.12)">
                            <span class="bdot" style="background:#ef4444; box-shadow:0 0 5px #ef4444"></span>
                            <span>Out of stock</span>
                            <span class="bcount" style="background:#ef4444">{{ $outOfStock }}</span>
                        </a>
                        @endif

                        @if($lowStock > 0)
                        <a href="{{ route('medicines') }}?filter=low_stock"
                        class="badge-filter-btn"
                        style="background: rgba(234,179,8,.12)">
                            <span class="bdot" style="background:#eab308; box-shadow:0 0 5px #eab308"></span>
                            <span>Low stock</span>
                            <span class="bcount" style="background:#eab308; color:#000">{{ $lowStock }}</span>
                        </a>
                        @endif

                        @if($expiringSoon > 0)
                        <a href="{{ route('medicines') }}?filter=expiring_soon"
                        class="badge-filter-btn"
                        style="background: rgba(249,115,22,.12)">
                            <span class="bdot" style="background:#f97316; box-shadow:0 0 5px #f97316"></span>
                            <span>Expiring soon</span>
                            <span class="bcount" style="background:#f97316">{{ $expiringSoon }}</span>
                        </a>
                        @endif

                        @if($expired > 0)
                        <a href="{{ route('medicines') }}?filter=expired"
                        class="badge-filter-btn"
                        style="background: rgba(239,68,68,.12)">
                            <span class="bdot" style="background:#ef4444; box-shadow:0 0 5px #ef4444"></span>
                            <span>Expired batches</span>
                            <span class="bcount" style="background:#ef4444">{{ $expired }}</span>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
                <a href="{{ route('manageMedicineRequests') }}" class="menu-items d-flex gap-3 text-decoration-none align-items-center  w-100 px-3 py-2" id="inventory_requests">
                    <i class="fa-solid fa-file-medical"></i>
                    <h5 class="mb-0">Manage Requests</h5>
                </a>
                <!-- TB dots -->
                <a href="{{ route('inventory-report') }}" class="menu-items d-flex gap-3 text-decoration-none align-items-center  w-100 px-3 py-2" id="inventory_report">
                    <i class="fa-solid fa-chart-column"></i>
                    <h5 class="mb-0">Report</h5>
                </a>
                <a href="{{ route('medicineRequestLog') }}" class="menu-items d-flex gap-3 text-decoration-none align-items-center  w-100 px-3 py-2" id="inventory_logs">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <h5 class="mb-0">Logs</h5>
                </a>
            </div>
        </div>

        @endif
        @if(Auth::user() -> role == 'nurse')
        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{ route('manage.vaccine.index') }}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="manage_vaccines">
                <i class="fa-solid fa-syringe"></i>
                <h4 class="mb-0 fs-5">Manage Vaccines</h4>
            </a>
        </div>
        @endif
        <!-- -----------------------------------PATIENT DASHBOARD CONTENT -->
        <!-- PATIENT DASHBOARD CONTENT -->
        @if(Auth::user()->role == 'patient')
        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{ route('dashboard.patient')}}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none w-100 px-3 py-2" id="patient_dashboard">
                <i class="fa-solid fs-5 fa-house"></i>
                <h4 class="mb-0 fs-5">Profile</h4>
            </a>
        </div>

        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{ route('notifications.index')}}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none w-100 px-3 py-2" id="patient_notification">
                <i class="fa-solid fa-bell"></i>
                <h4 class="mb-0 fs-5">Notification</h4>
            </a>
        </div>

        {{-- Medical Records link --}}
        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{ route('patient.record.overview') }}"
                class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none w-100 px-3 py-2"
                id="patient_medical_record">
                <i class="fa-solid fa-folder-open"></i>
                <h4 class="mb-0 fs-5">Medical Records</h4>
            </a>
        </div>

        <div class="wrapper w-100 d-flex justify-content-center">
            <a href="{{ route('medicineRequest')}}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none w-100 px-3 py-2" id="patient_medicine_request">
                <i class="fa-solid fa-pills"></i>
                <h4 class="mb-0 fs-5">Medication Requests</h4>
            </a>
        </div>
        @endif
        <!-- logout -->
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="" class="menu-items d-flex  align-items-center gap-3 text-decoration-none bg-danger  w-100 px-3 py-2" id="logout-btn">
                <i class="fa-solid fs-5 fa-right-from-bracket"></i>
                <h4 class="mb-0 fs-5">Log-out</h4>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrap = document.querySelector('.medicine-item-wrap');
        const bar  = document.querySelector('.badge-bar');
        if (!wrap || !bar) return;

        document.body.appendChild(bar);

        let hideTimer = null;
        let holdTimer = null;
        const FADE_DELAY = 2000;
        const HOLD_DURATION = 500; // ms to hold before showing

        function updatePosition() {
            const rect = wrap.getBoundingClientRect();
            bar.style.top  = (rect.top + rect.height / 2) + 'px';
            bar.style.left = (rect.right + 6) + 'px';
        }

        function show() {
            clearTimeout(hideTimer);
            updatePosition();
            bar.classList.add('badge-bar--visible');
        }

        function hide() {
            clearTimeout(hideTimer);
            hideTimer = setTimeout(() => bar.classList.remove('badge-bar--visible'), FADE_DELAY);
        }

        // Desktop: hover
        wrap.addEventListener('mouseenter', show);
        wrap.addEventListener('mouseleave', hide);
        bar.addEventListener('mouseenter', () => clearTimeout(hideTimer));
        bar.addEventListener('mouseleave', hide);

        // Mobile/tablet: long press (touchstart → hold → show)
        wrap.addEventListener('touchstart', function (e) {
            holdTimer = setTimeout(() => {
                show();
                // Vibrate for haptic feedback if supported
                if (navigator.vibrate) navigator.vibrate(40);
            }, HOLD_DURATION);
        }, { passive: true });

        wrap.addEventListener('touchend', function () {
            clearTimeout(holdTimer); // Cancel if released too early
            hide();
        });

        wrap.addEventListener('touchmove', function () {
            clearTimeout(holdTimer); // Cancel if finger moves (scrolling)
        });

        bar.addEventListener('touchstart', () => clearTimeout(hideTimer), { passive: true });
        bar.addEventListener('touchend', hide);
    });
</script>
<div id="sidebarOverlay" class="sidebar-overlay d-lg-none"></div>

<!-- test for medicine -->