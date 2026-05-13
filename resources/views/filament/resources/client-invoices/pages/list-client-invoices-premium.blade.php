<x-filament-panels::page>
    <style>
        .fi-header {
            display: none !important;
        }

        .ci-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .ci-hero {
            position: relative;
            overflow: hidden;
            border-radius: 26px;
            padding: 28px 32px;
            border: 1px solid rgba(76, 167, 168, 0.18);
            background: linear-gradient(135deg, #081a34 0%, #0b2a56 52%, #0f3f48 100%);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
        }

        .ci-hero::before {
            content: "";
            position: absolute;
            right: -70px;
            top: -70px;
            width: 220px;
            height: 220px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(255,255,255,.10), transparent 72%);
            pointer-events: none;
        }

        .ci-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b89332);
        }

        .ci-breadcrumb {
            position: relative;
            z-index: 1;
            font-size: 14px;
            color: rgba(255,255,255,.68);
            font-weight: 500;
        }

        .ci-title-row {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .ci-title {
            font-size: 56px;
            line-height: 1;
            font-weight: 900;
            color: #fff;
            letter-spacing: -.04em;
        }

        .ci-actions .fi-ac {
            justify-content: flex-end;
            gap: 12px !important;
        }

        .ci-actions .fi-btn {
            border-radius: 999px !important;
            min-height: 46px;
            padding-inline: 18px !important;
            font-weight: 800 !important;
            box-shadow: none !important;
            border: 0 !important;
        }

        .ci-actions .fi-btn-color-success {
            background: linear-gradient(135deg, #12c166 0%, #09a84f 100%) !important;
            color: #081a34 !important;
        }

        .ci-actions .fi-btn-color-primary,
        .ci-actions .fi-btn-color-warning,
        .ci-actions .fi-btn-color-gray {
            background: linear-gradient(135deg, #f59e0b 0%, #ea7a00 100%) !important;
            color: #1f1400 !important;
        }

        .ci-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .ci-kpi-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 22px 24px;
            transition: .22s ease;
            background: #ffffff;
            border: 1px solid #dbe7ef;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .ci-kpi-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #7ad6d7);
        }

        .ci-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
        }

        .ci-kpi-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        .ci-kpi-value {
            margin-top: 18px;
            font-size: 58px;
            line-height: 1;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -.03em;
        }

        .ci-kpi-sub {
            margin-top: 14px;
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        .ci-filter-note {
            font-size: 13px;
            color: #8ea8be;
            margin-top: -8px;
            margin-bottom: 4px;
        }

        .ci-table-shell {
            background: #ffffff;
            border: 1px solid #dbe7ef;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .ci-table-shell .fi-ta-outer {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .ci-table-shell .fi-ta-header,
        .ci-table-shell .fi-ta-table,
        .ci-table-shell .fi-ta-content,
        .ci-table-shell .fi-pagination,
        .ci-table-shell .fi-ta-toolbar,
        .ci-table-shell .fi-ta-filters {
            background: transparent !important;
        }

        .ci-table-shell .fi-input,
        .ci-table-shell .fi-select-input,
        .ci-table-shell .fi-ta-search-field input,
        .ci-table-shell .fi-input-wrp,
        .ci-table-shell .fi-select {
            border-radius: 14px !important;
        }

        .ci-table-shell .fi-input,
        .ci-table-shell .fi-select-input,
        .ci-table-shell .fi-ta-search-field input {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #dbe7ef !important;
        }

        .ci-table-shell .fi-input::placeholder,
        .ci-table-shell .fi-ta-search-field input::placeholder {
            color: #94a3b8 !important;
        }

        .ci-table-shell .fi-input-wrp,
        .ci-table-shell .fi-select {
            background: #ffffff !important;
            border-color: #dbe7ef !important;
        }

        .ci-table-shell .fi-tabs,
        .ci-table-shell .fi-dropdown-panel,
        .ci-table-shell .fi-modal-content,
        .ci-table-shell .fi-section {
            border-radius: 16px !important;
        }

        .ci-table-shell table thead th {
            background: #f8fafc !important;
            color: #64748b !important;
        }

        .ci-table-shell table tbody tr {
            background: transparent !important;
        }

        .ci-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
        }

        .ci-table-shell .fi-badge {
            border-radius: 999px !important;
        }

        .ci-table-shell .fi-pagination {
            border-top: 1px solid #e5edf5 !important;
        }

        .dark .ci-kpi-card {
            background: rgba(12,23,38,.96);
            border: 1px solid rgba(76,167,168,.16);
            box-shadow: 0 10px 24px rgba(0,0,0,.22);
        }

        .dark .ci-kpi-card:hover {
            box-shadow: 0 14px 28px rgba(0,0,0,.28);
        }

        .dark .ci-kpi-title {
            color: #f6fbff;
        }

        .dark .ci-kpi-value {
            color: #ffffff;
        }

        .dark .ci-kpi-sub {
            color: #9fb2c3;
        }

        .dark .ci-filter-note {
            color: #8ea8be;
        }

        .dark .ci-table-shell {
            background: rgba(12,23,38,.96);
            border: 1px solid rgba(76,167,168,.16);
            box-shadow: 0 10px 24px rgba(0,0,0,.18);
        }

        .dark .ci-table-shell .fi-input,
        .dark .ci-table-shell .fi-select-input,
        .dark .ci-table-shell .fi-ta-search-field input {
            background: rgba(255,255,255,.03) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.14) !important;
        }

        .dark .ci-table-shell .fi-input::placeholder,
        .dark .ci-table-shell .fi-ta-search-field input::placeholder {
            color: #8ea8be !important;
        }

        .dark .ci-table-shell .fi-input-wrp,
        .dark .ci-table-shell .fi-select {
            background: rgba(255,255,255,.03) !important;
            border-color: rgba(76,167,168,.14) !important;
        }

        .dark .ci-table-shell table thead th {
            background: rgba(255,255,255,.03) !important;
            color: #8ea8be !important;
        }

        .dark .ci-table-shell table tbody td {
            background: rgba(255,255,255,.015) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.10) !important;
        }

        .dark .ci-table-shell .fi-pagination {
            border-top: 1px solid rgba(76,167,168,.10) !important;
        }

        .dark .ci-table-shell .fi-ta-empty-state,
        .dark .ci-table-shell .fi-no-notification,
        .dark .ci-table-shell .text-gray-500,
        .dark .ci-table-shell .text-gray-600,
        .dark .ci-table-shell .text-gray-700 {
            color: #9fb2c3 !important;
        }

        @media (max-width: 1100px) {
            .ci-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ci-title {
                font-size: 44px;
            }
        }

        @media (max-width: 720px) {
            .ci-kpi-grid {
                grid-template-columns: 1fr;
            }

            .ci-title {
                font-size: 36px;
            }

            .ci-hero {
                padding: 22px 20px;
            }
        }

        /* FINAL Client Invoices List - Salary Slips Material Design style */
        .fi-header {
            display: none !important;
        }

        .ci-wrap {
            width: min(100%, 1240px) !important;
            max-width: 1240px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 24px !important;
        }

        .ci-hero {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            gap: 20px !important;
            border: 1px solid #d7e2e5 !important;
            border-radius: 22px !important;
            padding: 26px 28px !important;
            background: linear-gradient(135deg, #18344d 0%, #234d6f 50%, #2f6f73 100%) !important;
            box-shadow: 0 18px 34px rgba(15,23,42,.10) !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .ci-hero::before {
            content: "" !important;
            position: absolute !important;
            right: -90px !important;
            top: -90px !important;
            width: 260px !important;
            height: 260px !important;
            border-radius: 999px !important;
            background: radial-gradient(circle, rgba(255,255,255,.13), transparent 70%) !important;
            pointer-events: none !important;
        }

        .ci-hero::after {
            content: "" !important;
            position: absolute !important;
            inset: auto 0 0 0 !important;
            height: 4px !important;
            background: linear-gradient(90deg,#4ca7a8,#b38b2f) !important;
        }

        .ci-title-row {
            position: relative !important;
            z-index: 1 !important;
            width: 100% !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            gap: 20px !important;
            margin-top: 0 !important;
        }

        .ci-breadcrumb {
            position: relative !important;
            z-index: 1 !important;
            font-size: 14px !important;
            color: rgba(255,255,255,.78) !important;
            font-weight: 500 !important;
            margin-bottom: 8px !important;
        }

        .ci-title {
            margin-top: 8px !important;
            font-size: 56px !important;
            line-height: .95 !important;
            font-weight: 900 !important;
            color: #fff !important;
            letter-spacing: -.04em !important;
        }

        .ci-subtitle,
        .ci-hero-sub,
        .ci-description {
            margin-top: 16px !important;
            max-width: 920px !important;
            font-size: 15px !important;
            line-height: 1.7 !important;
            color: rgba(255,255,255,.84) !important;
        }

        .ci-actions {
            position: relative !important;
            z-index: 2 !important;
            flex-shrink: 0 !important;
        }

        .ci-actions .fi-ac {
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
            overflow: visible !important;
        }

        .ci-actions .fi-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 48px !important;
            padding: 0 18px !important;
            border-radius: 999px !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 900 !important;
            transition: all .18s ease !important;
            cursor: pointer !important;
            border: none !important;
            box-shadow: none !important;
            white-space: nowrap !important;
        }

        .ci-actions .fi-btn:hover {
            transform: translateY(-1px) !important;
        }

        .ci-actions .fi-btn-color-success {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            box-shadow: 0 10px 20px rgba(242,183,5,.22) !important;
        }

        .ci-actions .fi-btn-color-primary,
        .ci-actions .fi-btn-color-warning,
        .ci-actions .fi-btn-color-gray {
            background: rgba(255,255,255,.12) !important;
            color: #fff !important;
            border: 1px solid rgba(255,255,255,.14) !important;
        }

        .ci-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 16px !important;
        }

        .ci-kpi-card {
            display: block !important;
            text-decoration: none !important;
            color: inherit !important;
            border: 1px solid #d7e2e5 !important;
            border-radius: 18px !important;
            padding: 18px !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.04) !important;
            transition: all .18s ease !important;
            position: relative !important;
            overflow: hidden !important;
            background: rgba(255,255,255,.96) !important;
        }

        .ci-kpi-card::before {
            content: "" !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 5px !important;
            height: auto !important;
            background: linear-gradient(180deg,#1f4664,#4ca7a8) !important;
        }

        .ci-kpi-card:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 14px 24px rgba(15,23,42,.08) !important;
        }

        .ci-kpi-title {
            font-size: 13px !important;
            font-weight: 900 !important;
            letter-spacing: .04em !important;
            color: #1f4664 !important;
        }

        .ci-kpi-value {
            margin-top: 14px !important;
            font-size: 42px !important;
            line-height: 1 !important;
            font-weight: 900 !important;
            color: #0f172a !important;
            letter-spacing: -.04em !important;
        }

        .ci-kpi-sub {
            margin-top: 12px !important;
            font-size: 13px !important;
            color: #667085 !important;
            line-height: 1.55 !important;
        }

        .ci-filter-note {
            font-size: 13px !important;
            color: #607085 !important;
            margin-top: -6px !important;
            margin-bottom: 2px !important;
            font-weight: 700 !important;
        }

        .ci-table-shell {
            background: linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%) !important;
            border: 1px solid #d7e2e5 !important;
            border-radius: 22px !important;
            padding: 0 !important;
            overflow: hidden !important;
            box-shadow: 0 10px 24px rgba(15,23,42,.04) !important;
        }

        .ci-table-shell .fi-ta-outer,
        .ci-table-shell .fi-ta,
        .ci-table-shell .fi-ta-content,
        .ci-table-shell .fi-ta-table,
        .ci-table-shell .fi-ta-header,
        .ci-table-shell .fi-ta-toolbar,
        .ci-table-shell .fi-ta-filters,
        .ci-table-shell .fi-pagination {
            background: transparent !important;
            border-radius: 0 !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .ci-table-shell .fi-ta-header,
        .ci-table-shell .fi-ta-toolbar {
            padding: 16px 18px !important;
            border-bottom: 1px solid #e4ecef !important;
        }

        .ci-table-shell .fi-input,
        .ci-table-shell .fi-select-input,
        .ci-table-shell .fi-ta-search-field input,
        .ci-table-shell .fi-input-wrp,
        .ci-table-shell .fi-select {
            border-radius: 14px !important;
            background: #fff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: none !important;
        }

        .ci-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-size: 11px !important;
            font-weight: 900 !important;
            letter-spacing: .12em !important;
            text-transform: uppercase !important;
            padding-top: 14px !important;
            padding-bottom: 14px !important;
        }

        .ci-table-shell table tbody tr {
            background: transparent !important;
            transition: all .18s ease !important;
        }

        .ci-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .ci-table-shell table tbody td {
            background: #fff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
            padding-top: 15px !important;
            padding-bottom: 15px !important;
            font-weight: 650 !important;
        }

        .ci-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 900 !important;
            letter-spacing: .03em !important;
            padding: 7px 10px !important;
        }

        .ci-table-shell .fi-btn {
            border-radius: 999px !important;
            font-weight: 900 !important;
        }

        .ci-table-shell .fi-dropdown-panel,
        .ci-table-shell .fi-modal-content,
        .ci-table-shell .fi-section {
            border-radius: 16px !important;
        }

        .dark .ci-hero {
            border-color: rgba(76,167,168,.20) !important;
        }

        .dark .ci-kpi-card {
            background: rgba(12,23,38,.96) !important;
            border: 1px solid rgba(76,167,168,.16) !important;
            box-shadow: 0 10px 24px rgba(0,0,0,.22) !important;
        }

        .dark .ci-kpi-title,
        .dark .ci-kpi-value {
            color: #f6fbff !important;
        }

        .dark .ci-kpi-sub,
        .dark .ci-filter-note {
            color: #9fb2c3 !important;
        }

        .dark .ci-table-shell {
            background: linear-gradient(180deg,rgba(12,23,38,.96) 0%,rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .ci-table-shell table thead th {
            background: rgba(15,23,42,.92) !important;
            color: #8fd6d7 !important;
        }

        .dark .ci-table-shell table tbody td {
            background: rgba(12,23,38,.96) !important;
            color: #f8fafc !important;
            border-color: rgba(148,163,184,.12) !important;
        }

        .dark .ci-table-shell table tbody tr:hover td {
            background: rgba(30,41,59,.96) !important;
        }

        @media (max-width: 1200px) {
            .ci-title-row {
                flex-direction: column !important;
            }

            .ci-actions .fi-ac {
                justify-content: flex-start !important;
            }

            .ci-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 760px) {
            .ci-wrap {
                width: min(100% - 20px, 100%) !important;
            }

            .ci-hero {
                padding: 22px !important;
                border-radius: 22px !important;
            }

            .ci-title {
                font-size: 40px !important;
            }

            .ci-kpi-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* CLIENT INVOICES COLUMNS DROPDOWN REAL SCROLL FIX */
        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]),
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) {
            width: 300px !important;
            max-width: 300px !important;
            max-height: 460px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            border-radius: 22px !important;
            background: rgba(255,255,255,.98) !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 24px 55px rgba(15,23,42,.18) !important;
            padding: 14px !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar {
            width: 7px !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-track,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-track {
            background: transparent !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-thumb,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-thumb {
            border-radius: 999px !important;
            background: rgba(148,163,184,.58) !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) label,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) label {
            display: grid !important;
            grid-template-columns: 18px 1fr !important;
            align-items: center !important;
            gap: 11px !important;
            min-height: 38px !important;
            padding: 6px 8px !important;
            border-radius: 13px !important;
            color: #0f172a !important;
            font-weight: 850 !important;
            cursor: pointer !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) label:hover,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) label:hover {
            background: #f4f8fa !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"],
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"] {
            appearance: none !important;
            -webkit-appearance: none !important;
            display: inline-grid !important;
            place-content: center !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: static !important;
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            margin: 0 !important;
            border-radius: 4px !important;
            background: #ffffff !important;
            border: 1.5px solid #94a3b8 !important;
            box-shadow: none !important;
            cursor: pointer !important;
            pointer-events: auto !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]::before,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]::before {
            content: "" !important;
            width: 8px !important;
            height: 8px !important;
            transform: scale(0) !important;
            transition: transform .12s ease !important;
            background: #ffffff !important;
            clip-path: polygon(14% 44%, 0 65%, 43% 100%, 100% 18%, 80% 0%, 38% 62%) !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:checked,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:checked {
            background: #1f4664 !important;
            border-color: #1f4664 !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:checked::before,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:checked::before {
            transform: scale(1) !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:disabled,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:disabled {
            opacity: 1 !important;
            pointer-events: auto !important;
            cursor: pointer !important;
            filter: none !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) label:has(input[type="checkbox"]:disabled),
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) label:has(input[type="checkbox"]:disabled),
        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) label:has(input[type="checkbox"]:disabled) span,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) label:has(input[type="checkbox"]:disabled) span {
            opacity: 1 !important;
            color: #0f172a !important;
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) button[type="submit"],
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) button[type="submit"],
        .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) .fi-btn,
        .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) .fi-btn {
            position: sticky !important;
            bottom: 0 !important;
            z-index: 20 !important;
            margin-top: 10px !important;
            border-radius: 999px !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
            font-weight: 950 !important;
            min-height: 40px !important;
            padding-inline: 16px !important;
            box-shadow: 0 10px 22px rgba(242,183,5,.22) !important;
        }

        .dark .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]),
        .dark .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) {
            background: rgba(15,23,42,.98) !important;
            border-color: rgba(76,167,168,.22) !important;
            box-shadow: 0 24px 55px rgba(0,0,0,.38) !important;
        }

        .dark .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) label,
        .dark .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) label,
        .dark .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) label span,
        .dark .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) label span {
            color: #f8fafc !important;
        }

        .dark .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"]) label:hover,
        .dark .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"]) label:hover {
            background: rgba(76,167,168,.10) !important;
        }

        .dark .ci-table-shell .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-thumb,
        .dark .ci-wrap .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-thumb {
            background: rgba(148,163,184,.36) !important;
        }

