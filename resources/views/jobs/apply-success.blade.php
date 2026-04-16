<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Submitted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --sf-primary: #2c5377;
            --sf-primary-soft: #3f6c96;
            --sf-accent: #26b6b7;
            --sf-accent-dark: #16999a;
            --sf-text: #18212b;
            --sf-muted: #6b7f90;
            --sf-bg-1: #f4f8fa;
            --sf-bg-2: #eef4f7;
            --sf-success: #1d8f79;
            --sf-success-bg: #ebfbf7;
            --sf-shadow: 0 24px 70px rgba(25, 41, 61, 0.08);
            --sf-shadow-soft: 0 14px 35px rgba(25, 41, 61, 0.06);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(38, 182, 183, 0.10), transparent 28%),
                radial-gradient(circle at top right, rgba(44, 83, 119, 0.09), transparent 32%),
                linear-gradient(180deg, var(--sf-bg-1) 0%, var(--sf-bg-2) 100%);
            color: var(--sf-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 760px;
            border-radius: 34px;
            border: 1px solid rgba(255,255,255,0.60);
            background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(247,250,251,0.90) 100%);
            box-shadow: var(--sf-shadow);
            backdrop-filter: blur(10px);
            animation: fadeUp .5s ease;
        }

        .card::before {
            content: "";
            position: absolute;
            right: -70px;
            top: -70px;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(38,182,183,0.14) 0%, rgba(38,182,183,0.03) 60%, transparent 74%);
            pointer-events: none;
        }

        .card::after {
            content: "";
            position: absolute;
            left: -60px;
            bottom: -90px;
            width: 200px;
            height: 200px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(44,83,119,0.10) 0%, rgba(44,83,119,0.02) 62%, transparent 74%);
            pointer-events: none;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-inner {
            position: relative;
            z-index: 1;
            padding: 40px 36px 34px;
            text-align: center;
        }

        .logo {
            margin-bottom: 18px;
        }

        .logo img {
            height: 62px;
            object-fit: contain;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(38,182,183,0.10);
            color: var(--sf-accent-dark);
            border: 1px solid rgba(38,182,183,0.18);
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .10em;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .icon-wrap {
            width: 86px;
            height: 86px;
            border-radius: 999px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--sf-success-bg);
            box-shadow: inset 0 0 0 8px rgba(29,143,121,0.08);
        }

        .icon {
            font-size: 36px;
            line-height: 1;
            color: var(--sf-success);
            font-weight: 900;
        }

        h1 {
            margin: 0 0 12px;
            color: var(--sf-primary);
            font-size: clamp(30px, 4vw, 46px);
            line-height: 1.08;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .lead {
            margin: 0 auto 10px;
            max-width: 560px;
            color: var(--sf-muted);
            font-size: 16px;
            line-height: 1.85;
        }

        .job-box {
            margin: 26px auto 0;
            max-width: 560px;
            padding: 20px 22px;
            border-radius: 22px;
            background: rgba(255,255,255,0.76);
            border: 1px solid rgba(44,83,119,0.08);
            text-align: left;
            box-shadow: var(--sf-shadow-soft);
        }

        .job-box .label {
            margin: 0 0 6px;
            font-size: 11px;
            font-weight: 800;
            color: var(--sf-muted);
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .job-box .value {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: var(--sf-primary);
            line-height: 1.3;
        }

        .note {
            margin: 20px auto 0;
            max-width: 560px;
            color: var(--sf-muted);
            font-size: 14px;
            line-height: 1.75;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 170px;
            min-height: 52px;
            padding: 0 20px;
            border-radius: 16px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 850;
            transition: transform .15s ease, background .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--sf-accent) 0%, #39c7c8 100%);
            color: #fff;
            box-shadow: 0 14px 24px rgba(38, 182, 183, 0.22);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.84);
            color: var(--sf-primary);
            border: 1px solid rgba(44,83,119,0.12);
        }

        @media (max-width: 640px) {
            .card-inner {
                padding: 28px 18px 24px;
            }

            .job-box {
                text-align: center;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-inner">
        <div class="logo">
            <img src="/images/sada-horizontal.png" alt="Sada Fezzan">
        </div>

        <div class="badge">Application Received</div>

        <div class="icon-wrap">
            <div class="icon">✓</div>
        </div>

        <h1>Thank You for Your Submission</h1>

        <p class="lead">
            Your application has been submitted successfully.
            Our recruitment team will review your profile and contact you if your qualifications match the role requirements.
        </p>

        <div class="job-box">
            <p class="label">Position Applied For</p>
            <p class="value">{{ $job->title }}</p>
        </div>

        <p class="note">
            You can now return to the jobs page to explore other available roles.
        </p>

        <div class="actions">
            <a href="{{ \App\Support\PublicUrl::route('jobs.index') }}" class="btn btn-primary">Browse Jobs</a>
            <a href="{{ rtrim(config('app.public_app_url'), '/') }}/" class="btn btn-secondary">Home</a>
        </div>
    </div>
</div>

</body>
</html>