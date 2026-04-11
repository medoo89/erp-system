<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>General Letter</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            line-height: 1.65;
            margin: 120px 48px 70px 48px;
        }

        .meta {
            width: 100%;
            margin-bottom: 26px;
        }

        .meta table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 12px;
        }

        .meta .label {
            width: 70px;
            font-weight: 700;
            color: #111827;
        }

        .meta .value {
            color: #111827;
        }

        .ref {
            font-weight: 700;
            letter-spacing: .2px;
        }

        .candidate-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 26px 0;
        }

        .candidate-table th,
        .candidate-table td {
            border: 1px solid #1f2937;
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
        }

        .candidate-table th {
            background: #f3f4f6;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .candidate-table td {
            font-size: 12px;
        }

        p {
            margin: 0 0 14px 0;
        }

        .salutation {
            margin-bottom: 16px;
            font-weight: 600;
        }

        .closing {
            margin-top: 34px;
        }

        .signature-name {
            margin-top: 28px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="meta">
        <table>
            <tr>
                <td class="label">To:</td>
                <td class="value">{{ $toCompany }}</td>
            </tr>
            <tr>
                <td class="label">Att.:</td>
                <td class="value">{{ $attentionName }}</td>
            </tr>
            <tr>
                <td class="label">From:</td>
                <td class="value">{{ $fromCompany }}</td>
            </tr>
            <tr>
                <td class="label">Date:</td>
                <td class="value">{{ $generatedAt->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Ref:</td>
                <td class="value ref">{{ $document->reference }}</td>
            </tr>
        </table>
    </div>

    <table class="candidate-table">
        <thead>
            <tr>
                <th style="width: 32%;">Candidate Name</th>
                <th style="width: 43%;">Position</th>
                <th style="width: 25%;">Availability</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $candidateName }}</td>
                <td>{{ $positionTitle }}</td>
                <td>{{ $availability }}</td>
            </tr>
        </tbody>
    </table>

    <p class="salutation">Dear Sir,</p>

    <p>
        With reference to your request for <strong>{{ $positionTitle }}</strong>, please find attached to this
        letter the CV and CAF for the above-mentioned candidate for your review and approval.
    </p>

    <p>
        Your prompt approval is highly appreciated, as the candidate’s continued availability cannot be guaranteed.
    </p>

    <div class="closing">
        <p>Sincerely,</p>

        <div class="signature-name">{{ $signatoryName }}</div>
    </div>
</body>
</html>