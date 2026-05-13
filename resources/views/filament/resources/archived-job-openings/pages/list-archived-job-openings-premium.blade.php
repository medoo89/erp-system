<x-filament-panels::page>
    @php
        try {
            $archivedCount = \App\Models\Job::query()->where('is_archived', true)->count();
        } catch (\Throwable $e) {
            $archivedCount = 0;
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .sf-ajo-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-ajo-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px 36px;
            border: 1px solid rgba(76, 167, 168, .24);
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .26), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .16), transparent 30%),
                linear-gradient(135deg, #081a34 0%, #12385d 56%, #2f6f73 100%) !important;
            box-shadow: 0 18px 36px rgba(15, 23, 42, .14);
            color: #fff;
        }

        .sf-ajo-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-ajo-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .sf-ajo-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .sf-ajo-subtitle {
            margin-top: 16px;
            max-width: 840px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .sf-ajo-badge-row {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-ajo-badge {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.14);
            color: #fff;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .sf-ajo-table-shell {
            position: relative;
            overflow: visible !important;
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
        }

        .sf-ajo-table-shell .fi-ta-outer,
        .sf-ajo-table-shell .fi-ta,
        .sf-ajo-table-shell .fi-ta-content,
        .sf-ajo-table-shell .fi-ta-header,
        .sf-ajo-table-shell .fi-ta-toolbar,
        .sf-ajo-table-shell .fi-ta-table,
        .sf-ajo-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .sf-ajo-table-shell .fi-ta-ctn,
        .sf-ajo-table-shell .fi-ta-table {
            overflow: visible !important;
        }

        .sf-ajo-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-weight: 900 !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            border-color: #d7e2e5 !important;
        }

        .sf-ajo-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
            vertical-align: middle !important;
        }

        .sf-ajo-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .sf-ajo-table-shell .fi-input-wrp,
        .sf-ajo-table-shell .fi-select,
        .sf-ajo-table-shell .fi-input,
        .sf-ajo-table-shell .fi-select-input,
        .sf-ajo-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .sf-ajo-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 850 !important;
        }

        .sf-ajo-table-shell table th:last-child,
        .sf-ajo-table-shell table td:last-child {
            width: 150px !important;
            min-width: 150px !important;
            max-width: 150px !important;
            text-align: center !important;
            vertical-align: middle !important;
            padding-inline: 14px !important;
        }

        .sf-ajo-table-shell table th:last-child {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-size: 13px !important;
            font-weight: 950 !important;
            letter-spacing: .14em !important;
            text-transform: uppercase !important;
        }

        .sf-ajo-table-shell table td:last-child > *,
        .sf-ajo-table-shell .fi-ta-actions,
        .sf-ajo-table-shell .fi-ta-record-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-direction: row !important;
            gap: 12px !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .sf-ajo-table-shell .fi-ta-actions .fi-ac,
        .sf-ajo-table-shell .fi-ta-record-actions .fi-ac,
        .sf-ajo-table-shell .fi-ta-actions > *,
        .sf-ajo-table-shell .fi-ta-record-actions > * {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
        }

        .sf-ajo-table-shell .fi-ta-actions .fi-btn,
        .sf-ajo-table-shell .fi-ta-record-actions .fi-btn,
        .sf-ajo-table-shell .sf-archive-row-action {
            width: 52px !important;
            height: 52px !important;
            min-width: 52px !important;
            min-height: 52px !important;
            max-width: 52px !important;
            max-height: 52px !important;
            padding: 0 !important;
            border-radius: 18px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
        }

        .sf-ajo-table-shell .fi-ta-actions .fi-btn svg,
        .sf-ajo-table-shell .fi-ta-record-actions .fi-btn svg,
        .sf-ajo-table-shell .sf-archive-row-action svg {
            width: 27px !important;
            height: 27px !important;
            min-width: 27px !important;
            min-height: 27px !important;
            stroke-width: 2.3 !important;
        }

        .sf-ajo-table-shell .fi-ta-actions .fi-btn-label,
        .sf-ajo-table-shell .fi-ta-record-actions .fi-btn-label,
        .sf-ajo-table-shell .sf-archive-row-action span:not(.material-symbols-rounded) {
            display: none !important;
        }

        .sf-ajo-table-shell .sf-archive-row-action-restore,
        .sf-ajo-table-shell .sf-archive-row-action-restore.fi-btn {
            background: #dcfce7 !important;
            border: 1px solid rgba(34, 197, 94, .22) !important;
            color: #15803d !important;
            box-shadow: 0 10px 22px rgba(34, 197, 94, .12) !important;
        }

        .sf-ajo-table-shell .sf-archive-row-action-delete,
        .sf-ajo-table-shell .sf-archive-row-action-delete.fi-btn {
            background: #fff1f2 !important;
            border: 1px solid rgba(239, 68, 68, .18) !important;
            color: #64748b !important;
            box-shadow: 0 10px 22px rgba(15, 23, 42, .05) !important;
        }

        .sf-ajo-table-shell .sf-archive-row-action:hover {
            transform: translateY(-1px) !important;
        }

        .sf-ajo-table-shell .fi-checkbox-input,
        .sf-ajo-table-shell input[type="checkbox"] {
            width: 18px !important;
            height: 18px !important;
            border-radius: 6px !important;
            accent-color: #1f4664 !important;
        }

        .sf-ajo-table-shell .fi-ta-empty-state {
            min-height: 260px !important;
        }

        .dark .sf-ajo-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .sf-ajo-table-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .sf-ajo-table-shell table thead th,
        .dark .sf-ajo-table-shell table th:last-child {
            background: rgba(15, 23, 42, .88) !important;
            color: #e0f2fe !important;
            border-color: rgba(148,163,184,.16) !important;
        }

        .dark .sf-ajo-table-shell table tbody td {
            background: rgba(15,23,42,.66) !important;
            color: #f8fafc !important;
            border-color: rgba(148,163,184,.12) !important;
        }

        .dark .sf-ajo-table-shell table tbody tr:hover td {
            background: rgba(30,41,59,.78) !important;
        }

        @media (max-width: 900px) {
            .sf-ajo-hero {
                padding: 28px 24px;
            }

            .sf-ajo-table-shell {
                overflow-x: auto !important;
            }
        }
    </style>

    <div class="sf-ajo-wrap">
        <section class="sf-ajo-hero">
            <div class="sf-ajo-breadcrumb">Archive › Job Openings › List</div>
            <div class="sf-ajo-title">Archived Job Openings</div>
            <div class="sf-ajo-subtitle">
                Review archived job openings, archive reasons, archived dates, and restore or permanently delete records when required.
            </div>

            <div class="sf-ajo-badge-row">
                <div class="sf-ajo-badge">{{ $archivedCount }} Archived Openings</div>
            </div>
        </section>

        <section class="sf-ajo-table-shell">
            {{ $this->table }}
        </section>
    </div>

