@extends('layout.app')

@section('content')
<section class="hero-section" id="home">
        <!-- Swiper Background Carousel -->
        <div class="swiper mySwiperBackground">
            <div class="swiper-wrapper">
            <div class="swiper-slide bg1"></div>
            <div class="swiper-slide bg2"></div>
            <div class="swiper-slide bg3"></div>
            </div>
        </div>

        <!-- HERO CONTENT -->
        <div class="hero-content w-100">
            <h1 class="fw-bold text-light mb-5 text-wrap">Bringing Better <br><span class="" style="color: #4CAF50;">Healthcare to the Barangay</span><br> – Digitally</h1>
            <p class="text-light mt-5  w-4/5 m-auto">Streamline records, monitor vaccinations, and improve decision-making with our Healthcare Information System for Barangay Hugo Perez.</p>
            <a href="#" class="hero-btn">Join us Now</a>
        </div>

</section>

{{-- Home Section --}}
<!-- <section id="home" class=" home-section fullscreen-section d-flex align-items-center justify-content-center text-white">
    <div class="container text-center d-flex justify-content-center">
        <div class="col-lg-9">
            <h1 class="display-5 fw-bold text-light mb-5" style="font-size:60px;">Bringing Better <span class="" style="color: #4CAF50;font-size:60px;">Healthcare to the Barangay</span> – Digitally</h1>
            <p class="lead text-light mt-5" style="font-size: 2.3rem;">Streamline records, monitor vaccinations, and improve decision-making with our Healthcare Information System for Barangay Hugo Perez.</p>
            <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#viewMoreModal">View More</button>
        </div>
    </div>
</section> -->
<!-- Modal -->
<div class="modal fade" id="viewMoreModal" tabindex="-1" aria-labelledby="viewMoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-success" style="border: 4px solid #4CAF50;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="viewMoreModalLabel">Healthcare Information System</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('images/hugo_perez.jpg') }}" alt="System Overview" class="img-fluid rounded mb-3" style="max-height: 400px;">
                <p class="">Our Healthcare Information System streamlines patient records, tracks vaccinations, and empowers better health services in Barangay Hugo Perez.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- About Section -->
<section class="about-section min-h-80 mb-1 mb-md-5 w-full mt-1 mt-md-5 pt-0 pt-md-5" id="about">
    <h2 class="text-center mt-5 md:mb-5 mb-0 about-header">About Our Health Center</h2>
    <div class="min-h-80 flex flex-col lg:flex-row md:gap-10 gap-3 p-0 p-md-5 px-4  md:m-5 m-0 w-full mt-5">
        <div class="flex flex-column flex-1 order-2 order-md-1">
            <div>
                <h4 class="about-sub-heading fw-bold text-center">Empowering <span>Healthcare Workers</span></h4>
                <h5 class="text-info text-center">Enhancing Community Wellness</h5>
                <p class="about-content text-justify md:mb-5 mb-0">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi ullam beatae, officiis iste dolore, quis, odio deleniti pariatur assumenda eaque saepe inventore. Recusandae, repellat cumque quisquam voluptate in tenetur commodi.
                    Animi vitae doloremque quod quam alias possimus ratione libero laboriosam fuga nostrum soluta doloribus obcaecati magnam, earum fugiat odio nulla nobis dolores, a vero necessitatibus ducimus quisquam sapiente! Recusandae, itaque?
                    Dignissimos ratione explicabo eligendi quaerat eveniet quasi. Ea quasi facere natus magnam sequi! Animi dicta voluptates iste. Hic esse nemo incidunt repellendus numquam sunt, sint, nesciunt debitis cum suscipit voluptas.
                </p>
            </div>
            <div class="bottom-content flex flex-col md:flex-row items-center justify-center md:justify-between md:mt-5 mt-1 w-full">

                <div class="social-icons flex items-center gap-1 md:gap-3">
                    <a href=""><i class="fa-brands fa-facebook"></i></a>
                    <a href=""><i class="fa-brands fa-x-twitter"></i></a>
                    <a href=""><i class="fa-solid fa-envelope"></i></a>
                    <a href=""><i class="fa-brands fa-instagram"></i></a>
                    <a href=""><i class="fa-brands fa-youtube"></i></a>
                </div>

                <div class="mt-3 md:mt-0">
                    <a href="{{ route('about.full') }}" class="btn btn-success">See More</a>
                </div>

            </div>

        </div>
        <div class="flex-1 order-1 order-md-2 hover:scale-105 transition-transform duration-300 object-cover hover:shadow-lg">
            <img src="{{ asset('images/consult.jpg') }}"  class="rounded-md drop-shadow-md w-100 h-auto block"  alt="">
        </div>
    </div>
