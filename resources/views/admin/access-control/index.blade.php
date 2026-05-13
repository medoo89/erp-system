<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ERP Access Control</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.12), transparent 30%),
                radial-gradient(circle at bottom left, rgba(36,89,211,.10), transparent 26%),
                #eef3f8;
            color: #0f172a;
            padding: 26px;
        }

        .sf-shell {
            max-width: 1660px;
            margin: 0 auto;
        }

        .sf-hero {
            border-radius: 34px;
            padding: 28px;
            background:
                radial-gradient(circle at 92% 10%, rgba(255,255,255,.12), transparent 28%),
                linear-gradient(135deg, #18344d 0%, #234d6f 55%, #2f8a8d 100%);
            color: #fff;
            box-shadow: 0 24px 70px rgba(15,23,42,.18);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }

        .sf-kicker {
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
            opacity: .82;
            margin-bottom: 10px;
        }

        .sf-title {
            font-size: 42px;
            line-height: 1;
            font-weight: 950;
            letter-spacing: -.055em;
            margin: 0;
        }

        .sf-sub {
            margin-top: 12px;
            font-size: 14px;
            line-height: 1.65;
            opacity: .86;
            font-weight: 750;
            max-width: 900px;
        }

        .sf-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.22);
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 950;
        }

        .sf-alert {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 20px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid rgba(16,185,129,.25);
            font-weight: 900;
        }

        .sf-alert--danger {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .sf-create-card {
            margin-top: 20px;
            border-radius: 30px;
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(215,226,229,.95);
            box-shadow: 0 18px 48px rgba(15,23,42,.07);
            overflow: hidden;
        }

        .sf-card-head {
            padding: 18px 20px;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 34%),
                #f8fbff;
            border-bottom: 1px solid #e4ecef;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .sf-card-title {
            font-size: 22px;
            line-height: 1.15;
            font-weight: 950;
            letter-spacing: -.035em;
            color: #0f172a;
        }

        .sf-card-meta {
            margin-top: 6px;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .sf-body {
            padding: 20px;
        }

        .sf-create-grid {
            display: grid;
            grid-template-columns: 1.15fr 1.25fr .9fr .9fr .9fr auto;
            gap: 12px;
            align-items: end;
        }

        .sf-field label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .sf-input,
        .sf-select {
            width: 100%;
            min-height: 44px;
            border-radius: 15px;
            border: 1px solid #d7e2e5;
            background: #fff;
            color: #0f172a;
            padding: 0 12px;
            font-weight: 800;
            outline: none;
        }

        .sf-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 12px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.08);
            color: #334155;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .sf-btn {
            border: 0;
            min-height: 44px;
            border-radius: 999px;
            padding: 0 18px;
            background: linear-gradient(90deg, #2563eb, #4f8cff);
            color: #fff;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 14px 30px rgba(37,99,235,.18);
        }

        .sf-btn-soft {
            background: #fff;
            color: #0f172a;
            border: 1px solid #d7e2e5;
            box-shadow: none;
        }

        .sf-access-layout {
            margin-top: 22px;
            display: grid;
            grid-template-columns: 310px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .sf-users-panel {
            position: sticky;
            top: 18px;
            border-radius: 30px;
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(215,226,229,.95);
            box-shadow: 0 18px 48px rgba(15,23,42,.07);
            overflow: hidden;
        }

        .sf-users-panel-head {
            padding: 18px;
            border-bottom: 1px solid #e4ecef;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 34%),
                #f8fbff;
        }

        .sf-users-title {
            font-size: 20px;
            font-weight: 950;
            letter-spacing: -.035em;
            color: #0f172a;
        }

        .sf-users-sub {
            margin-top: 6px;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
        }

        .sf-user-tabs {
            display: grid;
            gap: 8px;
            padding: 12px;
            max-height: 72vh;
            overflow: auto;
        }

        .sf-user-tab {
            width: 100%;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            border-radius: 20px;
            padding: 12px;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            text-align: left;
            cursor: pointer;
            transition: .16s ease;
        }

        .sf-user-tab:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(15,23,42,.06);
        }

        .sf-user-tab.is-active {
            background: linear-gradient(90deg, rgba(37,99,235,.10), rgba(76,167,168,.08));
            border-color: rgba(37,99,235,.28);
            box-shadow: 0 14px 32px rgba(37,99,235,.10);
        }

        .sf-avatar {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: #0f172a;
            color: #fff;
            font-size: 13px;
            font-weight: 950;
            letter-spacing: .04em;
        }

        .sf-user-tab-name {
            color: #0f172a;
            font-size: 13px;
            font-weight: 950;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sf-user-tab-email {
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
            font-weight: 750;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sf-user-tab-role {
            margin-top: 6px;
            display: inline-flex;
            min-height: 24px;
            align-items: center;
            border-radius: 999px;
            padding: 0 8px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 9px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .sf-matrix-card {
            display: none;
            border-radius: 30px;
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(215,226,229,.95);
            box-shadow: 0 18px 48px rgba(15,23,42,.07);
            overflow: hidden;
        }

        .sf-matrix-card.is-active {
            display: block;
        }

        .sf-matrix-head {
            padding: 20px 22px;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.12), transparent 34%),
                linear-gradient(180deg,#f8fbff 0%,#ffffff 100%);
            border-bottom: 1px solid #e4ecef;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .sf-matrix-name {
            color: #0f172a;
            font-size: 26px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.045em;
        }

        .sf-matrix-email {
            margin-top: 7px;
            color: #64748b;
            font-size: 13px;
            font-weight: 750;
        }

        .sf-role-chip {
            display: inline-flex;
            min-height: 36px;
            align-items: center;
            border-radius: 999px;
            padding: 0 12px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid rgba(59,130,246,.20);
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .sf-profile-strip {
            padding: 18px 22px;
            border-bottom: 1px solid #e4ecef;
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            background: rgba(248,250,252,.72);
        }

        .sf-table-zone {
            padding: 0 0 18px;
        }

        .sf-matrix-scroll {
            overflow: auto;
            border-bottom: 1px solid #e4ecef;
        }

        .sf-matrix-table {
            width: 100%;
            min-width: 1280px;
            border-collapse: collapse;
        }

        .sf-matrix-table th,
        .sf-matrix-table td {
            border-bottom: 1px solid #e8eef5;
            border-right: 1px solid #eef2f7;
            padding: 12px 12px;
            vertical-align: middle;
        }

        .sf-matrix-table th {
            background: #f8fbff;
            color: #475569;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
            text-align: center;
            white-space: nowrap;
        }

        .sf-matrix-table th:first-child,
        .sf-matrix-table td:first-child {
            position: sticky;
            left: 0;
            z-index: 2;
            background: #fff;
            text-align: left;
            min-width: 260px;
            border-right: 2px solid #dde6ef;
        }

        .sf-matrix-table th:first-child {
            background: #f8fbff;
            z-index: 4;
        }

        .sf-module-label {
            color: #0f172a;
            font-size: 14px;
            line-height: 1.25;
            font-weight: 950;
        }

        .sf-module-group {
            margin-top: 4px;
            color: #64748b;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sf-perm-cell {
            text-align: center;
            min-width: 120px;
        }

        .sf-switch {
            position: relative;
            width: 54px;
            height: 30px;
            display: inline-block;
        }

        .sf-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .sf-slider {
            position: absolute;
            inset: 0;
            cursor: pointer;
            background: #e5e7eb;
            border: 1px solid #d5dde7;
            border-radius: 999px;
            transition: .18s ease;
        }

        .sf-slider:before {
            content: "";
            position: absolute;
            height: 22px;
            width: 22px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: .18s ease;
            box-shadow: 0 2px 6px rgba(15,23,42,.20);
        }

        .sf-switch input:checked + .sf-slider {
            background: #2563eb;
            border-color: #2563eb;
        }

        .sf-switch input:checked + .sf-slider:before {
            transform: translateX(24px);
        }

        .sf-matrix-footer {
            padding: 16px 22px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            background: #fff;
        }

        .sf-hint {
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
            line-height: 1.5;
        }

        .sf-bulk-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        @media(max-width: 1180px) {
            .sf-access-layout {
                grid-template-columns: 1fr;
            }

            .sf-users-panel {
                position: static;
            }

            .sf-user-tabs {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                max-height: none;
            }

            .sf-create-grid,
            .sf-profile-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width: 720px) {
            body { padding: 16px; }
            .sf-title { font-size: 32px; }

            .sf-user-tabs,
            .sf-create-grid,
            .sf-profile-strip {
                grid-template-columns: 1fr;
            }
        }
        .sf-user-action-bar {
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .sf-user-action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .sf-matrix-scroll {
            max-height: 70vh;
            overflow: auto;
            border-bottom: 1px solid #e4ecef;
        }

        .sf-matrix-table thead th {
            position: sticky;
            top: 0;
            z-index: 8;
            box-shadow: 0 1px 0 #e4ecef;
        }

        .sf-matrix-table thead th:first-child {
            z-index: 12;
        }

        .sf-matrix-table th:first-child,
        .sf-matrix-table td:first-child {
            position: sticky;
            left: 0;
        }

        .sf-matrix-table tbody td:first-child {
            z-index: 6;
            background: #fff;
        }

        .sf-btn-danger {
            background: #ef4444 !important;
            box-shadow: 0 14px 30px rgba(239,68,68,.18) !important;
            color: #fff !important;
            border: none !important;
        }

    </style>

<style id="sf-access-control-ui-polish">
    .sf-user-filter-box {
        margin-top: 14px;
        display: grid;
        gap: 10px;
    }

    .sf-user-search {
        min-height: 42px;
        border-radius: 16px;
    }

    .sf-user-filter-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .sf-user-tab {
        transition: transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease;
    }

    .sf-user-tab:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 34px rgba(15,23,42,.08);
    }

    .sf-user-tab.is-hidden {
        display: none !important;
    }

    .sf-check-pill {
        justify-content: center;
        min-height: 48px;
        padding: 0 14px;
        border-radius: 999px;
        background:
            linear-gradient(180deg, rgba(255,255,255,.95), rgba(248,250,252,.86));
        border: 1px solid rgba(15,23,42,.10);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        font-weight: 900;
        color: #334155;
    }

    .sf-check-pill input {
        width: 18px;
        height: 18px;
        accent-color: #2563eb;
    }

    .sf-create-card {
        position: relative;
        overflow: hidden;
    }

    .sf-create-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.13), transparent 34%),
            radial-gradient(circle at bottom left, rgba(37,99,235,.08), transparent 30%);
    }

    .sf-create-card > * {
        position: relative;
        z-index: 1;
    }

    .sf-create-grid {
        align-items: end;
    }

    .sf-user-action-bar {
        position: sticky;
        top: 0;
        z-index: 20;
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        background:
            linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.82));
        border-bottom: 1px solid rgba(15,23,42,.08);
    }

    .dark .sf-user-action-bar {
        background:
            linear-gradient(180deg, rgba(15,23,42,.92), rgba(15,23,42,.80));
        border-bottom-color: rgba(255,255,255,.10);
    }

    .sf-profile-strip {
        position: sticky;
        top: 78px;
        z-index: 18;
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        background:
            linear-gradient(180deg, rgba(248,250,252,.94), rgba(248,250,252,.84));
        border-bottom: 1px solid rgba(15,23,42,.08);
    }

    .dark .sf-profile-strip {
        background:
            linear-gradient(180deg, rgba(15,23,42,.88), rgba(15,23,42,.76));
        border-bottom-color: rgba(255,255,255,.10);
    }

    @media (max-width: 1100px) {
        .sf-profile-strip,
        .sf-user-action-bar {
            position: static;
        }
    }

        /* FIX: Access Control user avatar/logo containment */
        .sf-user-tab {
            overflow: hidden !important;
        }

        .sf-user-tab .sf-avatar {
            width: 46px !important;
            height: 46px !important;
            min-width: 46px !important;
            max-width: 46px !important;
            min-height: 46px !important;
            max-height: 46px !important;
            border-radius: 16px !important;
            overflow: hidden !important;
            position: relative !important;
            background: #0f172a !important;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.12) !important;
        }

        .sf-user-tab .sf-avatar.has-image {
            background: #ffffff !important;
            border: 1px solid rgba(15,23,42,.08) !important;
            padding: 4px !important;
        }

        .sf-user-tab .sf-avatar img {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
            border-radius: 12px !important;
        }

