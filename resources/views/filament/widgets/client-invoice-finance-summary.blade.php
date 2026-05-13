<div class="sf-finance-summary">
    <style>
        .sf-finance-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:14px}
        .sf-finance-card{border:1px solid #dbe7f0;border-radius:18px;background:#fff;padding:16px 18px;box-shadow:0 8px 24px rgba(15,23,42,.04)}
        .sf-finance-card.dark{border-top:4px solid #163a63}
        .sf-finance-card.info{border-top:4px solid #0ea5e9}
        .sf-finance-card.warning{border-top:4px solid #f59e0b}
        .sf-finance-card.neutral{border-top:4px solid #94a3b8}
        .sf-finance-label{font-size:12px;font-weight:800;color:#64748b;margin-bottom:8px}
        .sf-finance-value{font-size:28px;line-height:1.05;font-weight:900;color:#143a63;letter-spacing:-.03em;margin-bottom:8px}
        .sf-finance-note{font-size:12px;line-height:1.45;color:#6b7280}
        @media (max-width: 1100px){.sf-finance-summary{grid-template-columns:repeat(2,minmax(0,1fr));}}
        @media (max-width: 680px){.sf-finance-summary{grid-template-columns:1fr;}}
    </style>

    @foreach ($cards as $card)
        <div class="sf-finance-card {{ $card['tone'] ?? 'neutral' }}">
            <div class="sf-finance-label">{{ $card['label'] }}</div>
            <div class="sf-finance-value">{{ $card['value'] }}</div>
            <div class="sf-finance-note">{{ $card['note'] }}</div>
        </div>
    @endforeach
</div>