</style>

    <div class="ci-wrap">
        <section class="ci-hero">
            <div class="ci-breadcrumb">Client Invoices &nbsp;›&nbsp; List</div>

            <div class="ci-title-row">
                <div>
                    <div class="ci-title">Client Invoices</div>
                </div>

                <div class="ci-actions">
                    <x-filament::actions :actions="$this->getCachedHeaderActions()" />
                </div>
            </div>
        </section>

        <section class="ci-kpi-grid">
            <div class="ci-kpi-card">
                <div class="ci-kpi-title">Draft Invoices</div>
                <div class="ci-kpi-value">{{ number_format($draftCount) }}</div>
                <div class="ci-kpi-sub">Invoices still in draft stage</div>
            </div>

            <div class="ci-kpi-card">
                <div class="ci-kpi-title">Approved</div>
                <div class="ci-kpi-value">{{ number_format($approvedCount) }}</div>
                <div class="ci-kpi-sub">Approved and ready</div>
            </div>

            <div class="ci-kpi-card">
                <div class="ci-kpi-title">Submitted</div>
                <div class="ci-kpi-value">{{ number_format($submittedCount) }}</div>
                <div class="ci-kpi-sub">Sent to client</div>
            </div>

            <div class="ci-kpi-card">
                <div class="ci-kpi-title">Partially Paid</div>
                <div class="ci-kpi-value">{{ number_format($partialCount) }}</div>
                <div class="ci-kpi-sub">Partially settled</div>
            </div>

            <div class="ci-kpi-card">
                <div class="ci-kpi-title">Paid</div>
                <div class="ci-kpi-value">{{ number_format($paidCount) }}</div>
                <div class="ci-kpi-sub">Fully settled invoices</div>
            </div>

            <div class="ci-kpi-card">
                <div class="ci-kpi-title">Open Invoice Value</div>
                <div class="ci-kpi-value">{{ number_format((float) $openInvoiceValue, 2) }}</div>
                <div class="ci-kpi-sub">Draft + approved + submitted + partial</div>
            </div>
        </section>

        <div class="ci-filter-note">
            Use the filter bar below to narrow by client, project, status, year, and month.
        </div>

        <section class="ci-table-shell">
            {{ $this->table }}
        </section>
    </div>

