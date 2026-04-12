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
        <div class="card">
            <div class="top-strip"></div>
            <div class="card-inner">

                <div class="logo">
                    <img src="/images/sada-horizontal.png" alt="Sada Fezzan">
                </div>

                <h1 class="title">{{ $job->title }}</h1>
                <p class="subtitle">Fill your details below</p>

                <div class="progress-wrap">
                    <div class="progress-header">
                        <span id="stepLabel">Step 1 of 3</span>
                        <span id="stepPercent">33%</span>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
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
                    <form method="POST" action="{{ \App\Support\PublicUrl::route('jobs.apply.store', ['job' => $job]) }}" enctype="multipart/form-data" id="applicationForm">
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