<style id="sf-archived-job-openings-bulk-and-row-actions-final">
    /*
     | FINAL FIX:
     | - Bulk actions remain full readable pill buttons.
     | - Row actions remain icon-only premium buttons.
     | - Prevent bulk buttons from being compressed by row-action CSS.
     */

    .sf-ajo-table-shell .fi-ta-header-toolbar,
    .sf-ajo-table-shell .fi-ta-header,
    .sf-ajo-table-shell .fi-ta-toolbar {
        min-height: 92px !important;
        padding: 20px 22px !important;
        display: flex !important;
        align-items: center !important;
        gap: 14px !important;
        background: #ffffff !important;
        border-bottom: 1px solid #eef2f7 !important;
    }

    .sf-ajo-table-shell .fi-ta-bulk-actions,
    .sf-ajo-table-shell .fi-ta-selection-indicator,
    .sf-ajo-table-shell [class*="bulk"] {
        display: inline-flex !important;
        align-items: center !important;
        gap: 12px !important;
        flex-wrap: wrap !important;
        width: auto !important;
        max-width: 100% !important;
        overflow: visible !important;
    }

    /*
     | Bulk action buttons: readable pills, not icon-only.
     */
    .sf-ajo-table-shell .fi-ta-bulk-actions .fi-btn,
    .sf-ajo-table-shell .fi-ta-bulk-actions button,
    .sf-ajo-table-shell [class*="bulk"] .fi-btn,
    .sf-ajo-table-shell [class*="bulk"] button {
        width: auto !important;
        min-width: 150px !important;
        max-width: none !important;
        height: 44px !important;
        min-height: 44px !important;
        max-height: 44px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .01em !important;
        box-shadow: 0 12px 24px rgba(15, 23, 42, .10) !important;
        overflow: visible !important;
        white-space: nowrap !important;
    }

    .sf-ajo-table-shell .fi-ta-bulk-actions .fi-btn-label,
    .sf-ajo-table-shell .fi-ta-bulk-actions button span,
    .sf-ajo-table-shell [class*="bulk"] .fi-btn-label,
    .sf-ajo-table-shell [class*="bulk"] button span {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: auto !important;
        height: auto !important;
        overflow: visible !important;
        white-space: nowrap !important;
    }

    .sf-ajo-table-shell .fi-ta-bulk-actions .fi-btn svg,
    .sf-ajo-table-shell .fi-ta-bulk-actions button svg,
    .sf-ajo-table-shell [class*="bulk"] .fi-btn svg,
    .sf-ajo-table-shell [class*="bulk"] button svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
    }

    /*
     | Restore Selected bulk button.
     */
    .sf-ajo-table-shell .fi-ta-bulk-actions .fi-color-success,
    .sf-ajo-table-shell [class*="bulk"] .fi-color-success {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: #ffffff !important;
        border: 1px solid rgba(34, 197, 94, .24) !important;
    }

    /*
     | Permanent Delete bulk button.
     */
    .sf-ajo-table-shell .fi-ta-bulk-actions .fi-color-danger,
    .sf-ajo-table-shell [class*="bulk"] .fi-color-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
        border: 1px solid rgba(239, 68, 68, .24) !important;
    }

    /*
     | Row actions only: icon-only boxes.
     */
    .sf-ajo-table-shell table tbody td:last-child .fi-ta-actions,
    .sf-ajo-table-shell table tbody td:last-child .fi-ta-record-actions {
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 12px !important;
        width: 100% !important;
        margin: 0 !important;
        overflow: visible !important;
    }

    .sf-ajo-table-shell table tbody td:last-child .fi-ta-actions .fi-btn,
    .sf-ajo-table-shell table tbody td:last-child .fi-ta-record-actions .fi-btn,
    .sf-ajo-table-shell table tbody td:last-child .sf-archive-row-action {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        max-width: 52px !important;
        max-height: 52px !important;
        padding: 0 !important;
        border-radius: 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: visible !important;
        margin: 0 !important;
    }

    .sf-ajo-table-shell table tbody td:last-child .fi-ta-actions .fi-btn-label,
    .sf-ajo-table-shell table tbody td:last-child .fi-ta-record-actions .fi-btn-label,
    .sf-ajo-table-shell table tbody td:last-child .sf-archive-row-action .fi-btn-label {
        display: none !important;
    }

    .sf-ajo-table-shell table tbody td:last-child .fi-ta-actions .fi-btn svg,
    .sf-ajo-table-shell table tbody td:last-child .fi-ta-record-actions .fi-btn svg,
    .sf-ajo-table-shell table tbody td:last-child .sf-archive-row-action svg {
        width: 27px !important;
        height: 27px !important;
        min-width: 27px !important;
        min-height: 27px !important;
        stroke-width: 2.3 !important;
    }

    .sf-ajo-table-shell table tbody td:last-child .sf-archive-row-action-restore,
    .sf-ajo-table-shell table tbody td:last-child .sf-archive-row-action-restore.fi-btn {
        background: #dcfce7 !important;
        border: 1px solid rgba(34, 197, 94, .22) !important;
        color: #15803d !important;
        box-shadow: 0 10px 22px rgba(34, 197, 94, .12) !important;
    }

    .sf-ajo-table-shell table tbody td:last-child .sf-archive-row-action-delete,
    .sf-ajo-table-shell table tbody td:last-child .sf-archive-row-action-delete.fi-btn {
        background: #fff1f2 !important;
        border: 1px solid rgba(239, 68, 68, .18) !important;
        color: #64748b !important;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .05) !important;
    }

    .sf-ajo-table-shell table tbody td:last-child .sf-archive-row-action:hover {
        transform: translateY(-1px) !important;
    }

    /*
     | Actions column width/spacing same premium rhythm.
     */
    .sf-ajo-table-shell table th:last-child,
    .sf-ajo-table-shell table td:last-child {
        width: 170px !important;
        min-width: 170px !important;
        max-width: 170px !important;
        text-align: center !important;
        vertical-align: middle !important;
        padding-inline: 18px !important;
    }

    .sf-ajo-table-shell table th:last-child {
        color: #1f4664 !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    /*
     | Checkbox alignment.
     */
    .sf-ajo-table-shell table th:first-child,
    .sf-ajo-table-shell table td:first-child {
        width: 54px !important;
        min-width: 54px !important;
        max-width: 54px !important;
        text-align: center !important;
        padding-inline: 14px !important;
    }

    .sf-ajo-table-shell input[type="checkbox"],
    .sf-ajo-table-shell .fi-checkbox-input {
        width: 18px !important;
        height: 18px !important;
        border-radius: 6px !important;
        accent-color: #1f4664 !important;
    }

    .dark .sf-ajo-table-shell .fi-ta-header-toolbar,
    .dark .sf-ajo-table-shell .fi-ta-header,
    .dark .sf-ajo-table-shell .fi-ta-toolbar {
        background: rgba(15, 23, 42, .78) !important;
        border-bottom-color: rgba(148, 163, 184, .16) !important;
    }
</style>

</x-filament-panels::page>

<style id="sf-archive-job-openings-emergency-final-fix">
    /*
     | Emergency final override for Archived Job Openings.
     | Fixes:
     | 1) Restore Selected / Permanent Delete buttons clipped.
     | 2) Row action icons clipped on the right.
     | 3) Table width / shell overflow.
     */

    /* page width same clean rhythm */
    .ajo-wrap,
    .sf-ajo-wrap,
    .archive-wrap,
    .job-premium-wrap {
        max-width: 1240px !important;
        width: min(1240px, calc(100vw - 96px)) !important;
        margin-inline: auto !important;
        overflow: visible !important;
    }

    .ajo-hero,
    .sf-ajo-hero,
    .archive-hero,
    .job-premium-hero {
        width: 100% !important;
        max-width: 1240px !important;
        margin-inline: auto !important;
        overflow: hidden !important;
        border-radius: 30px !important;
    }

    .ajo-table-shell,
    .sf-ajo-table-shell,
    .archive-table-shell,
    .job-premium-table-shell,
    .fi-ta-ctn,
    .fi-ta,
    .fi-ta-outer,
    .fi-ta-content {
        width: 100% !important;
        max-width: 1240px !important;
        margin-inline: auto !important;
        overflow: visible !important;
        border-radius: 26px !important;
    }

    /*
     | Make sure table can show the final Actions column.
     */
    .ajo-table-shell table,
    .sf-ajo-table-shell table,
    .archive-table-shell table,
    .job-premium-table-shell table,
    .fi-ta-table {
        width: 100% !important;
        table-layout: auto !important;
    }

    /*
     | TOP BULK ACTION AREA ONLY.
     | These buttons are NOT row buttons, so force full pill buttons.
     */
    .ajo-table-shell > div:first-child .fi-btn,
    .sf-ajo-table-shell > div:first-child .fi-btn,
    .archive-table-shell > div:first-child .fi-btn,
    .fi-ta-header-toolbar .fi-btn,
    .fi-ta-header .fi-btn,
    .fi-ta-toolbar .fi-btn,
    .fi-ta-bulk-actions .fi-btn,
    .fi-ta-selection-indicator .fi-btn {
        width: auto !important;
        min-width: 158px !important;
        max-width: none !important;
        height: 46px !important;
        min-height: 46px !important;
        max-height: 46px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        overflow: visible !important;
        white-space: nowrap !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .01em !important;
        line-height: 1 !important;
    }

    .ajo-table-shell > div:first-child .fi-btn-label,
    .sf-ajo-table-shell > div:first-child .fi-btn-label,
    .archive-table-shell > div:first-child .fi-btn-label,
    .fi-ta-header-toolbar .fi-btn-label,
    .fi-ta-header .fi-btn-label,
    .fi-ta-toolbar .fi-btn-label,
    .fi-ta-bulk-actions .fi-btn-label,
    .fi-ta-selection-indicator .fi-btn-label {
        display: inline-flex !important;
        opacity: 1 !important;
        visibility: visible !important;
        width: auto !important;
        max-width: none !important;
        height: auto !important;
        overflow: visible !important;
        white-space: nowrap !important;
        text-indent: 0 !important;
    }

    .ajo-table-shell > div:first-child .fi-btn svg,
    .sf-ajo-table-shell > div:first-child .fi-btn svg,
    .archive-table-shell > div:first-child .fi-btn svg,
    .fi-ta-header-toolbar .fi-btn svg,
    .fi-ta-header .fi-btn svg,
    .fi-ta-toolbar .fi-btn svg,
    .fi-ta-bulk-actions .fi-btn svg,
    .fi-ta-selection-indicator .fi-btn svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
    }

    /*
     | Bulk colors.
     */
    .fi-ta-header-toolbar .fi-color-success,
    .fi-ta-toolbar .fi-color-success,
    .fi-ta-bulk-actions .fi-color-success,
    .fi-ta-selection-indicator .fi-color-success {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: #ffffff !important;
        border: 1px solid rgba(34, 197, 94, .24) !important;
        box-shadow: 0 12px 24px rgba(34, 197, 94, .16) !important;
    }

    .fi-ta-header-toolbar .fi-color-danger,
    .fi-ta-toolbar .fi-color-danger,
    .fi-ta-bulk-actions .fi-color-danger,
    .fi-ta-selection-indicator .fi-color-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
        border: 1px solid rgba(239, 68, 68, .24) !important;
        box-shadow: 0 12px 24px rgba(239, 68, 68, .16) !important;
    }

    /*
     | ROW ACTIONS ONLY.
     */
    .ajo-table-shell table tbody td:last-child,
    .sf-ajo-table-shell table tbody td:last-child,
    .archive-table-shell table tbody td:last-child,
    .job-premium-table-shell table tbody td:last-child {
        width: 170px !important;
        min-width: 170px !important;
        max-width: 170px !important;
        text-align: center !important;
        padding-inline: 18px !important;
        overflow: visible !important;
    }

    .ajo-table-shell table thead th:last-child,
    .sf-ajo-table-shell table thead th:last-child,
    .archive-table-shell table thead th:last-child,
    .job-premium-table-shell table thead th:last-child {
        width: 170px !important;
        min-width: 170px !important;
        max-width: 170px !important;
        text-align: center !important;
        padding-inline: 18px !important;
    }

    .ajo-table-shell table tbody td:last-child .fi-ta-actions,
    .sf-ajo-table-shell table tbody td:last-child .fi-ta-actions,
    .archive-table-shell table tbody td:last-child .fi-ta-actions,
    .job-premium-table-shell table tbody td:last-child .fi-ta-actions,
    .ajo-table-shell table tbody td:last-child .fi-ta-record-actions,
    .sf-ajo-table-shell table tbody td:last-child .fi-ta-record-actions,
    .archive-table-shell table tbody td:last-child .fi-ta-record-actions,
    .job-premium-table-shell table tbody td:last-child .fi-ta-record-actions {
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 12px !important;
        width: 100% !important;
        overflow: visible !important;
        flex-wrap: nowrap !important;
    }

    .ajo-table-shell table tbody td:last-child .fi-btn,
    .sf-ajo-table-shell table tbody td:last-child .fi-btn,
    .archive-table-shell table tbody td:last-child .fi-btn,
    .job-premium-table-shell table tbody td:last-child .fi-btn {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        max-width: 52px !important;
        max-height: 52px !important;
        padding: 0 !important;
        border-radius: 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: visible !important;
        margin: 0 !important;
    }

    .ajo-table-shell table tbody td:last-child .fi-btn-label,
    .sf-ajo-table-shell table tbody td:last-child .fi-btn-label,
    .archive-table-shell table tbody td:last-child .fi-btn-label,
    .job-premium-table-shell table tbody td:last-child .fi-btn-label {
        display: none !important;
    }

    .ajo-table-shell table tbody td:last-child .fi-btn svg,
    .sf-ajo-table-shell table tbody td:last-child .fi-btn svg,
    .archive-table-shell table tbody td:last-child .fi-btn svg,
    .job-premium-table-shell table tbody td:last-child .fi-btn svg {
        width: 27px !important;
        height: 27px !important;
        min-width: 27px !important;
        min-height: 27px !important;
        stroke-width: 2.3 !important;
    }