</section>

<!-- Services -->
<section class="services fullscreen-section py-5" id="services" style="background-color: #EBF9FF;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <span class="badge rounded-pill text-success border border-success px-3 py-2 fs-6">Our Health Services</span>
            <h2 class="mt-3 fw-semibold">
                Reliable, community-focused <strong class="text-dark">Healthcare made efficient</strong><br>
                through digital innovation.
            </h2>
        </div>
        <div class="row g-4 justify-content-center">
            <!-- Vaccination Services -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="bg-success text-white rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-syringe" style="font-size: 40px;"></i>
                        </div>
                        <h5 class="card-title fw-bold">Vaccination Services</h5>
                        <p class="card-text">Track, schedule, and manage vaccine records digitally.</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='{{ route('vaccine-service')}}'">Read More</button>
                    </div>
                </div>
            </div>

            <!-- Prenatal Care -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="bg-success text-white rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-person-pregnant" style="font-size: 45px;"></i>
                        </div>
                        <h5 class="card-title fw-bold">Prenatal Care</h5>
                        <p class="card-text">Monitor maternal health and schedule regular checkups.</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='{{ route('prenatal-service') }}'">Read More</button>
                    </div>
                </div>
            </div>

            <!-- Family Planning -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="bg-success text-white rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <h5 class="card-title fw-bold">Family Planning</h5>
                        <p class="card-text">Confidential and well-managed reproductive health support.</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='{{ route('familyPlanning-service') }}'">Read More</button>
                    </div>
                </div>
            </div>
            <!-- Senior Citizen -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="bg-success text-white rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-person-cane" style="font-size: 40px;"></i>
                        </div>
                        <h5 class="card-title fw-bold">Senior Citizen</h5>
                        <p class="card-text">Confidential and well-managed reproductive health support.</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='{{ route('seniorCitizen-service') }}'">Read More</button>
                    </div>
                </div>
            </div>
            <!-- TB Dots -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="bg-success text-white rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-lungs" style="font-size: 40px;"></i>
                        </div>
                        <h5 class="card-title fw-bold">TB Dots</h5>
                        <p class="card-text">Confidential and well-managed reproductive health support.</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='{{ route('tbDots-service') }}'">Read More</button>
                    </div>
                </div>
            </div>
            <!-- General Consultation -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="bg-success text-white rounded d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-stethoscope" style="font-size: 40px;"></i>
                        </div>
                        <h5 class="card-title fw-bold">General Consultation</h5>
                        <p class="card-text">Confidential and well-managed reproductive health support.</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="window.location.href='{{ route('generalConsultation-service') }}'">Read More</button>
                    </div>
                </div>
            </div>
            <!-- Add more services here as needed -->

        </div>
    </div>
    <!-- Vaccination Modal -->
    <div class="modal fade" id="vaccinationModal" tabindex="-1" aria-labelledby="vaccinationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title fw-bold" id="vaccinationModalLabel">Vaccination Services</h5>
                    <button type="button" class="btn-close text-danger  " data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/immunization1.jpg') }}" class="img-fluid mb-3" alt="Vaccination">
                    <p>Our Vaccination Services ensure timely immunizations with digital records and reminders for every patient. We are committed to safeguarding community health through accessible and reliable vaccination programs.
                        Our health center provides a complete and accessible vaccination program for individuals of all ages. We administer vaccines according to the Department of Health’s immunization schedule, including essential childhood vaccines such as BCG, DPT, OPV, and MMR, as well as adult vaccines like flu and COVID-19 boosters. These services are crucial in protecting the community from infectious diseases and maintaining herd immunity. Our trained healthcare providers ensure that all vaccinations are conducted safely, with proper record-keeping and post-vaccination care, especially for infants, school-aged children, and senior citizens.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Prenatal Care Modal -->
    <div class="modal fade" id="prenatalModal" tabindex="-1" aria-labelledby="prenatalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title fw-bold" id="prenatalModalLabel">Prenatal Care</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body custom-modal-body">
                    <img src="{{ asset('images/pregnant_woman.jpg') }}" class="img-fluid mb-3" alt="Prenatal Care">
                    <p>Our Prenatal Care program provides comprehensive support to expectant mothers. With regular health monitoring and easy appointment scheduling, we ensure both mother and baby are on the right track for a healthy delivery.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Family Planning Modal -->
    <div class="modal fade" id="familyPlanningModal" tabindex="-1" aria-labelledby="familyPlanningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title fw-bold" id="familyPlanningModalLabel">Family Planning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/family planning.jpg') }}" class="img-fluid mb-3" alt="Family Planning">
                    <p>Our Family Planning services provide confidential and accessible reproductive health support. We empower individuals and couples to make informed choices about their reproductive health and family goals.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Senior Citizen Modal -->
    <div class="modal fade" id="seniorCitizenModal" tabindex="-1" aria-labelledby="seniorCitizenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title fw-bold" id="familyPlanningModalLabel">Senior Citizen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/senior_citizen.jpg') }}" class="img-fluid mb-3" alt="Family Planning">
                    <p>We care for our senior citizens by offering services made just for them. These include regular health check-ups, blood pressure and sugar monitoring, and advice about medicines for chronic
                        illnesses like diabetes or high blood pressure. We also offer flu and pneumonia vaccines, and wellness activities to help seniors stay active and feel supported as they age.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Tb dots modal -->
    <div class="modal fade" id="tbdotsModal" tabindex="-1" aria-labelledby="tbdotsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title fw-bold" id="familyPlanningModalLabel">TB Dots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/tb.jpg') }}" class="img-fluid mb-3" alt="Family Planning">
                    <p>Our health center offers free TB treatment through the DOTS program. We provide testing, medicine, and regular monitoring for people who have tuberculosis. Health workers make sure that
                        patients take their medicines correctly every day until they are fully cured. We also educate families and communities to help stop the spread of TB and keep everyone safe.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- General Consultation -->
    <div class="modal fade" id="generalconsultModal" tabindex="-1" aria-labelledby="generalconsultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title fw-bold" id="familyPlanningModalLabel">TB Dots</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/consult.jpg') }}" class="img-fluid mb-3" alt="Family Planning">
                    <p>We offer general medical check-ups for people of all ages. Whether you have a fever, cough, minor injuries, or any other common health problem, our doctors and nurses are here to help. We check your vital signs,
                        give you the right medicine or advice, and refer you to hospitals if needed. We make sure every patient feels cared for and understood.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our specialist -->
