<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/biringan.png') }}">
    <title>City of Biringan - IT Support</title>

    @vite([
        'resources/css/app.css', 
        'resources/js/app.js'
    ])

    <script src="{{ asset('assets/js/landing.js') }}"></script>
</head>
<body>
    <x-header
        title="IT SUPPORT"
        subtitle="City of Biringan"
        logo="{{ asset('assets/images/biringan.png') }}"
        background="rgba(9, 22, 40, 0.3)"
        textColor="#ffffff"
        :showHamburger="true"
    >
        <a href="#home" class="nav-link">Home</a>
        <a href="#services" class="nav-link">Services</a>
        <a href="#features" class="nav-link">Features</a>
        <a href="#how-it-works" class="nav-link">How it works</a>
    </x-header>

    <main class="landing-page">
        
        <section id="home" class="hero-section">
            <div class="hero-badge">CITY OF BIRINGAN IT SUPPORT</div>
            <h1>IT SUPPORT FOR CITY OFFICES AND BARANGAYS</h1>
            <p>
                Providing reliable technical assistance for hardware, software, network, and other IT-related concerns for City Offices and Barangays.
            </p>

            <div class="cta-row">
                <a href="{{ route('track.request') }}" class="cta-button">
                    <span> Track Ticket</span>
                    <i class="ti ti-search"></i>
                </a>

                <a href="{{ route('submit.request') }}" class="cta-button submit">
                    <span> Submit Ticket </span>
                    <i class="ti ti-send"></i>
                </a>

                <a href="{{ route('login') }}" class="cta-button secondary">
                    <span> Login </span>
                    <i class="ti ti-login"></i>
                </a>
            </div>

            <div class="feature-list">
                <span class="feature-item">
                    <i class="text-xs ti ti-circle-check"></i>
                    <span class="text-xs">Fast issue Reporting</span>
                </span>

                <span class="feature-item">
                    <i class="text-xs ti ti-circle-check"></i>
                    <span class="text-xs">Real-Time Ticket Tracking</span>
                </span>

                <span class="feature-item">
                    <i class="text-xs ti ti-circle-check"></i>
                    <span class="text-xs">Organized Support History</span>
                </span>
            </div>

        </section>

    </main>
    
    <section id="services" class="services-section">
        <div class="animated-divider"></div>
        <div class="mx-auto max-w-6xl">
            <h2 class="feature-title text-center">Our Services</h2>
            <p class="text-center text-sm font-light opacity-75 leading-relaxed max-w-2xl mx-auto">
                Comprehensive IT support solutions tailored to your needs.
            </p>

            <div class="services-cards mt-12 grid grid-cols-2 md:grid-cols-2 gap-6">

                <div class="service-card">
                    <div class="service-icon">
                        <i class="ti ti-devices-2"></i>
                    </div>
                    <h3>Hardware Support</h3>
                    <p>Troubleshoot and resolve hardware issues, including desktop computers, laptops, printers, and peripherals.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="ti ti-brand-windows"></i>
                    </div>
                    <h3>Software Support</h3>
                    <p>Get assistance with software installation, updates, compatibility issues, and application troubleshooting.</p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="ti ti-wifi"></i>
                    </div>
                    <h3>Network Support</h3>
                    <p>Resolve connectivity issues, network configuration, and ensure stable internet access.</p>
                </div>

                

            </div>
        </div>
    </section>

    
    <section id="features" class="features-section">

        <div class="animated-divider"></div>

        <div class="mx-auto max-w-auto">
            <h2 class="feature-title text-center">Core Features</h2>

            <p class="text-center text-sm font-light opacity-75 leading-relaxed max-w-2xl mx-auto">
                Give City of Biringan an easy way to request help while giving IT staff the tools
                to manage every support issue efficiently.
            </p>

            <div class="flex grid grid-cols-1 md:grid-cols-4 mx-auto mt-10 gap-6">

                <x-feature_card
                    title="Easy Ticketing"
                    description="Offices and Barangay can report IT problems by providing
                                the issue, category, and supporting details."
                    icon="ti ti-ticket"
                />

                <x-feature_card
                    title="IT Support Management"
                    description="IT staff can review, assign, update, and
                                resolve City and Barangay Offices support tickets."
                    icon="ti ti-headset"
                />

                <x-feature_card
                    title="Ticket Tracking"
                    description="Offices and Barangays can monitor their ticket status from
                                Pending → In progress → Resolved → Closed."
                    icon="ti ti-git-merge"
                />

                <x-feature_card
                    title="Reports & Ticket History"
                    description="Keep a complete history of support requests and
                                generate reports on ticket volume, issue types,
                                and resolution performance."
                    icon="ti ti-history-toggle"
                />

            </div>
        </div>

        
    </section>

    <section id="how-it-works" class="how-it-works-section">

        <div class="animated-divider"></div>

        <h2 class="feature-title text-center font-light mx-auto">
             How it works?
        </h2>
        <p class="text-center text-sm font-light opacity-75 leading-relaxed max-w-2xl mx-auto">
            SIMPLE SUPPORT PROCESS.
        </p>

        <div class="timeline-container mt-12">
            <div class="timeline-step step-1">
                <div class="timeline-icon">
                    <i class="ti ti-send"></i>
                </div>
                <div class="timeline-content">
                    <h3>Submit a Ticket</h3>
                    <p class="font-bold text-white">Tell us what's wrong.</p>
                    <p class="text-xs">Describe your IT problem, select the appropriate category and issue, and provide any necessary details.</p>
                </div>
            </div>

            <div class="timeline-step step-2">
                <div class="timeline-icon">
                    <i class="ti ti-analyze"></i>
                </div>
                <div class="timeline-content">
                    <h3>IT Staff Reviews Your Request</h3>
                    <p class="font-bold text-white">Your request gets to the right people.</p>
                    <p class="text-xs">The IT support team reviews your ticket, checks the issue details, and assigns it for proper handling.</p>
                </div>
            </div>

            <div class="timeline-step step-3">
                <div class="timeline-icon">
                    <i class="ti ti-device-imac-cog"></i>
                </div>
                <div class="timeline-content">
                    <h3>Issue Gets Resolved</h3>
                    <p class="font-bold text-white">Get the technical help you need.</p>
                    <p class="text-xs">IT staff investigates the problem, and updates the ticket as the issue is being resolved.</p>
                </div>
            </div>

            <div class="timeline-step step-4">
                <div class="timeline-icon">
                    <i class="ti ti-device-ipad-horizontal-check"></i>
                </div>
                <div class="timeline-content">
                    <h3>Confirm & Close</h3>
                    <p class="font-bold text-white">Keep the record complete.</p>
                    <p class="text-xs">Once the issue is resolved, the ticket is closed and it's details remain available in admin support history.</p>
                </div>
            </div>
        </div>

    
    </section>


    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-brand">
                    <img src="{{ asset('assets/images/biringan.png') }}" alt="City of Biringan" class="footer-logo">
                    <div>
                        <span class="footer-title">IT SUPPORT</span>
                        <span class="footer-subtitle">City of Biringan</span>
                    </div>
                </div>
                <p class="footer-desc">
                    Providing reliable technical assistance for hardware, software, network, and other IT-related concerns for City Offices and Barangays.
                </p>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Actions</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('submit.request') }}">Submit Ticket</a></li>
                    <li><a href="{{ route('track.request') }}">Track Ticket</a></li>
                    <li><a href="{{ route('login') }}">Login</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">Contact</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="ti ti-mail"></i>
                        <span>itsupport@biringan.gov.ph</span>
                    </li>
                    <li>
                        <i class="ti ti-phone"></i>
                        <span>09876543210</span>
                    </li>
                    <li>
                        <i class="ti ti-map-pin"></i>
                        <span>City of Biringan, Philippines</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 City of Biringan &mdash; IT Support. All Rights Reserved</p>
        </div>
    </footer>


</body>
</html>