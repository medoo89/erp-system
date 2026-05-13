@extends('portal.layouts.app')


@php
    $pageTitle = 'Portal Files';

    $fileTypes = $files
        ->pluck('description')
        ->filter()
        ->map(fn ($type) => strtolower(trim((string) $type)))
        ->unique()
        ->values();
    $fileIcon = function (?string $type, ?string $title = null) {
        $text = strtolower(trim(($type ?? '') . ' ' . ($title ?? '')));


        return 'folder';
    };

    $renderSvgIcon = function (string $name, string $class = 'sf-svg-icon') {
        $icons = [
            'folder' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.75 6.75A2.25 2.25 0 0 1 6 4.5h4.2c.52 0 1.02.18 1.42.51l1.13.93c.4.33.9.51 1.42.51H18A2.25 2.25 0 0 1 20.25 8.7v8.55A2.25 2.25 0 0 1 18 19.5H6a2.25 2.25 0 0 1-2.25-2.25V6.75Z"/></svg>',
            'badge' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.5 4.75h9A2.25 2.25 0 0 1 18.75 7v10A2.25 2.25 0 0 1 16.5 19.25h-9A2.25 2.25 0 0 1 5.25 17V7A2.25 2.25 0 0 1 7.5 4.75Z"/><path d="M9 8h6M9 16h6"/><circle cx="12" cy="11.5" r="2"/></svg>',
            'id' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25H6.75a2.25 2.25 0 0 1-2.25-2.25V6.75Z"/><circle cx="9.25" cy="10" r="1.75"/><path d="M6.9 15.8c.65-1.45 1.42-2.05 2.35-2.05s1.7.6 2.35 2.05M13.5 9h3.75M13.5 12h3.75M13.5 15h2.5"/></svg>',
            'verified' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.75 14.35 6l3.25-.25.25 3.25L20.25 12l-2.4 3 .25 3.25-3.25-.25L12 20.25 9.15 18l-3.25.25.25-3.25-2.4-3 2.4-3-.25-3.25L9.15 6 12 3.75Z"/><path d="m8.75 12.25 2.05 2.05 4.45-4.6"/></svg>',
            'medical' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.25 6.75V5.5A1.75 1.75 0 0 1 10 3.75h4a1.75 1.75 0 0 1 1.75 1.75v1.25"/><path d="M5.75 6.75h12.5A2.25 2.25 0 0 1 20.5 9v8.25a2.25 2.25 0 0 1-2.25 2.25H5.75a2.25 2.25 0 0 1-2.25-2.25V9a2.25 2.25 0 0 1 2.25-2.25Z"/><path d="M12 10v6M9 13h6"/></svg>',
            'award' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="9" r="4.5"/><path d="M9.5 13.1 8.25 20.25 12 18.25l3.75 2-1.25-7.15"/><path d="m10.25 9 1.15 1.15 2.35-2.55"/></svg>',
            'contract' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 3.75h7.25L18.75 8v10.25A2 2 0 0 1 16.75 20.25h-9.5a2 2 0 0 1-2-2V5.75a2 2 0 0 1 2-2Z"/><path d="M14.5 3.75V8h4.25M8.25 11h7.5M8.25 14h7.5M8.25 17h4.5"/></svg>',
            'flight' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.75 13.5 20.25 6.75l-6.75 16.5-3.25-7.5-6.5-2.25Z"/><path d="M10.25 15.75 20.25 6.75"/></svg>',
            'image' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M5.75 4.75h12.5a2 2 0 0 1 2 2v10.5a2 2 0 0 1-2 2H5.75a2 2 0 0 1-2-2V6.75a2 2 0 0 1 2-2Z"/><circle cx="8.75" cy="9" r="1.5"/><path d="m4.25 17 4.25-4.25 3.25 3.25 2.25-2.25 5.75 5.75"/></svg>',
            'open' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 4.75h5.25V10"/><path d="M19.25 4.75 11.5 12.5"/><path d="M10 6.25H6.75a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2V14"/></svg>',
            'download' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4.75v10"/><path d="m8.25 11.5 3.75 3.75 3.75-3.75"/><path d="M5.25 18.75h13.5"/></svg>',
        ];

        return $icons[$name] ?? $icons['folder'];
    };

    $prettyType = fn ($value) => ucfirst(str_replace('_', ' ', (string) $value));
@endphp

