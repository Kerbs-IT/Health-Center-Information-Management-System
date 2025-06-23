<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>
<body>
    @vite(['resources/css/app.css',
    'resources/js/app.js',
    'resources/js/menudropdown.js',
    'resources/js/header.js',
    'resources/css/healthWorker.css',
    'resources/js/healthWorker.js',
    'resources/css/profile.css',])
    @include('sweetalert::alert')
    <div class="ms-0 ps-0 d-flex w-100" style="height: 100vh;">
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
                        <h1 class="mb-0"> Health Worker</span></h1>
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
                <main class="m-3 overflow-y-auto max-h-[calc(100vh-100px)]">
                    <h2>Manage Health Workers</h2>
                    <div class="records">
                        <table class="table px-3">
                            <thead>
                                <th style="width: 10%;">No</th>
                                <th style="width: 25%;">Name</th>
                                <th style="width: 15%;" class="text-center">Contact Info</th>
                                <th style="width: 20%;" class="text-center">Designated Area</th>
                                <th style="width: 10%;" class="text-center">Action</th>
                            </thead>
                            <tbody>
                                <?php $count = 1;?>
                                @foreach($healthWorker as $worker)
                                    <tr class="align-middle">
                                        <td>{{$count}}</td>
                                        <td>
                                            <?php $image = $worker -> profile_image;?>
                                            <div class="d-flex gap-2 align-items-center">
                                                <img src="{{asset($image)}}" alt="health worker img" class="health-worker-img">
                                                <h5>{{$worker -> full_name}}</h5>
                                            </div>
                                        </td>
                                        <td class="h-100">
                                            <div class="d-flex align-items-center h-100 justify-content-center">
                                                <p class=" d-block mb-0">{{ optional($worker) -> contact_number ?? 'none'}}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center w-100 h-100 justify-content-center">
                                                <p class="mb-0 ">{{$worker -> assigned_area -> brgy_unit}}</p>
                                            </div></td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="#" class="remove-icon-con d-flex align-items-center justify-content-center" data-id = '{{ $worker -> user_id}}'>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="action-icon remove-icon"  viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" fill="red"/></svg>
                                                </a>
                                                <a href="#" class="edit-icon-con d-flex align-items-center justify-content-center edit-icon" data-id = '{{ $worker -> user_id}}'>
                                                    <svg xmlns="http://www.w3.org/2000/svg"  class="action-icon"  viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z" fill="#53c082"/></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $count++; ?>
                                @endforeach
                            </tbody>
                        </table>
                        {{$healthWorker -> links()}}
                    </div>
                </main> 
                
            </div>
        </div>
     </div>

     <div class="pop-up  w-100 h-100 d-none align-items-center justify-content-center" id="pop-up">
        <form action="" method="post" class="p-3 gap-3 w-50 d-flex opacity-[1]" enctype="multipart/form-data" id="profile-form">
                         @csrf
           <!-- profile image section -->
            <div class="profile-image p-1  mb-3 d-flex flex-column align-items-center" style="min-width:280px;">
                <img src="" alt="profile picture" class="profile-section-image" id="profile-image" >
                <h3 class=""></h3>
                <h5 class="mb-3 text-muted text-capitalize fw-normal" id="full_name"></h5>
                <div class="upload-image d-flex flex-column">
                    <label for="fileInput"class="btn mb-2 btn-success justify-self-center ">Update Profile</label>
                    <input type="file" name="profile_image" class="d-none w-100" id="fileInput" onchange="showFileName(this)">
                    <span id="fileName" class="text-center text-muted">No file choosen</span>
                    <small class="text-danger" id="image-error"></small>
                </div>
           </div>
                        <!-- USER INFORMATION -->
                         <div class="user-info flex-grow-1 ">
                            <h4>Personal Info</h4>
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <input type="text" id="first_name" placeholder="First Name" class="form-control" name="first_name" value="" >
                                    <small class="text-danger" id="fname-error"></small>
                                </div>
                                <div class="input-field w-50">
                                    <input type="text" id="middle_initial" placeholder="Middle Initial" class="form-control" name="middle_initial" value="" >
                                    <small class="text-danger" id="middle-initial-error"></small>                                                                                                                                    
                                </div>
                                <div class="input-field w-50">
                                    <input type="text" id="last_name" placeholder="Last Name" class="form-control" name="last_name" value="" >
                                    <small class="text-danger" id="lname-error"></small> 
                                </div>
                            </div>
                            <!-- age -->
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <label for="age">Age</label>
                                    <input type="text" id="age" placeholder="20" class="form-control" name="age" value="" >
                                    <small class="text-danger" id="age-error"></small>
                                </div>
                                <div class="input-field w-50">
                                    <label for="birthdate">Date of Birth</label>
                                    <input type="date" id="birthdate" placeholder="20" class="form-control w-100 px-5" name="date_of_birth" value="" >
                                    <small class="text-danger" id="birthdate-error"></small>
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
                                        <small class="text-danger" id="sex-error"></small>
                                    </div> 
                                </div>
                            </div>
                            <!-- civil status, contact number, nationality -->
                            <div class="mb-2 d-flex gap-1">
                                <div class="input-field w-50">
                                    <label for="civil_status"class="">Civil Status</label>
                                    <!-- to display the current status -->

                                    <select name="civil_status" id="civil_status" class="form-select">
                                        <option value="single" >Single</option>
                                        <option value="married" >Married</option>
                                        <option value="divorce" >Divorce</option>
                                    </select>
                                    <small class="text-danger" id="civil-status-error"></small>
                                </div>
                                <!-- contact -->
                                 <div class="input-field w-50">
                                    <label for="contact_number"class="">Contact Number</label>
                                    <input type="number" placeholder="+63-936-627-8671" class="form-control" id="contact_num" name="contact_number" value="">
                                    <small class="text-danger" id="contact-error"></small>
                                </div>
                                <div class="input-field w-50">
                                    <label for="nationality"class="">Nationality</label>
                                    <input type="text" placeholder="ex. Filipino" class="form-control" id="nationality" name="nationality" value="">
                                    <small class="text-danger" id="nationality-error"></small>
                                </div>
                            </div>
                            <div class="mb-2 d-flex gap-1">
                                <!-- username -->
                                <div class="input-field w-50">
                                    <label for="username"class="">Username</label>
                                    <input type="text" placeholder="ex. yato" id="username" class="form-control" name="username" value="">
                                    <small class="text-danger" id="username-error"></small>
                                </div>
                                <!-- email -->
                                 <div class="input-field w-50">
                                    <label for="email"class="">Email</label>
                                    <input type="email" placeholder="ex. yato" id="email" class="form-control" name="email" value="">
                                    <small class="text-danger" id="email-error"></small>
                                </div>
                                <!-- password -->
                                 <div class="input-field w-50">
                                    <label for="password"class="">Password</label>
                                    <input type="password" id="password" class="form-control" name="password">
                                    <small class="text-muted">Leave blank if you don't want to change it.</small>
                                    <small class="text-danger"></small>
                                </div>
                            </div>
                            <!-- address -->
                            <div class="mb-2 d-flex gap-1 flex-column">
                                <h4>Address</h4>
                                <div class="input-field d-flex gap-2">
                                    <input type="text" placeholder="Blk & Lot n Street" class="form-control py-0" name="street" id="blk_n_street" value="">
                                    <small class="text-danger" id="street-error"></small>
                                    <div class="postal">
                                        <label for="postal">Postal Code</label>
                                        <input type="number" placeholder="0123" name="postal_code" id="postal_code" class="form-control" value="">
                                        <small class="text-danger" id="postal-error"></small>
                                    </div>

                                </div>
                                <div class="input-field d-flex gap-2">
                                    <!-- region -->
                                    <div class="mb-2 w-50">
                                        <label for="region">Region*</label>
                                        <select name="region" id="region" class="form-select" data-selected="">
                                            <option value="">Select a region</option>
                                        </select>
                                        <small class="text-danger" id="region-error"></small>
                                    </div>
                                    <!-- province -->
                                     <div class="mb-2 w-50">
                                        <label for="province">Province*</label>
                                        <select name="province" id="province" class="form-select" disabled data-selected="">
                                            <option value="">Select a province</option>
                                        </select>
                                        <small class="text-danger" id="province-error"></small>
                                    </div>     
                                </div>

                                <!-- city n brgy -->
                                <div class="input-field d-flex gap-2">
                                    <!-- city -->
                                    <div class="mb-2 w-50">
                                        <label for="city">City*</label>
                                        <select name="city" id="city" class="form-select" disabled data-selected="">
                                            <option value="">Select a city</option>
                                        </select>
                                        <small class="text-danger" id="city-error"></small>
                                    </div>
                                    <!-- brgy -->
                                     <div class="mb-2 w-50">
                                        <label for="brgy">Barangay*</label>
                                        <select name="brgy" id="brgy" class="form-select" disabled data-selected="">
                                            <option value="">Select a brgy</option>
                                        </select>
                                        <small class="text-danger" id="brgy-error"></small>
                                    </div>     
                                </div>
                            </div>
                            <!-- save button -->
                            <div class="mb-2 d-flex justify-content-end gap-2">
                                <button class="btn btn-danger px-4" id="cancel-btn">Cancel</button>
                                <input type="submit" value="Save" class="btn btn-success px-4" id="submit-btn" data-user>
                            </div>
                         </div>
        </form>        
     </div>
     @if($isActive)
        <script>
            // load all of the content first
            document.addEventListener('DOMContentLoaded', () =>{
                const con = document.getElementById('healthWorker');

                if(con){
                    con.classList.add('active');
                }
            })

             function showFileName(input) {
                const fileName = input.files.length ? input.files[0].name : "No file chosen";
                document.getElementById("fileName").textContent = fileName;
            }
            
        </script>
    @endif


</body>
</html>