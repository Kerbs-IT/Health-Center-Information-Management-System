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

    <div class="ms-0 ps-0 d-flex w-100">
        <!-- aside contains the sidebar menu -->
        <div class="d-flex w-100">
            <aside >
                @include('layout.menuBar')
            </aside>
            <!-- the main content -->
             <!-- we use flex-grow-1 to take the remaining space of the right side -->
            <div class="flex-grow-1"> 
                <header class=" d-flex align-items-center px-3 " >
                    <nav class="d-flex justify-content-between align-items-center w-100 ">
                        <h1 class="mb-0">Profile</h1>
                        <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2" style="min-width: 150px;">
                            <img src="{{ optional(Auth::user()->nurses)->profile_image 
                        ? asset(optional(Auth::user()->nurses)->profile_image) 
                        : (optional(Auth::user()->staff)->profile_image 
                            ? asset(optional(Auth::user()->staff)->profile_image) 
                            : asset('images/default_profile.png')) }}" alt="profile picture" class="profile-img" id="profile_img">
                            <div class="username-n-role">
                                <h5 class="mb-0">{{ optional(Auth::user()->nurses)->full_name
                                                    ?? optional(Auth::user()->staff)->full_name
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
                    <form action="" method="post" class="p-4 gap-3 w-75">
                        <!-- profile image section -->
                         <div class="profile-image p-3  mb-3 d-flex flex-column align-items-center" style="min-width:280px;">
                            <img src="{{ optional(Auth::user()->nurses)->profile_image 
                        ? asset(optional(Auth::user()->nurses)->profile_image) 
                        : (optional(Auth::user()->staff)->profile_image 
                            ? asset(optional(Auth::user()->staff)->profile_image) 
                            : asset('images/default_profile.png')) }}" alt="profile picture" class="profile-section-image" >
                            <h3 class="">{{optional(Auth::user() -> staff) -> full_name ?? optional(Auth::user() -> nurse) ?? 'none'}}</h3>
                            <h5 class="mb-3 text-muted text-capitalize fw-normal">{{ optional(Auth::user()) -> role ?? 'none'}}</h5>
                            <div class="upload-image d-flex flex-column">
                                <label for="fileInput"class="btn mb-2 btn-success justify-self-center ">Update Profile</label>
                                <input type="file" name="profile_img" class="d-none w-100" id="fileInput" onchange="showFileName(this)">
                                <span id="fileName" class="text-center text-muted">No file choosen</span>
                            </div>
                           
                        </div>
                        <!-- USER INFORMATION -->
                         <div class="user-info flex-grow-1 ">
                            <h4>Personal Info</h4>
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name">
                                </div>
                                <div class="input-field w-50">
                                    <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial">
                                </div>
                                <div class="input-field w-50">
                                    <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name">
                                </div>
                            </div>
                            <!-- age -->
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <label for="age">Age</label>
                                    <input type="text" id="age" placeholder="20" class="form-control" name="age">
                                </div>
                                <div class="input-field w-50">
                                    <label for="birthdate">Date of Birth</label>
                                    <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth">
                                </div>
                                <div class="input-field w-25">
                                    <label for="sex">Sex</label>
                                    <div class="input-field d-flex align-items-center p-2">
                                        <div class="sex-input d-flex align-items-center gap-1">
                                            <input type="radio" id="male" class="mb-0" name="sex" value="male">
                                            <label for="male" class="mb-0">Male</label>
                                            <input type="radio" id="female" class="mb-0" name="sex" value="female">
                                            <label for="female" class="mb-0">Female</label>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <!-- civil status, contact number, nationality -->
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <label for="civil_status"class="">Civil Status</label>
                                    <select name="civil_status" id="civil_status" class="form-select ">
                                        <option value="single">Single</option>
                                        <option value="married">Married</option>
                                        <option value="divorce">Divorce</option>
                                    </select>
                                </div>
                                <!-- contact -->
                                 <div class="input-field w-50">
                                    <label for="contact_number"class="">Contact Number</label>
                                    <input type="number" placeholder="+63-936-627-8671" class="form-control" name="contact_number">
                                </div>
                                <div class="input-field w-50">
                                    <label for="nationality"class="">Nationality</label>
                                    <input type="text" placeholder="ex. Filipino" class="form-control" name="nationality">
                                </div>
                            </div>
                            <div class="mb-2 d-flex gap-1">
                                <!-- username -->
                                <div class="input-field w-50">
                                    <label for="username"class="">Username</label>
                                    <input type="text" placeholder="ex. yato" id="username" class="form-control" name="username">
                                </div>
                                <!-- email -->
                                 <div class="input-field w-50">
                                    <label for="email"class="">Email</label>
                                    <input type="email" placeholder="ex. yato" id="email" class="form-control" name="email">
                                </div>
                                <!-- password -->
                                 <div class="input-field w-50">
                                    <label for="password"class="">Password</label>
                                    <input type="password" placeholder="ex. yato" id="password" class="form-control" name="password">
                                </div>
                            </div>
                            <!-- address -->
                            <div class="mb-2 d-flex gap-1 flex-column">
                                <h4>Address</h4>
                                <div class="input-field">
                                    <input type="text" placeholder="Blk & Lot n Street" class="form-control py-2">
                                </div>
                                <div class="input-field d-flex gap-2">
                                    <!-- region -->
                                    <div class="mb-2 w-50">
                                        <label for="region">Region*</label>
                                        <select name="region" id="region" class="form-select">
                                            <option value="">Select a region</option>
                                            @foreach(\App\Models\region::orderBy('name')->get() as $r)
                                                <option value="{{$r -> code}}">{{ $r -> name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- province -->
                                     <div class="mb-2 w-50">
                                        <label for="province">Province*</label>
                                        <select name="province" id="province" class="form-select" disabled>
                                            <option value="">Select a province</option>
                                        </select>
                                    </div>     
                                </div>

                                <!-- city n brgy -->
                                <div class="input-field d-flex gap-2">
                                    <!-- city -->
                                    <div class="mb-2 w-50">
                                        <label for="city">City*</label>
                                        <select name="city" id="city" class="form-select" disabled>
                                            <option value="">Select a city</option>
                                        </select>
                                    </div>
                                    <!-- brgy -->
                                     <div class="mb-2 w-50">
                                        <label for="brgy">Barangay*</label>
                                        <select name="brgy" id="brgy" class="form-select" disabled>
                                            <option value="">Select a brgy</option>
                                        </select>
                                    </div>     
                                </div>
                            </div>
                            <!-- save button -->
                            <div class="mb-2 d-flex justify-content-end">
                                <input type="submit" value="Save" class="btn btn-success px-4">
                            </div>
                         </div>
                    </form>
                </main>

            </div>
        </div>
     </div>

     @if($isProfile)
        <script>
            // load all of the content first
            document.addEventListener('DOMContentLoaded', () =>{
                const profileCon = document.getElementById('profile');

                if(profileCon){
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