<section class="slider-content container  px-3 px-md-5" id="specialist">
    <h2 class="mb-2 mb-md-5">Our Specialists</h2>
    <p class="specialist-text mb-1 mb-md-4 text-center">
        Our dedicated team of <strong>healthcare workers</strong> is here to serve the community of
        Barangay Hugo Perez. Get to know the professionals behind your care.
    </p>
    <div class="swiper specialist-slider">
        <div class="swiper-wrapper py-2 py-md-5">

        <!-- Slide 1 -->
        <div class="swiper-slide specialist-card">
            <div class="img-slider">
                <div class="img-overlay"></div>
                <img src="{{ asset('images/bhw1.png') }}" class="slide-img" alt="">
            </div>
            <h3 class="slide-title">Joy Andala</h3>
            <p class="slide-text">Kaia Homesp</p>
            <p class="slide-text">Nurse</p>
        </div>

        <!-- Slide 2 -->
        <div class="swiper-slide specialist-card">
            <img src="{{ asset('images/nurse.jpg') }}" class="slide-img" alt="">
            <h3 class="slide-title">Gina Lopez</h3>
            <p class="slide-text">Purok 2</p>
            <p class="slide-text">BHW</p>
        </div>

        <!-- Slide 3 -->
        <div class="swiper-slide specialist-card">
            <img src="{{ asset('images/bhw2.png') }}" class="slide-img" alt="">
            <h3 class="slide-title">Katrina Mae Apostol</h3>
            <p class="slide-text">Purok 3</p>
            <p class="slide-text">BHW</p>
        </div>

        <div class="swiper-slide specialist-card">
            <img src="{{ asset('images/bhw4.png') }}" class="slide-img" alt="">
            <h3 class="slide-title">Trisha Cortez</h3>
            <p class="slide-text">Golden Horizon</p>
            <p class="slide-text">BHW</p>
        </div>


        <div class="swiper-slide specialist-card">
            <img src="{{ asset('images/bhw6.webp') }}" class="slide-img" alt="">
            <h3 class="slide-title">Gerge Salimbago</h3>
            <p class="slide-text">Pabahay</p>
            <p class="slide-text">Staff</p>
        </div>
    </div>


    <!-- Navigation + Pagination -->
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    </div>
</section>
<!-- FAQs -->
<section id="faq" class="faqs fullscreen-section" style="background-color: #EBF9FF;">
    <div class="container-fluid">
        <div class="container text-white">
            <div class="text-center pm-5 mb-2 mb-md-5">
                <span class="">
                    <h2>Frequent Asked Questions </h2>
                </span>
                <h4 class="mt-3 text-black">
                    Quick answers to common questions about using our health information system.
                </h4>
            </div>

            <div class="row align-items-center">
                <!-- FAQ Column -->
                <div class="col-md-6 order-2 order-md-1">
                    <div class="accordion gap-col" id="faqAccordion">
                        <!-- Accordion Item 1 -->
                        <div class="mt-2">
                            <button onclick="toggleAccordion(1)" class="w-full flex justify-between items-center py-3 text-slate-800">
                                <span>Anong oras nagbubukas ang health Center?</span>
                                <span id="icon-1" class="transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </button>
                            <div id="content-1" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="pb-2 px-3 text-sm">
                                Material Tailwind is a framework that enhances Tailwind CSS with additional styles and components.
                                </div>
                            </div>
                        </div>

                        <!-- Accordion Item 2 -->
                        <div class="mt-2">
                            <button onclick="toggleAccordion(2)" class="w-full flex justify-between items-center py-3 text-slate-800">
                                <span>How to use Material Tailwind?</span>
                                <span id="icon-2" class="transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </button>
                            <div id="content-2" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="pb-2 px-3 text-sm">
                                You can use Material Tailwind by importing its components into your Tailwind CSS project.
                                </div>
                            </div>
                        </div>

                        <!-- Accordion Item 3 -->
                        <div class="mt-2">
                            <button onclick="toggleAccordion(3)" class="w-full flex justify-between items-center py-3 text-slate-800">
                                <span>What can I do with Material Tailwind?</span>
                                <span id="icon-3" class="transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </button>
                            <div id="content-3" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="pb-2 px-3 text-sm">
                                Material Tailwind allows you to quickly build modern, responsive websites with a focus on design.
                                </div>
                            </div>
                        </div>
                        <!-- Accordion Item 4 -->
                        <div class="mt-2">
                            <button onclick="toggleAccordion(4)" class="w-full flex justify-between items-center py-3 text-slate-800">
                                <span>What can I do with Material Tailwind?</span>
                                <span id="icon-4" class="transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </button>
                            <div id="content-4" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="pb-2 px-3 text-sm">
                                Material Tailwind allows you to quickly build modern, responsive websites with a focus on design.
                                </div>
                            </div>
                        </div>
                        <!-- Accordion Item 5 -->
                        <div class="mt-2">
                            <button onclick="toggleAccordion(5)" class="w-full flex justify-between items-center py-3 text-slate-800">
                                <span>What can I do with Material Tailwind?</span>
                                <span id="icon-5" class="transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </button>
                            <div id="content-5" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="pb-2 px-3 text-sm">
                                Material Tailwind allows you to quickly build modern, responsive websites with a focus on design.
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button onclick="toggleAccordion(6)" class="w-full flex justify-between items-center py-3 text-slate-800">
                                <span>What can I do with Material Tailwind?</span>
                                <span id="icon-6" class="transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </button>
                            <div id="content-6" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="pb-2 px-3 text-sm">
                                Material Tailwind allows you to quickly build modern, responsive websites with a focus on design.
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button onclick="toggleAccordion(7)" class="w-full flex justify-between items-center py-3 text-slate-800">
                                <span>What can I do with Material Tailwind?</span>
                                <span id="icon-7" class="transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </button>
                            <div id="content-7" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="pb-3 text-sm">
                                Material Tailwind allows you to quickly build modern, responsive websites with a focus on design.
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button onclick="toggleAccordion(8)" class="w-full flex justify-between items-center py-3 text-slate-800">
                                <span>What can I do with Material Tailwind?</span>
                                <span id="icon-8" class="transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                </svg>
                                </span>
                            </button>
                            <div id="content-8" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <div class="pb-3 text-sm">
                                Material Tailwind allows you to quickly build modern, responsive websites with a focus on design.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Column -->
                 <div class="col-md-6 order-1 order-md-2  md:mb-0 mb-3 px-3">
                    <img src="{{ asset('images/immunization.webp') }}"
                        alt="Doctor Consultation"
                        class="max-w-full h-auto lg:h-[600px] rounded shadow-lg hover:scale-105 transition-transform duration-300 object-cover">
                 </div>

            </div>
        </div>
    </div>
