<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Submitted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --primary: #3C9FA3;
            --primary-dark: #2E8B8F;
            --primary-soft: #E8F7F7;
            --heading: #1F314D;
            --text: #1F2937;
            --muted: #6B7280;
            --white: #FFFFFF;
            --success: #065F46;
            --success-bg: #ECFDF5;
            --shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(60,159,163,.10), transparent 20%),
                radial-gradient(circle at top right, rgba(119,200,203,.14), transparent 22%),
                linear-gradient(180deg, #F8FBFC 0%, #F3F7F9 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 680px;
            background: var(--white);
            border-radius: 26px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeUp .5s ease;
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

        .top-strip {
            height: 9px;
            background: linear-gradient(90deg, var(--primary) 0%, #7ED0D3 100%);
        }

        .card-inner {
            padding: 38px 34px 32px;
            text-align: center;
        }

        .logo {
            margin-bottom: 18px;
        }

        .logo img {
            height: 60px;
            object-fit: contain;
        }

        .icon-wrap {
            width: 82px;
            height: 82px;
            border-radius: 999px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--success-bg);
            box-shadow: inset 0 0 0 8px rgba(16,185,129,0.08);
        }

        .icon {
            font-size: 36px;
            line-height: 1;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            border: 1px solid #D7EFEF;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        h1 {
            margin: 0 0 12px;
            color: var(--heading);
            font-size: 30px;
            line-height: 1.15;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .lead {
            margin: 0 auto 10px;
            max-width: 540px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.8;
        }

        .job-box {
            margin: 24px auto 0;
            max-width: 520px;
            padding: 18px 20px;
            border-radius: 18px;
            background: linear-gradient(180deg, #FAFDFD 0%, #F3FBFB 100%);
            border: 1px solid #E1F0F1;
            text-align: left;
        }

        .job-box .label {
            margin: 0 0 6px;
            font-size: 12px;
            font-weight: 800;
            color: var(--muted);
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .job-box .value {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: var(--heading);
        }

        .note {
            margin: 20px auto 0;
            max-width: 540px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 28px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            padding: 12px 18px;
            border-radius: 14px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
            transition: transform .15s ease, background .2s ease, box-shadow .2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary) 0%, #55B5BA 100%);
            color: #fff;
            box-shadow: 0 12px 22px rgba(60, 159, 163, 0.18);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: #EEF4F6;
            color: var(--heading);
        }

        .btn-secondary:hover {
            background: #E3ECEF;
        }

        @media (max-width: 640px) {
            .card-inner {
                padding: 28px 18px 24px;
            }

            h1 {
                font-size: 25px;
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
    <div class="top-strip"></div>

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
            Our recruitment team will review your CV and contact you if your profile matches the position requirements.
        </p>

        <div class="job-box">
            <p class="label">Position Applied For</p>
            <p class="value">{{ $job->title }}</p>
        </div>

        <p class="note">
            You may now return to the jobs page to explore other openings, or go back to the homepage.
        </p>

        <div class="actions">
            <a href="/jobs" class="btn btn-primary">Browse Jobs</a>
            <a href="/" class="btn btn-secondary">Home</a>
        </div>
    </div>
</div>

</body>
</html>