@section('content')
    @include('portal.partials.pending-file-requests')

<style id="sf-pending-requests-clean-style">
    .sf-pending-requests-clean {
        margin: 22px 0 28px;
        overflow: hidden;
        border-radius: 30px;
        background: rgba(255,255,255,.96);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 22px 58px rgba(15,23,42,.08);
    }

    .sf-pending-head {
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 35%),
            linear-gradient(135deg, #0f172a, #234b74);
        color: #fff;
    }

    .sf-pending-kicker {
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
        opacity: .78;
        margin-bottom: 8px;
    }

    .sf-pending-title {
        margin: 0;
        font-size: 28px;
        line-height: 1.1;
        font-weight: 950;
        letter-spacing: -.04em;
        color: #fff;
    }

    .sf-pending-subtitle {
        margin-top: 8px;
        color: rgba(255,255,255,.78);
        font-size: 14px;
        font-weight: 750;
        line-height: 1.55;
    }

    .sf-pending-open-files,
    .sf-pending-download-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 999px;
        background: #2563eb;
        color: #fff !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 950;
        box-shadow: 0 12px 28px rgba(37,99,235,.22);
    }

    .sf-pending-body {
        padding: 22px 24px 26px;
        display: grid;
        gap: 16px;
    }

    .sf-pending-card {
        border-radius: 26px;
        padding: 20px;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 35%),
            rgba(255,255,255,.96);
        border: 1px solid rgba(15,23,42,.08);
    }

    .sf-pending-card-top {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .sf-pending-label {
        color: #0f172a;
        font-size: 20px;
        font-weight: 950;
        letter-spacing: -.03em;
    }

    .sf-pending-help {
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        font-weight: 750;
        margin-top: 5px;
    }

    .sf-pending-badge {
        border-radius: 999px;
        padding: 8px 13px;
        background: #eff6ff;
        color: #075985;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .10em;
        text-transform: uppercase;
    }

    .sf-pending-download-line {
        margin: 12px 0 16px;
        padding: 12px;
        border-radius: 20px;
        background: #f8fafc;
        border: 1px solid rgba(15,23,42,.08);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    .sf-pending-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) repeat(2, minmax(160px, .6fr));
        gap: 12px;
        align-items: end;
    }

    .sf-pending-field label {
        display: block;
        color: #334155;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .10em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .sf-pending-input,
    .sf-pending-textarea {
        width: 100%;
        min-height: 46px;
        border-radius: 18px;
        border: 1px solid rgba(15,23,42,.12);
        background: rgba(248,250,252,.95);
        color: #0f172a;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 750;
        outline: none;
    }

    .sf-pending-textarea {
        min-height: 80px;
        resize: vertical;
        margin-top: 12px;
    }

    .sf-pending-submit {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        border: none;
        border-radius: 999px;
        padding: 0 22px;
        background: #0f172a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 950;
        cursor: pointer;
    }

    .dark .sf-pending-requests-clean,
    .dark .sf-pending-card {
        background: rgba(15,23,42,.86);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-pending-label {
        color: #fff;
    }

    .dark .sf-pending-help,
    .dark .sf-pending-download-line {
        color: rgba(226,232,240,.76);
    }

    .dark .sf-pending-input,
    .dark .sf-pending-textarea,
    .dark .sf-pending-download-line {
        background: rgba(15,23,42,.70);
        border-color: rgba(255,255,255,.12);
        color: #ffffff;
    }

    @media (max-width: 900px) {
        .sf-pending-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


    @if(session('success'))
        <div class="sf-portal-flash sf-portal-flash-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="sf-portal-flash sf-portal-flash-danger">
            <strong>Upload could not be completed:</strong>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <style>
        .sf-files-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 28px;
            background:
                radial-gradient(circle at 88% 12%, rgba(76,167,168,.18), transparent 30%),
                linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,251,255,.92));
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 22px 58px rgba(15,23,42,.08);
        }

        .sf-files-hero::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -110px;
            width: 300px;
            height: 300px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(76,167,168,.18), transparent 68%);
            pointer-events: none;
        }

        .sf-files-hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            gap: 22px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .sf-files-kicker {
            color: #2459d3;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .sf-files-title {
            margin: 0;
            color: #0f172a;
            font-size: 38px;
            line-height: 1.05;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        .sf-files-subtitle {
            margin-top: 12px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            font-weight: 650;
            max-width: 850px;
        }

        .sf-files-count-card {
            min-width: 126px;
            border-radius: 26px;
            padding: 16px;
            text-align: center;
            background: rgba(255,255,255,.86);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 16px 40px rgba(15,23,42,.06);
        }

        .sf-files-count-card strong {
            display: block;
            color: #0f172a;
            font-size: 30px;
            line-height: 1;
            font-weight: 950;
        }

        .sf-files-count-card span {
            display: block;
            margin-top: 7px;
            color: #64748b;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sf-files-toolbar {
            margin-top: 22px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(220px, 280px);
            gap: 14px;
        }

        .sf-files-control {
            width: 100%;
            min-height: 48px;
            border-radius: 18px;
            border: 1px solid rgba(15,23,42,.10);
            background: rgba(255,255,255,.94);
            color: #0f172a;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 750;
            outline: none;
        }

        .sf-files-control:focus {
            border-color: rgba(47,138,141,.55);
            box-shadow: 0 0 0 4px rgba(47,138,141,.10);
        }

        .sf-files-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-top: 22px;
        }

        .sf-file-card {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 18px;
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 18px 48px rgba(15,23,42,.065);
            transition: .18s ease;
        }

        .sf-file-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 60px rgba(15,23,42,.10);
        }

        .sf-file-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.12), transparent 34%),
                radial-gradient(circle at bottom left, rgba(36,89,211,.08), transparent 36%);
            pointer-events: none;
        }

        .sf-file-card-inner {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            min-height: 230px;
        }

        .sf-file-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
        }

        .sf-file-icon {
            width: 58px;
            height: 58px;
            display: grid;
            place-items: center;
            border-radius: 20px;
            background: #f1f5f9;
            border: 1px solid rgba(15,23,42,.07);
            font-size: 25px;
            flex-shrink: 0;
        }

        .sf-file-type {
            border-radius: 999px;
            padding: 8px 11px;
            background: #eff6ff;
            color: #2459d3;
            border: 1px solid rgba(36,89,211,.16);
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
            max-width: 180px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .sf-file-title {
            color: #0f172a;
            font-size: 20px;
            line-height: 1.18;
            font-weight: 950;
            letter-spacing: -.035em;
            margin-bottom: 10px;
        }

        .sf-file-meta {
            color: #64748b;
            font-size: 13px;
            line-height: 1.55;
            font-weight: 700;
        }

        .sf-file-actions {
            margin-top: auto;
            padding-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sf-file-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 15px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 950;
            border: 1px solid transparent;
        }

        .sf-file-btn-open {
            background: #eff6ff;
            color: #2459d3;
            border-color: rgba(36,89,211,.16);
        }

        .sf-file-btn-download {
            background: #ecfdf5;
            color: #047857;
            border-color: rgba(16,185,129,.20);
        }

        .sf-files-empty {
            margin-top: 22px;
            border-radius: 30px;
            padding: 42px 24px;
            background: rgba(255,255,255,.88);
            border: 1px dashed rgba(15,23,42,.18);
            text-align: center;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            font-weight: 700;
        }


        .sf-pending-requests {
            margin-top: 22px;
            border-radius: 34px;
            overflow: hidden;
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 22px 58px rgba(15,23,42,.08);
        }

        .sf-pending-head {
            padding: 24px 28px;
            border-bottom: 1px solid rgba(15,23,42,.08);
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 34%),
                linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,251,255,.94));
        }

        .sf-pending-title {
            margin: 0;
            color: #0f172a;
            font-size: 26px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .sf-pending-subtitle {
            margin-top: 8px;
            color: #64748b;
            font-size: 14px;
            font-weight: 750;
            line-height: 1.6;
        }

        .sf-pending-body {
            padding: 24px 28px 28px;
            display: grid;
            gap: 16px;
        }

        .sf-pending-card {
            border-radius: 26px;
            padding: 20px;
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 35%),
                rgba(255,255,255,.92);
            border: 1px solid rgba(15,23,42,.08);
        }

        .sf-pending-card-top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .sf-pending-label {
            color: #0f172a;
            font-size: 20px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .sf-pending-badge {
            border-radius: 999px;
            padding: 8px 13px;
            background: #eff6ff;
            color: #075985;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .sf-pending-help {
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .sf-pending-form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) repeat(2, minmax(160px, .6fr));
            gap: 12px;
            align-items: end;
        }

        .sf-pending-field label {
            display: block;
            color: #334155;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .sf-pending-input,
        .sf-pending-textarea {
            width: 100%;
            min-height: 46px;
            border-radius: 18px;
            border: 1px solid rgba(15,23,42,.12);
            background: rgba(248,250,252,.95);
            color: #0f172a;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 750;
            outline: none;
        }

        .sf-pending-textarea {
            min-height: 80px;
            resize: vertical;
            margin-top: 12px;
        }

        .sf-pending-submit {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            border: none;
            border-radius: 999px;
            padding: 0 22px;
            background: #0f172a;
            color: #ffffff;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
        }

        .dark .sf-pending-requests,
        .dark .sf-pending-card {
            background: rgba(15,23,42,.86);
            border-color: rgba(255,255,255,.10);
        }

        .dark .sf-pending-head {
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 34%),
                rgba(15,23,42,.92);
            border-color: rgba(255,255,255,.10);
        }

        .dark .sf-pending-title,
        .dark .sf-pending-label {
            color: #ffffff;
        }

        .dark .sf-pending-input,
        .dark .sf-pending-textarea {
            background: rgba(15,23,42,.70);
            border-color: rgba(255,255,255,.12);
            color: #ffffff;
        }

        @media (max-width: 900px) {
            .sf-pending-form-grid {
                grid-template-columns: 1fr;
            }
        }


        .sf-files-empty strong {
            display: block;
            color: #0f172a;
            font-size: 24px;
            font-weight: 950;
            margin-bottom: 8px;
        }

        .sf-files-hidden {
            display: none !important;
        }

        .dark .sf-files-hero,
        .dark .sf-file-card,
        .dark .sf-files-count-card,
        .dark .sf-files-empty {
            background: rgba(15,23,42,.86);
            border-color: rgba(255,255,255,.10);
        }

        .dark .sf-files-title,
        .dark .sf-files-count-card strong,
        .dark .sf-file-title,
        .dark .sf-files-empty strong {
            color: #ffffff;
        }

        .dark .sf-files-subtitle,
        .dark .sf-files-count-card span,
        .dark .sf-file-meta,
        .dark .sf-files-empty {
            color: rgba(226,232,240,.76);
        }

        .dark .sf-files-control {
            background: rgba(15,23,42,.92);
            border-color: rgba(255,255,255,.12);
            color: #ffffff;
        }

        .dark .sf-file-icon {
            background: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.10);
        }

        @media (max-width: 1100px) {
            .sf-files-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .sf-files-toolbar,
            .sf-files-grid {
                grid-template-columns: 1fr;
            }

            .sf-files-title {
                font-size: 32px;
            }
        }
        .sf-svg-icon {
            width: 30px;
            height: 30px;
            display: block;
            stroke: #2459d3;
            stroke-width: 1.85;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sf-svg-icon-sm {
            width: 18px;
            height: 18px;
            display: inline-block;
            vertical-align: -4px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            margin-right: 6px;
        }

    </style>


    
    @if(session('success'))
        <div style="margin:18px 0;padding:16px 18px;border-radius:20px;background:#ecfdf5;border:1px solid rgba(16,185,129,.25);color:#047857;font-weight:900;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="margin:18px 0;padding:16px 18px;border-radius:20px;background:#fef2f2;border:1px solid rgba(239,68,68,.25);color:#b91c1c;font-weight:850;">
            <strong style="display:block;margin-bottom:6px;">Upload could not be completed:</strong>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
<section class="sf-files-hero">
        <div class="sf-files-hero-inner">
            <div>
                <div class="sf-files-kicker">Employee Files</div>
                <h1 class="sf-files-title">Recent Files & Documents</h1>
                <div class="sf-files-subtitle">
                    Access your employee-visible documents from employment and pre-employment stages.
                    Private finance, payroll, bank, invoice, and internal cost files are hidden from this portal.
                </div>
            </div>

            <div class="sf-files-count-card">
                <strong id="sfVisibleFilesCount">{{ $files->count() }}</strong>
                <span>Files</span>
            </div>
        </div>

        <div class="sf-files-toolbar">
            <input
                id="sfFilesSearch"
                class="sf-files-control"
                type="search"
                placeholder="Search by file name, type, or source..."
                autocomplete="off"
            >

            <select id="sfFilesTypeFilter" class="sf-files-control">
                <option value="">All file types</option>
                @foreach($fileTypes as $type)
                    <option value="{{ $type }}">{{ $prettyType($type) }}</option>
                @endforeach
            </select>
        </div>
    </section>

    @if($files->count())
        <section class="sf-files-grid" id="sfFilesGrid">
            @foreach($files as $file)
                @php
                    $title = $file['title'] ?: 'Untitled File';
                    $description = strtolower(trim((string) ($file['description'] ?: '-')));
                    $sourceType = $file['source_type'] ?: 'file';
                    $sourceLabel = $prettyType($sourceType);
                    $createdAt = !empty($file['created_at']) ? $file['created_at']->format('Y-m-d H:i') : '-';

                    $searchText = strtolower($title . ' ' . $description . ' ' . $sourceType . ' ' . $createdAt);
                @endphp

                <article
                    class="sf-file-card"
                    data-title="{{ e($searchText) }}"
                    data-type="{{ e($description) }}"
                >
                    <div class="sf-file-card-inner">
                        <div class="sf-file-top">
                            <div class="sf-file-icon">{!! $renderSvgIcon($fileIcon($description, $title)) !!}</div>
                            <div class="sf-file-type">{{ $prettyType($description) }}</div>
                        </div>

                        <div class="sf-file-title">{{ $title }}</div>

                        <div class="sf-file-meta">
                            Source: {{ $sourceLabel }}<br>
                            Added: {{ $createdAt }}
                        </div>

                        <div class="sf-file-actions">
                            <a
                                href="{{ route('portal.files.open', ['type' => $file['source_type'], 'id' => $file['source_id']]) }}"
                                target="_blank"
                                class="sf-file-btn sf-file-btn-open"
                            >
                                {!! $renderSvgIcon('open', 'sf-svg-icon-sm') !!}
                                Open
                            </a>

                            <a
                                href="{{ route('portal.files.download', ['type' => $file['source_type'], 'id' => $file['source_id']]) }}"
                                class="sf-file-btn sf-file-btn-download"
                            >
                                {!! $renderSvgIcon('download', 'sf-svg-icon-sm') !!}
                                Download
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <div id="sfFilesNoResults" class="sf-files-empty" style="display:none;">
            <strong>No matching files</strong>
            Try changing the search keyword or file type filter.
        </div>
    @else
        <section class="sf-files-empty">
            <strong>No files yet</strong>
            No employee-visible files are available in your portal at this time.
        </section>
    @endif

    <script>
        (function () {
            const search = document.getElementById('sfFilesSearch');
            const typeFilter = document.getElementById('sfFilesTypeFilter');
            const cards = Array.from(document.querySelectorAll('.sf-file-card'));
            const noResults = document.getElementById('sfFilesNoResults');
            const visibleCount = document.getElementById('sfVisibleFilesCount');

            function applyFilters() {
                const q = (search?.value || '').trim().toLowerCase();
                const type = (typeFilter?.value || '').trim().toLowerCase();

                let count = 0;

                cards.forEach(function (card) {
                    const text = card.dataset.title || '';
                    const cardType = card.dataset.type || '';

                    const matchSearch = q === '' || text.includes(q);
                    const matchType = type === '' || cardType === type;

                    const visible = matchSearch && matchType;

                    card.classList.toggle('sf-files-hidden', !visible);

                    if (visible) {
                        count += 1;
                    }
                });

                if (visibleCount) {
                    visibleCount.textContent = count;
                }

                if (noResults) {
                    noResults.style.display = count === 0 ? 'block' : 'none';
                }
            }

            search?.addEventListener('input', applyFilters);
            typeFilter?.addEventListener('change', applyFilters);
        })();
    </script>

<style id="sf-pending-requests-md3-final-style">
    /*
     * FINAL MD3 / Premium style for Employee Portal Pending File Requests.
     * Style only. No upload / visibility logic changed.
     */

    .sf-pending-requests-clean {
        width: min(100%, 1280px) !important;
        margin: 0 auto 34px !important;
        overflow: hidden !important;
        border-radius: 34px !important;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 34%),
            rgba(255,255,255,.96) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 24px 70px rgba(15,23,42,.10) !important;
    }

    .sf-pending-requests-clean::before {
        content: "";
        display: block;
        height: 5px;
        background: linear-gradient(90deg, #22d3ee, #2563eb);
    }

    .sf-pending-head {
        padding: 28px 30px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 18px !important;
        flex-wrap: wrap !important;
        background:
            radial-gradient(circle at 88% 10%, rgba(34,211,238,.12), transparent 32%),
            linear-gradient(135deg, rgba(255,255,255,.98), rgba(248,251,255,.94)) !important;
        color: #0f172a !important;
        border-bottom: 1px solid rgba(15,23,42,.06) !important;
    }

    .sf-pending-kicker {
        margin-bottom: 8px !important;
        color: #2563eb !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .22em !important;
        text-transform: uppercase !important;
    }

    .sf-pending-title {
        margin: 0 !important;
        color: #0f172a !important;
        font-size: clamp(28px, 3vw, 42px) !important;
        line-height: 1.03 !important;
        font-weight: 950 !important;
        letter-spacing: -.055em !important;
    }

    .sf-pending-subtitle {
        margin-top: 9px !important;
        color: #64748b !important;
        font-size: 14px !important;
        font-weight: 750 !important;
        line-height: 1.55 !important;
        max-width: 780px !important;
    }

    .sf-pending-open-files {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 46px !important;
        padding: 0 20px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #ffffff !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: -.02em !important;
        text-decoration: none !important;
        box-shadow: 0 14px 34px rgba(37,99,235,.22) !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .sf-pending-open-files:hover {
        transform: translateY(-1px);
        filter: saturate(1.06) brightness(1.03);
    }

    .sf-pending-body {
        padding: 24px 28px 28px !important;
        display: grid !important;
        gap: 18px !important;
    }

    .sf-pending-card,
    .sf-pending-item,
    .sf-pending-request-card {
        position: relative !important;
        overflow: hidden !important;
        border-radius: 28px !important;
        padding: 22px !important;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 36%),
            rgba(255,255,255,.98) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 16px 44px rgba(15,23,42,.06) !important;
    }

    .sf-pending-card::before,
    .sf-pending-item::before,
    .sf-pending-request-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: linear-gradient(180deg, #22d3ee, #2563eb);
        opacity: .95;
    }

    .sf-pending-card-head,
    .sf-pending-card-header,
    .sf-pending-request-head {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 14px !important;
        margin-bottom: 18px !important;
        flex-wrap: wrap !important;
    }

    .sf-pending-card h3,
    .sf-pending-card-title,
    .sf-pending-request-title {
        margin: 0 !important;
        color: #0f172a !important;
        font-size: 22px !important;
        line-height: 1.12 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
    }

    .sf-pending-card p,
    .sf-pending-card-subtitle,
    .sf-pending-request-subtitle,
    .sf-pending-help {
        margin-top: 6px !important;
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 750 !important;
        line-height: 1.5 !important;
    }

    .sf-pending-status,
    .sf-pending-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 32px !important;
        padding: 0 12px !important;
        border-radius: 999px !important;
        background: #e0f2fe !important;
        color: #075985 !important;
        border: 1px solid rgba(37,99,235,.16) !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
    }

    .sf-pending-form-grid {
        display: grid !important;
        grid-template-columns: minmax(260px, 1.45fr) minmax(180px, .7fr) minmax(180px, .7fr) !important;
        gap: 12px !important;
        align-items: end !important;
    }

    .sf-pending-field label {
        display: block !important;
        margin: 0 0 8px !important;
        color: #334155 !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
    }

    .sf-pending-input,
    .sf-pending-textarea,
    .sf-pending-field input,
    .sf-pending-field select,
    .sf-pending-field textarea {
        width: 100% !important;
        border-radius: 18px !important;
        border: 1px solid rgba(15,23,42,.12) !important;
        background: rgba(248,250,252,.92) !important;
        color: #0f172a !important;
        font-size: 13px !important;
        font-weight: 750 !important;
        outline: none !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7) !important;
    }

    .sf-pending-input,
    .sf-pending-field input,
    .sf-pending-field select {
        min-height: 46px !important;
        padding: 0 14px !important;
    }

    .sf-pending-textarea,
    .sf-pending-field textarea {
        min-height: 76px !important;
        padding: 14px !important;
        margin-top: 12px !important;
        resize: vertical !important;
    }

    .sf-pending-input:focus,
    .sf-pending-textarea:focus,
    .sf-pending-field input:focus,
    .sf-pending-field select:focus,
    .sf-pending-field textarea:focus {
        border-color: rgba(37,99,235,.45) !important;
        box-shadow:
            0 0 0 4px rgba(37,99,235,.08),
            inset 0 1px 0 rgba(255,255,255,.7) !important;
    }

    .sf-pending-submit {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 44px !important;
        margin-top: 14px !important;
        padding: 0 20px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: linear-gradient(135deg, #22d3ee, #2563eb) !important;
        color: #ffffff !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        text-decoration: none !important;
        cursor: pointer !important;
        box-shadow: 0 14px 32px rgba(37,99,235,.20) !important;
    }

    .sf-pending-submit:hover {
        transform: translateY(-1px);
        filter: saturate(1.06) brightness(1.03);
    }

    .sf-sign-download-box {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 14px !important;
        flex-wrap: wrap !important;
        margin: 0 0 16px !important;
        padding: 16px !important;
        border-radius: 22px !important;
        background: #eff6ff !important;
        border: 1px solid rgba(37,99,235,.16) !important;
    }

    .sf-sign-download-box strong {
        display: block !important;
        color: #1d4ed8 !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        margin-bottom: 4px !important;
    }

    .sf-sign-download-box span {
        display: block !important;
        color: #475569 !important;
        font-size: 13px !important;
        font-weight: 750 !important;
        line-height: 1.45 !important;
    }

    .sf-download-sign-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 40px !important;
        padding: 0 16px !important;
        border-radius: 999px !important;
        background: #2563eb !important;
        color: #fff !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        text-decoration: none !important;
        box-shadow: 0 12px 26px rgba(37,99,235,.18) !important;
    }

    .dark .sf-pending-requests-clean {
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.12), transparent 34%),
            rgba(15,23,42,.82) !important;
        border-color: rgba(148,163,184,.16) !important;
        box-shadow: 0 22px 64px rgba(0,0,0,.22) !important;
    }

    .dark .sf-pending-head {
        background:
            radial-gradient(circle at 88% 10%, rgba(34,211,238,.12), transparent 32%),
            rgba(15,23,42,.72) !important;
        border-bottom-color: rgba(148,163,184,.14) !important;
    }

    .dark .sf-pending-title,
    .dark .sf-pending-card h3,
    .dark .sf-pending-card-title,
    .dark .sf-pending-request-title {
        color: #ffffff !important;
    }

    .dark .sf-pending-subtitle,
    .dark .sf-pending-card p,
    .dark .sf-pending-card-subtitle,
    .dark .sf-pending-request-subtitle,
    .dark .sf-pending-help {
        color: rgba(226,232,240,.76) !important;
    }

    .dark .sf-pending-card,
    .dark .sf-pending-item,
    .dark .sf-pending-request-card {
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.12), transparent 36%),
            rgba(15,23,42,.70) !important;
        border-color: rgba(148,163,184,.16) !important;
    }

    .dark .sf-pending-field label {
        color: #cbd5e1 !important;
    }

    .dark .sf-pending-input,
    .dark .sf-pending-textarea,
    .dark .sf-pending-field input,
    .dark .sf-pending-field select,
    .dark .sf-pending-field textarea {
        background: rgba(15,23,42,.82) !important;
        border-color: rgba(148,163,184,.18) !important;
        color: #ffffff !important;
    }

    .dark .sf-sign-download-box {
        background: rgba(37,99,235,.13) !important;
        border-color: rgba(96,165,250,.18) !important;
    }

    .dark .sf-sign-download-box strong {
        color: #bfdbfe !important;
    }

    .dark .sf-sign-download-box span {
        color: rgba(226,232,240,.78) !important;
    }

    @media (max-width: 900px) {
        .sf-pending-head {
            padding: 24px !important;
        }

        .sf-pending-body {
            padding: 20px !important;
        }

        .sf-pending-form-grid {
            grid-template-columns: 1fr !important;
        }

        .sf-pending-title {
            font-size: 30px !important;
        }
    }
