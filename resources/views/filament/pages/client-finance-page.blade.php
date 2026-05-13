<x-filament-panels::page>
    @php
        $client = $this->client;
        $rows = $this->rows;
        $type = $this->type;
    @endphp

    <style>
        .sf-wrap{display:flex;flex-direction:column;gap:28px}
        .sf-hero{border-radius:28px;padding:30px;background:linear-gradient(135deg,#eff6ff 0%,#f8fbff 42%,#ecfeff 100%);border:1px solid #dbeafe;box-shadow:0 20px 60px rgba(15,23,42,.06)}
        .sf-title{font-size:42px;line-height:1.02;font-weight:900;color:#234b7b;letter-spacing:-.03em;margin:0}
        .sf-sub{margin-top:10px;color:#64748b;font-size:16px}
        .sf-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}
        .sf-btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;border-radius:999px;text-decoration:none;font-weight:800;font-size:13px;border:1px solid transparent;transition:.18s ease}
        .sf-btn:hover{transform:translateY(-1px)}
        .sf-btn-primary{background:#14b8a6;color:#fff}
        .sf-btn-secondary{background:#fff;color:#1d4ed8;border-color:#dbeafe}
        .sf-card{background:#fff;border:1px solid #dbe4ee;border-radius:24px;padding:24px;box-shadow:0 14px 36px rgba(15,23,42,.04)}
        .sf-card-title{font-size:22px;font-weight:800;color:#0f172a;margin-bottom:18px}
        .sf-badge{display:inline-flex;align-items:center;padding:8px 12px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-weight:800;font-size:12px}
        table{width:100%;border-collapse:collapse}
        th{text-align:left;padding:14px;background:#f8fafc;color:#7c8aa0;text-transform:uppercase;font-size:13px}
        td{padding:16px 14px;border-top:1px solid #e5edf5;color:#0f172a;vertical-align:top}
        .empty{color:#94a3b8;padding:18px 14px}
    </style>

    <div class="sf-wrap">
        <div class="sf-hero">
            <h1 class="sf-title">{{ $this->titleText }}</h1>
            <div class="sf-sub">Client: {{ $client->name ?? '-' }}</div>

            <div class="sf-actions">
                <a href="{{ \App\Filament\Pages\ClientProfilePage::getUrl(['client' => $client->id]) }}" class="sf-btn sf-btn-primary">Back to Client</a>
                <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('index') }}" class="sf-btn sf-btn-secondary">Back to Clients</a>
            </div>
        </div>

        <div class="sf-card">
            <div class="sf-card-title">{{ $this->titleText }}</div>

            <div style="margin-bottom:16px;">
                <span class="sf-badge">Total Records: {{ $this->totalRecords }}</span>
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
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ $row['invoice'] }}</td>
                                <td>{{ $row['status'] }}</td>
                                <td>{{ $row['amount'] }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['project'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty">No invoice records found for this client.</td></tr>
                        @endforelse
                        </tbody>
                    @else
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th>Records</th>
                            <th>Latest Status</th>
                            <th>Total Amount</th>
                            <th>Latest Project</th>
                            <th>Latest Expense Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ $row['category'] }}</td>
                                <td>{{ $row['records_count'] }}</td>
                                <td>{{ $row['status'] }}</td>
                                <td>{{ $row['total_amount'] }}</td>
                                <td>{{ $row['project'] }}</td>
                                <td>{{ $row['latest_date'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="empty">No expense records found for this client.</td></tr>
                        @endforelse
                        </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
