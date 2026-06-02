<x-filament-panels::page>
    @php
        $client = $record;
    @endphp

    <style>
        .sf-edit-client-page {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 8px 0 44px;
        }

        .sf-edit-hero {
            border-radius: 30px;
            padding: 32px 34px;
            color: white;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .22), transparent 34%),
                linear-gradient(135deg, #071426 0%, #123a5c 48%, #155f64 100%);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .16);
            border: 1px solid rgba(255,255,255,.12);
            position: relative;
            overflow: hidden;
        }

        .sf-edit-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, #22d3ee, #2563eb, #facc15);
        }

        .sf-edit-kicker {
            font-size: 12px;
            letter-spacing: .20em;
            text-transform: uppercase;
            font-weight: 900;
            color: rgba(255,255,255,.72);
        }

        .sf-edit-title {
            margin-top: 10px;
            font-size: clamp(36px, 5vw, 64px);
            line-height: .95;
            font-weight: 1000;
            letter-spacing: -.055em;
        }

        .sf-edit-subtitle {
            margin-top: 12px;
            font-size: 13px;
            font-weight: 800;
            color: rgba(255,255,255,.74);
        }

        .sf-edit-card {
            margin-top: 22px;
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 35%),
                rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 18px 45px rgba(15,23,42,.08);
            border-radius: 30px;
            overflow: hidden;
        }

        .dark .sf-edit-card {
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.12), transparent 35%),
                rgba(15,23,42,.92);
            border-color: rgba(148,163,184,.18);
        }

        .sf-edit-card-head {
            padding: 22px 26px;
            border-bottom: 1px solid rgba(15,23,42,.08);
        }

        .dark .sf-edit-card-head {
            border-bottom-color: rgba(148,163,184,.16);
        }

        .sf-edit-pill {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 7px 12px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 1000;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .sf-edit-card-title {
            margin-top: 12px;
            font-size: 24px;
            font-weight: 1000;
            color: #0f172a;
            letter-spacing: -.03em;
        }

        .dark .sf-edit-card-title {
            color: white;
        }

        .sf-edit-form-wrap {
            padding: 24px 26px 28px;
        }

        .sf-edit-form-wrap .fi-fo-component-ctn {
            gap: 18px;
        }

        .sf-edit-actions-note {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 20px;
            background: rgba(224,242,254,.65);
            color: #234b74;
            font-size: 13px;
            font-weight: 800;
            border: 1px solid rgba(37,99,235,.12);
        }

        .dark .sf-edit-actions-note {
            background: rgba(30,41,59,.68);
            color: #bfdbfe;
            border-color: rgba(148,163,184,.16);
        }
    </style>

    <div class="sf-edit-client-page">
        <section class="sf-edit-hero">
            <div class="sf-edit-kicker">HR › Clients › Edit</div>
            <div class="sf-edit-title">{{ $client->name ?? 'Client' }}</div>
            <div class="sf-edit-subtitle">
                Client Code:
                {{ $client->client_code ?? $client->code ?? ('CL-' . str_pad((string) $client->id, 4, '0', STR_PAD_LEFT)) }}
            </div>
        </section>

        <section class="sf-edit-card">
            <div class="sf-edit-card-head">
                <div class="sf-edit-pill">Client Information</div>
                <div class="sf-edit-card-title">Edit Profile & Contact Details</div>
            </div>

            <div class="sf-edit-form-wrap">
                {{ $this->form }}

                <div class="sf-edit-actions-note">
                    Save changes will return you automatically to the premium client view page.
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