</style>


@endsection

<script id="sf-request-upload-guard">
document.addEventListener('submit', function (event) {
    const form = event.target.closest('form[action*="/portal/files/requested/"]');
    if (!form) return;

    const input = form.querySelector('input[type="file"][name="requested_file"]');
    if (!input || !input.files || !input.files.length) {
        event.preventDefault();
        alert('Please choose a file before submitting.');
        return false;
    }
});
</script>


<style id="sf-premium-file-request-final-css">
    .sf-portal-flash {
        margin: 18px 0;
        padding: 16px 18px;
        border-radius: 22px;
        font-weight: 900;
    }

    .sf-portal-flash-success {
        background: #ecfdf5;
        border: 1px solid rgba(16,185,129,.25);
        color: #047857;
    }

    .sf-portal-flash-danger {
        background: #fef2f2;
        border: 1px solid rgba(239,68,68,.25);
        color: #b91c1c;
    }

    .sf-pending-requests {
        margin: 0 0 28px;
        border-radius: 34px;
        overflow: hidden;
        background: rgba(255,255,255,.96);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 24px 70px rgba(15,23,42,.08);
    }

    .sf-pending-head {
        padding: 26px 28px;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.14), transparent 36%),
            linear-gradient(135deg, #0f172a, #234b74);
        color: #ffffff;
    }

    .sf-pending-kicker {
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .22em;
        text-transform: uppercase;
        opacity: .82;
        margin-bottom: 8px;
    }

    .sf-pending-title {
        margin: 0;
        font-size: 30px;
        line-height: 1.08;
        font-weight: 950;
        letter-spacing: -.04em;
    }

    .sf-pending-subtitle {
        margin: 10px 0 0;
        max-width: 900px;
        font-size: 14px;
        line-height: 1.7;
        font-weight: 750;
        opacity: .88;
    }

    .sf-pending-body {
        padding: 24px 28px 28px;
        display: grid;
        gap: 16px;
    }

    .sf-pending-card {
        border-radius: 28px;
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 35%),
            #ffffff;
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 16px 46px rgba(15,23,42,.055);
    }

    .sf-pending-card-top {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .sf-pending-label {
        color: #0f172a;
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .sf-pending-badge {
        border-radius: 999px;
        padding: 10px 14px;
        background: #e0f2fe;
        color: #075985;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .10em;
        text-transform: uppercase;
    }

    .sf-pending-help {
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        font-weight: 750;
    }

    .sf-sign-download-box {
        margin: 12px 0 16px;
        border-radius: 24px;
        padding: 16px;
        background: #f8fafc;
        border: 1px dashed rgba(37,99,235,.28);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .sf-sign-download-box strong {
        display: block;
        color: #1e3a8a;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sf-sign-download-box span {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 13px;
        font-weight: 750;
    }

    .sf-download-sign-btn,
    .sf-pending-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        border-radius: 999px;
        padding: 0 22px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 950;
        border: none;
        cursor: pointer;
    }

    .sf-download-sign-btn {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 14px 32px rgba(37,99,235,.22);
    }

    .sf-pending-submit {
        margin-top: 14px;
        background: #0f172a;
        color: #ffffff;
        box-shadow: 0 14px 34px rgba(15,23,42,.18);
    }

    .sf-pending-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) repeat(2, minmax(160px, .65fr));
        gap: 12px;
        align-items: end;
    }

    .sf-pending-field label {
        display: block;
        color: #334155;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .10em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .sf-pending-input,
    .sf-pending-textarea {
        width: 100%;
        min-height: 48px;
        border-radius: 18px;
        border: 1px solid rgba(15,23,42,.12);
        background: rgba(248,250,252,.95);
        color: #0f172a;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 750;
        outline: none;
    }

    .sf-pending-textarea {
        min-height: 82px;
        resize: vertical;
        margin-top: 12px;
    }

    .dark .sf-pending-requests,
    .dark .sf-pending-card {
        background: rgba(15,23,42,.88);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-pending-label {
        color: #ffffff;
    }

    .dark .sf-pending-help,
    .dark .sf-sign-download-box span {
        color: rgba(226,232,240,.76);
    }

    .dark .sf-sign-download-box,
    .dark .sf-pending-input,
    .dark .sf-pending-textarea {
        background: rgba(15,23,42,.70);
        border-color: rgba(255,255,255,.12);
        color: #ffffff;
    }

    @media (max-width: 900px) {
        .sf-pending-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<style id="sf-pending-open-files-white-text-final">
    .sf-pending-open-files,
    .sf-pending-open-files:visited,
    .sf-pending-open-files:hover,
    .sf-pending-open-files:active,
    .sf-pending-open-files:focus {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        opacity: 1 !important;
    }

    .sf-pending-open-files * {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
    }
</style>
