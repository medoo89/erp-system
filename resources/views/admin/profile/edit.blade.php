<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Sada Fezzan RFO Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400..700,0..1,-50..200" rel="stylesheet">

    <style>
        :root {
            --sf-navy: #10243f;
            --sf-blue: #234b74;
            --sf-teal: #4ca7a8;
            --sf-cyan: #22d3ee;
            --sf-bg: #eef7f8;
            --sf-surface: rgba(255,255,255,.88);
            --sf-surface-strong: rgba(255,255,255,.96);
            --sf-text: #0f172a;
            --sf-muted: #64748b;
            --sf-border: rgba(15, 23, 42, .10);
            --sf-shadow: 0 18px 50px rgba(15, 23, 42, .08);
            --sf-radius-xl: 34px;
            --sf-radius-lg: 26px;
            --sf-radius-md: 18px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--sf-text);
            background:
                radial-gradient(circle at top right, rgba(76, 167, 168, .24), transparent 35%),
                radial-gradient(circle at bottom left, rgba(37, 99, 235, .08), transparent 32%),
                linear-gradient(135deg, #f8fafc, #eef7f8);
        }

        .material-symbols-rounded {
            font-variation-settings: 'FILL' 0, 'wght' 650, 'GRAD' 0, 'opsz' 28;
            line-height: 1;
            vertical-align: middle;
        }

        .sf-shell {
            width: min(1180px, calc(100% - 36px));
            margin: 0 auto;
            padding: 36px 0 64px;
        }

        .sf-hero {
            position: relative;
            overflow: hidden;
            border-radius: var(--sf-radius-xl);
            padding: 30px;
            color: #fff;
            background:
                radial-gradient(circle at top right, rgba(45, 212, 191, .24), transparent 34%),
                radial-gradient(circle at bottom left, rgba(37, 99, 235, .16), transparent 30%),
                linear-gradient(135deg, #0f172a, #1f4664 62%, #0f766e);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 18px;
            border: 1px solid rgba(255,255,255,.14);
        }

        .sf-hero::after {
            content: "";
            position: absolute;
            right: -100px;
            top: -140px;
            width: 330px;
            height: 330px;
            border-radius: 999px;
            background: rgba(255,255,255,.10);
        }

        .sf-hero > * {
            position: relative;
            z-index: 1;
        }

        .sf-logo-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .sf-logo {
            width: 96px;
            height: 52px;
            object-fit: contain;
            border-radius: 18px;
            padding: 8px 10px;
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(255,255,255,.26);
            box-shadow: 0 14px 34px rgba(15,23,42,.16);
        }

        .sf-kicker {
            color: #99f6e4;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .sf-mini {
            margin-top: 4px;
            color: rgba(226,232,240,.78);
            font-size: 12px;
            font-weight: 850;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sf-title {
            margin: 0;
            font-size: clamp(44px, 5vw, 76px);
            line-height: .92;
            letter-spacing: -.07em;
            font-weight: 950;
        }

        .sf-sub {
            margin-top: 14px;
            max-width: 720px;
            color: rgba(226,232,240,.88);
            font-weight: 700;
            line-height: 1.6;
        }

        .sf-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            text-decoration: none;
            border-radius: 999px;
            min-height: 48px;
            padding: 0 17px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.18);
            font-weight: 950;
            white-space: nowrap;
            backdrop-filter: blur(14px);
        }

        .sf-grid {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            gap: 18px;
            margin-top: 20px;
            align-items: stretch;
        }

        .sf-card {
            border-radius: var(--sf-radius-xl);
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 36%),
                var(--sf-surface);
            border: 1px solid var(--sf-border);
            box-shadow: var(--sf-shadow);
            padding: 24px;
            backdrop-filter: blur(18px);
        }

        .sf-profile-card {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 520px;
        }

        .sf-avatar {
            width: 154px;
            height: 154px;
            border-radius: 46px;
            margin: 4px auto 18px;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.35), transparent 38%),
                linear-gradient(135deg, #0f172a, #234b74);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            font-weight: 950;
            overflow: hidden;
            border: 7px solid rgba(255,255,255,.78);
            box-shadow: 0 18px 44px rgba(15,23,42,.16);
        }

        .sf-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sf-name {
            font-size: 28px;
            font-weight: 950;
            letter-spacing: -.05em;
            color: var(--sf-blue);
        }

        .sf-email {
            margin-top: 6px;
            color: var(--sf-muted);
            font-weight: 750;
            word-break: break-all;
        }

        .sf-chip-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-top: 18px;
        }

        .sf-chip {
            border-radius: 999px;
            padding: 9px 12px;
            background: #e0f2fe;
            color: #075985;
            font-size: 12px;
            font-weight: 950;
            border: 1px solid rgba(14,165,233,.16);
        }

        .sf-profile-note {
            margin-top: 24px;
            border-radius: 24px;
            padding: 16px;
            background: rgba(224,242,254,.60);
            color: #234b74;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.55;
            text-align: left;
            width: 100%;
            border: 1px solid rgba(14,165,233,.14);
        }

        .sf-section-title {
            color: var(--sf-blue);
            font-size: 25px;
            font-weight: 950;
            letter-spacing: -.05em;
            margin: 0;
        }

        .sf-section-sub {
            color: var(--sf-muted);
            font-size: 13px;
            font-weight: 750;
            margin-top: 6px;
        }

        .sf-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .sf-section-icon {
            width: 46px;
            height: 46px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #0f766e;
            background: #ccfbf1;
            flex: 0 0 auto;
        }

        .sf-section-icon .material-symbols-rounded {
            font-size: 28px;
        }

        .sf-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .sf-field {
            display: grid;
            gap: 8px;
        }

        .sf-field.full {
            grid-column: 1 / -1;
        }

        .sf-field label {
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .10em;
            color: #475569;
        }

        .sf-input {
            width: 100%;
            min-height: 52px;
            border-radius: 18px;
            border: 1px solid rgba(15,23,42,.10);
            background: rgba(255,255,255,.92);
            padding: 0 15px;
            color: #0f172a;
            font-weight: 800;
            outline: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.80);
        }

        .sf-input:focus {
            border-color: rgba(37,99,235,.55);
            box-shadow: 0 0 0 4px rgba(37,99,235,.10);
        }

        .sf-input[disabled] {
            color: #475569;
            background: rgba(241,245,249,.75);
        }

        .sf-upload-zone {
            position: relative;
            min-height: 148px;
            border-radius: 24px;
            border: 1.5px dashed rgba(76,167,168,.40);
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.14), transparent 36%),
                rgba(248,250,252,.82);
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px;
            cursor: pointer;
            transition: transform .18s ease, border-color .18s ease, background .18s ease;
            overflow: hidden;
        }

        .sf-upload-zone:hover {
            transform: translateY(-1px);
            border-color: rgba(37,99,235,.50);
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.20), transparent 36%),
                rgba(255,255,255,.96);
        }

        .sf-upload-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .sf-upload-icon {
            width: 64px;
            height: 64px;
            border-radius: 24px;
            background: #e0f2fe;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.86);
        }

        .sf-upload-icon .material-symbols-rounded {
            font-size: 34px;
        }

        .sf-upload-title {
            color: #0f172a;
            font-weight: 950;
            font-size: 16px;
            letter-spacing: -.02em;
        }

        .sf-upload-sub {
            margin-top: 5px;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
            line-height: 1.45;
        }

        .sf-upload-file {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 30px;
            padding: 0 10px;
            border-radius: 999px;
            background: #ecfeff;
            color: #0e7490;
            font-size: 12px;
            font-weight: 950;
            max-width: 100%;
        }

        .sf-upload-file span:last-child {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sf-divider {
            height: 1px;
            background: rgba(15,23,42,.08);
            margin: 24px 0;
        }

        .sf-btn-row {
            margin-top: 24px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .sf-btn {
            border: 0;
            border-radius: 999px;
            min-height: 52px;
            padding: 0 20px;
            background: #2563eb;
            color: #fff;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 14px 30px rgba(37,99,235,.20);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sf-btn-soft {
            background: #e0f2fe;
            color: #075985;
            box-shadow: none;
            text-decoration: none;
        }

        .sf-alert {
            margin-top: 18px;
            border-radius: 22px;
            padding: 15px 17px;
            font-weight: 900;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid rgba(16,185,129,.22);
        }

        .sf-alert.danger {
            background: #fef2f2;
            color: #b91c1c;
            border-color: rgba(239,68,68,.22);
        }

        @media (max-width: 940px) {
            .sf-grid {
                grid-template-columns: 1fr;
            }

            .sf-profile-card {
                min-height: auto;
            }

            .sf-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .sf-form-grid {
                grid-template-columns: 1fr;
            }

            .sf-btn-row {
                justify-content: stretch;
                flex-direction: column;
            }

            .sf-btn,
            .sf-btn-soft {
                width: 100%;
            }
        }
        .sf-avatar-upload-card {
            margin-top: 12px;
            border-radius: 22px;
            border: 1px solid rgba(148,163,184,.22);
            background: rgba(248,250,252,.76);
            padding: 14px;
            display: grid;
            gap: 12px;
        }

        .sf-avatar-preview-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .sf-avatar-live-preview {
            width: 74px;
            height: 74px;
            min-width: 74px;
            border-radius: 999px;
            overflow: hidden;
            background: #020617;
            color: #fff;
            border: 4px solid #2563eb;
            box-shadow: 0 14px 28px rgba(37,99,235,.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 950;
        }

        .sf-avatar-live-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .sf-avatar-upload-meta {
            min-width: 0;
        }

        .sf-avatar-upload-title {
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
            margin-bottom: 4px;
        }

        .sf-avatar-upload-sub {
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
            line-height: 1.5;
        }

        .sf-avatar-upload-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border-radius: 999px;
            padding: 0 16px;
            background: #e0f2fe;
            color: #075985;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            border: 1px solid rgba(14,165,233,.22);
            width: fit-content;
        }

        .sf-avatar-upload-button input {
            display: none;
        }

        .sf-upload-progress {
            display: none;
            height: 10px;
            border-radius: 999px;
            background: rgba(226,232,240,.9);
            overflow: hidden;
            position: relative;
        }

        .sf-upload-progress.is-active {
            display: block;
        }

        .sf-upload-progress::before {
            content: "";
            position: absolute;
            inset: 0;
            width: 35%;
            border-radius: inherit;
            background: linear-gradient(90deg, #14b8a6, #2563eb);
            animation: sfUploadProgress 1s ease-in-out infinite;
        }

        @keyframes sfUploadProgress {
            0% { transform: translateX(-120%); }
            100% { transform: translateX(320%); }
        }

    </style>

<style id="sf-profile-avatar-crop-modal-final">
    /*
     | Hide duplicate old upload panel if previous patches created two upload areas.
     | Keep only the first profile picture upload card.
     */
    .sf-avatar-upload-card ~ .sf-avatar-upload-card,
    .sf-avatar-upload-card + .sf-upload-panel,
    .sf-avatar-upload-card + [class*="upload"],
    .sf-avatar-upload-card ~ [class*="upload-profile"],
    .sf-avatar-upload-card ~ .sf-file-upload-card {
        display: none !important;
    }

    .sf-avatar-crop-modal {
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15,23,42,.62);
        backdrop-filter: blur(14px);
    }

    .sf-avatar-crop-modal.is-open {
        display: flex;
    }

    .sf-avatar-crop-card {
        width: min(620px, 96vw);
        border-radius: 34px;
        background: rgba(255,255,255,.96);
        border: 1px solid rgba(148,163,184,.24);
        box-shadow: 0 30px 90px rgba(15,23,42,.28);
        overflow: hidden;
    }

    .sf-avatar-crop-head {
        padding: 22px 24px;
        border-bottom: 1px solid rgba(148,163,184,.18);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .sf-avatar-crop-title {
        color: #0f172a;
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -.04em;
        margin: 0;
    }

    .sf-avatar-crop-sub {
        color: #64748b;
        font-size: 13px;
        font-weight: 750;
        margin-top: 5px;
    }

    .sf-avatar-crop-close {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.22);
        background: #f8fafc;
        color: #0f172a;
        font-size: 22px;
        font-weight: 950;
        cursor: pointer;
    }

    .sf-avatar-crop-body {
        padding: 24px;
        display: grid;
        gap: 20px;
    }

    .sf-avatar-crop-stage-wrap {
        display: flex;
        justify-content: center;
    }

    .sf-avatar-crop-stage {
        width: 320px;
        height: 320px;
        border-radius: 999px;
        overflow: hidden;
        background: #020617;
        border: 5px solid #2563eb;
        box-shadow: 0 18px 42px rgba(37,99,235,.20);
        position: relative;
        cursor: grab;
        touch-action: none;
        user-select: none;
    }

    .sf-avatar-crop-stage:active {
        cursor: grabbing;
    }

    .sf-avatar-crop-stage img {
        position: absolute;
        left: 50%;
        top: 50%;
        max-width: none;
        max-height: none;
        transform-origin: center;
        user-select: none;
        pointer-events: none;
        will-change: transform;
    }

    .sf-avatar-crop-stage::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: inherit;
        box-shadow: inset 0 0 0 999px rgba(255,255,255,.00), inset 0 0 0 1px rgba(255,255,255,.35);
        pointer-events: none;
    }

    .sf-avatar-crop-controls {
        display: grid;
        gap: 14px;
    }

    .sf-avatar-crop-control label {
        display: flex;
        justify-content: space-between;
        color: #334155;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .sf-avatar-crop-control input[type="range"] {
        width: 100%;
        accent-color: #2563eb;
    }

    .sf-avatar-crop-actions {
        padding: 18px 24px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .sf-avatar-crop-btn {
        min-height: 46px;
        border-radius: 999px;
        border: 0;
        padding: 0 20px;
        font-size: 14px;
        font-weight: 950;
        cursor: pointer;
    }

    .sf-avatar-crop-btn-soft {
        background: #f8fafc;
        color: #0f172a;
        border: 1px solid rgba(148,163,184,.24);
    }

    .sf-avatar-crop-btn-primary {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 14px 30px rgba(37,99,235,.20);
    }

    .sf-avatar-upload-card {
        border-style: solid !important;
        border-radius: 28px !important;
    }

    .sf-avatar-upload-button {
        background: #dff4ff !important;
        color: #075985 !important;
    }

    .dark .sf-avatar-crop-card {
        background: rgba(15,23,42,.96);
        border-color: rgba(148,163,184,.18);
    }

    .dark .sf-avatar-crop-title {
        color: #f8fafc;
    }

    .dark .sf-avatar-crop-sub,
    .dark .sf-avatar-crop-control label {
        color: #94a3b8;
    }

    .dark .sf-avatar-crop-close,
    .dark .sf-avatar-crop-btn-soft {
        background: rgba(15,23,42,.82);
        color: #f8fafc;
        border-color: rgba(148,163,184,.18);
    }
</style>

</head>

<body>
    @php
        $avatarUrl = $user->avatar_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) : null;
        $initials = collect(explode(' ', trim($user->name ?: $user->email)))
            ->filter()
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
    @endphp

    <main class="sf-shell">
        <section class="sf-hero">
            <div>
                <div class="sf-logo-row">
                    <img src="/images/sada-horizontal.png" alt="Sada Fezzan" class="sf-logo">
                    <div>
                        <div class="sf-kicker">Sada Fezzan RFO Platform</div>
                        <div class="sf-mini">User Control Panel</div>
                    </div>
                </div>

                <h1 class="sf-title">My Profile</h1>
                <div class="sf-sub">
                    Manage your ERP account information, avatar, phone number, and password with a secure profile control panel.
                </div>
            </div>

            <a class="sf-back" href="{{ url('/admin') }}">
                <span class="material-symbols-rounded">arrow_back</span>
                Back to Dashboard
            </a>
        </section>

        @if(session('success'))
            <div class="sf-alert">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="sf-alert danger">{{ $errors->first() }}</div>
        @endif

        <section class="sf-grid">
            <aside class="sf-card sf-profile-card">
                <div class="sf-avatar">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="Avatar">
                    @else
                        {{ strtoupper($initials ?: 'U') }}
                    @endif
                </div>

                <div class="sf-name">{{ $user->name ?: 'ERP User' }}</div>
                <div class="sf-email">{{ $user->email }}</div>

                <div class="sf-chip-row">
                    <span class="sf-chip">{{ \App\Models\User::erpRoleOptions()[$user->erp_role] ?? ($user->erp_role ?: 'No Role') }}</span>
                    <span class="sf-chip">{{ $user->erp_department ?: 'No Department' }}</span>
                </div>

                <div class="sf-profile-note">
                    <strong>Profile identity</strong><br>
                    This information is used across the ERP top bar, notifications, approvals, and internal activity records.
                </div>
            </aside>

            <section class="sf-card">
                <form id="sfProfileForm" method="POST" action="{{ route('admin.my-profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="sf-section-head">
                        <div>
                            <h2 class="sf-section-title">Account Details</h2>
                            <div class="sf-section-sub">Update your visible identity and contact information.</div>
                        </div>

                        <div class="sf-section-icon">
                            <span class="material-symbols-rounded">account_circle</span>
                        </div>
                    </div>

                    <div class="sf-form-grid">
                        <div class="sf-field">
                            <label>Name</label>
                            <input class="sf-input" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="sf-field">
                            <label>Email</label>
                            <input class="sf-input" type="email" value="{{ $user->email }}" disabled>
                        </div>

                        <div class="sf-field">
                            <label>Phone</label>
                            <input class="sf-input" type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+218 ...">
                        </div>

                        <div class="sf-field">
                            <label>Account Role</label>
                            <input class="sf-input" type="text" value="{{ \App\Models\User::erpRoleOptions()[$user->erp_role] ?? ($user->erp_role ?: 'No Role') }}" disabled>
                        </div>

                        <div class="sf-field full">
                            <label>Avatar</label>

                            <label class="sf-upload-zone">
                                <div class="sf-avatar-upload-card">
                                    <div class="sf-avatar-preview-row">
                                        <div class="sf-avatar-live-preview" id="avatarLivePreview">
                                            @if($avatarUrl)
                                                <img src="{{ $avatarUrl }}" alt="Avatar preview">
                                            @else
                                                {{ $initials ?: 'U' }}
                                            @endif
                                        </div>

                                        <div class="sf-avatar-upload-meta">
                                            <div class="sf-avatar-upload-title">Profile Picture</div>
                                            <div class="sf-avatar-upload-sub">Upload JPG or PNG image up to 15MB. Crop and zoom are available before saving.</div>
                                        </div>
                                    </div>

                                    <label class="sf-avatar-upload-button">
                                        Choose Picture
                                        <input id="avatarInput" type="file" name="avatar" accept="image/*" onchange="previewAvatarFileName(this)">
                                    </label>

                                    <div class="sf-upload-progress" id="profileUploadProgress"></div>
                                </div>
                                <div class="sf-upload-icon">
                                    <span class="material-symbols-rounded">cloud_upload</span>
                                </div>

                                <div style="min-width:0;">
                                    <div class="sf-upload-title">Upload profile photo</div>
                                    <div class="sf-upload-sub">Click here to choose an image. PNG, JPG, or WEBP up to 4MB.</div>

                                    <div class="sf-upload-file">
                                        <span class="material-symbols-rounded" style="font-size:18px;">image</span>
                                        <span id="avatarFileName">{{ $user->avatar_path ? basename($user->avatar_path) : 'No file selected yet' }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="sf-divider"></div>

                    <div class="sf-section-head">
                        <div>
                            <h2 class="sf-section-title">Change Password</h2>
                            <div class="sf-section-sub">Leave password fields empty if you do not want to change your password.</div>
                        </div>

                        <div class="sf-section-icon">
                            <span class="material-symbols-rounded">lock_reset</span>
                        </div>
                    </div>

                    <div class="sf-form-grid">
                        <div class="sf-field">
                            <label>Current Password</label>
                            <input class="sf-input" type="password" name="current_password" autocomplete="current-password">
                        </div>

                        <div class="sf-field">
                            <label>New Password</label>
                            <input class="sf-input" type="password" name="new_password" autocomplete="new-password">
                        </div>

                        <div class="sf-field">
                            <label>Confirm New Password</label>
                            <input class="sf-input" type="password" name="new_password_confirmation" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="sf-btn-row">
                        <a href="{{ url('/admin') }}" class="sf-btn sf-btn-soft">
                            <span class="material-symbols-rounded">dashboard</span>
                            Dashboard
                        </a>

                        <button class="sf-btn" type="submit">
                            <span class="material-symbols-rounded">save</span>
                            Save Profile
                        </button>
                    </div>
                </form>
            </section>
        </section>
    </main>

    <script>
        function previewAvatarFileName(input) {
            const label = document.getElementById('avatarFileName');

            if (!label) return;

            if (input.files && input.files.length > 0) {
                label.textContent = input.files[0].name;
            } else {
                label.textContent = 'No file selected yet';
            }
        }
    </script>

<script id="sf-profile-avatar-preview-loading-final">
    function previewAvatarFileName(input) {
        const label = document.getElementById('avatarFileName');
        const preview = document.getElementById('avatarLivePreview');

        if (label) {
            label.textContent = input.files && input.files.length ? input.files[0].name : 'No file selected yet';
        }

        if (preview && input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function (event) {
                preview.innerHTML = `<img src="${event.target.result}" alt="Avatar preview">`;
            };

            reader.readAsDataURL(file);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('sfProfileForm');
        const progress = document.getElementById('profileUploadProgress');

        if (!form || !progress) return;

        form.addEventListener('submit', function () {
            const fileInput = document.getElementById('avatarInput');

            if (fileInput && fileInput.files && fileInput.files.length) {
                progress.classList.add('is-active');
            }
        });
    });
</script>


<div class="sf-avatar-crop-modal" id="sfAvatarCropModal" aria-hidden="true">
    <div class="sf-avatar-crop-card">
        <div class="sf-avatar-crop-head">
            <div>
                <h2 class="sf-avatar-crop-title">Adjust Profile Picture</h2>
                <div class="sf-avatar-crop-sub">Drag the image to center it inside the circle, then use zoom.</div>
            </div>

            <button type="button" class="sf-avatar-crop-close" id="sfAvatarCropClose">×</button>
        </div>

        <div class="sf-avatar-crop-body">
            <div class="sf-avatar-crop-stage-wrap">
                <div class="sf-avatar-crop-stage" id="sfAvatarCropStage">
                    <img id="sfAvatarCropImage" alt="Crop preview">
                </div>
            </div>

            <div class="sf-avatar-crop-controls">
                <div class="sf-avatar-crop-control">
                    <label>
                        <span>Zoom</span>
                        <span id="sfAvatarZoomValue">100%</span>
                    </label>
                    <input id="sfAvatarZoom" type="range" min="1" max="3" step="0.01" value="1">
                </div>
            </div>
        </div>

        <div class="sf-avatar-crop-actions">
            <button type="button" class="sf-avatar-crop-btn sf-avatar-crop-btn-soft" id="sfAvatarCropCancel">Cancel</button>
            <button type="button" class="sf-avatar-crop-btn sf-avatar-crop-btn-primary" id="sfAvatarCropApply">Apply Crop</button>
        </div>
    </div>
</div>


<script id="sf-profile-avatar-crop-js-final">
(function () {
    let originalFile = null;
    let imageNaturalWidth = 0;
    let imageNaturalHeight = 0;
    let zoom = 1;
    let offsetX = 0;
    let offsetY = 0;
    let dragging = false;
    let startX = 0;
    let startY = 0;
    let baseX = 0;
    let baseY = 0;

    function qs(id) {
        return document.getElementById(id);
    }

    function getInput() {
        return qs('avatarInput') || document.querySelector('input[type="file"][name="avatar"]');
    }

    function getPreview() {
        return qs('avatarLivePreview') || document.querySelector('.sf-avatar-live-preview') || document.querySelector('.sf-avatar img')?.parentElement;
    }

    function openModal() {
        const modal = qs('sfAvatarCropModal');
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal(resetInput = false) {
        const modal = qs('sfAvatarCropModal');
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        if (resetInput) {
            const input = getInput();
            if (input) input.value = '';
        }
    }

    function renderCropImage() {
        const img = qs('sfAvatarCropImage');
        const zoomLabel = qs('sfAvatarZoomValue');
        if (!img) return;

        const stageSize = 320;
        const coverScale = Math.max(stageSize / imageNaturalWidth, stageSize / imageNaturalHeight);
        const finalScale = coverScale * zoom;

        img.style.width = `${imageNaturalWidth}px`;
        img.style.height = `${imageNaturalHeight}px`;
        img.style.transform = `translate(calc(-50% + ${offsetX}px), calc(-50% + ${offsetY}px)) scale(${finalScale})`;

        if (zoomLabel) {
            zoomLabel.textContent = `${Math.round(zoom * 100)}%`;
        }
    }

    function loadFile(file) {
        originalFile = file;
        const reader = new FileReader();

        reader.onload = function (event) {
            const img = qs('sfAvatarCropImage');
            if (!img) return;

            img.onload = function () {
                imageNaturalWidth = img.naturalWidth;
                imageNaturalHeight = img.naturalHeight;
                zoom = 1;
                offsetX = 0;
                offsetY = 0;

                const zoomInput = qs('sfAvatarZoom');
                if (zoomInput) zoomInput.value = '1';

                renderCropImage();
                openModal();
            };

            img.src = event.target.result;
        };

        reader.readAsDataURL(file);
    }

    function applyCrop() {
        const cropImg = qs('sfAvatarCropImage');
        const input = getInput();
        const preview = getPreview();

        if (!cropImg || !input || !originalFile) return;

        const outputSize = 800;
        const stageSize = 320;
        const canvas = document.createElement('canvas');
        canvas.width = outputSize;
        canvas.height = outputSize;

        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, outputSize, outputSize);

        const coverScale = Math.max(stageSize / imageNaturalWidth, stageSize / imageNaturalHeight);
        const finalScaleStage = coverScale * zoom;
        const finalScaleCanvas = finalScaleStage * (outputSize / stageSize);

        const drawW = imageNaturalWidth * finalScaleCanvas;
        const drawH = imageNaturalHeight * finalScaleCanvas;
        const drawX = (outputSize / 2) - (drawW / 2) + (offsetX * (outputSize / stageSize));
        const drawY = (outputSize / 2) - (drawH / 2) + (offsetY * (outputSize / stageSize));

        ctx.drawImage(cropImg, drawX, drawY, drawW, drawH);

        canvas.toBlob(function (blob) {
            if (!blob) return;

            const croppedFile = new File([blob], originalFile.name.replace(/\.[^.]+$/, '') + '-cropped.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            input.files = dataTransfer.files;

            const url = URL.createObjectURL(blob);

            if (preview) {
                preview.innerHTML = `<img src="${url}" alt="Avatar preview">`;
            }

            const label = qs('avatarFileName');
            if (label) {
                label.textContent = croppedFile.name;
            }

            closeModal(false);
        }, 'image/jpeg', 0.92);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = getInput();
        const stage = qs('sfAvatarCropStage');
        const zoomInput = qs('sfAvatarZoom');

        if (input) {
            input.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    loadFile(this.files[0]);
                }
            });
        }

        if (zoomInput) {
            zoomInput.addEventListener('input', function () {
                zoom = parseFloat(this.value || '1');
                renderCropImage();
            });
        }

        if (stage) {
            stage.addEventListener('pointerdown', function (event) {
                dragging = true;
                startX = event.clientX;
                startY = event.clientY;
                baseX = offsetX;
                baseY = offsetY;
                stage.setPointerCapture(event.pointerId);
            });

            stage.addEventListener('pointermove', function (event) {
                if (!dragging) return;
                offsetX = baseX + (event.clientX - startX);
                offsetY = baseY + (event.clientY - startY);
                renderCropImage();
            });

            stage.addEventListener('pointerup', function () {
                dragging = false;
            });

            stage.addEventListener('pointercancel', function () {
                dragging = false;
            });
        }

        qs('sfAvatarCropApply')?.addEventListener('click', applyCrop);
        qs('sfAvatarCropCancel')?.addEventListener('click', function () { closeModal(true); });
        qs('sfAvatarCropClose')?.addEventListener('click', function () { closeModal(true); });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal(true);
            }
        });
    });
})();
</script>

</body>
</html>
