@extends('layout.app')

@section('content')
<div class="min-h-screen bg-light">
    <section class="head-section">
        <div class="head-overlay"></div>
        <div class="head-content text-center">
            <h1 class="fw-bold" style="color:white !important;">General Inforamation</h1>
            <p class="mt-2 fs-5">Detailed information about Prenatal Service</p>
        </div>
    </section>
    <div class="container mt-5">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-6xl mx-auto">

        <!-- COLUMN 1: IMAGE -->
        <div class="flex justify-center">
            <img
                src="{{ asset('images/hiv_testing.jpg') }}"
                class="rounded-2xl w-full max-w-md shadow object-cover"
                alt="General Consultation">
        </div>

        <!-- COLUMN 2: TEXT -->
        <div>
            <div class="card p-3 ">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">What is General Consultation?</h2>
                <p class="text-gray-700 leading-relaxed mb-6">
                    General consultation is the primary healthcare service available at the health center.
                    Patients can visit for medical checkups, diagnosis, and professional advice regarding
                    common health issues and concerns.
                </p>
            </div>
            <div class="card p-3 ">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Services Included</h2>
                <ul class="space-y-3 text-gray-700 mb-6">
                    <li class="flex items-start gap-2"><span class="text-pink-600">•</span> Health assessment</li>
                    <li class="flex items-start gap-2"><span class="text-pink-600">•</span> Blood pressure monitoring</li>
                    <li class="flex items-start gap-2"><span class="text-pink-600">•</span> Diagnosis for common illnesses</li>
                    <li class="flex items-start gap-2"><span class="text-pink-600">•</span> Medication guidance</li>
                    <li class="flex items-start gap-2"><span class="text-pink-600">•</span> Health counseling</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection