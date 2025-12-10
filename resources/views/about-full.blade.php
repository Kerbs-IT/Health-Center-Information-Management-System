@extends('layout.app')

@section('content')
  <div class="about-full bg-gray-50 text-gray-800">

    <!-- Hero Section -->
    <section class="about-bg relative py-20">
      <div class="max-w-6xl mx-auto px-6 text-center">
        <h2 class="text-4xl font-bold text-[#b31252] mb-4">About Our Health Center</h2>
        <p class="text-lg text-gray-600 max-w-3xl mx-auto">
          Dedicated to providing accessible, quality, and compassionate healthcare services
          for the residents of Barangay Hugo Perez, Trece Martires City.
        </p>
      </div>
    </section>

    <!-- About Section -->
    <section class="py-16">
      <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">

        <div>
          <h3 class="text-3xl font-bold text-[#d6336c] mb-4">Who We Are</h3>
          <p class="text-gray-700 leading-relaxed">
            The **Barangay Hugo Perez Health Center** is committed to delivering essential
            primary healthcare services to our growing community. As a frontline health
            facility, we focus on ensuring that every resident has access to professional,
            organized, and timely medical care.
          </p>
          <p class="text-gray-700 mt-4 leading-relaxed">
            Our health center supports various public health programs such as maternal and
            child care, immunization, family planning, communicable disease control, and
            health education. Guided by compassion and dedication, our healthcare team strives
            to maintain the well-being of every household in our barangay.
          </p>
        </div>

        <div>
          <img src="{{ asset('images/hugo_perez.jpg') }}"
              class="rounded-xl shadow-md w-full object-cover" />
        </div>

      </div>
    </section>

    <!-- Mission, Vision & Core Values -->
    <section class="bg-white py-16">
      <div class="max-w-6xl mx-auto px-6">

        <div class="grid md:grid-cols-3 gap-10">

          <!-- Mission -->
          <div class="bg-card p-8 rounded-xl shadow hover:shadow-lg transition">
            <h3 class="text-2xl font-bold text-[#d6336c] mb-3">Our Mission</h3>
            <p class="text-gray-700 leading-relaxed">
              To provide high-quality, affordable, and community-centered healthcare services
              that promote wellness, prevent illness, and ensure a safe and healthy environment
              for all residents of Barangay Hugo Perez.
            </p>
          </div>

          <!-- Vision -->
          <div class="bg-card p-8 rounded-xl shadow hover:shadow-lg transition">
            <h3 class="text-2xl font-bold text-[#d6336c] mb-3">Our Vision</h3>
            <p class="text-gray-700 leading-relaxed">
              A healthy, empowered, and resilient community where every family has access to
              responsive, efficient, and compassionate healthcare services.
            </p>
          </div>

          <!-- Core Values -->
          <div class="bg-card p-8 rounded-xl shadow hover:shadow-lg transition">
            <h3 class="text-2xl font-bold text-[#d6336c] mb-3">Core Values</h3>
            <ul class="list-disc ml-5 text-gray-700 leading-relaxed space-y-2">
              <li>Compassion</li>
              <li>Integrity</li>
              <li>Service Excellence</li>
              <li>Respect for All</li>
              <li>Community Commitment</li>
              <li>Professionalism</li>
            </ul>
          </div>

        </div>
      </div>
    </section>
  </div>
@endsection

