<x-filament-panels::page>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,600,1,0" rel="stylesheet">
    @php
        /*
         | Archive Overview counters
         | Keep these counts aligned with the real archive pages/resources.
         | Do not use generic status-only counting because some records are archived
         | through is_archived / archived_at while their status remains different.
         */
        $countSafely = function (callable $callback): int {
            try {
                return (int) $callback();
            } catch (\Throwable $e) {
                return 0;
            }
        };

        $archivedJobApplicationsCount = $countSafely(function () {
            return \App\Models\JobApplication::query()
                ->where('is_archived', true)
                ->where(function ($query) {
                    $query
                        ->where('archive_reason', 'declined')
                        ->orWhere('archive_reason', 'archived_manually')
                        ->orWhereNull('archive_reason');
                })
                ->count();
        });

        $archivedJobOpeningsCount = $countSafely(function () {
            return \App\Models\Job::query()
                ->where('is_archived', true)
                ->count();
        });

        $archivedPreEmploymentsCount = $countSafely(function () {
            return \App\Models\PreEmployment::query()
                ->where(function ($query) {
                    $query
                        ->where('is_archived', true)
                        ->orWhere('is_declined', true)
                        ->orWhereNotNull('declined_at');
                })
                ->count();
        });

        $archivedEmploymentsCount = $countSafely(function () {
            return \App\Models\Employment::query()
                ->where(function ($query) {
                    $query
                        ->whereIn('status', [
                            'inactive',
                            'resigned',
                            'terminated',
                            'archived',
                        ])
                        ->orWhereIn('contract_status', [
                            'expired',
                            'terminated',
                            'closed',
                        ]);
                })
                ->count();
        });

        $cards = [
            [
                'title' => 'Archived Job Applications',
                'desc' => 'Declined, closed, or archived candidate applications.',
                'count' => $archivedJobApplicationsCount,
                'url' => url('/admin/archived-job-applications'),
                'icon' => 'assignment_ind',
            ],
            [
                'title' => 'Archived Job Openings',
                'desc' => 'Closed, expired, or archived job openings.',
                'count' => $archivedJobOpeningsCount,
                'url' => url('/admin/archived-job-openings'),
                'icon' => 'work_history',
            ],
            [
                'title' => 'Archived Pre-Employment',
                'desc' => 'Pre-employment records closed or moved to archive.',
                'count' => $archivedPreEmploymentsCount,
                'url' => url('/admin/archived-pre-employments'),
                'icon' => 'badge',
            ],
            [
                'title' => 'Archived Employment',
                'desc' => 'Demobilized, ended, or archived employment records.',
                'count' => $archivedEmploymentsCount,
                'url' => url('/admin/archived-employments'),
                'icon' => 'business_center',
            ],
        ];

        $totalArchive = collect($cards)->sum('count');
    @endphp

    <style id="sf-archive-premium-blue-final">
        .fi-header {
            display: none !important;
        }

        .archive-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /*
         | Premium blue page hero — aligned with Employment premium pages.
         */
        .archive-hero {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            padding: 34px 36px;
            border: 1px solid rgba(34, 211, 238, .22);
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .26), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .16), transparent 30%),
                linear-gradient(135deg, #081a34 0%, #12385d 56%, #2f6f73 100%) !important;
            box-shadow: 0 22px 46px rgba(15, 23, 42, .16);
            color: #fff;
        }

        .archive-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 5px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f) !important;
        }

        .archive-breadcrumb {
            position: relative;
            z-index: 1;
            font-size: 13px;
            color: rgba(255,255,255,.74);
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: .02em;
        }

        .archive-title {
            position: relative;
            z-index: 1;
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .archive-subtitle {
            position: relative;
            z-index: 1;
            margin-top: 16px;
            max-width: 880px;
            font-size: 15px;
            line-height: 1.75;
            color: rgba(255,255,255,.84) !important;
            font-weight: 650;
        }

        .archive-badges {
            position: relative;
            z-index: 1;
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .archive-badge {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 0 15px;
            border-radius: 999px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.16);
            color: #fff;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
        }

        /*
         | Archive cards — Employment-style blue cards.
         */
        .archive-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .archive-card {
            position: relative;
            overflow: hidden;
            min-height: 238px;
            border-radius: 30px;
            border: 1px solid rgba(203, 213, 225, .80);
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 38%),
                linear-gradient(180deg, rgba(255,255,255,.99), rgba(248,250,252,.94)) !important;
            box-shadow: 0 16px 34px rgba(15, 23, 42, .075);
            padding: 24px;
            text-decoration: none !important;
            color: #0f172a !important;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .archive-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #22d3ee, #2563eb) !important;
            opacity: .95;
        }

        .archive-card:hover {
            transform: translateY(-4px);
            border-color: rgba(37, 99, 235, .30);
            box-shadow: 0 24px 54px rgba(15, 23, 42, .13);
        }

        .archive-icon {
            width: 58px;
            height: 58px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: linear-gradient(135deg, #e0f2fe, #eff6ff) !important;
            color: #1d4ed8 !important;
            border: 1px solid rgba(37, 99, 235, .11);
            box-shadow: 0 12px 26px rgba(37, 99, 235, .10);
            margin-bottom: 18px;
        }

        .archive-icon .material-symbols-rounded {
            font-family: 'Material Symbols Rounded' !important;
            font-weight: 500 !important;
            font-style: normal !important;
            font-size: 31px !important;
            line-height: 1 !important;
            letter-spacing: normal !important;
            text-transform: none !important;
            display: inline-block !important;
            white-space: nowrap !important;
            direction: ltr !important;
            -webkit-font-feature-settings: 'liga' !important;
            -webkit-font-smoothing: antialiased !important;
            font-variation-settings:
                'FILL' 0,
                'wght' 500,
                'GRAD' 0,
                'opsz' 24 !important;
        }

        .archive-card-title {
            font-size: 18px;
            font-weight: 950;
            color: #0f172a !important;
            margin-bottom: 10px;
            letter-spacing: -.025em;
            line-height: 1.25;
        }

        .archive-card-desc {
            font-size: 13px;
            line-height: 1.65;
            color: #64748b !important;
            font-weight: 700;
        }

        .archive-card-footer {
            margin-top: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .archive-count {
            font-size: 36px;
            line-height: 1;
            font-weight: 950;
            color: #234b74 !important;
            letter-spacing: -.05em;
        }

        .archive-open {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 16px;
            border-radius: 999px;
            background: linear-gradient(135deg, #e0f2fe, #dbeafe) !important;
            color: #1d4ed8 !important;
            border: 1px solid rgba(37, 99, 235, .14);
            font-size: 12px;
            font-weight: 950;
            box-shadow: 0 10px 22px rgba(37, 99, 235, .10);
        }

        /*
         | Dark mode compatibility.
         */
        .dark .archive-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(34, 211, 238, .16) !important;
            box-shadow: 0 22px 48px rgba(0, 0, 0, .34);
        }

        .dark .archive-card {
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 38%),
                linear-gradient(180deg, rgba(15,23,42,.84), rgba(15,23,42,.68)) !important;
            border-color: rgba(148, 163, 184, .16) !important;
            box-shadow: 0 18px 40px rgba(0, 0, 0, .30) !important;
        }

        .dark .archive-card-title {
            color: #f8fafc !important;
        }

        .dark .archive-card-desc {
            color: #aab8c6 !important;
        }

        .dark .archive-count {
            color: #e0f2fe !important;
        }

        .dark .archive-icon {
            background: rgba(224, 242, 254, .10) !important;
            color: #93c5fd !important;
            border-color: rgba(147, 197, 253, .16) !important;
            box-shadow: 0 12px 26px rgba(0, 0, 0, .20);
        }

        .dark .archive-open {
            background: rgba(37, 99, 235, .18) !important;
            color: #bfdbfe !important;
            border-color: rgba(147, 197, 253, .18) !important;
        }

        @media (max-width: 1100px) {
            .archive-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .archive-hero {
                padding: 28px 24px;
            }

            .archive-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="archive-wrap">
        <section class="archive-hero">
            <div class="archive-breadcrumb">System › Archive</div>
            <div class="archive-title">Archive</div>
            <div class="archive-subtitle">
                Central archive overview for closed recruitment, pre-employment, employment, and job-opening records.
                Use these blocks instead of keeping many archive links in the sidebar.
            </div>

            <div class="archive-badges">
                <div class="archive-badge">{{ $totalArchive }} Archived Records</div>
                <div class="archive-badge">4 Archive Areas</div>
            </div>
        </section>

        <section class="archive-grid">
            @foreach($cards as $card)
                <a href="{{ $card['url'] }}" class="archive-card">
                    <div>
                        <div class="archive-icon">
                            <span class="material-symbols-rounded">{{ $card['icon'] }}</span>
                        </div>
                        <div class="archive-card-title">{{ $card['title'] }}</div>
                        <div class="archive-card-desc">{{ $card['desc'] }}</div>
                    </div>

                    <div class="archive-card-footer">
                        <div class="archive-count">{{ $card['count'] }}</div>
                        <div class="archive-open">Open</div>
                    </div>
                </a>
            @endforeach
        </section>
    </div>
</x-filament-panels::page>


<style id="sf-candidate-request-decision-colors">
    /*
     * Colored decision buttons — visual only.
     */

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]) {
        overflow: hidden !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important;
        border-color: rgba(34,197,94,.42) !important;
        color: #047857 !important;
        box-shadow: 0 12px 28px rgba(34,197,94,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: linear-gradient(135deg, #fef2f2, #fee2e2) !important;
        border-color: rgba(239,68,68,.38) !important;
        color: #b91c1c !important;
        box-shadow: 0 12px 28px rgba(239,68,68,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
        border-color: rgba(249,115,22,.38) !important;
        color: #c2410c !important;
        box-shadow: 0 12px 28px rgba(249,115,22,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]:checked) {
        transform: translateY(-1px) !important;
        filter: saturate(1.12) !important;
        box-shadow: 0 0 0 5px rgba(37,99,235,.10), 0 18px 38px rgba(15,23,42,.12) !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: rgba(6,78,59,.55) !important;
        border-color: rgba(52,211,153,.34) !important;
        color: #a7f3d0 !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: rgba(127,29,29,.48) !important;
        border-color: rgba(248,113,113,.34) !important;
        color: #fecaca !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: rgba(124,45,18,.48) !important;
        border-color: rgba(251,146,60,.34) !important;
        color: #fed7aa !important;
    }
</style>

