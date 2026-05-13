<div class="sf-finance-summary">
    <style>
        .sf-finance-summary {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .sf-finance-card {
            border: 1px solid #dbe7f0;
            border-radius: 18px;
            background: #fff;
            padding: 16px 18px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
            min-height: 168px;
            display: flex;
            flex-direction: column;
        }

        .sf-finance-card.dark {
            border-top: 4px solid #163a63;
        }

        .sf-finance-card.info {
            border-top: 4px solid #0ea5e9;
        }

        .sf-finance-card.warning {
            border-top: 4px solid #f59e0b;
        }

        .sf-finance-card.neutral {
            border-top: 4px solid #94a3b8;
        }

        .sf-finance-card.success {
            border-top: 4px solid #22c55e;
        }

        .sf-finance-label {
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 10px;
            line-height: 1.35;
        }

        .sf-finance-value {
            font-size: 20px;
            line-height: 1.2;
            font-weight: 900;
            color: #143a63;
            letter-spacing: -.02em;
            margin-bottom: 12px;
            text-align: center;
            padding: 6px 0;
        }

        .sf-finance-lines {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 12px;
            line-height: 1.45;
            color: #6b7280;
        }

        .sf-finance-line {
            word-break: break-word;
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
            <div class="sf-finance-value">{{ $card['value'] }}</div>

            <div class="sf-finance-lines">
                @foreach (($card['lines'] ?? []) as $line)
                    <div class="sf-finance-line">{{ $line }}</div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
