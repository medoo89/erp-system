<x-filament-panels::page>
    <style>
        .sf-audit-shell {
            width: 100%;
            max-width: 1480px;
            margin: 0 auto;
            padding-bottom: 90px;
            box-sizing: border-box;
        }

        .sf-audit-hero {
            border-radius: 34px;
            padding: 34px;
            background:
                radial-gradient(circle at top right, rgba(20,184,166,.22), transparent 34%),
                linear-gradient(135deg, #14213d, #0f766e);
            box-shadow: 0 22px 55px rgba(15,23,42,.14);
            color: #fff;
            margin-bottom: 22px;
            overflow: hidden;
        }

        .sf-audit-kicker {
            color: #9ff4e9;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .sf-audit-title {
            font-size: clamp(42px, 5vw, 72px);
            font-weight: 950;
            letter-spacing: -.07em;
            line-height: .9;
            margin: 0;
        }

        .sf-audit-sub {
            max-width: 840px;
            margin-top: 16px;
            color: rgba(255,255,255,.78);
            font-size: 15px;
            line-height: 1.65;
            font-weight: 700;
        }

        .sf-audit-filters {
            border-radius: 28px;
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(148,163,184,.22);
            box-shadow: 0 18px 44px rgba(15,23,42,.08);
            padding: 18px;
            margin-bottom: 18px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .sf-audit-filter-grid {
            display: grid;
            grid-template-columns: 1.4fr repeat(4, minmax(150px, 1fr));
            gap: 12px;
            align-items: end;
        }

        .sf-audit-field label {
            display: block;
            color: #334155;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .sf-audit-input,
        .sf-audit-select {
            width: 100%;
            height: 44px;
            border-radius: 16px;
            border: 1px solid rgba(148,163,184,.32);
            background: #fff;
            color: #0f172a;
            padding: 0 13px;
            font-size: 13px;
            font-weight: 800;
            outline: none;
            box-sizing: border-box;
        }

        .sf-audit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .sf-audit-btn {
            min-height: 42px;
            border-radius: 999px;
            border: 0;
            padding: 0 18px;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            background: #2563eb;
            color: #fff;
            box-shadow: 0 14px 28px rgba(37,99,235,.16);
        }

        .sf-audit-btn-soft {
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid rgba(148,163,184,.25);
            box-shadow: none;
        }

        .sf-audit-table-card {
            width: 100%;
            max-width: 100%;
            border-radius: 28px;
            overflow-x: auto;
            overflow-y: visible;
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(148,163,184,.22);
            box-shadow: 0 18px 44px rgba(15,23,42,.08);
            box-sizing: border-box;
        }

        .sf-audit-table {
            width: 100%;
            min-width: 1040px;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
        }

        .sf-audit-table th,
        .sf-audit-table td {
            text-align: left;
            vertical-align: top;
            box-sizing: border-box;
            padding: 16px 14px;
            border-bottom: 1px solid rgba(148,163,184,.16);
        }

        .sf-audit-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .sf-audit-col-time { width: 135px; }
        .sf-audit-col-user { width: 230px; }
        .sf-audit-col-action { width: 140px; }
        .sf-audit-col-module { width: 210px; }
        .sf-audit-col-description { width: auto; }

        .sf-audit-row {
            position: relative;
        }

        .sf-audit-row td:first-child {
            border-left: 5px solid #2563eb;
        }

        .sf-audit-row-danger td:first-child { border-left-color: #ef4444; }
        .sf-audit-row-warning td:first-child { border-left-color: #f59e0b; }
        .sf-audit-row-success td:first-child { border-left-color: #10b981; }
        .sf-audit-row-purple td:first-child { border-left-color: #7c3aed; }
        .sf-audit-row-info td:first-child { border-left-color: #2563eb; }

        .sf-audit-row-danger { background: linear-gradient(90deg, rgba(239,68,68,.09), rgba(255,255,255,0) 38%); }
        .sf-audit-row-warning { background: linear-gradient(90deg, rgba(245,158,11,.10), rgba(255,255,255,0) 38%); }
        .sf-audit-row-success { background: linear-gradient(90deg, rgba(16,185,129,.09), rgba(255,255,255,0) 38%); }
        .sf-audit-row-purple { background: linear-gradient(90deg, rgba(124,58,237,.09), rgba(255,255,255,0) 38%); }
        .sf-audit-row-info { background: linear-gradient(90deg, rgba(37,99,235,.06), rgba(255,255,255,0) 38%); }

        .sf-audit-main {
            color: #0f172a;
            font-size: 14px;
            font-weight: 950;
            line-height: 1.35;
            margin-bottom: 5px;
            overflow-wrap: anywhere;
        }

        .sf-audit-muted,
        .sf-audit-meta {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .sf-audit-meta {
            display: grid;
            gap: 5px;
        }

        .sf-audit-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 26px;
            border-radius: 999px;
            padding: 0 11px;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .07em;
            text-transform: uppercase;
            white-space: nowrap;
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid rgba(37,99,235,.18);
        }

        .sf-audit-chip-danger {
            background: #fee2e2;
            color: #dc2626;
            border-color: rgba(239,68,68,.22);
        }

        .sf-audit-chip-warning {
            background: #fef3c7;
            color: #b45309;
            border-color: rgba(245,158,11,.24);
        }

        .sf-audit-chip-success {
            background: #dcfce7;
            color: #047857;
            border-color: rgba(16,185,129,.22);
        }

        .sf-audit-chip-purple {
            background: #ede9fe;
            color: #6d28d9;
            border-color: rgba(124,58,237,.20);
        }

        .sf-audit-diff {
            width: 100%;
            max-width: 100%;
            margin-top: 12px;
            border-radius: 18px;
            border: 1px solid rgba(148,163,184,.24);
            background: rgba(248,250,252,.80);
            overflow: hidden;
            box-sizing: border-box;
        }

        .sf-audit-diff summary {
            list-style: none;
            cursor: pointer;
            padding: 13px 48px 13px 14px;
            color: #234b74;
            font-size: 12px;
            font-weight: 950;
            line-height: 1.25;
            position: relative;
            outline: none;
        }

        .sf-audit-diff summary::-webkit-details-marker {
            display: none;
        }

        .sf-audit-diff summary::after {
            content: "⌄";
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 26px;
            height: 26px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(219,234,254,.85);
            color: #234b74;
            font-size: 24px;
            font-weight: 950;
            font-family: Arial, sans-serif;
        }

        .sf-audit-diff[open] summary::after {
            content: "⌃";
        }

        .sf-audit-diff-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .sf-audit-diff-table th,
        .sf-audit-diff-table td {
            padding: 10px;
            border-bottom: 1px solid rgba(148,163,184,.14);
            font-size: 12px;
            font-weight: 800;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .sf-audit-diff-table th {
            background: rgba(239,246,255,.86);
            color: #334155;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .sf-audit-before,
        .sf-audit-after {
            display: inline-block;
            max-width: 100%;
            border-radius: 10px;
            padding: 5px 7px;
            overflow-wrap: anywhere;
            white-space: normal;
        }

        .sf-audit-before {
            color: #dc2626;
            background: rgba(254,226,226,.55);
        }

        .sf-audit-after {
            color: #047857;
            background: rgba(220,252,231,.60);
        }


        .sf-audit-technical-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding: 12px;
            border-top: 1px solid rgba(148,163,184,.16);
        }

        .sf-audit-technical-details div {
            border-radius: 14px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(148,163,184,.18);
            padding: 10px;
            min-width: 0;
        }

        .sf-audit-technical-details strong {
            display: block;
            color: #334155;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .sf-audit-technical-details span {
            display: block;
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .dark .sf-audit-technical-details div {
            background: rgba(15,23,42,.74);
            border-color: rgba(148,163,184,.16);
        }

        .dark .sf-audit-technical-details strong {
            color: #e2e8f0;
        }

        .dark .sf-audit-technical-details span {
            color: #94a3b8;
        }


        .sf-audit-empty {
            padding: 34px;
            text-align: center;
            color: #64748b;
            font-weight: 800;
        }

        .sf-audit-pagination {
            padding: 14px 16px;
        }

        .sf-audit-pagination svg {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
        }

        .dark .sf-audit-filters,
        .dark .sf-audit-table-card {
            background: rgba(15,23,42,.78);
            border-color: rgba(148,163,184,.18);
            box-shadow: 0 18px 44px rgba(0,0,0,.22);
        }

        .dark .sf-audit-table th {
            background: rgba(15,23,42,.92);
            color: #cbd5e1;
            border-bottom-color: rgba(148,163,184,.18);
        }

        .dark .sf-audit-table td {
            border-bottom-color: rgba(148,163,184,.12);
        }

        .dark .sf-audit-main,
        .dark .sf-audit-field label {
            color: #f8fafc;
        }

        .dark .sf-audit-muted,
        .dark .sf-audit-meta {
            color: #94a3b8;
        }

        .dark .sf-audit-input,
        .dark .sf-audit-select {
            background: rgba(15,23,42,.88);
            color: #f8fafc;
            border-color: rgba(148,163,184,.20);
        }

        .dark .sf-audit-diff {
            background: rgba(15,23,42,.72);
            border-color: rgba(148,163,184,.16);
        }

        .dark .sf-audit-diff summary {
            color: #bfdbfe;
        }

        .dark .sf-audit-diff summary::after {
            background: rgba(30,64,175,.45);
            color: #bfdbfe;
        }

        @media (max-width: 1100px) {
            .sf-audit-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .sf-audit-table {
                min-width: 1040px;
            }
        }

        @media (max-width: 700px) {
            .sf-audit-filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="sf-audit-shell">
        <section class="sf-audit-hero">
            <div class="sf-audit-kicker">Admin Settings · Security Monitor</div>
            <h1 class="sf-audit-title">Audit Logs</h1>
            <div class="sf-audit-sub">
                Track important ERP movements by user, module, action, record, IP address, and time. Use filters to review sensitive activity before and after publishing.
            </div>
        </section>

        <section class="sf-audit-filters">
            <div class="sf-audit-filter-grid">
                <div class="sf-audit-field">
                    <label>Search</label>
                    <input class="sf-audit-input" type="search" wire:model.live.debounce.500ms="search" placeholder="Search user, record, module, IP, description...">
                </div>

                <div class="sf-audit-field">
                    <label>User</label>
                    <select class="sf-audit-select" wire:model.live="userId">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name ?: $user->email }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sf-audit-field">
                    <label>Module</label>
                    <select class="sf-audit-select" wire:model.live="module">
                        <option value="">All Modules</option>
                        @foreach($modules as $item)
                            <option value="{{ $item }}">{{ str($item)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sf-audit-field">
                    <label>Action</label>
                    <select class="sf-audit-select" wire:model.live="action">
                        <option value="">All Actions</option>
                        @foreach($actions as $item)
                            <option value="{{ $item }}">{{ str($item)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sf-audit-field">
                    <label>Severity</label>
                    <select class="sf-audit-select" wire:model.live="severity">
                        <option value="">All Severity</option>
                        <option value="info">Info</option>
                        <option value="success">Success</option>
                        <option value="warning">Warning</option>
                        <option value="danger">Danger</option>
                    </select>
                </div>

                <div class="sf-audit-field">
                    <label>Role</label>
                    <select class="sf-audit-select" wire:model.live="role">
                        <option value="">All Roles</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sf-audit-field">
                    <label>Department</label>
                    <input class="sf-audit-input" type="text" wire:model.live.debounce.500ms="department" placeholder="finance / hr / admin">
                </div>

                <div class="sf-audit-field">
                    <label>IP Address</label>
                    <input class="sf-audit-input" type="text" wire:model.live.debounce.500ms="ip" placeholder="127.0.0.1">
                </div>

                <div class="sf-audit-field">
                    <label>Date From</label>
                    <input class="sf-audit-input" type="date" wire:model.live="dateFrom">
                </div>

                <div class="sf-audit-field">
                    <label>Date To</label>
                    <input class="sf-audit-input" type="date" wire:model.live="dateTo">
                </div>
            </div>

            <div class="sf-audit-actions">
                <button class="sf-audit-btn-soft sf-audit-btn" type="button" wire:click="$set('dangerOnly', '{{ $dangerOnly === '1' ? '' : '1' }}')">
                    {{ $dangerOnly === '1' ? 'Show All Actions' : 'High Risk Actions Only' }}
                </button>

                <button class="sf-audit-btn-soft sf-audit-btn" type="button" onclick="window.print()">
                    Print Audit Report
                </button>

                <button class="sf-audit-btn-soft sf-audit-btn" type="button" wire:click="resetFilters">
                    Reset Filters
                </button>
            </div>
        </section>

        <section class="sf-audit-table-card">
            <table class="sf-audit-table">
                <colgroup>
                    <col class="sf-audit-col-time">
                    <col class="sf-audit-col-user">
                    <col class="sf-audit-col-action">
                    <col class="sf-audit-col-module">
                    <col class="sf-audit-col-description">
                </colgroup>

                <thead>
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module / Record</th>
                        <th>Description</th>

                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                        @php
                            $action = strtolower((string) $log->action);

                            $dangerActions = ['delete', 'disable', 'reject', 'decline', 'archive', 'void', 'cancel', 'failed', 'security'];
                            $warningActions = ['update', 'edit', 'approve', 'process', 'move', 'close'];
                            $successActions = ['create', 'created', 'enable', 'hire', 'upload', 'import_existing'];
                            $purpleActions = ['print', 'send', 'email', 'request_file', 'reset_password'];

                            $actionColorClass = 'sf-audit-row-info';
                            $chipClass = '';

                            if (in_array($action, $dangerActions, true) || $log->severity === 'danger') {
                                $actionColorClass = 'sf-audit-row-danger';
                                $chipClass = 'sf-audit-chip-danger';
                            } elseif (in_array($action, $warningActions, true) || $log->severity === 'warning') {
                                $actionColorClass = 'sf-audit-row-warning';
                                $chipClass = 'sf-audit-chip-warning';
                            } elseif (in_array($action, $successActions, true) || $log->severity === 'success') {
                                $actionColorClass = 'sf-audit-row-success';
                                $chipClass = 'sf-audit-chip-success';
                            } elseif (in_array($action, $purpleActions, true)) {
                                $actionColorClass = 'sf-audit-row-purple';
                                $chipClass = 'sf-audit-chip-purple';
                            }

                            $oldValues = $log->old_values ?: [];
                            $newValues = $log->new_values ?: [];
                            $changedFields = collect(array_unique(array_merge(array_keys($oldValues), array_keys($newValues))))
                                ->reject(fn ($field) => in_array($field, ['updated_at', 'created_at', 'password', 'remember_token'], true))
                                ->values();
                        @endphp

                        <tr class="sf-audit-row {{ $actionColorClass }}">
                            <td>
                                <div class="sf-audit-main">{{ optional($log->performed_at)->format('d M Y') ?: '-' }}</div>
                                <div class="sf-audit-muted">{{ optional($log->performed_at)->format('H:i:s') ?: '-' }}</div>
                            </td>

                            <td>
                                <div class="sf-audit-main">{{ $log->user_name ?: 'System' }}</div>
                                <div class="sf-audit-muted">{{ $log->user_email ?: '-' }}</div>
                                <div class="sf-audit-muted">
                                    {{ $log->user_role ?: '-' }}
                                    @if($log->user_department)
                                        · {{ $log->user_department }}
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="sf-audit-chip {{ $chipClass }}">{{ $log->actionLabel() }}</span>
                                <div class="sf-audit-muted" style="margin-top:6px;">{{ ucfirst($log->status ?: 'success') }}</div>
                            </td>

                            <td>
                                <div class="sf-audit-main">{{ $log->moduleLabel() }}</div>
                                <div class="sf-audit-muted">{{ $log->subject_title ?: '-' }}</div>
                                <div class="sf-audit-muted">Ref: {{ $log->subject_reference ?: '-' }}</div>
                            </td>

                            <td>
                                <div class="sf-audit-main">{{ $log->description ?: '-' }}</div>

                                <details class="sf-audit-diff">
                                    <summary>{{ $log->action === 'update' && $changedFields->isNotEmpty() ? 'View before / after changes' : 'View log details' }}</summary>

                                    @if($log->action === 'update' && $changedFields->isNotEmpty())
                                        <table class="sf-audit-diff-table">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Before</th>
                                                    <th>After</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach($changedFields as $field)
                                                    <tr>
                                                        <td>{{ str($field)->replace('_', ' ')->title() }}</td>
                                                        <td>
                                                            <span class="sf-audit-before">
                                                                {{ blank(data_get($oldValues, $field)) ? 'Empty / Not set' : data_get($oldValues, $field) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="sf-audit-after">
                                                                {{ blank(data_get($newValues, $field)) ? 'Empty / Not set' : data_get($newValues, $field) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif

                                    <div class="sf-audit-technical-details">
                                        <div>
                                            <strong>IP Address</strong>
                                            <span>{{ $log->ip_address ?: '-' }}</span>
                                        </div>

                                        <div>
                                            <strong>Method</strong>
                                            <span>{{ $log->method ?: '-' }}</span>
                                        </div>

                                        <div>
                                            <strong>Route</strong>
                                            <span>{{ $log->route_name ?: '-' }}</span>
                                        </div>

                                        <div>
                                            <strong>URL</strong>
                                            <span>{{ $log->url ?: '-' }}</span>
                                        </div>
                                    </div>

                                    @if(!empty($log->meta) && data_get($log->meta, 'source') !== 'model_observer')
                                        <div class="sf-audit-muted" style="padding: 0 12px 12px;">
                                            Meta: {{ str(json_encode($log->meta, JSON_UNESCAPED_UNICODE))->limit(160) }}
                                        </div>
                                    @endif
                                </details>
                            </td>


                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="sf-audit-empty">No audit logs found for the selected filters.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="sf-audit-pagination-clean">
                <div class="sf-audit-page-info">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                </div>

                @if($logs->hasPages())
                    <div class="sf-audit-page-buttons">
                        <button
                            type="button"
                            class="sf-audit-page-btn"
                            wire:click="previousPage"
                            @disabled($logs->onFirstPage())
                        >
                            Previous
                        </button>

                        @foreach($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                            @if($page <= 3 || $page === $logs->currentPage() || $page > $logs->lastPage() - 2)
                                <button
                                    type="button"
                                    class="sf-audit-page-btn {{ $logs->currentPage() === $page ? 'is-active' : '' }}"
                                    wire:click="gotoPage({{ $page }})"
                                >
                                    {{ $page }}
                                </button>
                            @elseif($page === 4 || $page === $logs->lastPage() - 2)
                                <span class="sf-audit-page-dots">…</span>
                            @endif
                        @endforeach

                        <button
                            type="button"
                            class="sf-audit-page-btn"
                            wire:click="nextPage"
                            @disabled(! $logs->hasMorePages())
                        >
                            Next
                        </button>
                    </div>
                @endif
            </div>
        </section>
    </div>

<style id="sf-audit-final-pagination-print-fix">
    .sf-audit-pagination-clean {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 18px;
        border-top: 1px solid rgba(148,163,184,.16);
        background: rgba(255,255,255,.72);
        flex-wrap: wrap;
    }

    .sf-audit-page-info {
        color: #64748b;
        font-size: 13px;
        font-weight: 850;
    }

    .sf-audit-page-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sf-audit-page-btn {
        min-width: 38px;
        height: 38px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.24);
        background: #fff;
        color: #0f172a;
        font-size: 12px;
        font-weight: 950;
        padding: 0 13px;
        cursor: pointer;
    }

    .sf-audit-page-btn:hover {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: rgba(37,99,235,.25);
    }

    .sf-audit-page-btn.is-active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
        box-shadow: 0 10px 20px rgba(37,99,235,.18);
    }

    .sf-audit-page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
        background: #f8fafc;
    }

    .sf-audit-page-dots {
        color: #94a3b8;
        font-weight: 950;
        padding: 0 4px;
    }

    .dark .sf-audit-pagination-clean {
        background: rgba(15,23,42,.72);
        border-top-color: rgba(148,163,184,.14);
    }

    .dark .sf-audit-page-info {
        color: #94a3b8;
    }

    .dark .sf-audit-page-btn {
        background: rgba(15,23,42,.90);
        color: #e2e8f0;
        border-color: rgba(148,163,184,.18);
    }

    .dark .sf-audit-page-btn.is-active {
        background: #2563eb;
        color: #fff;
    }

    @media print {
        body {
            background: #fff !important;
        }

        .fi-sidebar,
        .fi-topbar,
        .sf-global-footer,
        .sf-audit-filters,
        .sf-audit-pagination-clean,
        .sf-powered-footer,
        .sf-fixed-footer {
            display: none !important;
        }

        .sf-audit-shell {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .sf-audit-hero {
            box-shadow: none !important;
            border-radius: 0 !important;
            margin-bottom: 14px !important;
        }

        .sf-audit-table-card {
            box-shadow: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }

        .sf-audit-table {
            min-width: 0 !important;
            width: 100% !important;
            font-size: 11px !important;
        }

        .sf-audit-diff {
            display: none !important;
        }
    }
</style>

</x-filament-panels::page>