</section>
<!-- Events -->
<section id="events" class="events fullscreen-section">
    <div class="container text-center my-5">
        <div class="mb-3">
            <span class="">
                <h2>Upcoming Health Center Events</h2>
            </span>
        </div>
        <h5 class="mb-4">Stay informed about important medical services and community health initiatives.</h5>

        <div class="row  gap-4 gap-md-0 gap-lg-0">
            <!-- Event Card -->
            <div class=" col-12 col-md-6 col-lg-4">
                <div class="card event-card h-100">
                    <img src="{{ asset('images/mpox.jpg') }} " class="card-img-top event-image" alt="Vaccination Image">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">Vaccination Drive: MPOX Shots</h6>
                        <p class="card-text mb-1">May 25, 2025 • 9:00 AM</p>
                        <p class="card-text small">Free MPOX vaccination for children and elderly</p>
                        <p class="card-text"><small class="text-muted">Kaia Homes</small></p>
                    </div>
                </div>
            </div>
            <div class=" col-12 col-md-6 col-lg-4">
                <div class="card event-card h-100">
                    <img src="{{ asset('images/medical mission1.jpg') }}" class="card-img-top event-image" alt="Vaccination Image">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">Medical Mission: Initiative of Sen. Riza Hontiveros</h6>
                        <p class="card-text mb-1">April 21, 2025 • 9:00 AM</p>
                        <p class="card-text small">Free flu vaccination for children and elderly</p>
                        <p class="card-text"><small class="text-muted">Green Forbes City Ph.1 Covered Court</small></p>
                    </div>
                </div>
            </div>

            <div class=" col-12 col-md-6 col-lg-4 mt-0 mt-md-3 mt-lg-0">
                <div class="card event-card h-100">
                    <img src="{{ asset('images/hiv_testing.jpg') }}" class="card-img-top event-image" alt="Vaccination Image">
                    <div class="card-body">
                        <h6 class="card-title fw-bold">PUROKALUSUGAN CARAVAN</h6>
                        <p class="card-text mb-1">April 21, 2025 • 9:00 AM</p>
                        <p class="card-text small">Free medical checkups, tooth extraction and oplan tuli</p>
                        <p class="card-text"><small class="text-muted">Purok 1 Covered Court</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Add more sections: Specialists, FAQ, Events --}}