</style>

</head>
<body>
@php
    $rolePermissionPresets = collect(array_keys($roles ?? []))
        ->mapWithKeys(fn ($role) => [$role => \App\Models\User::defaultErpPermissionsForRole($role)])
        ->toArray();
@endphp

<main class="sf-shell">
    <section class="sf-hero">
        <div>
            <div class="sf-kicker">Admin Settings</div>
            <h1 class="sf-title">ERP Access Control</h1>
            <div class="sf-sub">
                Manage users, roles, page access, and action-level permissions using a clean matrix like a professional CRM access module.
            </div>
        </div>

        <a class="sf-back" href="{{ url('/admin') }}">← Back to ERP</a>
    </section>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof showAccessToast === 'function') {
                    showAccessToast(@json(session('success')));
                }
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof showAccessToast === 'function') {
                    showAccessToast(@json($errors->first()));
                }
            });
        </script>
    @endif

    <form class="sf-create-card" method="POST" action="{{ route('admin.erp-access-control.store') }}">
        @csrf

        <div class="sf-card-head">
            <div>
                <div class="sf-card-title">Create ERP User</div>
                <div class="sf-card-meta">Create a new ERP login user, assign role, department, and default permissions.</div>
            </div>
        </div>

        <div class="sf-body">
            <div class="sf-create-grid">
                <div class="sf-field">
                    <label>Name</label>
                    <input class="sf-input" type="text" name="name" required>
                </div>

                <div class="sf-field">
                    <label>Email</label>
                    <input class="sf-input" type="email" name="email" required>
                </div>

                <div class="sf-field">
                    <label>Phone</label>
                    <input class="sf-input" type="text" name="phone" placeholder="+218 ...">
                </div>

                <div class="sf-field">
                    <label>Password</label>
                    <input class="sf-input" type="text" name="password" value="password123" required>
                </div>

                <div class="sf-field">
                    <label>Role</label>
                    <select class="sf-select" name="erp_role" onchange="syncCreateUserDepartment(this)">
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sf-field">
                    <label>Department</label>
                    <input class="sf-input" type="text" name="erp_department" placeholder="finance / hr / recruitment">
                </div>

                <div class="sf-field">
                    <label>ERP Login</label>
                    <label class="sf-check sf-check-pill">
                        <input type="checkbox" name="is_admin" value="1" checked>
                        <span>Can access ERP system</span>
                    </label>
                </div>
            </div>

            <div style="margin-top:14px;display:flex;justify-content:flex-end;">
                <button class="sf-btn" type="submit">Create User</button>
            </div>
        </div>
    </form>

    @php
    $accessDepartments = $users
        ->map(fn ($user) => trim((string) ($user->erp_department ?: 'Unassigned')))
        ->filter()
        ->unique()
        ->sort()
        ->values();

    $accessRoles = $users
        ->map(fn ($user) => trim((string) ($user->erp_role ?: 'no_role')))
        ->filter()
        ->unique()
        ->sort()
        ->values();
