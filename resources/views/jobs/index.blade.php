<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Open Jobs</title>
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
            --sf-card: rgba(255, 255, 255, 0.88);
            --sf-bg-1: #f4f8fa;
            --sf-bg-2: #eef4f7;
            --sf-danger: #c93434;
            --sf-danger-bg: #fff0f0;
            --sf-danger-border: #f3c1c1;
            --sf-success: #1d8f79;
            --sf-success-bg: #ebfbf7;
            --sf-success-border: #c8efe6;
            --sf-shadow: 0 24px 70px rgba(25, 41, 61, 0.08);
            --sf-shadow-soft: 0 14px 35px rgba(25, 41, 61, 0.06);
            --sf-radius-xl: 30px;
            --sf-radius-lg: 22px;
            --sf-radius-md: 16px;
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
            max-width: 1320px;
            margin: 0 auto;
            padding: 42px 24px 60px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            padding: 42px 42px 34px;
            border-radius: 36px;
            border: 1px solid rgba(255, 255, 255, 0.55);
            background:
                linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(242,248,250,0.90) 52%, rgba(223,239,240,0.88) 100%);
            box-shadow: var(--sf-shadow);
            backdrop-filter: blur(10px);
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: auto -90px -100px auto;
            width: 280px;
            height: 280px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(38,182,183,0.16) 0%, rgba(38,182,183,0.03) 60%, transparent 75%);
            pointer-events: none;
        }

        .hero::after {
            content: "";
            position: absolute;
            top: -70px;
            right: 120px;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(44,83,119,0.12) 0%, rgba(44,83,119,0.02) 62%, transparent 75%);
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 24px;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .hero-left {
            max-width: 760px;
        }

        .hero-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 78px;
            height: 78px;
            margin-bottom: 18px;
            border-radius: 24px;
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(44,83,119,0.10);
            box-shadow: var(--sf-shadow-soft);
        }

        .hero-logo img {
            max-width: 54px;
            max-height: 54px;
            object-fit: contain;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
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

        .hero-title {
            margin: 0;
            font-size: clamp(38px, 5vw, 68px);
            line-height: 0.98;
            font-weight: 900;
            letter-spacing: -0.04em;
            color: var(--sf-primary);
        }

        .hero-subtitle {
            max-width: 760px;
            margin: 16px 0 0;
            font-size: 18px;
            line-height: 1.7;
            color: var(--sf-muted);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(140px, 1fr));
            gap: 14px;
            min-width: 360px;
        }

        .hero-stat {
            padding: 18px 18px 16px;
            border-radius: 20px;
            border: 1px solid rgba(44,83,119,0.10);
            background: rgba(255,255,255,0.68);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.70);
            backdrop-filter: blur(10px);
        }

        .hero-stat-value {
            font-size: 30px;
            font-weight: 900;
            line-height: 1;
            color: var(--sf-primary);
        }

        .hero-stat-label {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.10em;
            color: var(--sf-muted);
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin: 0 0 18px;
            flex-wrap: wrap;
        }

        .section-title {
            margin: 0;
            font-size: 28px;
            font-weight: 850;
            color: var(--sf-primary);
            letter-spacing: -0.03em;
        }

        .section-note {
            color: var(--sf-muted);
            font-size: 15px;
        }

        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 24px;
        }

        .job-card {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 295px;
            padding: 24px;
            border-radius: 28px;
            border: 1px solid rgba(44,83,119,0.10);
            background:
                linear-gradient(180deg, rgba(255,255,255,0.94) 0%, rgba(248,251,252,0.90) 100%);
            box-shadow: var(--sf-shadow-soft);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .job-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(38,182,183,0.06), transparent 45%, rgba(44,83,119,0.04));
            pointer-events: none;
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 22px 45px rgba(25, 41, 61, 0.10);
            border-color: rgba(38,182,183,0.18);
        }

        .job-card-top {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .job-title-wrap {
            min-width: 0;
        }

        .job-title {
            margin: 0;
            font-size: 32px;
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -0.03em;
            color: var(--sf-primary);
            word-break: break-word;
        }

        .job-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(44,83,119,0.07);
            color: var(--sf-primary-soft);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .job-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 36px;
            padding: 8px 14px;
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

        .job-meta {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 54px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid rgba(44,83,119,0.08);
            background: rgba(255,255,255,0.62);
        }

        .meta-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: rgba(38,182,183,0.12);
            color: var(--sf-accent-dark);
            font-size: 16px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .meta-text {
            min-width: 0;
        }

        .meta-label {
            display: block;
            margin-bottom: 3px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--sf-muted);
        }

        .meta-value {
            display: block;
            color: var(--sf-primary);
            font-size: 16px;
            font-weight: 750;
            word-break: break-word;
        }

        .job-actions {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: auto;
            padding-top: 10px;
        }

        .job-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--sf-accent) 0%, #39c7c8 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.01em;
            box-shadow: 0 14px 26px rgba(38,182,183,0.25);
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .job-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(38,182,183,0.32);
            filter: saturate(1.05);
        }

        .closed-note {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 16px;
            border-radius: 14px;
            background: var(--sf-danger-bg);
            border: 1px solid var(--sf-danger-border);
            color: var(--sf-danger);
            font-weight: 800;
            font-size: 14px;
        }

        .empty {
            padding: 48px 32px;
            border-radius: 28px;
            border: 1px solid rgba(44,83,119,0.10);
            background: rgba(255,255,255,0.84);
            box-shadow: var(--sf-shadow-soft);
            text-align: center;
        }

        .empty-title {
            margin: 0 0 10px;
            font-size: 28px;
            font-weight: 900;
            color: var(--sf-primary);
        }

        .empty-text {
            margin: 0;
            color: var(--sf-muted);
            line-height: 1.7;
            font-size: 16px;
        }

        @media (max-width: 980px) {
            .hero {
                padding: 30px 24px 24px;
                border-radius: 28px;
            }

            .hero-stats {
                min-width: 100%;
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 720px) {
            .page {
                padding: 24px 16px 40px;
            }

            .hero-title {
                font-size: 40px;
            }

            .hero-subtitle {
                font-size: 15px;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }

            .jobs-grid {
                grid-template-columns: 1fr;
            }

            .job-card {
                padding: 20px;
                border-radius: 22px;
            }

            .job-card-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .job-title {
                font-size: 28px;
            }

            .section-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="page">
    @php
        $totalJobs = $jobs->count();
        $closedJobs = $jobs->filter(fn ($job) => $job->isClosed())->count();
        $openJobs = $totalJobs - $closedJobs;
    @endphp

    <section class="hero">
        <div class="hero-inner">
            <div class="hero-left">
                <div class="hero-logo">
                    <img src="/images/sada-horizontal.png" alt="Sada Fezzan">
                </div>

                <div class="eyebrow">Careers Portal</div>

                <h1 class="hero-title">Open Jobs</h1>

                <p class="hero-subtitle">
                    Explore career opportunities at Sada Fezzan for Oil Services and review the latest openings across departments, projects, and work locations.
                </p>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value">{{ $totalJobs }}</div>
                    <div class="hero-stat-label">Total Jobs</div>
                </div>

                <div class="hero-stat">
                    <div class="hero-stat-value">{{ $openJobs }}</div>
                    <div class="hero-stat-label">Open Roles</div>
                </div>

                <div class="hero-stat">
                    <div class="hero-stat-value">{{ $closedJobs }}</div>
                    <div class="hero-stat-label">Closed Roles</div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-head">
        <h2 class="section-title">Available Positions</h2>
    </div>

    @if($jobs->count())
        <div class="jobs-grid">
            @foreach($jobs as $job)
                @php
                    $isClosed = $job->isClosed();
                @endphp

                <article class="job-card">
                    <div class="job-card-top">
                        <div class="job-title-wrap">
                            <h3 class="job-title">{{ $job->title }}</h3>
                            <div class="job-label">Sada Fezzan Opportunity</div>
                        </div>

                        @if($isClosed)
                            <span class="job-status job-status-closed">Closed</span>
                        @else
                            <span class="job-status job-status-open">Open</span>
                        @endif
                    </div>

                    <div class="job-meta">
                        <div class="meta-item">
                            <div class="meta-icon">L</div>
                            <div class="meta-text">
                                <span class="meta-label">Location</span>
                                <span class="meta-value">{{ $job->location ?: 'Not specified' }}</span>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-icon">D</div>
                            <div class="meta-text">
                                <span class="meta-label">Department</span>
                                <span class="meta-value">{{ $job->department ?: 'Not specified' }}</span>
                            </div>
                        </div>

                        <div class="meta-item">
                            <div class="meta-icon">T</div>
                            <div class="meta-text">
                                <span class="meta-label">Employment Type</span>
                                <span class="meta-value">{{ $job->employment_type ? ucfirst(str_replace('_', ' ', $job->employment_type)) : 'Not specified' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="job-actions">
                        <a class="job-link" href="{{ \App\Support\PublicUrl::route('jobs.show', ['job' => $job]) }}">
                            View Details
                        </a>

                        @if($isClosed)
                            <span class="closed-note">Applications Closed</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="empty">
            <h3 class="empty-title">No Jobs Available</h3>
            <p class="empty-text">
                There are no active openings at the moment. Please check back again soon for new opportunities.
            </p>
        </div>
    @endif
</div>

</body>
</html>