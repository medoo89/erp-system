<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $job->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Inter, system-ui, sans-serif;
            background: #F7F9FA;
            color: #1F2A37;
        }

        .page {
            max-width: 900px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .card {
            background: #fff;
            border: 1px solid #D9E3E6;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .04);
        }

        .logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo img {
            height: 56px;
        }

        h1 {
            font-size: 38px;
            color: #2C5377;
            margin-bottom: 20px;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 25px;
        }

        .meta-box {
            background: #F7F9FA;
            border: 1px solid #D9E3E6;
            border-radius: 12px;
            padding: 16px;
        }

        .meta-box strong {
            display: block;
            color: #2C5377;
            margin-bottom: 6px;
        }

        h3 {
            margin-top: 28px;
            color: #2C5377;
        }

        p {
            line-height: 1.8;
            color: #334155;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: #3C9FA3;
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 700;
        }

        .btn-primary:hover {
            background: #2F878A;
        }

        .btn-secondary {
            background: #fff;
            color: #2C5377;
            text-decoration: none;
            padding: 12px 20px;
            border: 1px solid #D9E3E6;
            border-radius: 10px;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="page">
    <div class="card">
        <div class="logo">
            <img src="/images/sada-horizontal.png" alt="Sada Fezzan">
        </div>

        <h1>{{ $job->title }}</h1>

        <div class="meta">
            <div class="meta-box">
                <strong>Department</strong>
                {{ $job->department ?: 'Not specified' }}
            </div>

            <div class="meta-box">
                <strong>Location</strong>
                {{ $job->location ?: 'Not specified' }}
            </div>

            <div class="meta-box">
                <strong>Employment Type</strong>
                {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}
            </div>
        </div>

        <h3>Description</h3>
        <p>{{ $job->description ?: 'No description available.' }}</p>

        <h3>Requirements</h3>
        <p>{{ $job->requirements ?: 'No requirements available.' }}</p>

        <div class="actions">
            <a class="btn-primary" href="{{ \App\Support\PublicUrl::route('jobs.apply', ['job' => $job]) }}">Apply for this Job</a>
            <a class="btn-secondary" href="{{ \App\Support\PublicUrl::route('jobs.index') }}">Back to Jobs</a>
        </div>
    </div>
</div>

</body>
</html>