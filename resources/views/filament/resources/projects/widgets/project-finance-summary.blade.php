<div class="sf-finance-summary">
    <style>
        .sf-finance-summary {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin: 16px 0 18px;
        }

        .sf-finance-card {
            border: 1px solid #dbe7f0;
            border-radius: 20px;
            background: #fff;
            padding: 16px 16px 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
            min-height: 178px;
            display: flex;
            flex-direction: column;
        }

        .sf-finance-card.dark { border-top: 4px solid #163a63; }
        .sf-finance-card.info { border-top: 4px solid #0ea5e9; }
        .sf-finance-card.warning { border-top: 4px solid #f59e0b; }
        .sf-finance-card.neutral { border-top: 4px solid #94a3b8; }
        .sf-finance-card.success { border-top: 4px solid #22c55e; }

        .sf-finance-label {
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 12px;
            line-height: 1.35;
            min-height: 32px;
        }

        .sf-finance-entries {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 2px;
            flex: 1;
        }

        .sf-finance-entry {
            padding: 10px 10px 12px;
            border-radius: 14px;
            background: #f8fbff;
            border: 1px solid #e5eef7;
        }

        .sf-finance-entry.empty {
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 112px;
        }

        .sf-finance-currency {
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 6px;
            letter-spacing: .06em;
            text-transform: uppercase;
            text-align: center;
        }

        .sf-finance-amount {
            font-size: 19px;
            line-height: 1.1;
            font-weight: 900;
            color: #143a63;
            letter-spacing: -.02em;
            text-align: center;
            word-break: break-word;
        }

        .sf-finance-empty {
            font-size: 12px;
            color: #7b8794;
            line-height: 1.4;
            text-align: center;
            font-weight: 600;
        }

        @media (max-width: 1400px) {
            .sf-finance-summary {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1000px) {
            .sf-finance-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .sf-finance-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @foreach ($cards as $card)
        <div class="sf-finance-card {{ $card['tone'] ?? 'neutral' }}">
            <div class="sf-finance-label">{{ $card['label'] }}</div>

            <div class="sf-finance-entries">
                @foreach (($card['entries'] ?? []) as $entry)
                    <div class="sf-finance-entry {{ !empty($entry['empty']) ? 'empty' : '' }}">
                        @if (!empty($entry['empty']))
                            <div class="sf-finance-empty">No records yet</div>
                        @else
                            <div class="sf-finance-currency">{{ $entry['currency'] }}</div>
                            <div class="sf-finance-amount">{{ $entry['amount'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