@endphp

<section class="sf-access-layout">
        <aside class="sf-users-panel">
            <div class="sf-users-panel-head">
                <div class="sf-users-title">Users</div>
                <div class="sf-users-sub">Select a user to manage detailed permissions.</div>

                <div class="sf-user-filter-box">
                    <input
                        class="sf-input sf-user-search"
                        type="text"
                        placeholder="Search user..."
                        oninput="filterAccessUsers()"
                    >

                    <div class="sf-user-filter-grid">
                        <select class="sf-select" id="sfDepartmentFilter" onchange="filterAccessUsers()">
                            <option value="">All Departments</option>
                            @foreach($accessDepartments as $department)
                                <option value="{{ strtolower($department) }}">{{ $department }}</option>
                            @endforeach
                        </select>

                        <select class="sf-select" id="sfRoleFilter" onchange="filterAccessUsers()">
                            <option value="">All Roles</option>
                            @foreach($accessRoles as $role)
                                <option value="{{ strtolower($role) }}">{{ \App\Models\User::erpRoleOptions()[$role] ?? $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="sf-user-tabs">
                @foreach($users as $index => $user)
                    @php
                        $initials = collect(explode(' ', trim($user->name ?: $user->email)))
                            ->filter()
                            ->map(fn ($part) => mb_substr($part, 0, 1))
                            ->take(2)
                            ->implode('');
                    @endphp

                    <button
                        type="button"
                        class="sf-user-tab {{ $index === 0 ? 'is-active' : '' }}"
                        data-user-target="user-{{ $user->id }}"
                        data-user-name="{{ strtolower($user->name ?: '') }}"
                        data-user-email="{{ strtolower($user->email ?: '') }}"
                        data-user-role="{{ strtolower($user->erp_role ?: 'no_role') }}"
                        data-user-department="{{ strtolower($user->erp_department ?: 'unassigned') }}"
                        onclick="selectAccessUser(this)"
                    >
                        <div class="sf-avatar {{ ! empty($user->avatar_path) ? 'has-image' : '' }}">
                            @if(! empty($user->avatar_path))
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) }}" alt="{{ $user->name ?: $user->email }}">
                            @else
                                {{ strtoupper($initials ?: 'U') }}
                            @endif
                        </div>
                        <div style="min-width:0;">
                            <div class="sf-user-tab-name">{{ $user->name ?: 'Unnamed User' }}</div>
                            <div class="sf-user-tab-email">{{ $user->email }}</div>
                            <div class="sf-user-tab-role">{{ $user->erp_role ?: 'No Role' }}</div>
                        </div>
                    </button>
                @endforeach
            </div>
        </aside>

        <section>
            @foreach($users as $index => $user)
                @php
                    $userPermissions = $user->erpPermissions();
                @endphp

                <form
                    id="delete-user-{{ $user->id }}"
                    method="POST"
                    action="{{ route('admin.erp-access-control.destroy', $user) }}"
                    style="display:none;"
                >
                    @csrf
                    @method('DELETE')
                </form>

                <form
                    id="user-{{ $user->id }}"
                    class="sf-matrix-card {{ $index === 0 ? 'is-active' : '' }}"
                    method="POST"
                    action="{{ route('admin.erp-access-control.update', $user) }}"
                    data-access-user-form="1"
                >
                    @csrf

                    <div class="sf-matrix-head">
                        <div>
                            <div class="sf-matrix-name">{{ $user->name ?: 'Unnamed User' }}</div>
                            <div class="sf-matrix-email">{{ $user->email }}</div>
                        </div>

                        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                            <span class="sf-role-chip">{{ $user->erp_role ?: 'No Role' }}</span>
                            <span class="sf-role-chip sf-enabled-count" style="background:#ecfdf5;color:#047857;border-color:rgba(16,185,129,.25);">
                                Enabled:
                                {{
                                    collect($userPermissions)
                                        ->flatMap(fn ($actions) => is_array($actions) ? $actions : [])
                                        ->filter()
                                        ->count()
                                }}
                            </span>
                        </div>
                    </div>

                    {{-- Top permissions action bar --}}
                    <div class="sf-user-action-bar">
                        <div class="sf-hint">
                            Select page/action access for this user. Role and department are labels only; access depends on these toggles.
                        </div>

                        <div class="sf-user-action-buttons">
                            <button type="button" class="sf-btn sf-btn-soft" onclick="toggleCurrentMatrix(this, true)">Allow All</button>
                            <button type="button" class="sf-btn sf-btn-soft" onclick="toggleCurrentMatrix(this, false)">Clear All</button>
                            <button class="sf-btn" type="submit">Save Rules for {{ $user->name ?: $user->email }}</button>

                            @if((int) auth()->id() !== (int) $user->id)
                                <button
                                    type="submit"
                                    form="delete-user-{{ $user->id }}"
                                    class="sf-btn sf-btn-danger"
                                    onclick="return confirm('Delete this user permanently? This cannot be undone.')"
                                >
                                    Delete User
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="sf-profile-strip">
                        <div class="sf-field">
                            <label>Name</label>
                            <input class="sf-input" type="text" name="name" value="{{ $user->name }}">
                        </div>

                        <div class="sf-field">
                            <label>Role</label>
                            <select class="sf-select js-role-preset-select" name="erp_role" onchange="applyRolePresetToForm(this)">
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}" @selected($user->erp_role === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sf-field">
                            <label>Department</label>
                            <input class="sf-input" type="text" name="erp_department" value="{{ $user->erp_department }}">
                        </div>

                        <div class="sf-field">
                            <label>Phone</label>
                            <input class="sf-input" type="text" name="phone" value="{{ $user->phone ?? '' }}" placeholder="+218 ...">
                        </div>

                        <div class="sf-field">
                            <label>New Password</label>
                            <input class="sf-input" type="text" name="new_password" placeholder="Leave empty">
                        </div>

                        <div class="sf-field">
                            <label>ERP Login</label>
                            <label class="sf-check sf-check-pill">
                                <input type="checkbox" name="is_admin" value="1" @checked($user->is_admin)>
                                <span>Can access ERP system</span>
                            </label>
                        </div>
                    </div>

                    <div class="sf-table-zone">
                        <div class="sf-matrix-scroll">
                            <table class="sf-matrix-table">
                                <thead>
                                    <tr>
                                        <th>Module / Page</th>
                                        <th>View</th>
                                        <th>Create</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                        <th>Approve</th>
                                        <th>Send</th>
                                        <th>Upload</th>
                                        <th>Process</th>
                                        <th>Print</th>
                                        <th>Manage</th>
                                        <th>Other Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($registry as $areaKey => $module)
                                        @php
                                            $actions = $module['actions'] ?? [];
                                            $standardColumns = [
                                                'view',
                                                'create',
                                                'edit',
                                                'delete',
                                                'approve',
                                                'send_email',
                                                'upload_file',
                                                'process_cash',
                                                'print',
                                                'manage_form',
                                            ];

                                            $otherActions = collect($actions)
                                                ->reject(fn ($label, $key) => in_array($key, $standardColumns, true))
                                                ->all();
                                        @endphp

                                        <tr>
                                            <td>
                                                <div class="sf-module-label">{{ $module['label'] ?? $areaKey }}</div>
                                                <div class="sf-module-group">{{ $module['group'] ?? 'General' }}</div>
                                            </td>

                                            @foreach($standardColumns as $columnAction)
                                                @php
                                                    $exists = array_key_exists($columnAction, $actions);
                                                @endphp

                                                <td class="sf-perm-cell">
                                                    @if($exists)
                                                        <label class="sf-switch">
                                                            <input
                                                                type="checkbox"
                                                                name="permissions[{{ $areaKey }}][{{ $columnAction }}]"
                                                                value="1"
                                                                @checked((bool) data_get($userPermissions, "{$areaKey}.{$columnAction}", false))
                                                            >
                                                            <span class="sf-slider"></span>
                                                        </label>
                                                    @else
                                                        <span style="color:#cbd5e1;font-weight:900;">—</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            <td class="sf-perm-cell" style="min-width:280px;text-align:left;">
                                                @if(count($otherActions))
                                                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                                        @foreach($otherActions as $actionKey => $actionLabel)
                                                            <label class="sf-check" style="min-height:34px;">
                                                                <input
                                                                    type="checkbox"
                                                                    name="permissions[{{ $areaKey }}][{{ $actionKey }}]"
                                                                    value="1"
                                                                    @checked((bool) data_get($userPermissions, "{$areaKey}.{$actionKey}", false))
                                                                >
                                                                {{ $actionLabel }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span style="color:#cbd5e1;font-weight:900;">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="sf-matrix-footer">
                            <div class="sf-hint">
                                Tip: View controls page access. Other toggles control buttons and workflow actions.
                            </div>
                        </div>
                    </div>
                </form>
            @endforeach
        </section>
    </section>
</main>

<script>
    function selectAccessUser(button) {
        document.querySelectorAll('.sf-user-tab').forEach(item => item.classList.remove('is-active'));
        document.querySelectorAll('.sf-matrix-card').forEach(item => item.classList.remove('is-active'));

        button.classList.add('is-active');

        const target = button.getAttribute('data-user-target');
        const form = document.getElementById(target);

        if (form) {
            form.classList.add('is-active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function toggleCurrentMatrix(button, state) {
        const form = button.closest('.sf-matrix-card');
        if (!form) return;

        form.querySelectorAll('input[type="checkbox"][name^="permissions"]').forEach(input => {
            input.checked = state;
        });
    }
</script>

<script>
    window.SadaRolePermissionPresets = @json($rolePermissionPresets ?? []);

    function syncCreateUserDepartment(select) {
        const form = select.closest('form');
        if (!form) return;

        const deptInput = form.querySelector('input[name="erp_department"]');
        if (!deptInput) return;

        const role = String(select.value || '').toLowerCase();

        if (!deptInput.value.trim()) {
            if (role === 'finance') deptInput.value = 'finance';
            if (role === 'hr' || role === 'recruitment') deptInput.value = 'hr / recruitment';
            if (role === 'viewer') deptInput.value = 'viewer';
            if (role === 'super_admin') deptInput.value = 'admin';
        }
    }

    function applyRolePresetToForm(select) {
        const form = select.closest('form[data-access-user-form="1"], form.sf-matrix-card');
        if (!form) return;

        const role = select.value;
        const preset = window.SadaRolePermissionPresets?.[role];

        if (!preset) return;

        const ok = confirm('Apply default permissions for role: ' + role + '?\\n\\nYou can still edit toggles manually after applying.');
        if (!ok) return;

        const checkboxes = form.querySelectorAll('input[type="checkbox"][name^="permissions["]');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = false;
        });

        Object.entries(preset).forEach(([module, actions]) => {
            Object.entries(actions || {}).forEach(([action, allowed]) => {
                const name = `permissions[${module}][${action}]`;
                const checkbox = form.querySelector(`input[type="checkbox"][name="${CSS.escape(name)}"]`);
                if (checkbox) {
                    checkbox.checked = !!allowed;
                }
            });
        });

        const deptInput = form.querySelector('input[name="erp_department"]');
        if (deptInput && !deptInput.value.trim()) {
            const lower = String(role || '').toLowerCase();
            if (lower === 'finance') deptInput.value = 'finance';
            if (lower === 'hr' || lower === 'recruitment') deptInput.value = 'hr / recruitment';
            if (lower === 'viewer') deptInput.value = 'viewer';
            if (lower === 'super_admin') deptInput.value = 'admin';
        }

        const activeCard = form.closest('.sf-matrix-card') || form;
        const enabledCounter = activeCard.querySelector('.sf-enabled-count');
        if (enabledCounter) {
            const enabled = activeCard.querySelectorAll('input[type="checkbox"][name^="permissions["]:checked').length;
            enabledCounter.innerHTML = 'Enabled: ' + enabled;
        }

        if (typeof showAccessToast === 'function') {
            showAccessToast('Default role permissions applied. Review and click Save Rules.');
        }
    }

    function showAccessToast(message) {
        let toast = document.getElementById('sfAccessToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'sfAccessToast';
            toast.style.cssText = `
                position: fixed;
                right: 24px;
                bottom: 24px;
                z-index: 99999;
                max-width: 420px;
                padding: 14px 18px;
                border-radius: 18px;
                background: #0f172a;
                color: #fff;
                font-weight: 850;
                box-shadow: 0 20px 60px rgba(15,23,42,.25);
                transform: translateY(20px);
                opacity: 0;
                transition: .22s ease;
            `;
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        clearTimeout(window.sfAccessToastTimer);
        window.sfAccessToastTimer = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
        }, 3200);
    }
