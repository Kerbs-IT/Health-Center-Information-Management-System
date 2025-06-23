<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>

<body>
    @vite(['resources/css/app.css','resources/js/app.js','resources/js/menudropdown.js','resources/js/header.js', 'resources/css/profile.css','resources/js/address/address.js'])
    <!-- always include the sweetalert -->
    @include('sweetalert::alert')
    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside>
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
            <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1">
                <header class=" d-flex align-items-center px-3 ">
                    <nav class="d-flex justify-content-between align-items-center w-100 ">
                        <h1 class="mb-0">Profile</h1>
                        <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2">
                            <img src="{{ optional(Auth::user()->nurses)->profile_image 
                        ? asset(optional(Auth::user()->nurses)->profile_image) 
                        : (optional(Auth::user()->staff)->profile_image 
                            ? asset(optional(Auth::user()->staff)->profile_image) 
                            : asset('images/default_profile.png')) }}" alt="profile picture" class="profile-img" id="profile_img">
                            <div class="username-n-role">
                                <h5 class="mb-0">{{ optional(Auth::user()->nurses)-> full_name
                                                    ?? optional(Auth::user()->staff)-> full_name
                                                    ?? 'none'  }}</h5>
                                <h6 class="mb-0 text-muted fw-light">{{Auth::user() -> role ?? 'none';}}</h6>
                            </div>
                            <div class="links position-absolute flex-column top-17 w-100 bg-white" id="links">
                                <a href="{{ route('page.profile') }}" class="text-decoration-none text-black">view profile</a>
                                <a href="{{route('logout')}}" class="text-decoration-none text-black">Logout</a>
                            </div>
                        </div>
                    </nav>
                </header>
                <main>
                    <form action="{{ route('user.update-profile') }}" method="post" class="p-4 gap-3 w-100 " enctype="multipart/form-data">
                        @csrf
                        <!-- profile image section -->
                        <div class="profile-image p-3  mb-3 d-flex flex-column align-items-center" style="min-width:280px;">
                            <img src="{{ optional(Auth::user()->nurses)->profile_image 
                        ? asset(optional(Auth::user()->nurses)->profile_image) 
                        : (optional(Auth::user()->staff)->profile_image 
                            ? asset(optional(Auth::user()->staff)->profile_image) 
                            : asset('images/default_profile.png')) }}" alt="profile picture" class="profile-section-image">
                            <h3 class="">{{optional(Auth::user() -> staff) -> full_name ?? optional(Auth::user() -> nurses) -> full_name ?? 'none'}}</h3>
                            <h5 class="mb-3 text-muted text-capitalize fw-normal">{{ optional(Auth::user()) -> role ?? 'none'}}</h5>
                            <div class="upload-image d-flex flex-column">
                                <label for="fileInput" class="btn mb-2 btn-success justify-self-center ">Update Profile</label>
                                <input type="file" name="profile_image" class="d-none w-100" id="fileInput" onchange="showFileName(this)">
                                <span id="fileName" class="text-center text-muted">No file choosen</span>
                                @error('profile_image')
                                <small class="text-danger">{{$message}}</small>
                                @enderror
                            </div>

                        </div>
                        <!-- USER INFORMATION -->
                        <div class="user-info flex-grow-1 card p-2 ">
                            <h4>Personal Info</h4>
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="{{ optional(Auth::user() -> staff) -> first_name ??
                                                                                                                                                optional(Auth::user() -> nurses) -> first_name ?? null }}">
                                    @error('first_name')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror

                                </div>
                                <div class="input-field w-50">
                                    <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="{{ optional(Auth::user() -> staff) -> middle_initial ??
                                                                                                                                                optional(Auth::user() -> nurses) -> middle_initial ?? null }}">
                                    @error('middle_initial')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror

                                </div>
                                <div class="input-field w-50">
                                    <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="{{ optional(Auth::user() -> staff) -> last_name ??
                                                                                                                                                optional(Auth::user() -> nurses) -> last_name ?? null }}">
                                    @error('last_name')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                            <!-- age -->
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <label for="age">Age</label>
                                    <input type="text" id="age" placeholder="20" class="form-control" name="age" value="{{ optional(Auth::user() -> staff) -> age ??
                                                                                                                          optional(Auth::user() -> nurses) -> age ?? null }}">
                                    @error('age')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <div class="input-field w-50">
                                    <label for="birthdate">Date of Birth</label>
                                    <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="{{ optional(Auth::user() -> staff) -> date_of_birth ??
                                                                                                                                                optional(Auth::user() -> nurses) -> date_of_birth ?? null }}">
                                    @error('date_of_birth')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <div class="input-field w-25">
                                    <label for="sex">Sex</label>
                                    <div class="input-field d-flex align-items-center p-2">
                                        @php
                                        $selectedSex = optional(Auth::user() -> staff) -> sex ?? optional(Auth::user() -> nurses) -> sex ?? 'none';
                                        @endphp
                                        <div class="sex-input d-flex align-items-center gap-1">
                                            <input type="radio" id="male" class="mb-0" name="sex" value="male" {{ $selectedSex === 'male'? 'checked' : '' }}>
                                            <label for="male" class="mb-0">Male</label>
                                            <input type="radio" id="female" class="mb-0" name="sex" value="female" {{ $selectedSex === 'female'? 'checked' : '' }}>
                                            <label for="female" class="mb-0">Female</label>
                                        </div>
                                        @error('sex')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- civil status, contact number, nationality -->
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <label for="civil_status" class="">Civil Status</label>
                                    <!-- to display the current status -->
                                    @php
                                    $civilStatus = optional(Auth::user()->staff)-> civil_status ?? optional(Auth::user()->nurse)->civil_status;
                                    @endphp

                                    <select name="civil_status" id="civil_status" class="form-select">
                                        <option value="single" {{ $civilStatus === 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ $civilStatus === 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorce" {{ $civilStatus === 'divorce' ? 'selected' : '' }}>Divorce</option>
                                    </select>
                                    @error('civil_status')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <!-- contact -->
                                <div class="input-field w-50">
                                    <label for="contact_number" class="">Contact Number</label>
                                    <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number" value="{{ optional(Auth::user() -> staff) -> contact_number ??
                                                                                                                                                optional(Auth::user() -> nurses) -> contact_number ?? null }}">
                                    @error('contact_number')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <div class="input-field w-50">
                                    <label for="nationality" class="">Nationality</label>
                                    <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality" value="{{ optional(Auth::user() -> staff) -> nationality ??
                                                                                                                                    optional(Auth::user() -> nurses) -> nationality ?? null }}">
                                    @error('nationality')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2 d-flex gap-1">
                                <!-- username -->
                                <div class="input-field w-50">
                                    <label for="username" class="">Username</label>
                                    <input type="text" placeholder="ex. yato" id="username" class="form-control" name="username" value="{{ optional(Auth::user()) -> username ?? null }}">
                                    @error('username')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <!-- email -->
                                <div class="input-field w-50">
                                    <label for="email" class="">Email</label>
                                    <input type="email" placeholder="ex. yato" id="email" class="form-control" name="email" value="{{ optional(Auth::user()) -> email  ?? null }}">
                                    @error('email')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                                <!-- password -->
                                <div class="input-field w-50">
                                    <label for="password" class="">Password</label>
                                    <input type="password" id="password" class="form-control" name="password">
                                    <small class="text-muted">Leave blank if you don't want to change it.</small>
                                    @error('password')
                                    <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                            @if(Auth::user()-> staff)
                            <div class="mb-2 d-flex align-items-center w-25 gap-2">
                                <label for="assigned_area" class="text-nowrap">Assigned Area:</label>
                                @php
                                $brgy_area = \App\Models\brgy_unit::OrderBy('brgy_unit')-> get();
                                @endphp
                                <select name="assigned_are" id="" class="form-select" disabled>
                                    @foreach($brgy_area as $area)
                                    <option value="{{$area -> id}}"> {{$area -> brgy_unit}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <!-- address -->
                            <div class="mb-2 d-flex gap-1 flex-column border-bottom">
                                <h4>Address</h4>
                                <div class="input-field d-flex gap-2">
                                    <div class="address w-50">
                                        <label for="" class="">Street & Lot</label>
                                        <input type="text" placeholder="Blk & Lot n Street" class="form-control flex-grow-1 bg-light lg py-2" name="street" value="{{ optional(Auth::user()) -> addresses -> street ?? 'null'}}">
                                        @error('street')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>

                                    <div class="postal w-50">
                                        <label for="postal">Postal Code</label>
                                        <input type="number" placeholder="0123" name="postal_code" class="form-control bg-light py-2 " value="{{ optional(Auth::user()) -> addresses -> postal_code ?? 'null'}}">
                                        @error('postal_code')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>

                                </div>
                                <div class="input-field d-flex gap-2">
                                    <!-- region -->
                                    <div class="mb-2 w-50">
                                        <label for="region">Region*</label>
                                        <select name="region" id="region" class="form-select bg-light" data-selected="{{ optional(Auth::user())-> addresses-> region_id }}">
                                            @php
                                            $region_id = optional(Auth::user()) -> addresses -> region_id;
                                            @endphp
                                            <option value="">Select a region</option>
                                            @foreach(\App\Models\region::orderBy('name')->get() as $r)
                                            <option value="{{$r -> code}}" {{ $region_id == $r -> code ? 'selected' : '' }}>{{ $r -> name}}</option>
                                            @endforeach
                                        </select>
                                        @error('region')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                    <!-- province -->
                                    <div class="mb-2 w-50">
                                        <label for="province">Province*</label>
                                        <select name="province" id="province" class="form-select bg-light" disabled data-selected="{{ optional(Auth::user())-> addresses-> province_id }}">
                                            <option value="">Select a province</option>
                                        </select>
                                        @error('province')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- city n brgy -->
                                <div class="input-field d-flex gap-2">
                                    <!-- city -->
                                    <div class="mb-2 w-50">
                                        <label for="city">City*</label>
                                        <select name="city" id="city" class="form-select bg-light" disabled data-selected="{{ optional(Auth::user())-> addresses-> city_id }}">
                                            <option value="">Select a city</option>
                                        </select>
                                        @error('city')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                    <!-- brgy -->
                                    <div class="mb-2 w-50">
                                        <label for="brgy">Barangay*</label>
                                        <select name="brgy" id="brgy" class="form-select bg-light" disabled data-selected="{{ optional(Auth::user())-> addresses-> brgy_id }}">
                                            <option value="">Select a brgy</option>
                                        </select>
                                        @error('brgy')
                                        <small class="text-danger">{{$message}}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!-- save button -->
                            <div class="mb-2 d-flex justify-content-end">
                                <input type="submit" value="Save" class="btn btn-success px-4 py-2 fs-5">
                            </div>
                        </div>
                    </form>
                </main>

            </div>
        </div>
    </div>

    @if($isActive)
    <script>
        // load all of the content first
        document.addEventListener('DOMContentLoaded', () => {
            const profileCon = document.getElementById('profile');

            if (profileCon) {
                profileCon.classList.add('active');
            }
        })
    </script>
    @endif

    <script>
        function showFileName(input) {
            const fileName = input.files.length ? input.files[0].name : "No file chosen";
            document.getElementById("fileName").textContent = fileName;
        }
    </script>
</body>

</html>