<!-- CLIENT INVOICES FORCE ENABLE COLUMNS CHECKBOXES -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    function enableClientInvoiceColumnCheckboxes() {
        document
            .querySelectorAll('.ci-table-shell .fi-dropdown-panel input[type="checkbox"], .ci-wrap .fi-dropdown-panel input[type="checkbox"]')
            .forEach(function (checkbox) {
                checkbox.removeAttribute('disabled');
                checkbox.disabled = false;
                checkbox.style.pointerEvents = 'auto';
                checkbox.style.opacity = '1';
                checkbox.style.filter = 'none';

                const label = checkbox.closest('label');
                if (label) {
                    label.style.pointerEvents = 'auto';
                    label.style.opacity = '1';
                    label.style.cursor = 'pointer';

                    label.querySelectorAll('span').forEach(function (span) {
                        span.style.opacity = '1';
                    });
                }
            });
    }

    enableClientInvoiceColumnCheckboxes();

    document.addEventListener('click', function () {
        setTimeout(enableClientInvoiceColumnCheckboxes, 40);
        setTimeout(enableClientInvoiceColumnCheckboxes, 160);
        setTimeout(enableClientInvoiceColumnCheckboxes, 350);
    }, true);

    new MutationObserver(function () {
        enableClientInvoiceColumnCheckboxes();
    }).observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['disabled', 'class', 'style']
    });
});
</script>
<!-- /CLIENT INVOICES FORCE ENABLE COLUMNS CHECKBOXES -->

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

