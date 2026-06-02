<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Employment Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            color: #0f172a;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 24px;
            max-width: 900px;
            margin: 0 auto;
        }

        h1 {
            margin: 0 0 16px;
            font-size: 28px;
        }

        .row {
            display: grid;
            grid-template-columns: 220px 1fr;
            border-top: 1px solid #eef2f7;
            padding: 12px 0;
        }

        .label {
            font-weight: 700;
            color: #475569;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()">Print</button>

    <div class="card">
        <h1>Employment Print</h1>

        <div class="row">
            <div class="label">Name</div>
            <div>{{ $employment->name ?? $employment->employee_name ?? $employment->full_name ?? 'Employment #' . $employment->id }}</div>
        </div>

        <div class="row">
            <div class="label">Code</div>
            <div>{{ $employment->employment_code ?? $employment->code ?? '-' }}</div>
        </div>

        <div class="row">
            <div class="label">Status</div>
            <div>{{ $employment->status ?? '-' }}</div>
        </div>
    </div>
</body>
</html>
