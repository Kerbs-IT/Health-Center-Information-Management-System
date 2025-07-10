@extends('layout.app')

@section('content')

{{-- Home Section --}}
<section id="home" class=" home-section fullscreen-section d-flex align-items-center justify-content-center text-white">
    <div class="container text-center d-flex justify-content-center">
        <div class="col-lg-9">
            <!--  col-lg-6 mb-4 mb-lg-0 -->
            <h1 class="display-5 fw-bold text-light mb-5" style="font-size:60px;">Bringing Better <span class="" style="color: #4CAF50;font-size:60px;">Healthcare to the Barangay</span> – Digitally</h1>
            <p class="lead text-light mt-5" style="font-size: 2.3rem;">Streamline records, monitor vaccinations, and improve decision-making with our Healthcare Information System for Barangay Hugo Perez.</p>
            <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#viewMoreModal">View More</button>
        </div>
    </div>
</section>
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
                <p class="fs-5">Our Healthcare Information System streamlines patient records, tracks vaccinations, and empowers better health services in Barangay Hugo Perez.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- About Section --}}
<section class="py-5 min-vh-100 d-flex align-items-center justify-content-center" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-3">About Health Center</h1>
                <h4 class="text-primary fw-bold">Empowering <span>Healthcare Workers</span></h4>
                <h5 class="text-info">Enhancing Community Wellness</h5>

                <div class="mb-3">
                    <button class="btn btn-outline-success me-2 active-btn" id="btn-about" onclick="changeContent('about')">ABOUT</button>
                    <button class="btn btn-outline-success me-2" id="btn-mission" onclick="changeContent('mission')">MISSION</button>
                    <button class="btn btn-outline-success me-2" id="btn-vision" onclick="changeContent('vision')">VISION</button>
                    <button class="btn btn-outline-success" id="btn-history" onclick="changeContent('history')">HISTORY</button>
                </div>

                <p id="about-content" class="fade-content">
                    The Health Center in Barangay Hugo Perez, Trece Martires City is a friendly and welcoming place where residents can get the care they need.
                    It offers free general check-ups, vaccinations, TB-DOTS, and health programs for moms, kids, and the whole family. The center works closely with the City Health Office to bring medical missions and health services right to the community.
                    It's here to help everyone stay healthy and live better every day.
                </p>
            </div>s

            <div class="col-md-5">
                <img src="{{ asset('images/about.jpg') }}" alt="healthcare team" class="img-fluid rounded shadow h-100">
            </div>
        </div>
    </div>
</section>



<section class="fullscreen-section py-5" id="services" style="background-color: #EBF9FF;">
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
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#vaccinationModal">Read More</button>
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
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#prenatalModal">Read More</button>
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
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#familyPlanningModal">Read More</button>
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
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#seniorCitizenModal">Read More</button>
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
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tbdotsModal">Read More</button>
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
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generalconsultModal">Read More</button>
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

<!-- Our Specialists Section -->
<section id="specialist" class="specialist min-vh-100 mb-5">
    <div class="container my-5 text-center">
        <h4 class="text-info fw-bold mb-3">Our Specialists</h4>
        <p class="mb-4">
            Our dedicated team of <strong>healthcare workers</strong> is here to serve the community of
            Barangay Hugo Perez. Get to know the professionals behind your care.
        </p>

        <div class="my-5">
            <select id="roleFilter" class="form-select w-auto mx-auto">
                <option value="all">All</option>
                <option value="Nurse">Nurse</option>
                <option value="BHW">BHW</option>
            </select>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-5">
            <button id="prevBtn" class="btn btn-outline-info rounded-circle"><i class="bi bi-chevron-left"></i></button>
            <div class="d-flex overflow-hidden" style="width: 100vw;" id="cardCarousel">
                <div class="d-flex transition" id="cardContainer">
                    <!-- Card 1 -->
                    <div class="card specialist-card m-2" data-role="Nurse">
                        <img src="{{ asset('images/nurse.jpg') }}" class="card-img-top" alt="Kerby Buan">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Rn. Jane Maria</h5>
                            <p class="card-text">Nurse</p>

                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="card specialist-card m-2" data-role="BHW">
                        <img src="{{ asset('images/bhw.png') }}" class="card-img-top" alt="Padre Damaso">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Dolores Santa Ana</h5>
                            <p>Area: Karla Ville Ph. 1</p>
                            <p class="card-text">BHW</p>

                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="card specialist-card m-2" data-role="BHW">
                        <img src="{{ asset('images/bhw7.jpg') }}" class="card-img-top" alt="Padre Damaso">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Catherine Mendoza</h5>
                            <p>Area: Sugarland</p>
                            <p class="card-text">BHW</p>

                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="card specialist-card m-2" data-role="BHW">
                        <img src="{{ asset('images/bhw8.jpg') }}" class="card-img-top" alt="Padre Damaso">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Michelle Ramos</h5>
                            <p>Area: Purok 1</p>
                            <p class="card-text">BHW</p>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="card specialist-card m-2" data-role="BHW">
                        <img src="{{ asset('images/bhw9.jpg') }}" class="card-img-top" alt="Padre Damaso">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Rosemarie Castillo</h5>
                            <p>Area: Kaia Homes Ph. 2</p>
                            <p class="card-text">BHW</p>

                        </div>
                    </div>


                    <!-- Repeat other cards similarly... -->
                    <div class="card specialist-card m-2" data-role="BHW">
                        <img src="{{ asset('images/bhw1.png') }}" class="card-img-top" alt="Jane Marie Andrada">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Jane Marie Andrada</h5>
                            <p>Area: Gawad Kalinga</p>
                            <p class="card-text">BHW</p>

                        </div>
                    </div>
                    <div class="card specialist-card m-2" data-role="BHW">
                        <img src="{{ asset('images/bhw2.png') }}" class="card-img-top" alt="Jane Marie Andrada">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Karen Villanueva</h5>
                            <p>Area: Purok 2</p>
                            <p class="card-text">BHW</p>
                        </div>
                    </div>
                    <div class="card specialist-card m-2" data-role="BHW">
                        <img src="{{ asset('images/bhw4.png') }}" class="card-img-top" alt="Jack Roberto">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Jack Roberto</h5>
                            <p>Area: Golden Horizon</p>
                            <p class="card-text">BHW</p>

                        </div>
                    </div>
                </div>
            </div>
            <button id="nextBtn" class="btn btn-outline-info rounded-circle"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</section>
