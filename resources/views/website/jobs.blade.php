@extends('layouts.website')

@section('title', 'Job Opportunities | Sada Fezzan Oil Services Company')

@section('content')

<section class="hero" style="padding-bottom: 50px;">
    <div class="container">
        <div class="eyebrow">
            <span class="eyebrow-dot"></span>
            Careers at Sada Fezzan
        </div>

        <h1 style="max-width: 900px;">Job opportunities for oil & gas professionals.</h1>

        <p style="max-width: 760px;">
            Explore available job openings and submit your application to Sada Fezzan Oil Services Company. This page will later connect directly with the ERP recruitment system.
        </p>

        <div class="hero-actions">
            <a href="#openings" class="btn btn-jobs">
                <span class="material-symbols-rounded">work</span>
                View Openings
            </a>

            <a href="{{ route('website.home') }}#inquiry" class="btn btn-outline">
                <span class="material-symbols-rounded">mail</span>
                Contact Recruitment
            </a>
        </div>
    </div>
</section>

<section id="openings" class="section section-soft">
    <div class="container">
        <div class="center-title">
            <div class="section-label">Current Openings</div>
            <h2 class="section-title">Available positions</h2>
            <p class="large-text">
                These are sample cards for now. In the next step, we can connect them to your ERP Job Openings module.
            </p>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <span class="material-symbols-rounded">engineering</span>
                </div>
                <h3>Senior Instrument Maintenance Technician</h3>
                <p>
                    Oil & gas maintenance role for experienced instrumentation professionals.
                </p>
                <a href="{{ route('website.home') }}#inquiry" class="learn-link">Apply now →</a>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <span class="material-symbols-rounded">verified</span>
                </div>
                <h3>ATEX Certified Instrument Engineer</h3>
                <p>
                    Technical engineering role requiring ATEX certification and field experience.
                </p>
                <a href="{{ route('website.home') }}#inquiry" class="learn-link">Apply now →</a>
            </div>

            <div class="service-card">
                <div class="service-icon">
                    <span class="material-symbols-rounded">health_and_safety</span>
                </div>
                <h3>HSE Officer</h3>
                <p>
                    Safety-focused role supporting field readiness, compliance, and reporting.
                </p>
                <a href="{{ route('website.home') }}#inquiry" class="learn-link">Apply now →</a>
            </div>
        </div>
    </div>
</section>

@endsection
