<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply - {{ $job->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @include('jobs.partials.apply-styles')
</head>
<body>

@php
    $countryCodes = config('country_codes', []);
@endphp

<div class="page">
    <div class="container">
        <div class="apply-shell">
            <div class="apply-topbar">
                <div class="brand-box">
                    <img src="/images/sada-horizontal.png" alt="Sada Fezzan">
                </div>

                <div class="apply-hero">
                    <div class="apply-hero-left">
                        <div class="eyebrow">Application Form</div>
                        <h1 class="title">Apply for {{ $job->title }}</h1>
                        <p class="subtitle">
                            Complete the form carefully and submit your application to the recruitment team at Sada Fezzan.
                        </p>
                    </div>

                    <div class="apply-hero-right">
                        <div class="summary-card">
                            <h4>Position Summary</h4>

                            <div class="summary-item">
                                <span class="summary-label">Job Title</span>
                                <span class="summary-value">{{ $job->title }}</span>
                            </div>

                            <div class="summary-item">
                                <span class="summary-label">Department</span>
                                <span class="summary-value">{{ $job->department ?: 'Not specified' }}</span>
                            </div>

                            <div class="summary-item">
                                <span class="summary-label">Location</span>
                                <span class="summary-value">{{ $job->location ?: 'Not specified' }}</span>
                            </div>

                            <div class="summary-item">
                                <span class="summary-label">Employment Type</span>
                                <span class="summary-value">
                                    {{ $job->employment_type ? ucfirst(str_replace('_', ' ', $job->employment_type)) : 'Not specified' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="apply-content">
                <div class="progress-wrap">
                    <div class="progress-header">
                        <span id="stepLabel">Step 1 of 3</span>
                        <span id="stepPercent">33%</span>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>

                    <div class="progress-steps">
                        <div class="progress-step is-active" data-progress-step="0">
                            <span class="progress-step-number">1</span>
                            <span class="progress-step-text">Basic Details</span>
                        </div>

                        <div class="progress-step" data-progress-step="1">
                            <span class="progress-step-number">2</span>
                            <span class="progress-step-text">Professional Information</span>
                        </div>

                        <div class="progress-step" data-progress-step="2">
                            <span class="progress-step-number">3</span>
                            <span class="progress-step-text">Final Information</span>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please fix the following:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($fields->isEmpty())
                    <div class="alert alert-danger">
                        No application fields are connected to this job yet.
                    </div>
                @else
                    <form
                        method="POST"
                        action="{{ \App\Support\PublicUrl::route('jobs.apply.store', ['job' => $job]) }}"
                        enctype="multipart/form-data"
                        id="applicationForm"
                    >
                        @csrf

                        @php
                            $fieldChunks = $fields->values()->chunk(max(1, ceil($fields->count() / 3)));
                        @endphp

                        @include('jobs.partials.apply-fields', [
                            'fieldChunks' => $fieldChunks,
                            'fields' => $fields,
                            'countryCodes' => $countryCodes,
                            'job' => $job,
                        ])
                    </form>
                @endif

                <a class="back-link" href="{{ \App\Support\PublicUrl::route('jobs.index') }}">← Back to Jobs</a>
            </div>
        </div>
    </div>
</div>

@include('jobs.partials.apply-scripts')

</body>
</html>