</style>

<style id="sf-archived-job-openings-actions-match-job-openings-final">
    /*
     | Archived Job Openings action column must match Job Openings.
     | Same header title, same button size, same icons, same horizontal layout.
     */

    .ajo-table-shell table thead tr th:last-child,
    .sf-ajo-table-shell table thead tr th:last-child,
    .archive-table-shell table thead tr th:last-child,
    .fi-ta-table thead tr th:last-child {
        width: 170px !important;
        min-width: 170px !important;
        max-width: 170px !important;
        text-align: center !important;
        padding-inline: 18px !important;
        background: #eef5f8 !important;
        color: #1f4664 !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        vertical-align: middle !important;
    }

    .ajo-table-shell table thead tr th:last-child *,
    .sf-ajo-table-shell table thead tr th:last-child *,
    .archive-table-shell table thead tr th:last-child *,
    .fi-ta-table thead tr th:last-child * {
        color: #1f4664 !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .ajo-table-shell table tbody tr td:last-child,
    .sf-ajo-table-shell table tbody tr td:last-child,
    .archive-table-shell table tbody tr td:last-child,
    .fi-ta-table tbody tr td:last-child {
        width: 170px !important;
        min-width: 170px !important;
        max-width: 170px !important;
        text-align: center !important;
        padding-inline: 18px !important;
        vertical-align: middle !important;
        overflow: visible !important;
    }

    .ajo-table-shell table tbody tr td:last-child > *,
    .sf-ajo-table-shell table tbody tr td:last-child > *,
    .archive-table-shell table tbody tr td:last-child > *,
    .fi-ta-table tbody tr td:last-child > * {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        overflow: visible !important;
    }

    .ajo-table-shell table tbody tr td:last-child .fi-ta-actions,
    .ajo-table-shell table tbody tr td:last-child .fi-ta-record-actions,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-ta-actions,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-ta-record-actions,
    .archive-table-shell table tbody tr td:last-child .fi-ta-actions,
    .archive-table-shell table tbody tr td:last-child .fi-ta-record-actions,
    .fi-ta-table tbody tr td:last-child .fi-ta-actions,
    .fi-ta-table tbody tr td:last-child .fi-ta-record-actions {
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 12px !important;
        width: 100% !important;
        min-width: 120px !important;
        overflow: visible !important;
        flex-wrap: nowrap !important;
    }

    .ajo-table-shell table tbody tr td:last-child .fi-btn,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-btn,
    .archive-table-shell table tbody tr td:last-child .fi-btn,
    .fi-ta-table tbody tr td:last-child .fi-btn,
    .ajo-table-shell .sf-job-row-action,
    .sf-ajo-table-shell .sf-job-row-action,
    .archive-table-shell .sf-job-row-action {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        max-width: 52px !important;
        max-height: 52px !important;
        padding: 0 !important;
        border-radius: 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        overflow: visible !important;
        box-shadow: 0 12px 24px rgba(15, 23, 42, .06) !important;
    }

    .ajo-table-shell .sf-job-row-action-restore,
    .sf-ajo-table-shell .sf-job-row-action-restore,
    .archive-table-shell .sf-job-row-action-restore,
    .fi-ta-table .sf-job-row-action-restore {
        background: #f2b705 !important;
        color: #3b2a00 !important;
        border: 1px solid rgba(179, 139, 47, .28) !important;
        box-shadow: 0 10px 22px rgba(242, 183, 5, .18) !important;
    }

    .ajo-table-shell .sf-job-row-action-delete,
    .sf-ajo-table-shell .sf-job-row-action-delete,
    .archive-table-shell .sf-job-row-action-delete,
    .fi-ta-table .sf-job-row-action-delete {
        background: #f8fafc !important;
        color: #64748b !important;
        border: 1px solid #d7e2e5 !important;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .04) !important;
    }

    .ajo-table-shell table tbody tr td:last-child .fi-btn-label,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-btn-label,
    .archive-table-shell table tbody tr td:last-child .fi-btn-label,
    .fi-ta-table tbody tr td:last-child .fi-btn-label,
    .ajo-table-shell .sf-job-row-action .fi-btn-label,
    .sf-ajo-table-shell .sf-job-row-action .fi-btn-label,
    .archive-table-shell .sf-job-row-action .fi-btn-label {
        display: none !important;
    }

    .ajo-table-shell table tbody tr td:last-child .fi-btn svg,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-btn svg,
    .archive-table-shell table tbody tr td:last-child .fi-btn svg,
    .fi-ta-table tbody tr td:last-child .fi-btn svg,
    .ajo-table-shell .sf-job-row-action svg,
    .sf-ajo-table-shell .sf-job-row-action svg,
    .archive-table-shell .sf-job-row-action svg {
        width: 27px !important;
        height: 27px !important;
        min-width: 27px !important;
        min-height: 27px !important;
        stroke-width: 2.3 !important;
    }

    .dark .ajo-table-shell table thead tr th:last-child,
    .dark .sf-ajo-table-shell table thead tr th:last-child,
    .dark .archive-table-shell table thead tr th:last-child,
    .dark .fi-ta-table thead tr th:last-child {
        background: rgba(15, 23, 42, .88) !important;
        color: #e0f2fe !important;
    }

    .dark .ajo-table-shell table thead tr th:last-child *,
    .dark .sf-ajo-table-shell table thead tr th:last-child *,
    .dark .archive-table-shell table thead tr th:last-child *,
    .dark .fi-ta-table thead tr th:last-child * {
        color: #e0f2fe !important;
    }

    .dark .ajo-table-shell .sf-job-row-action-delete,
    .dark .sf-ajo-table-shell .sf-job-row-action-delete,
    .dark .archive-table-shell .sf-job-row-action-delete,
    .dark .fi-ta-table .sf-job-row-action-delete {
        background: rgba(15, 23, 42, .72) !important;
        color: #cbd5e1 !important;
        border-color: rgba(148, 163, 184, .22) !important;
    }
</style>


<style id="sf-archived-job-openings-actions-header-smaller-final">
    /*
     | Final fixed polish:
     | - ACTIONS title stays inside the header row
     | - No absolute positioning / no clipping
     | - Smaller action buttons/icons
     */

    .ajo-table-shell table thead tr th:last-child,
    .sf-ajo-table-shell table thead tr th:last-child,
    .archive-table-shell table thead tr th:last-child,
    .fi-ta-table thead tr th:last-child {
        width: 148px !important;
        min-width: 148px !important;
        max-width: 148px !important;
        padding: 0 12px !important;
        text-align: center !important;
        vertical-align: middle !important;
        background: #eef5f8 !important;
        overflow: visible !important;
        white-space: nowrap !important;
        font-size: 0 !important;
        color: transparent !important;
        line-height: 1 !important;
    }

    .ajo-table-shell table thead tr th:last-child > *,
    .sf-ajo-table-shell table thead tr th:last-child > *,
    .archive-table-shell table thead tr th:last-child > *,
    .fi-ta-table thead tr th:last-child > * {
        display: none !important;
    }

    .ajo-table-shell table thead tr th:last-child::before,
    .sf-ajo-table-shell table thead tr th:last-child::before,
    .archive-table-shell table thead tr th:last-child::before,
    .fi-ta-table thead tr th:last-child::before {
        content: "ACTIONS" !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 100% !important;
        min-height: 52px !important;
        color: #1f4664 !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        line-height: 1 !important;
        visibility: visible !important;
    }

    .ajo-table-shell table tbody tr td:last-child,
    .sf-ajo-table-shell table tbody tr td:last-child,
    .archive-table-shell table tbody tr td:last-child,
    .fi-ta-table tbody tr td:last-child {
        width: 148px !important;
        min-width: 148px !important;
        max-width: 148px !important;
        padding: 10px 12px !important;
        text-align: center !important;
        vertical-align: middle !important;
        overflow: visible !important;
    }

    .ajo-table-shell table tbody tr td:last-child > *,
    .sf-ajo-table-shell table tbody tr td:last-child > *,
    .archive-table-shell table tbody tr td:last-child > *,
    .fi-ta-table tbody tr td:last-child > *,
    .ajo-table-shell table tbody tr td:last-child .fi-ta-actions,
    .ajo-table-shell table tbody tr td:last-child .fi-ta-record-actions,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-ta-actions,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-ta-record-actions,
    .archive-table-shell table tbody tr td:last-child .fi-ta-actions,
    .archive-table-shell table tbody tr td:last-child .fi-ta-record-actions,
    .fi-ta-table tbody tr td:last-child .fi-ta-actions,
    .fi-ta-table tbody tr td:last-child .fi-ta-record-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-direction: row !important;
        gap: 10px !important;
        flex-wrap: nowrap !important;
        width: 100% !important;
        margin: 0 !important;
    }

    .ajo-table-shell table tbody tr td:last-child .fi-btn,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-btn,
    .archive-table-shell table tbody tr td:last-child .fi-btn,
    .fi-ta-table tbody tr td:last-child .fi-btn,
    .ajo-table-shell .sf-job-row-action,
    .sf-ajo-table-shell .sf-job-row-action,
    .archive-table-shell .sf-job-row-action {
        width: 44px !important;
        height: 44px !important;
        min-width: 44px !important;
        min-height: 44px !important;
        max-width: 44px !important;
        max-height: 44px !important;
        border-radius: 16px !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .ajo-table-shell table tbody tr td:last-child .fi-btn svg,
    .sf-ajo-table-shell table tbody tr td:last-child .fi-btn svg,
    .archive-table-shell table tbody tr td:last-child .fi-btn svg,
    .fi-ta-table tbody tr td:last-child .fi-btn svg,
    .ajo-table-shell .sf-job-row-action svg,
    .sf-ajo-table-shell .sf-job-row-action svg,
    .archive-table-shell .sf-job-row-action svg {
        width: 22px !important;
        height: 22px !important;
        min-width: 22px !important;
        min-height: 22px !important;
        stroke-width: 2.25 !important;
    }

    .dark .ajo-table-shell table thead tr th:last-child,
    .dark .sf-ajo-table-shell table thead tr th:last-child,
    .dark .archive-table-shell table thead tr th:last-child,
    .dark .fi-ta-table thead tr th:last-child {
        background: rgba(15, 23, 42, .88) !important;
    }

    .dark .ajo-table-shell table thead tr th:last-child::before,
    .dark .sf-ajo-table-shell table thead tr th:last-child::before,
    .dark .archive-table-shell table thead tr th:last-child::before,
    .dark .fi-ta-table thead tr th:last-child::before {
        color: #e0f2fe !important;
    }
</style>


<style id="sf-archived-openings-hide-bulk-until-selected-final">
    /*
     | Archived Job Openings ONLY:
     | Hide Restore Selected / Permanent Delete until a real row is selected.
     */

    body:not(.sf-archived-openings-has-selection) .fi-ta-bulk-actions,
    body:not(.sf-archived-openings-has-selection) .fi-ta-bulk-actions-toolbar,
    body:not(.sf-archived-openings-has-selection) .sf-archived-opening-bulk-actions,
    body:not(.sf-archived-openings-has-selection) .sf-ajo-bulk-actions,
    body:not(.sf-archived-openings-has-selection) .sf-archive-bulk-actions,
    body:not(.sf-archived-openings-has-selection) .fi-ta-toolbar button[title*="Restore Selected"],
    body:not(.sf-archived-openings-has-selection) .fi-ta-toolbar button[title*="Permanent Delete"],
    body:not(.sf-archived-openings-has-selection) .fi-ta-toolbar a[title*="Restore Selected"],
    body:not(.sf-archived-openings-has-selection) .fi-ta-toolbar a[title*="Permanent Delete"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        height: 0 !important;
        min-height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    body.sf-archived-openings-has-selection .fi-ta-bulk-actions,
    body.sf-archived-openings-has-selection .fi-ta-bulk-actions-toolbar,
    body.sf-archived-openings-has-selection .sf-archived-opening-bulk-actions,
    body.sf-archived-openings-has-selection .sf-ajo-bulk-actions,
    body.sf-archived-openings-has-selection .sf-archive-bulk-actions {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        height: auto !important;
        min-height: 44px !important;
        max-height: none !important;
        overflow: visible !important;
    }
</style>

<script id="sf-archived-openings-hide-bulk-until-selected-final-script">
    (() => {
        const isArchivedJobOpeningsPage = () => {
            return (window.location.pathname || '').includes('/admin/archived-job-openings');
        };

        const isBulkButton = (el) => {
            const text = (el.textContent || '').trim().toLowerCase();
            const title = (el.getAttribute('title') || '').trim().toLowerCase();
            const aria = (el.getAttribute('aria-label') || '').trim().toLowerCase();

            return (
                text.includes('restore selected') ||
                text.includes('permanent delete') ||
                title.includes('restore selected') ||
                title.includes('permanent delete') ||
                aria.includes('restore selected') ||
                aria.includes('permanent delete')
            );
        };

        const apply = () => {
            if (!isArchivedJobOpeningsPage()) {
                document.body.classList.remove('sf-archived-openings-has-selection');
                return;
            }

            const hasCheckedRow = document.querySelectorAll(
                '.fi-ta-table tbody input[type="checkbox"]:checked, table tbody input[type="checkbox"]:checked'
            ).length > 0;

            document.body.classList.toggle('sf-archived-openings-has-selection', hasCheckedRow);

            document.querySelectorAll('button, a').forEach((el) => {
                if (!isBulkButton(el)) return;

                const isInsideTableActions = !!el.closest('tbody');
                if (isInsideTableActions) return;

                if (!hasCheckedRow) {
                    el.style.setProperty('display', 'none', 'important');
                    el.style.setProperty('visibility', 'hidden', 'important');
                    el.style.setProperty('opacity', '0', 'important');
                    el.style.setProperty('pointer-events', 'none', 'important');
                    el.style.setProperty('height', '0', 'important');
                    el.style.setProperty('min-height', '0', 'important');
                    el.style.setProperty('max-height', '0', 'important');
                    el.style.setProperty('overflow', 'hidden', 'important');
                    el.style.setProperty('margin', '0', 'important');
                    el.style.setProperty('padding', '0', 'important');
                } else {
                    el.style.removeProperty('display');
                    el.style.removeProperty('visibility');
                    el.style.removeProperty('opacity');
                    el.style.removeProperty('pointer-events');
                    el.style.removeProperty('height');
                    el.style.removeProperty('min-height');
                    el.style.removeProperty('max-height');
                    el.style.removeProperty('overflow');
                    el.style.removeProperty('margin');
                    el.style.removeProperty('padding');
                }
            });

            /*
             | Also hide parent wrappers if they contain only those bulk buttons.
             */
            document.querySelectorAll('.fi-ta-bulk-actions, .fi-ta-bulk-actions-toolbar, .fi-ta-toolbar > div, .fi-ta-header-toolbar > div').forEach((box) => {
                const bulkButtons = Array.from(box.querySelectorAll('button, a')).filter(isBulkButton);

                if (bulkButtons.length === 0) return;

                if (!hasCheckedRow) {
                    box.style.setProperty('display', 'none', 'important');
                    box.style.setProperty('visibility', 'hidden', 'important');
                    box.style.setProperty('opacity', '0', 'important');
                    box.style.setProperty('pointer-events', 'none', 'important');
                } else {
                    box.style.removeProperty('display');
                    box.style.removeProperty('visibility');
                    box.style.removeProperty('opacity');
                    box.style.removeProperty('pointer-events');
                }
            });
        };

        document.addEventListener('DOMContentLoaded', apply);

        document.addEventListener('change', (event) => {
            if (event.target && event.target.matches('input[type="checkbox"]')) {
                setTimeout(apply, 20);
                setTimeout(apply, 120);
                setTimeout(apply, 300);
            }
        });

        new MutationObserver(apply).observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
        });

        setInterval(apply, 500);
    })();
</script>
