<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Open Jobs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Inter, system-ui, sans-serif;
            background: #F7F9FA;
            color: #1F2A37;
        }

        .page {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .topbar {
            text-align: center;
            margin-bottom: 30px;
        }

        .topbar img {
            height: 58px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 36px;
            font-weight: 800;
            color: #2C5377;
            margin: 0;
        }

        .subtitle {
            color: #6F8598;
            margin-top: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 22px;
            margin-top: 35px;
        }

        .job-card {
            background: #fff;
            border: 1px solid #D9E3E6;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .04);
        }

        .job-title {
            font-size: 24px;
            font-weight: 800;
            color: #2C5377;
            margin-bottom: 12px;
        }

        .job-meta {
            color: #6F8598;
            margin-bottom: 18px;
            line-height: 1.7;
        }

        .job-link {
            display: inline-block;
            background: #3C9FA3;
            color: #fff;
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 10px;
            font-weight: 700;
        }

        .job-link:hover {
            background: #2F878A;
        }

        .empty {
            background: #fff;
            border: 1px solid #D9E3E6;
            border-radius: 18px;
            padding: 30px;
            text-align: center;
            color: #6F8598;
        }
    </style>
</head>
<body>

<div class="page">
    <div class="topbar">
        <img src="/images/sada-horizontal.png" alt="Sada Fezzan">
        <h1 class="title">Open Jobs</h1>
        <p class="subtitle">Explore current opportunities at Sada Fezzan For Oil Services</p>
    </div>

    @if($jobs->count())
        <div class="grid">
            @foreach($jobs as $job)
                <div class="job-card">
                    <div class="job-title">{{ $job->title }}</div>

                    <div class="job-meta">
                        <div><strong>Location:</strong> {{ $job->location ?: 'Not specified' }}</div>
                        <div><strong>Department:</strong> {{ $job->department ?: 'Not specified' }}</div>
                        <div><strong>Employment Type:</strong> {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</div>
                    </div>

                    <a class="job-link" href="{{ route('jobs.show', $job) }}">View Details</a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty">
            No open jobs right now.
        </div>
    @endif
</div>

</body>
</html>