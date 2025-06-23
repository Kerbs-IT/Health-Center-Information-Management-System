<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/hugoperez_logo.png'); }}">
    <title>Health Center Information Management System</title>
</head>
<body class="">
    @include('sweetalert::alert')

    @vite(['resources/css/app.css', 'resources/js/register.js'])


    <main class="d-flex align-items-center justify-content-center vh-100  ">
        <!-- login form -->
        <form action="{{ route('user.store') }}" method="POST" class="rounded shadow d-flex flex-column align-items-center p-4  w-sm-25 w-md-50 w-lg-25 bg-white" >
            @csrf
            <h1 class="text-center fs-1 fw-bold">Register</h1>

            <!-- username -->
            <div class="mb-2 w-100">
                <label for="username" class="mb-1 h4 fs-4">Username:</label>
                <input type="text" placeholder="Enter your username" name="username" class="py-2 px-2 w-100 fs-5 bg-light" autocomplete="off">
                @error('username')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <!-- full name -->
            <div class="mb-2 w-100">
                <label for="" class="mb-1 h4 fs-4">Personal Info:</label>
                <div class="gap-2 d-flex justify-content-center">
                    <input type="text" placeholder="First Name" name="first_name" class="py-2 px-2 fs-5 bg-light " autocomplete="off" style="width:200px;">
                    <input type="text" placeholder="Middle Initial" name="middle_initial" class="py-2 px-2 fs-5 bg-light " autocomplete="off" style="width:200px;">
                    <input type="text" placeholder="Last Name" name="last_name" class="py-2 px-2 fs-5 bg-light " autocomplete="off" style="width:200px;">
                </div>
                @error('first_name')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                @error('middle_initial')
                    <small class="text-danger">{{$message}}</small>
                @enderror
                @error('last_name')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <!-- email -->
            <div class="mb-2 w-100">
                <label for="email" class="mb-1 h4 fs-4">Email:</label>
                <input type="email" placeholder="Enter your email" name="email" class="py-2 px-2 w-100 fs-5 bg-light">
                @error('email')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <!-- Password -->
            <div class="mb-3 w-100">
                <label for="password" class="mb-1 h4 fs-4 w-100">Password:</label>
                <div class="input-pass d-flex align-items-center">
                    <input type="password" placeholder="Enter your password" name="password" class="py-2 px-2 w-100 fs-5 bg-light" id="password" autocomplete="off">
                    <i class="fa-solid fa-eye p-3 bg-primary text-white" id="eye-icon"></i>
                </div>
                @error('password')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>
            <!-- retype pass -->
            <div class="mb-3 w-100">
                <label for="re-type-pass" class="mb-1 font-weight-normal h4">Retype password:</label>
                <div class="input-pass d-flex align-items-center">
                     <input type="password" placeholder="Re-type-pass" name="password_confirmation" class="py-2 px-2 w-100 fs-5 bg-light" id="re-type-pass">
                     <i class="fa-solid fa-eye p-3 bg-primary text-white" id="Retype-eye-icon"></i>
                </div>
               
                @error('password_confirmation')
                    <small class="text-danger">{{$message}}</small>
                @enderror
            </div>


            <!-- hidden input role -->
             <input type="text" name="role" value="staff" hidden>

             <!-- STAFF -->
             <div class="mb-3 w-100 staff-fields"  id="staff-fields">
                <div class="mb-3 w-100 d-flex gap-2">
                    <div class="input-group w-100 d-flex gap-2 align-items-center">
                        <label for="role" class="h4 fs-4 m-0">Assigned Area:</label>
                        <select name="assigned_area" id="assigned_area" class="border py-2 px-3">
                            <option value="">Select an Area</option>
                        
                        </select>
                        @error('assigned_area')
                            <small class="text-danger">{{$message}}</small>
                        @enderror
                    </div>
                </div> 
             </div>
             <!-- RECOVERY QUESTION -->
              <div class="mb-3 w-100">
                    <div class="input-group w-100">
                        <label for="recovery_question" class="fs-4 fw-bold w-100">Recovery Question:</label>
                        <select name="recovery_question" id="recovery_question" class="form-select w-75 mb-2" required>
                            <option value="">Select a question</option>
                            <option value="1">What is your nickname? </option>
                            <option value="2">What is the ame of your mother?</option>
                            <option value="3">What is the name of your pet? </option>
                        </select>
                        <input type="text" name="recovery_answer" placeholder="Enter your answer" class="form-control w-100" required>
                    </div>
              </div>
          

            <div class="mb-3 w-95">
                <input type="submit" value="Register" class="d-block btn btn-success py-1 m-auto fw-bold fs-5">
            </div>

            <div class="mb-3 w-95">
                <p class="text-center">Already have an account? <a href="{{route('login')}}">Sign-in</a></p>
            </div>

        </form>
    </main>
    @if(session('reg_success'))
        <script>
            setTimeout(() =>{
                window.location.href = "{{ route('login') }}";
            },2000);
        </script>
    @endif

    <script>
        const eyeIcon = document.getElementById('eye-icon');
        const password = document.getElementById('password');

        // retype
        const RetypeeyeIcon = document.getElementById('Retype-eye-icon');
        const Retypepassword = document.getElementById('re-type-pass');


        function passwordToggle(eyeIcon,passwordInput){
            eyeIcon.addEventListener('mousedown', () =>{
                passwordInput.type = 'text';
            })
            eyeIcon.addEventListener('mouseup', () =>{
                passwordInput.type = 'password';
            })

            eyeIcon.addEventListener('mouseout',() =>{
                passwordInput.type = 'password';
            })
        }

        passwordToggle(eyeIcon,password);
        passwordToggle(RetypeeyeIcon,Retypepassword);

      
    </script>
</body>
</html>