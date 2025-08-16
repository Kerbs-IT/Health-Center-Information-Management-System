<header class=" d-flex align-items-center px-3 ">
  <nav class="d-flex justify-content-between align-items-center w-100 ">
    <div class="box d-flex gap-3 align-items-center justify-content-center">
      <button>
        <svg xmlns="http://www.w3.org/2000/svg" class="icons" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
          <path d="M0 96C0 78.3 14.3 64 32 64l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 128C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32L32 448c-17.7 0-32-14.3-32-32s14.3-32 32-32l384 0c17.7 0 32 14.3 32 32z" />
        </svg>
      </button>
      @if ($page === 'DASHBOARD')
      <h1 class="mb-0">Welcome, <span>{{ Auth::user()->username ?? 'Guest' }}</span></h1>
      @else
      <h1 class="mb-0">{{ $page }}</h1>
      @endif
    </div>
    <div class="right-info d-flex align-items-center justify-content-center gap-3">
      <button type="button" class="btn position-relative p-0 border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#notificationModal">
        <!-- Bell SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
          class="bi bi-bell" viewBox="0 0 16 16">
          <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2z" />
          <path d="M8 1a4 4 0 0 0-4 4c0 1.098-.354 2.5-.975 3.5-.356.596-.525 1.057-.525 1.5h11c0-.443-.169-.904-.525-1.5C12.354 7.5 12 6.098 12 5a4 4 0 0 0-4-4z" />
        </svg>

        <!-- Red Dot Badge -->
        <span style="
                    position: absolute;
                    top: 2px;
                    right: 2px;
                    width: 10px;
                    height: 10px;
                    background-color: red;
                    border-radius: 50%;
                    border: 2px solid white;">
        </span>
      </button>
      @php
      $profileImage = null;

      if (optional(Auth::user()->nurses)->profile_image) {
      $profileImage = asset(Auth::user()->nurses->profile_image);
      } elseif (optional(Auth::user()->staff)->profile_image) {
      $profileImage = asset(Auth::user()->staff->profile_image);
      } elseif (optional(Auth::user()->patient)->profile_image) {
      $profileImage = asset(Auth::user()->patient->profile_image);
      } else {
      $profileImage = asset('images/profile_images/default_profile.png');
      }
      @endphp
      <div class="profile-con position-relative justify-content-space d-flex align-items-center gap-2" style="min-width: 150px;">
        <img src="{{ $profileImage }}" alt=" profile picture" class="profile-img" id="profile_img">
        <div class="username-n-role">
          <h5 class="mb-0">{{ optional(Auth::user()->nurses)->full_name
                                        ?? optional(Auth::user()->staff)->full_name
                                        ?? optional(Auth::user()->patient)->full_name ?? 'none' }}</h5>
          <h6 class="mb-0 text-muted fw-light">{{Auth::user() -> role ?? 'none';}}</h6>
        </div>
        <div class="links position-absolute z-index flex-column top-17 w-100 bg-white" id="links" style="z-index: 9999;">
          <a href="{{ route('page.profile') }}" class="text-decoration-none text-black">view profile</a>
          <a href="{{route('logout')}}" class="text-decoration-none text-black" id="headerLogOut">Logout</a>
        </div>
      </div>
    </div>
  </nav>
</header>

<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- slide-in from right -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Vaccination Notification -->
        <div class="alert alert-info mb-3 d-flex justify-content-between align-items-center">
          <span>💉 <strong>Scheduled vaccination list is updated.</strong></span>
          <a href="#" class="btn btn-sm btn-primary">Visit</a>
        </div>

        <!-- Prenatal Notification -->
        <div class="alert alert-warning d-flex justify-content-between align-items-center">
          <span>🤰 <strong>New prenatal checkup list is available.</strong></span>
          <a href="#" class="btn btn-sm btn-warning text-white">Visit</a>
        </div>
      </div>
    </div>
  </div>
</div>