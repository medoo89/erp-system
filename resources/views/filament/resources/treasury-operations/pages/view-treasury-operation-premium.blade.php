<x-filament-panels::page>
    @php
        $operation = $this->record;

        $operationType = (string) ($operation->operation_type ?? 'operation');
        $currency = $operation->currency ?: '-';
        $amount = (float) ($operation->amount ?? 0);
        $feeAmount = (float) ($operation->fee_amount ?? 0);

        $sourceAccount = $operation->sourceAccount?->account_name ?: '-';
        $destinationAccount = $operation->destinationAccount?->account_name ?: '-';
        $clearingAccount = $operation->clearingAccount?->account_name ?: '-';

        $businessStatus = (string) ($operation->business_status ?? '-');
        $settlementStatus = (string) ($operation->settlement_status ?? '-');

        $theme = match ($settlementStatus) {
            'pending' => [
                'page' => 'radial-gradient(circle at top right, rgba(245,158,11,.16), transparent 22%), radial-gradient(circle at bottom left, rgba(250,204,21,.12), transparent 22%)',
                'header' => 'linear-gradient(90deg, #fff7ed 0%, #ffffff 40%, #fde68a 100%)',
                'glow' => 'rgba(245,158,11,.48)',
                'title' => '#b45309',
                'meta' => '#9a6b20',
                'border' => '#f59e0b',
                'badgeBg' => '#fff7ed',
                'badgeText' => '#c2410c',
            ],
            'cleared' => [
                'page' => 'radial-gradient(circle at top right, rgba(16,185,129,.16), transparent 22%), radial-gradient(circle at bottom left, rgba(74,222,128,.12), transparent 22%)',
                'header' => 'linear-gradient(90deg, #ecfdf5 0%, #ffffff 40%, #bbf7d0 100%)',
                'glow' => 'rgba(16,185,129,.44)',
                'title' => '#047857',
                'meta' => '#3b7e69',
                'border' => '#34d399',
                'badgeBg' => '#ecfdf5',
                'badgeText' => '#047857',
            ],
            'failed', 'reversed' => [
                'page' => 'radial-gradient(circle at top right, rgba(239,68,68,.16), transparent 22%), radial-gradient(circle at bottom left, rgba(251,113,133,.12), transparent 22%)',
                'header' => 'linear-gradient(90deg, #fff1f2 0%, #ffffff 40%, #fecdd3 100%)',
                'glow' => 'rgba(239,68,68,.44)',
                'title' => '#b91c1c',
                'meta' => '#8f4b55',
                'border' => '#fb7185',
                'badgeBg' => '#fff1f2',
                'badgeText' => '#be123c',
            ],
            default => [
                'page' => 'radial-gradient(circle at top right, rgba(148,163,184,.13), transparent 22%), radial-gradient(circle at bottom left, rgba(203,213,225,.15), transparent 22%)',
                'header' => 'linear-gradient(90deg, #f8fafc 0%, #ffffff 45%, #e2e8f0 100%)',
                'glow' => 'rgba(148,163,184,.38)',
                'title' => '#475569',
                'meta' => '#64748b',
                'border' => '#cbd5e1',
                'badgeBg' => '#f8fafc',
                'badgeText' => '#475569',
            ],
        };

        $statusBadge = function (?string $value): array {
            return match ((string) $value) {
                'pending' => ['Pending', '#fff7ed', '#c2410c', '#fdba74'],
                'cleared' => ['Cleared', '#ecfdf5', '#047857', '#86efac'],
                'failed' => ['Failed', '#fff1f2', '#be123c', '#fda4af'],
                'reversed' => ['Reversed', '#f8fafc', '#475569', '#cbd5e1'],
                'received' => ['Received', '#ecfdf5', '#047857', '#86efac'],
                'sent' => ['Sent', '#eff6ff', '#1d4ed8', '#93c5fd'],
                default => [ucfirst((string) $value), '#f8fafc', '#475569', '#cbd5e1'],
            };
        };

    @endphp

    <style>
        .fi-page { gap: 1rem !important; }

        .fi-header {
            position: relative;
            overflow: hidden;
            border: 1px solid {{ $theme['border'] }} !important;
            border-radius: 32px !important;
            padding: 20px 24px 18px 24px !important;
            background: {{ $theme['header'] }} !important;
            box-shadow: 0 16px 38px rgba(15, 23, 42, .06) !important;
            margin-bottom: 4px !important;
        }

        .fi-header::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            right: -70px;
            top: -70px;
            border-radius: 999px;
            background: {{ $theme['glow'] }};
            filter: blur(42px);
            opacity: .95;
            pointer-events: none;
        }

        .fi-header > * { position: relative; z-index: 1; }

        .fi-header-heading {
            color: {{ $theme['title'] }} !important;
            font-size: 34px !important;
            line-height: 1.05 !important;
            font-weight: 900 !important;
            max-width: 720px !important;
            white-space: normal !important;
            overflow-wrap: anywhere !important;
        }

        .fi-header-subheading {
            color: {{ $theme['meta'] }} !important;
            font-size: 17px !important;
            line-height: 1.7 !important;
            font-weight: 500 !important;
            max-width: 100% !important;
            margin-top: 10px !important;
        }
    </style>

    <div style="display:flex;flex-direction:column;gap:24px;padding:6px;border-radius:34px;background: {{ $theme['page'] }};">
        <section style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;">
            <div style="background:#fff;border:1px solid #dbe7ef;border-radius:24px;padding:22px;">
                <div style="font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#94a3b8;">Amount</div>
                <div style="margin-top:12px;font-size:36px;font-weight:900;color:#0f172a;">{{ number_format($amount, 2) }}</div>
                <div style="margin-top:8px;font-size:14px;color:#64748b;">{{ $currency }}</div>
            </div>

            <div style="background:#fff;border:1px solid #dbe7ef;border-radius:24px;padding:22px;">
                <div style="font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#94a3b8;">Fee Amount</div>
                <div style="margin-top:12px;font-size:30px;font-weight:900;color:#0f172a;">{{ number_format($feeAmount, 2) }}</div>
                <div style="margin-top:8px;font-size:14px;color:#64748b;">{{ $currency }}</div>
            </div>

            <div style="background:#fff;border:1px solid #dbe7ef;border-radius:24px;padding:22px;">
                <div style="font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#94a3b8;">Business Status</div>
                <div style="margin-top:14px;">
                    <span style="display:inline-flex;align-items:center;padding:8px 14px;border-radius:999px;background:{{ $businessBg }};color:{{ $businessColor }};border:1px solid {{ $businessBorder }};font-weight:900;font-size:12px;">
                        {{ $businessText }}
                    </span>
                </div>
            </div>

            <div style="background:#fff;border:1px solid #dbe7ef;border-radius:24px;padding:22px;">
                <div style="font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:#94a3b8;">Settlement Status</div>
                <div style="margin-top:14px;">
                    <span style="display:inline-flex;align-items:center;padding:8px 14px;border-radius:999px;background:{{ $settlementBg }};color:{{ $settlementColor }};border:1px solid {{ $settlementBorder }};font-weight:900;font-size:12px;">
                        {{ $settlementText }}
                    </span>
                </div>
            </div>
        </section>

        <section style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div style="background:#fff;border:1px solid #dbe7ef;border-radius:28px;padding:28px;">
                <div style="display:inline-block;background:{{ $theme['badgeBg'] }};color:{{ $theme['badgeText'] }};border-radius:999px;padding:8px 14px;font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;">
                    Operation Details
                </div>

                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:20px;">
                    <div>
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Operation ID</div>
                        <div style="margin-top:8px;font-size:20px;font-weight:900;color:#0f172a;">#{{ $operation->id }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Operation Type</div>
                        <div style="margin-top:8px;font-size:20px;font-weight:900;color:#0f172a;">{{ $operationType }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Operation Date</div>
                        <div style="margin-top:8px;font-size:18px;font-weight:800;color:#0f172a;">{{ $operation->operation_date?->format('Y-m-d') ?: '-' }}</div>
                    </div>

                    <div>
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Cleared At</div>
                        <div style="margin-top:8px;font-size:18px;font-weight:800;color:#0f172a;">{{ $operation->cleared_at?->format('Y-m-d H:i') ?: '-' }}</div>
                    </div>

                    <div style="grid-column:1 / -1;">
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Description</div>
                        <div style="margin-top:8px;font-size:15px;line-height:1.8;color:#475569;">{{ $operation->description ?: 'No description added.' }}</div>
                    </div>

                    <div style="grid-column:1 / -1;">
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Notes</div>
                        <div style="margin-top:8px;font-size:15px;line-height:1.8;color:#475569;">{{ $operation->notes ?: 'No notes added.' }}</div>
                    </div>
                </div>
            </div>

            <div style="background:#fff;border:1px solid #dbe7ef;border-radius:28px;padding:28px;">
                <div style="display:inline-block;background:#f8fafc;color:#334155;border-radius:999px;padding:8px 14px;font-size:12px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;">
                    Account Flow
                </div>

                <div style="display:flex;flex-direction:column;gap:16px;margin-top:20px;">
                    <div style="border:1px solid #dbe7ef;border-radius:20px;padding:18px;background:#f8fafc;">
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Source Account</div>
                        <div style="margin-top:8px;font-size:20px;font-weight:900;color:#0f172a;">{{ $sourceAccount }}</div>
                        <div style="margin-top:6px;font-size:13px;color:#64748b;">{{ $operation->sourceAccount?->currency ?: '-' }}</div>
                    </div>

                    <div style="border:1px solid #dbe7ef;border-radius:20px;padding:18px;background:#f8fafc;">
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Destination Account</div>
                        <div style="margin-top:8px;font-size:20px;font-weight:900;color:#0f172a;">{{ $destinationAccount }}</div>
                        <div style="margin-top:6px;font-size:13px;color:#64748b;">{{ $operation->destinationAccount?->currency ?: '-' }}</div>
                    </div>

                    <div style="border:1px solid #dbe7ef;border-radius:20px;padding:18px;background:#f8fafc;">
                        <div style="font-size:12px;color:#94a3b8;font-weight:800;text-transform:uppercase;letter-spacing:.12em;">Clearing Account</div>
                        <div style="margin-top:8px;font-size:20px;font-weight:900;color:#0f172a;">{{ $clearingAccount }}</div>
                        <div style="margin-top:6px;font-size:13px;color:#64748b;">{{ $operation->clearingAccount?->currency ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
