@extends('layout.app')


@section('content')
<!-- head SECTION -->
<section class="head-section">
    <div class="head-overlay"></div>
    <div class="head-content text-center">
        <h1 class="fw-bold">Vaccine Information</h1>
        <p class="mt-2 fs-5">Detailed information about this vaccine</p>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card info-card p-4">
                <h2 class="fw-bold mb-3">Vaccine Name</h2>

                <span class="badge badge-custom mb-3">Recommended for all ages</span>

                <p class="text-secondary lh-lg">
                    This section will include a detailed explanation about the vaccine. You can include details about how it works, what it protects against, and why it is important for public health. Provide relevant medical facts written in simple and easy‑to‑understand language.
                </p>

                <h4 class="mt-4 fw-bold">Dosage & Schedule</h4>
                <ul class="mt-2 text-secondary">
                    <li>Initial dose recommended at birth or according to schedule.</li>
                    <li>Follow-up booster doses as needed.</li>
                    <li>Consult your healthcare provider for personalized schedule.</li>
                </ul>

                <h4 class="mt-4 fw-bold">Possible Side Effects</h4>
                <p class="text-secondary">Common temporary side effects may include:</p>
                <ul class="text-secondary">
                    <li>Mild fever</li>
                    <li>Pain/swelling at injection site</li>
                    <li>Fatigue or irritability</li>
                </ul>
            </div>
        </div>

        <!-- SIDE INFO CARD -->
        <div class="col-lg-4">
            <div class="card p-4 info-card">
                <h5 class="fw-bold">General Information</h5>
                <hr>

                <p class="mb-1"><strong>Category:</strong> Required Immunization</p>
                <p class="mb-1"><strong>Age Group:</strong> Children / Adults</p>
                <p class="mb-1"><strong>Availability:</strong> Available in Health Center</p>
                <p class="mb-1"><strong>Last Updated:</strong> Nov 2025</p>

                <a href="#" class="btn vaccine-btn btn-success w-100 mt-3">Book Vaccination</a>
            </div>
        </div>
    </div>
</div>
@endsection
