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

        if (str_contains($text, 'passport')) return 'id';
        if (str_contains($text, 'visa')) return 'verified';
        if (str_contains($text, 'medical')) return 'medical';
        if (str_contains($text, 'certificate')) return 'award';
        if (str_contains($text, 'contract')) return 'contract';
        if (str_contains($text, 'ticket') || str_contains($text, 'travel')) return 'flight';
        if (str_contains($text, 'cv') || str_contains($text, 'resume')) return 'badge';
        if (str_contains($text, 'photo') || str_contains($text, 'image')) return 'image';

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
@endsection
