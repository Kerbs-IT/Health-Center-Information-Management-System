<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container-fluid d-flex align-items-center justify-content-between">

    <!-- 🔹 Left: Toggler + Brand -->
    <div class="d-flex align-items-center">
        <!-- Hamburger -->
      <button class="navbar-toggler custom-toggler me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="toggler-line"></span>
      <span class="toggler-line"></span>
      <span class="toggler-line"></span>
      </button>


        <!-- Brand -->
      <a class="navbar-brand d-flex gap-2 align-items-center h-100 ms-5" href="#">
          <img src="{{ asset('images/hugo_perez_logo.png') }}" alt="Logo" style="height: 40px;">
          <h3 class="mb-0 fs-5 d-none d-md-block">Health Center IMS</h3>
      </a>
    </div>

    <!-- 🔹 Center: Nav links (collapsible) -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link fw-bold fs-5 me-2" href="{{ route('homepage') }}#home">Home</a></li>
        <li class="nav-item"><a class="nav-link fw-bold fs-5 me-2" href="{{ route('homepage') }}#about">About</a></li>
        <li class="nav-item"><a class="nav-link fw-bold fs-5 me-2" href="{{ route('homepage') }}#services">Services</a></li>
        <li class="nav-item"><a class="nav-link fw-bold fs-5 me-2" href="{{ route('homepage') }}#specialist">Specialist</a></li>
        <li class="nav-item"><a class="nav-link fw-bold fs-5 me-2" href="{{ route('homepage') }}#faq">FAQ</a></li>
        <li class="nav-item me-5"><a class="nav-link fw-bold fs-5 me-2" href="{{ route('homepage') }}#events">Events</a></li>
      </ul>
    </div>

    <!-- 🔹 Right: Always visible Login -->
      <div class="ms-5 me-5 d-flex">
        <a class="btn btn-success fw-normal ms-lg-3" href="{{ route('login') }}">Login</a>
      </div>
  </div>
</nav>


