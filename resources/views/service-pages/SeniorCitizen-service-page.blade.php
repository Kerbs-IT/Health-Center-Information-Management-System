@extends('layout.app')

@section('content')
<section class="py-10 bg-light">
    <div class="container">

        <!-- TITLE -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-primary">Senior Citizen Services</h2>
            <p class="text-muted">Comprehensive healthcare programs designed to support our elderly community.</p>
        </div>

        <!-- MAIN CONTENT -->
        <div class="row g-4">

            <!-- WHAT WE OFFER -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 p-4">
                    <h4 class="fw-bold text-primary mb-3">What We Offer</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">✔ Regular Health Check-ups</li>
                        <li class="list-group-item">✔ Free Maintenance Medicine Dispensing</li>
                        <li class="list-group-item">✔ Vaccination Programs (Flu, Pneumonia, etc.)</li>
                        <li class="list-group-item">✔ Blood Pressure & Sugar Monitoring</li>
                        <li class="list-group-item">✔ Nutrition & Wellness Counseling</li>
                        <li class="list-group-item">✔ Priority Queuing for Services</li>
                    </ul>
                </div>

                <!-- SPECIAL PROGRAMS -->
                <div class="card shadow-sm border-0 p-4 mt-4">
                    <h4 class="fw-bold text-primary mb-3">Special Programs</h4>

                    <p><strong>1. Home Visit Check-Up</strong></p>
                    <p class="text-muted">
                        For senior citizens who are bedridden or unable to travel, our health workers conduct scheduled home visits.
                    </p>

                    <p><strong>2. Senior Wellness Monitoring</strong></p>
                    <p class="text-muted">
                        Monthly monitoring of vital signs and general health status to prevent complications.
                    </p>

                    <p><strong>3. Medication Assistance Program</strong></p>
                    <p class="text-muted">
                        Eligible seniors receive free or discounted maintenance medications from partner pharmacies.
                    </p>
                </div>
            </div>

            <!-- SIDE INFO -->
            <div class="col-lg-4">

                <!-- Contact Info -->
                <div class="card shadow-sm border-0 p-4 mb-4">
                    <h5 class="fw-bold text-primary">Contact Information</h5>
                    <p class="mb-1"><strong>Health Center:</strong> Barangay Hugo Perez Health Center</p>
                    <p class="mb-1"><strong>Office Hours:</strong> Mon–Fri, 8:00 AM – 5:00 PM</p>
                    <p class="mb-2"><strong>Contact Number:</strong> 0912-345-6789</p>
                    <a href="#" class="btn btn-primary btn-sm w-100">Contact Us</a>
                </div>

                <!-- Requirements -->
                <div class="card shadow-sm border-0 p-4">
                    <h5 class="fw-bold text-primary">Requirements</h5>
                    <ul>
                        <li>Senior Citizen ID</li>
                        <li>Valid ID (Government-issued)</li>
                        <li>Latest Medical Record (if any)</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- BACK BUTTON -->
        <div class="text-center mt-5">
            <a href="#" class="btn btn-outline-primary px-4">
                ← Back to Services
            </a>
        </div>

    </div>
</section>
@endsection