<!-- FAQ -->
<section id="faq" class="fullscreen-section" style="background-color: #0b2c56;">
    <div class="container-fluid">
        <div class="container text-white">
            <div class="text-center pm-5 mb-5">
                <span class="badge rounded-pill text-success border border-success px-3 py-2 fs-6 bg-white">
                    <h1>Frequent Asked Questions </h1>
                </span>
                <h4 class="mt-3">
                    Quick answers to common questions about using our health information system.
                </h4>
            </div>

            <div class="row align-items-center">
                <!-- FAQ Column -->
                <div class="col-md-6">
                    <div class="accordion" id="faqAccordion">

                        <!-- Question 1 -->
                        <div class="accordion-item bg-transparent border-0 mb-2">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button bg-info text-white rounded-pill px-4 py-3 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <span class="me-2 bg-white text-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">1</span>
                                    What are the operating hours of the health center?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white px-4 py-2">
                                    Our health center is open Monday to Friday, from 8:00 AM to 5:00 PM, excluding holidays. Emergency cases may be coordinated with the barangay health workers or referred to the nearest hospital
                                </div>
                            </div>
                        </div>

                        <!-- Question 2 -->
                        <div class="accordion-item bg-transparent border-0 mb-2">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed bg-info text-white rounded-pill px-4 py-3 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <span class="me-2 bg-white text-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">2</span>
                                    Do I need to bring any documents when visiting the health center?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white px-4 py-2">
                                    Yes. Please bring any valid ID and your barangay health ID or booklet, especially for prenatal check-ups, immunizations, and TB-DOTS. For new patients, a brief registration form will be filled out.
                                </div>
                            </div>
                        </div>

                        <!-- Question 3 -->
                        <div class="accordion-item bg-transparent border-0 mb-2">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed bg-info text-white rounded-pill px-4 py-3 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    <span class="me-2 bg-white text-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">3</span>
                                    How can I schedule a prenatal check-up?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white px-4 py-2">
                                    You can walk in during clinic hours or coordinate with your barangay health worker to schedule a prenatal visit. Make sure to bring your mother’s book or prenatal record.


                                </div>
                            </div>
                        </div>

                        <!-- Question 4 -->
                        <div class="accordion-item bg-transparent border-0 mb-2">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed bg-info text-white rounded-pill px-4 py-3 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <span class="me-2 bg-white text-info rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">4</span>
                                    Who should I contact for health emergencies after clinic hours?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white px-4 py-2">
                                    For emergencies after 5:00 PM, you may contact your barangay health worker or proceed to the nearest hospital. The health center does not operate 24/7.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Image Column -->
                <div class="col-md-6 mt-4 mt-md-0">
                    <img src="{{ asset('images/doctor.webp') }}" alt="Doctor Consultation" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
</section>
<section id="events" class="fullscreen-section">
    <div class="container text-center my-5">
        <div class="mb-3">
            <span class="event-badge">
                <h2>Upcoming Health Center Events</h2>
            </span>
        </div>
        <h5 class="mb-4">Stay informed about important medical services and community health initiatives.</h5>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Event Card -->
            <div class="col">
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

            <!-- Duplicate the above card for more -->
            <div class="col">
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

            <div class="col">
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