@extends('layout.app')

@section('content')
<section class="head-section">
    <div class="head-overlay"></div>
    <div class="head-content text-center">
        <h1 class="fw-bold" style="color:white !important;">General Inforamation</h1>
        <p class="mt-2 fs-5">Detailed information about Prenatal Service</p>
    </div>
</section>
<section class="services py-10">
    <div class="container">
        <div class="row g-4">

            <!-- MAIN CONTENT -->
            <div class="col-lg-8">

                <!-- ABOUT TB DOTS -->
                <div class="card shadow-sm border-0 p-4">
                    <h4 class="fw-bold mb-3">About the Program</h4>
                    <p class="text-muted">
                        The TB DOTS program ensures that patients with tuberculosis are properly diagnosed, monitored, and treated.
                        Health workers supervise medication intake to guarantee successful recovery and prevent drug resistance.
                    </p>
                </div>

                <!-- SERVICES OFFERED -->
                <div class="card shadow-sm border-0 p-4 mt-4">
                    <h4 class="fw-bold mb-3">Services Offered</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">✔ Free TB Screening and Sputum Examination</li>
                        <li class="list-group-item">✔ Chest X-ray Referral</li>
                        <li class="list-group-item">✔ Directly Observed Treatment by Health Workers</li>
                        <li class="list-group-item">✔ Free TB Medicines for the Entire Treatment Cycle</li>
                        <li class="list-group-item">✔ Regular Progress Monitoring</li>
                        <li class="list-group-item">✔ Counseling and Health Education for Families</li>
                        <li class="list-group-item">✔ Home Visit Monitoring for Non-Compliant Patients</li>
                    </ul>
                </div>

                <!-- PROCESS -->
                <div class="card shadow-sm border-0 p-4 mt-4">
                    <h4 class="fw-bold mb-3">Treatment Process</h4>

                    <p><strong>1. Initial Screening</strong></p>
                    <p class="text-muted">z
                        Patients undergo an initial TB symptom check and sputum test or scheduled X-ray referral.
                    </p>

                    <p><strong>2. Treatment Enrollment</strong></p>
                    <p class="text-muted">
                        Qualified TB patients are enrolled in the DOTS program and assigned a treatment partner.
                    </p>

                    <p><strong>3. Supervised Medication</strong></p>
                    <p class="text-muted">
                        Health workers directly observe and record the patient’s medication intake daily or weekly.
                    </p>

                    <p><strong>4. Monitoring & Follow-Up</strong></p>
                    <p class="text-muted">
                        Patients are checked regularly for improvements, side effects, and treatment progress.
                    </p>

                    <p><strong>5. Treatment Completion</strong></p>
                    <p class="text-muted">
                        Once treatment is completed, patients undergo a final examination and receive discharge clearance.
                    </p>
                </div>

            </div>

            <!-- SIDE INFO -->
            <div class="col-lg-4">

                <!-- Contact Information -->
                <div class="card shadow-sm border-0 p-4 mb-4">
                    <h5 class=" contact-info-text fw-bold">Contact Information</h5>
                    <p class="mb-1"><strong>Health Center:</strong> Barangay Hugo Perez Health Center</p>
                    <p class="mb-1"><strong>TB DOTS Clinic Hours:</strong> Mon–Fri, 8:00 AM – 5:00 PM</p>
                    <p class="mb-2"><strong>Hotline:</strong> 0912-345-6789</p>
                    <a href="#" class="btn btn-sm w-100">Contact Us</a>
                </div>

                <!-- Requirements -->
                <div class="card shadow-sm border-0 p-4">
                    <h5 class="contact-info-text fw-bold">Requirements</h5>
                    <ul>
                        <li>Valid ID</li>
                        <li>Initial Health Screening Form</li>
                        <li>Sputum Sample (if required)</li>
                        <li>Chest X-ray Result (if available)</li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- BACK BUTTON -->
        <div class="text-end mt-5">
            <a href="#" class="btn px-4">
                ← Back to Services
            </a>
        </div>

    </div>
</section>
@endsection