</script>


<script id="sf-access-user-filter-js">
    function filterAccessUsers() {
        const searchInput = document.querySelector('.sf-user-search');
        const departmentSelect = document.getElementById('sfDepartmentFilter');
        const roleSelect = document.getElementById('sfRoleFilter');

        const search = String(searchInput?.value || '').toLowerCase().trim();
        const department = String(departmentSelect?.value || '').toLowerCase().trim();
        const role = String(roleSelect?.value || '').toLowerCase().trim();

        const tabs = Array.from(document.querySelectorAll('.sf-user-tab'));
        let firstVisible = null;

        tabs.forEach((tab) => {
            const haystack = [
                tab.dataset.userName || '',
                tab.dataset.userEmail || '',
                tab.dataset.userRole || '',
                tab.dataset.userDepartment || '',
            ].join(' ').toLowerCase();

            const matchSearch = !search || haystack.includes(search);
            const matchDepartment = !department || tab.dataset.userDepartment === department;
            const matchRole = !role || tab.dataset.userRole === role;

            const visible = matchSearch && matchDepartment && matchRole;
            tab.classList.toggle('is-hidden', !visible);

            if (visible && !firstVisible) {
                firstVisible = tab;
            }
        });

        const active = document.querySelector('.sf-user-tab.is-active:not(.is-hidden)');

        if (!active && firstVisible) {
            selectAccessUser(firstVisible);
        }
    }

    function refreshEnabledCounters() {
        document.querySelectorAll('.sf-matrix-card').forEach((card) => {
            const counter = card.querySelector('.sf-enabled-count');
            if (!counter) return;

            const enabled = card.querySelectorAll('input[type="checkbox"][name^="permissions["]:checked').length;
            counter.innerHTML = 'Enabled: ' + enabled;
        });
    }

    document.addEventListener('change', function (event) {
        if (event.target && event.target.matches('input[type="checkbox"][name^="permissions["]')) {
            refreshEnabledCounters();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        refreshEnabledCounters();
    });
</script>

</body>
</html>


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

