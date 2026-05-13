<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }
        .page-shell { display: flex; flex-direction: column; gap: 24px; }
        .hero {
            border-radius: 28px;
            padding: 28px;
            background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 42%, #ecfeff 100%);
            border: 1px solid #dbeafe;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.06);
        }
        .hero-title {
            font-size: 38px;
            line-height: 1.05;
            font-weight: 900;
            color: #234b7b;
            margin: 0;
        }
        .hero-subtitle {
            margin-top: 10px;
            color: #64748b;
            font-size: 15px;
        }
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
            border: 1px solid transparent;
        }
        .btn-primary {
            background: #14b8a6;
            color: #ffffff;
        }
        .btn-secondary {
            background: #ffffff;
            color: #1d4ed8;
            border-color: #dbeafe;
        }
        .card {
            background: #ffffff;
            border: 1px solid #dbe4ee;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.04);
        }
        .card-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 14px;
            background: #f8fafc;
            color: #7c8aa0;
            text-transform: uppercase;
            font-size: 13px;
        }
        td {
            padding: 16px 14px;
            border-top: 1px solid #e5edf5;
            color: #0f172a;
            vertical-align: top;
        }
        .empty {
            color: #94a3b8;
            padding: 18px 14px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 800;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="page-shell">
    <div class="hero">
        <h1 class="hero-title">{{ $title }}</h1>
        <div class="hero-subtitle">
            Client: {{ $client->name ?? '-' }}
        </div>

        <div class="actions">
            <a href="{{ route('admin.clients.profile', ['client' => $client]) }}" class="btn btn-primary">Back to Client</a>
            <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('index') }}" class="btn btn-secondary">Back to Clients</a>
        </div>
    </div>

    <div class="card">
        <div class="card-title">{{ $title }}</div>

        <div style="margin-bottom:16px;">
            <span class="badge">Total Records: {{ $items->count() }}</span>
        </div>

        <div style="overflow:auto;">
            <table>
                @if($type === 'invoices')
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Project</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->invoice_number ?? $item->reference_no ?? ('#' . $item->id) }}</td>
                                <td>{{ $item->status ?? '-' }}</td>
                                <td>{{ number_format((float) ($item->total_amount ?? $item->grand_total ?? $item->amount ?? 0), 2) }} {{ $item->currency ?? '' }}</td>
                                <td>{{ optional($item->invoice_date ?? $item->created_at)->format('M j, Y') ?? '-' }}</td>
                                <td>{{ $item->project_name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty">No invoice records found for this client.</td></tr>
                        @endforelse
                    </tbody>
                @elseif($type === 'salary_slips')
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Slip</th>
                            <th>Status</th>
                            <th>Net Amount</th>
                            <th>Project</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->employee_name ?? '-' }}</td>
                                <td>{{ $item->slip_number ?? ('#' . $item->id) }}</td>
                                <td>{{ $item->status ?? '-' }}</td>
                                <td>{{ number_format((float) ($item->net_salary ?? $item->net_amount ?? $item->total_net ?? 0), 2) }} {{ $item->currency ?? '' }}</td>
                                <td>{{ $item->project_name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty">No salary slip records found for this client.</td></tr>
                        @endforelse
                    </tbody>
                @else
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Project</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->title ?? $item->subject ?? ('#' . $item->id) }}</td>
                                <td>{{ $item->category ?? '-' }}</td>
                                <td>{{ $item->status ?? '-' }}</td>
                                <td>{{ number_format((float) ($item->amount ?? 0), 2) }} {{ $item->currency ?? '' }}</td>
                                <td>{{ $item->project_name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty">No expense records found for this client.</td></tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
    </div>
</div>
</body>
</html>
