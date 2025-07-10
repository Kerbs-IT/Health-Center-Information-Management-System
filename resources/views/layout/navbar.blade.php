<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid ">
        <a class="navbar-brand d-flex gap-3 align-items-center h-100" href="#">
            <img src="{{ asset('images/hugo_perez_logo.png') }}" alt="" style="height: 40px;">
            <h3 class="mb-0">Health Center Information Mangement System</h3>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link fw-bold" href="{{ route('homepage') }}#home">Home</a></li>
                <li class="nav-item"><a class="nav-link text-color-danger fw-bold" href="{{ route('homepage')  }}#about">About</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="{{ route('homepage') }}#services">Services</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="{{ route('homepage') }}#specialist">Specialist</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="{{ route('homepage') }}#faq">FAQ</a></li>
                <li class="nav-item"><a class="nav-link fw-bold" href="{{ route('homepage')  }}#events">Events</a></li>
                <li class="nav-item"><a class="btn btn-primary fw-normal btn-success" href="{{ route('login') }}">Login</a></li>
            </ul>
        </div>
    </div>
</nav>