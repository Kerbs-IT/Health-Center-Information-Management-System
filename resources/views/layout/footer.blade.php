<footer class="bg-dark text-light pt-5 pb-3">
    <div class="container-fluid">
        <div class="row">

            {{-- Logo & Tagline --}}
            <div class="col-md-4 mb-4 home">
                <h5 class="text-info fw-bold">SmartHealth</h5>
                <p class="small">Empowering health centers with secure, digital record-keeping and smart tools.</p>
            </div>

            {{-- Navigation Links --}}
            <div class="col-md-4 mb-4">
                <h6 class="text-uppercase fw-bold">Navigation Links</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('homepage') }}#home" class="text-light text-decoration-none">Home</a></li>
                    <li><a href="{{ route('homepage') }}#about" class="text-light text-decoration-none">About</a></li>
                    <li><a href="{{ route('homepage') }}#services" class="text-light text-decoration-none">Services</a></li>
                    <li><a href="{{ route('homepage') }}#specialist" class="text-light text-decoration-none">Specialist</a></li>
                    <li><a href="{{ route('homepage') }}#faq" class="text-light text-decoration-none">FAQ</a></li>
                    <li><a href="{{ route('homepage') }}#events" class="text-light text-decoration-none">Events</a></li>
                </ul>
            </div>

            {{-- Contact Info --}}
            <div class="col-md-4 mb-4">
                <h6 class="text-uppercase fw-bold">Contact Information</h6>
                <p class="mb-1">Barangay Hugo Perez</p>
                <p class="mb-1">Trece Martires City, Cavite</p>
                <p class="mb-1">Phone: 0950-023-9450</p>
                <p class="mb-0">Email: hugoperezhc@gmail.com</p>
            </div>
        </div>

        <hr class="border-secondary">

        {{-- Developer Info & Copyright --}}
        <div class="d-flex flex-column flex-md-row justify-content-between text-center text-md-start">
            <p class="mb-0 small">Developed by BSIT Students, Cavite State University – Trece Martires</p>
            <p class="mb-0 small">Capstone Project 2025 ©</p>
        </div>
    </div>
</footer>