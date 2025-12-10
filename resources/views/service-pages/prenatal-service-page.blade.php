@extends('layout.app')

@section('content')

    <!-- Header / Banner -->
<!-- head SECTION -->
    <section class="head-section">
        <div class="head-overlay"></div>
        <div class="head-content text-center">
            <h1 class="fw-bold" style="color:white !important;">Prenatal Information</h1>
            <p class="mt-2 fs-5">Detailed information about Prenatal Service</p>
        </div>
    </section>

    <!-- Content -->
    <section class="max-w-5xl mx-auto p-6 md:p-10">

        <!-- About Prenatal Care -->
        <div class="mb-12">
            <h2 class="text-3xl font-semibold mb-4">What is Prenatal Care?</h2>
            <p class="leading-relaxed text-gray-700">
                Prenatal care is essential for monitoring the health and development of both the mother and the baby throughout the pregnancy. At the Barangay Hugo Perez Health Center, we provide comprehensive prenatal services to ensure safe pregnancy, detect any risks early, and guide mothers toward a healthy childbirth experience.
            </p>
        </div>

        <!-- Services Offered -->
        <div class="mb-12">
            <h2 class="text-3xl font-semibold mb-4">Services Offered</h2>

            <ul class="space-y-4 pl-5 text-gray-700 list-disc">
                <li>Regular prenatal check-ups</li>
                <li>Monitoring of fetal development and maternal health</li>
                <li>Blood pressure and weight monitoring</li>
                <li>Iron and vitamin supplementation</li>
                <li>Health education and pregnancy counseling</li>
                <li>Tetanus toxoid vaccination (TT1, TT2)</li>
                <li>Referral for ultrasound and laboratory tests (if needed)</li>
            </ul>
        </div>

        <!-- Importance of Prenatal Care -->
        <div class="mb-12">
            <h2 class="text-3xl font-semibold mb-4">Why Prenatal Care is Important</h2>
            <p class="leading-relaxed text-gray-700">
                Prenatal care helps identify potential complications early and provides mothers with the knowledge they need for a safe and healthy pregnancy. Regular check-ups ensure that both the mother and the baby are developing properly and that any issues are addressed promptly.
            </p>
        </div>

        <!-- Schedule -->
        <div class="mb-12">
            <h2 class="text-3xl font-semibold mb-4">Schedule of Prenatal Services</h2>
            <div class="bg-green-100 border-l-4 border-green-500 p-5 rounded-lg">
                <p class="text-gray-700">
                    <strong>Every Tuesday & Thursday</strong>
                    <br>
                    8:00 AM – 3:00 PM
                    <br>
                    At Barangay Hugo Perez Health Center
                </p>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="text-center mt-16">
            <h2 class="text-2xl font-semibold mb-3">Need More Information?</h2>
            <p class="mb-6 text-gray-700">
                Feel free to contact our health center staff for inquiries or appointments.
            </p>

            <a href="/contact"
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg">
                Contact Us
            </a>
        </div>

    </section>
@endsection