<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CAF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11.5px;
            color: #111827;
            line-height: 1.55;
            margin: 120px 48px 70px 48px;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: .4px;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            margin: 18px 0 12px 0;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .fields {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .fields td {
            padding: 7px 0;
            vertical-align: top;
            border-bottom: 1px solid #d1d5db;
        }

        .fields .label {
            width: 42%;
            font-weight: 700;
            padding-right: 14px;
        }

        .fields .value {
            width: 58%;
        }

        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        .approval-table th,
        .approval-table td {
            border: 1px solid #1f2937;
            padding: 10px 12px;
            text-align: center;
            vertical-align: middle;
        }

        .approval-table th {
            background: #f3f4f6;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .approved {
            margin-top: 18px;
            font-weight: 700;
            letter-spacing: .3px;
        }
    </style>
</head>
<body>
    <div class="title">CANDIDATE APPROVAL FORM (CAF)</div>

    <div class="section-title">A. To Be Completed by Sada Fezzan</div>

    <table class="fields">
        <tr>
            <td class="label">Location / Project</td>
            <td class="value">{{ $locationProject }}</td>
        </tr>
        <tr>
            <td class="label">Contract Number</td>
            <td class="value">{{ $contractNumber }}</td>
        </tr>
        <tr>
            <td class="label">Job Title</td>
            <td class="value">{{ $jobTitle }}</td>
        </tr>
        <tr>
            <td class="label">Billing Classification / Rate</td>
            <td class="value">{{ $billingRate }}</td>
        </tr>
        <tr>
            <td class="label">Date Required (Effective Date)</td>
            <td class="value">{{ $dateRequired }}</td>
        </tr>
        <tr>
            <td class="label">Requested By Client (Name)</td>
            <td class="value">{{ $requestedByClient }}</td>
        </tr>
        <tr>
            <td class="label">Sada Fezzan (Name)</td>
            <td class="value">{{ $requestedBySf }}</td>
        </tr>
        <tr>
            <td class="label">Candidate Name</td>
            <td class="value">{{ $candidateName }}</td>
        </tr>
        <tr>
            <td class="label">Nationality</td>
            <td class="value">{{ $nationality }}</td>
        </tr>
        <tr>
            <td class="label">Type of Assignment</td>
            <td class="value">{{ $assignmentType }}</td>
        </tr>
        <tr>
            <td class="label">Recommended By</td>
            <td class="value">{{ $recommendedBy }}</td>
        </tr>
        <tr>
            <td class="label">Date</td>
            <td class="value">{{ $generatedAt->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Reference</td>
            <td class="value"><strong>{{ $document->reference }}</strong></td>
        </tr>
    </table>

    <div class="section-title">B. Approval</div>

    <table class="approval-table">
        <thead>
            <tr>
                <th style="width: 34%;">Name</th>
                <th style="width: 33%;">Signature</th>
                <th style="width: 33%;">Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="height: 54px;"></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="approved">APPROVED</div>
</body>
</html>