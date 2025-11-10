
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
<div class="menu-bar  min-vh-100" > 
    <!-- Close button for mobile/tablet -->
    <button id="closeSidebar" class=" d-lg-none">&times;</button>

    <div class="logo-con d-flex justify-content-center p-3 mb-1 mt-3">
        <img src="{{asset(path: 'images/hugoperez_logo.png')}}"  alt="logo" class="logo">
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
            <a href="#" class="menu-option menu-items d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="heatmap">
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
            <a href="{{ route('page.profile') }}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="profile">
                <i class="fa-solid fs-5 fa-user"></i>
                <h4 class="mb-0 fs-5">Profile</h4>
            </a>

        </div>
        <!-- patient -->
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="{{ route('add-patient')}}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none   w-100 px-3 py-2" id="add-patient">
                <div class="d-flex align-items-center gap-3 ">
                    <i class="fa-solid fs-5 fa-hospital-user"></i>
                    <h4 class="mb-0 fs-5">Add Patients</h4>
                </div>

            </a>

        </div>

        <!-- Records -->
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="#" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2">
                <div class="menu-text d-flex align-items-center my-auto gap-3 ">
                    <i class="fa-solid fs-5 fa-folder-open"></i>
                    <h4 class="mb-0 fs-6">Records</h4>
                </div>      
                <i class="fa-solid fs-5 fa-chevron-right ms-auto dropdown-arrow"></i>
            </a>
            <div class="sub-menu  patient-menu w-75 align-self-end">
                <a href="{{ route('all.record')}}" class="menu-items d-flex  gap-3 text-decoration-none  w-100 px-3 py-2" id="record_all_records">
                    <i class="fa-solid fs-5 fa-folder-open"></i>
                    <h5 class="mb-0">All Record</h5>
                </a>
                <a href="{{ route('record.vaccination')}}" class="menu-items d-flex  gap-3 text-decoration-none  w-100 px-3 py-2" id="record_vaccination">
                    <i class="fa-solid fs-5 fa-syringe"></i>
                    <h5 class="mb-0">Vaccination</h5>
                </a>
                <!-- prenatal -->
                <a href=" {{ route('records.prenatal') }}" class="menu-items d-flex  gap-3 text-decoration-none  w-100 px-3 py-2" id="record_prenatal">
                    <i class="fa-solid fs-5 fa-person-pregnant"></i>
                    <h5 class="mb-0">Prenatal</h5>
                </a>
                <!-- senior citizen -->
                <a href="{{ route('record.senior.citizen')}}" class="menu-items d-flex gap-3 text-decoration-none   w-100 px-3 py-2" id="record_senior_citizen">
                    <i class="fa-solid fs-5 fa-person-cane"></i>
                    <h5 class="mb-0">Senior Citizen</h5>
                </a>
                <!-- TB dots -->
                <a href=" {{ route('record.tb-dots')}}" class="menu-items d-flex gap-3 text-decoration-none  w-100 px-3 py-2" id="record_tb_dots">
                    <i class="fa-solid fs-5 fa-lungs"></i>
                    <h5 class="mb-0">TB Dots</h5>
                </a>
                <!-- family planning -->
                <a href="{{ route('record.family.planning') }}" class="menu-items d-flex gap-3 text-decoration-none  w-100 px-3 py-2" id="record_family_planning">
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
                <a href="#" class="menu-items d-flex gap-3 text-decoration-none   w-100 px-3 py-2">
                    <i class="fa-solid fa-person-cane"></i>
                    <h5 class="mb-0">Senior Citizen</h5>
                </a>
                <!-- TB dots -->
                <a href="#" class="menu-items d-flex gap-3 text-decoration-none  w-100 px-3 py-2">
                    <i class="fa-solid fa-lungs"></i>
                    <h5 class="mb-0">TB Dots</h5>
                </a>
                <!-- family planning -->
                <a href=" {{ route('masterlist.wra')}}" class="menu-items d-flex gap-3 text-decoration-none  w-100 px-3 py-2" id="masterlist_wra">
                    <i class="fa-solid fa-people-roof"></i>
                    <h5 class="mb-0">WRA</h5>
                </a>
            </div>

        </div>
        <!-- Inventory  here, tinanggal ko muna-->

        @endif
        <!-- -----------------------------------PATIENT DASHBOARD CONTENT -->
        @if(Auth::user() -> role == 'patient')
        <a href="{{ route('dashboard.patient')}}" class="menu-option menu-items d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="patient_dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class=" icons home-icon" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                <path fill="currentColor" d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
            </svg>
            <h4 class="mb-0 fs-5">Dashboard</h4>
        </a>
        <a href="{{ route('view.medical.record')}}" class="menu-items menu-option d-flex align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="patient_medical_record">
            <div class="menu-text d-flex align-items-center my-auto gap-3 ">
                <svg xmlns="http://www.w3.org/2000/svg" class="icons" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                    <path fill="currentColor" d="M88.7 223.8L0 375.8 0 96C0 60.7 28.7 32 64 32l117.5 0c17 0 33.3 6.7 45.3 18.7l26.5 26.5c12 12 28.3 18.7 45.3 18.7L416 96c35.3 0 64 28.7 64 64l0 32-336 0c-22.8 0-43.8 12.1-55.3 31.8zm27.6 16.1C122.1 230 132.6 224 144 224l400 0c11.5 0 22 6.1 27.7 16.1s5.7 22.2-.1 32.1l-112 192C453.9 474 443.4 480 432 480L32 480c-11.5 0-22-6.1-27.7-16.1s-5.7-22.2 .1-32.1l112-192z" />
                </svg>
                <h4 class="mb-0 fs-5">Medical Records</h4>
            </div>
        </a>
        @endif
        <!-- logout -->
        <div class="wrapper w-100 d-flex justify-content-center flex-column">
            <a href="" class="menu-items d-flex  align-items-center gap-3 text-decoration-none  w-100 px-3 py-2" id="logout-btn">
                <i class="fa-solid fs-5 fa-right-from-bracket"></i>
                <h4 class="mb-0 fs-5">Log-out</h4>
            </a>
        </div>
    </div>
</div>
 <div id="sidebarOverlay" class="sidebar-overlay d-lg-none"></div>