@endsection
<script>
  function toggleAccordion(index) {
    const content = document.getElementById(`content-${index}`);
    const icon = document.getElementById(`icon-${index}`);

    // SVG for Down icon
    const downSVG = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
        <path fill-rule="evenodd" d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
      </svg>
    `;

    // SVG for Up icon
    const upSVG = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
        <path fill-rule="evenodd" d="M11.78 9.78a.75.75 0 0 1-1.06 0L8 7.06 5.28 9.78a.75.75 0 0 1-1.06-1.06l3.25-3.25a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
      </svg>
    `;

    // Toggle the content's max-height for smooth opening and closing
    if (content.style.maxHeight && content.style.maxHeight !== '0px') {
      content.style.maxHeight = '0';
      icon.innerHTML = upSVG;
    } else {
      content.style.maxHeight = content.scrollHeight + 'px';
      icon.innerHTML = downSVG;
    }
  }
</script>

<script>
    function changeContent(type) {
        const content = document.getElementById('about-content');

        // Remove active class from all buttons
        document.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active-btn'));

        // Add active class to the clicked button
        document.getElementById(`btn-${type}`).classList.add('active-btn');

        // Fade out content
        content.classList.add('hide');

        // After fade-out transition, change text and fade back in
        setTimeout(() => {
            if (type === 'about') {
                content.innerHTML = `
                The Health Center in Barangay Hugo Perez, Trece Martires City is a friendly and welcoming place where residents can get the care they need.
                It offers free general check-ups, vaccinations, TB-DOTS, and health programs for moms, kids, and the whole family. The center works closely with the City Health Office to bring medical missions and health services right to the community.
                It's here to help everyone stay healthy and live better every day.
            `;
            } else if (type === 'mission') {
                content.innerHTML = `
                Our mission is to provide quality, accessible, and compassionate healthcare services to all residents of Barangay Hugo Perez.
                We are committed to promoting health, preventing disease, and ensuring the well-being of every individual and family in our community.
            `;
            } else if (type === 'vision') {
                content.innerHTML = `
                We envision a healthy, safe, and empowered community where every resident has access to proper healthcare, reliable medical services,
                and health education — guided by care, integrity, and dedication from our health workers.
            `;
            } else if (type === 'history') {
                content.innerHTML = `
                The Health Center in Barangay Hugo Perez has been a vital part of the community for years, providing essential healthcare services
                to residents. Through partnerships with the City Health Office and dedicated healthcare workers, it has grown to serve families with
                expanded medical programs and continuous health education.
            `;
            }

            // Fade in content
            content.classList.remove('hide');
        }, 300);
    }
</script>
