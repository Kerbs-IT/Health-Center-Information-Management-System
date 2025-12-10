@extends('layout.app')


@section('content')
    <!-- Header / Banner -->
<section class="head-section">
    <div class="head-overlay"></div>
    <div class="head-content text-center">
        <h1 class="fw-bold" style="color:white !important;">Prenatal Information</h1>
        <p class="mt-2 fs-5">Detailed information about Family Planning Service</p>
    </div>
</section>


<!-- Content -->
<section class="max-w-5xl mx-auto p-6 md:p-10">

    <!-- What is Family Planning -->
    <div class="mb-12">
        <h2 class="text-3xl font-semibold mb-4">What is Family Planning?</h2>
        <p class="leading-relaxed text-gray-700">
            Family planning helps individuals and couples decide the number of children they want and the spacing between pregnancies. Our health center provides safe, accessible, and confidential services to guide families in making informed decisions that support overall well-being.
        </p>
    </div>

    <!-- Services Offered -->
    <div class="mb-12">
        <h2 class="text-3xl font-semibold mb-4">Services Offered</h2>

        <ul class="space-y-4 pl-5 text-gray-700 list-disc">
            <li>Family planning counseling and education</li>
            <li>Provision of contraceptives (pills, condoms, injectables)</li>
            <li>Birth spacing guidance</li>
            <li>Fertility awareness education (calendar method, BBT, cervical mucus monitoring)</li>
            <li>Assessment before choosing a family planning method</li>
            <li>Follow-up consultations</li>
            <li>Referrals for IUD, implants, and other advanced methods if needed</li>
        </ul>
    </div>

    <!-- Importance -->
    <div class="mb-12">
        <h2 class="text-3xl font-semibold mb-4">Why Family Planning Matters</h2>
        <p class="leading-relaxed text-gray-700">
            Family planning helps promote healthy pregnancies, prevents risks related to unplanned births, and supports financial and emotional stability in families. It empowers couples to make responsible decisions for their future and their children’s well-being.
        </p>
    </div>

    <!-- Available Methods -->
    <div class="mb-12">
        <h2 class="text-3xl font-semibold mb-4">Available Family Planning Methods</h2>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="font-semibold text-xl mb-2">Natural Methods</h3>
                <p class="text-gray-700">
                    Calendar method, withdrawal, basal body temperature monitoring, and cervical mucus assessment.
                </p>
            </div>

            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="font-semibold text-xl mb-2">Barrier Methods</h3>
                <p class="text-gray-700">Condoms and diaphragms (availability may vary).</p>
            </div>

            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="font-semibold text-xl mb-2">Hormonal Methods</h3>
                <p class="text-gray-700">
                    Birth control pills, injectables (Depo-Provera), and hormonal implants (via referral).
                </p>
            </div>

            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="font-semibold text-xl mb-2">Long-Term Methods</h3>
                <p class="text-gray-700">
                    IUD, implants, and permanent methods such as vasectomy or tubal ligation (through referral hospitals).
                </p>
            </div>
        </div>
    </div>

    <!-- Schedule -->
    <div class="mb-12">
        <h2 class="text-3xl font-semibold mb-4">Schedule of Family Planning Services</h2>

        <div class="bg-green-100 border-l-4 border-green-500 p-5 rounded-lg">
            <p class="text-gray-700">
                <strong>Every Monday & Wednesday</strong>
                <br>
                8:00 AM – 3:00 PM
                <br>
                At Barangay Hugo Perez Health Center
            </p>
        </div>
    </div>

    <!-- Contact -->
    <div class="text-center mt-16">
        <h2 class="text-2xl font-semibold mb-3">Need Assistance?</h2>
        <p class="mb-6 text-gray-700">
            Our healthcare workers are ready to provide guidance and support.
        </p>

        <a href="/contact"
            class="inline-block bg-gren-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg">
            Contact Us
        </a>
    </div>

</section>
@endsection