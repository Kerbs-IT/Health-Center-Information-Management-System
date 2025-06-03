<header class=" d-flex align-items-center px-3 " >
    <nav class="d-flex justify-content-between align-items-center w-100 ">
        <h1 class="mb-0">Welcome,<span>{{Auth::user() -> username ?? 'none'}}</span></h1>
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