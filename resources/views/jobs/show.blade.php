<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $job->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --sf-primary: #2c5377;
            --sf-primary-soft: #3f6c96;
            --sf-accent: #26b6b7;
            --sf-accent-dark: #16999a;
            --sf-text: #18212b;
            --sf-muted: #6b7f90;
            --sf-border: rgba(44, 83, 119, 0.12);
            --sf-bg-1: #f4f8fa;
            --sf-bg-2: #eef4f7;
            --sf-card: rgba(255,255,255,0.90);
            --sf-shadow: 0 24px 70px rgba(25, 41, 61, 0.08);
            --sf-shadow-soft: 0 16px 38px rgba(25, 41, 61, 0.06);
            --sf-danger: #c93434;
            --sf-danger-bg: #fff1f1;
            --sf-danger-border: #f2c1c1;
            --sf-success: #1d8f79;
            --sf-success-bg: #ebfbf7;
            --sf-success-border: #c8efe6;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--sf-text);
            background:
                radial-gradient(circle at top left, rgba(38, 182, 183, 0.10), transparent 28%),
                radial-gradient(circle at top right, rgba(44, 83, 119, 0.09), transparent 32%),
                linear-gradient(180deg, var(--sf-bg-1) 0%, var(--sf-bg-2) 100%);
            min-height: 100vh;
        }

        a {
            text-decoration: none;
        }

        .page {
            max-width: 1080px;
            margin: 0 auto;
            padding: 42px 24px 60px;
        }

        .details-shell {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            border: 1px solid rgba(255,255,255,0.60);
            background:
                linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(247,250,251,0.90) 100%);
            box-shadow: var(--sf-shadow);
            backdrop-filter: blur(10px);
        }

        .details-shell::before {
            content: "";
            position: absolute;
            right: -80px;
            top: -80px;
            width: 250px;
            height: 250px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(38,182,183,0.14) 0%, rgba(38,182,183,0.03) 60%, transparent 74%);
            pointer-events: none;
        }

        .details-shell::after {
            content: "";
            position: absolute;
            left: -70px;
            bottom: -90px;
            width: 230px;
            height: 230px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(44,83,119,0.10) 0%, rgba(44,83,119,0.02) 62%, transparent 74%);
            pointer-events: none;
        }

        .topbar {
            position: relative;
            z-index: 1;
            padding: 34px 34px 24px;
            border-bottom: 1px solid rgba(44,83,119,0.08);
        }

        .logo-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 18px;
        }

        .logo-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 82px;
            height: 82px;
            border-radius: 24px;
            background: rgba(255,255,255,0.78);
            border: 1px solid rgba(44,83,119,0.10);
            box-shadow: var(--sf-shadow-soft);
        }

        .logo-box img {
            max-width: 56px;
            max-height: 56px;
            object-fit: contain;
        }

        .title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .title-block {
            max-width: 760px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(38,182,183,0.20);
            background: rgba(38,182,183,0.10);
            color: var(--sf-accent-dark);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .job-title {
            margin: 0;
            font-size: clamp(34px, 5vw, 58px);
            line-height: 1.02;
            font-weight: 900;
            letter-spacing: -0.04em;
            color: var(--sf-primary);
        }

        .title-subtitle {
            margin: 14px 0 0;
            color: var(--sf-muted);
            font-size: 17px;
            line-height: 1.75;
        }

        .job-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 9px 16px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .job-status-open {
            color: var(--sf-success);
            background: var(--sf-success-bg);
            border-color: var(--sf-success-border);
        }

        .job-status-closed {
            color: var(--sf-danger);
            background: var(--sf-danger-bg);
            border-color: var(--sf-danger-border);
        }

        .content {
            position: relative;
            z-index: 1;
            padding: 28px 34px 34px;
        }

        .alert-closed {
            margin-bottom: 22px;
            padding: 16px 18px;
            border-radius: 16px;
            background: var(--sf-danger-bg);
            border: 1px solid var(--sf-danger-border);
            color: #b42318;
            font-weight: 700;
            line-height: 1.6;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 26px;
        }

        .meta-box {
            padding: 18px;
            border-radius: 20px;
            border: 1px solid rgba(44,83,119,0.08);
            background: rgba(255,255,255,0.66);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.72);
        }

        .meta-label {
            display: block;
            margin-bottom: 8px;
            color: var(--sf-primary);
            font-size: 13px;
            font-weight: 850;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .meta-value {
            display: block;
            color: var(--sf-text);
            font-size: 21px;
            font-weight: 800;
            line-height: 1.35;
            word-break: break-word;
        }

        .section {
            margin-top: 22px;
            padding: 24px;
            border-radius: 24px;
            border: 1px solid rgba(44,83,119,0.08);
            background: rgba(255,255,255,0.60);
        }

        .section-title {
            margin: 0 0 14px;
            color: var(--sf-primary);
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.02em;
        }

        .section-content {
            margin: 0;
            color: #334155;
            line-height: 1.85;
            font-size: 16px;
            white-space: pre-line;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-primary,
        .btn-secondary,
        .btn-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            padding: 0 20px;
            border-radius: 15px;
            font-size: 15px;
            font-weight: 850;
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--sf-accent) 0%, #39c7c8 100%);
            color: #fff;
            box-shadow: 0 14px 26px rgba(38,182,183,0.24);
        }

        .btn-primary:hover {
            filter: saturate(1.04);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.80);
            color: var(--sf-primary);
            border: 1px solid rgba(44,83,119,0.12);
        }

        .btn-danger {
            background: linear-gradient(135deg, #da4a4a 0%, #bf2f2f 100%);
            color: #fff;
            box-shadow: 0 14px 26px rgba(201, 52, 52, 0.22);
            cursor: not-allowed;
        }

        @media (max-width: 860px) {
            .page {
                padding: 24px 16px 40px;
            }

            .topbar,
            .content {
                padding-left: 20px;
                padding-right: 20px;
            }

            .meta-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .details-shell {
                border-radius: 24px;
            }

            .job-title {
                font-size: 34px;
            }

            .section {
                padding: 18px;
                border-radius: 18px;
            }

            .section-title {
                font-size: 21px;
            }
        }
    </style>
</head>
<body>

<div class="page">
    <div class="details-shell">
        <div class="topbar">
            <div class="logo-wrap">
                <div class="logo-box">
                    <img src="/images/sada-horizontal.png" alt="Sada Fezzan">
                </div>
            </div>

            @php
                $isClosed = $job->isClosed();
            @endphp

            <div class="title-row">
                <div class="title-block">
                    <div class="eyebrow">Job Opportunity</div>
                    <h1 class="job-title">{{ $job->title }}</h1>
                    <p class="title-subtitle">
                        Review the role details, job requirements, and application availability before submitting your application.
                    </p>
                </div>

                @if($isClosed)
                    <span class="job-status job-status-closed">Closed</span>
                @else
                    <span class="job-status job-status-open">Open</span>
                @endif
            </div>
        </div>

        <div class="content">
            @if(session('job_closed'))
                <div class="alert-closed">
                    {{ session('job_closed') }}
                </div>
            @elseif($isClosed)
                <div class="alert-closed">
                    This job is closed and no longer accepting applications.
                </div>
            @endif

            <div class="meta-grid">
                <div class="meta-box">
                    <span class="meta-label">Department</span>
                    <span class="meta-value">{{ $job->department ?: 'Not specified' }}</span>
                </div>

                <div class="meta-box">
                    <span class="meta-label">Location</span>
                    <span class="meta-value">{{ $job->location ?: 'Not specified' }}</span>
                </div>

                <div class="meta-box">
                    <span class="meta-label">Employment Type</span>
                    <span class="meta-value">{{ $job->employment_type ? ucfirst(str_replace('_', ' ', $job->employment_type)) : 'Not specified' }}</span>
                </div>
            </div>

            <section class="section">
                <h2 class="section-title">Description</h2>
                <p class="section-content">{{ $job->description ?: 'No description available.' }}</p>
            </section>

            <section class="section">
                <h2 class="section-title">Requirements</h2>
                <p class="section-content">{{ $job->requirements ?: 'No requirements available.' }}</p>
            </section>

            <div class="actions">
                @if($isClosed)
                    <span class="btn-danger">Cannot Apply — Job Closed</span>
                @else
                    <a class="btn-primary" href="{{ \App\Support\PublicUrl::route('jobs.apply', ['job' => $job]) }}">Apply for this Job</a>
                @endif

                <a class="btn-secondary" href="{{ \App\Support\PublicUrl::route('jobs.index') }}">Back to Jobs</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>