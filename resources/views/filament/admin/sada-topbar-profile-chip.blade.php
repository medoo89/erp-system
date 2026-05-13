@php
    $user = auth()->user();

    $name = $user?->name ?: $user?->email ?: 'ERP User';
    $email = $user?->email ?: '';
    $role = $user?->erp_role ? str_replace('_', ' ', $user->erp_role) : 'ERP User';

    $avatarUrl = null;

    if ($user && ! empty($user->avatar_path)) {
        $avatarUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path);
    }

    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');

    $logoutRoute = \Illuminate\Support\Facades\Route::has('filament.admin.auth.logout')
        ? route('filament.admin.auth.logout')
        : url('/logout');
@endphp

<style>
    /*
        Hide default Filament small avatar/menu so only Sada profile chip remains.
        The custom chip below becomes the only profile menu.
    */
    .fi-topbar .fi-user-menu,
    .fi-topbar [class*="user-menu"]:not(.sf-topbar-profile-wrap):not(.sf-topbar-profile-wrap *),
    .fi-topbar [aria-label="User menu"] {
        display: none !important;
    }

    .sf-topbar-profile-wrap {
        position: relative;
        display: inline-flex;
        margin-inline-start: 10px;
        z-index: 80;
    }

    .sf-topbar-profile-trigger {
        border: 0;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        min-height: 56px;
        padding: 6px 18px 6px 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .92);
        border: 1px solid rgba(15, 23, 42, .10);
        box-shadow: 0 10px 28px rgba(15, 23, 42, .10);
        color: #0f172a;
        backdrop-filter: blur(14px);
        max-width: 290px;
    }

    .dark .sf-topbar-profile-trigger {
        background: rgba(15, 23, 42, .84);
        border-color: rgba(148, 163, 184, .18);
        color: #fff;
    }

    .sf-topbar-profile-avatar {
        width: 46px;
        height: 46px;
        border-radius: 999px;
        overflow: hidden;
        background: #0f172a;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 950;
        flex: 0 0 auto;
        border: 3px solid #2563eb;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .18);
    }

    .sf-topbar-profile-avatar.has-image {
        background: #fff;
        padding: 2px;
    }

    .sf-topbar-profile-avatar img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: contain;
        object-position: center;
        border-radius: 999px;
    }

    .sf-topbar-profile-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
        line-height: 1.08;
        text-align: left;
    }

    .sf-topbar-profile-name {
        font-size: 15px;
        font-weight: 950;
        letter-spacing: -.025em;
        max-width: 165px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sf-topbar-profile-role {
        margin-top: 5px;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .10em;
        text-transform: uppercase;
        color: #64748b;
        max-width: 165px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dark .sf-topbar-profile-role {
        color: #94a3b8;
    }

    .sf-topbar-chevron {
        color: #94a3b8;
        font-size: 20px;
        transition: transform .18s ease;
    }

    .sf-topbar-profile-wrap.is-open .sf-topbar-chevron {
        transform: rotate(180deg);
    }

    .sf-topbar-profile-menu {
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 320px;
        border-radius: 28px;
        background: rgba(255, 255, 255, .96);
        border: 1px solid rgba(15, 23, 42, .10);
        box-shadow: 0 26px 70px rgba(15, 23, 42, .18);
        backdrop-filter: blur(18px);
        overflow: hidden;
        display: none;
    }

    .dark .sf-topbar-profile-menu {
        background: rgba(15, 23, 42, .96);
        border-color: rgba(148, 163, 184, .18);
    }

    .sf-topbar-profile-wrap.is-open .sf-topbar-profile-menu {
        display: block;
    }

    .sf-menu-head {
        padding: 18px;
        display: flex;
        gap: 12px;
        align-items: center;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .dark .sf-menu-head {
        border-bottom-color: rgba(148, 163, 184, .16);
    }

    .sf-menu-name {
        font-size: 17px;
        font-weight: 950;
        color: #0f172a;
        letter-spacing: -.03em;
    }

    .dark .sf-menu-name {
        color: #fff;
    }

    .sf-menu-email {
        margin-top: 4px;
        font-size: 12px;
        font-weight: 750;
        color: #64748b;
        word-break: break-all;
    }

    .dark .sf-menu-email {
        color: #94a3b8;
    }

    .sf-theme-row {
        padding: 12px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .dark .sf-theme-row {
        border-bottom-color: rgba(148, 163, 184, .16);
    }

    .sf-theme-btn {
        min-height: 48px;
        border-radius: 999px;
        border: 1px solid rgba(15, 23, 42, .10);
        background: #fff;
        color: #0f172a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .sf-theme-btn:hover {
        background: #eef6ff;
    }

    .dark .sf-theme-btn {
        background: rgba(255, 255, 255, .06);
        color: #e2e8f0;
        border-color: rgba(255, 255, 255, .10);
    }

    .sf-menu-body {
        padding: 10px;
    }

    .sf-menu-link,
    .sf-menu-button {
        width: 100%;
        min-height: 48px;
        border-radius: 999px;
        padding: 0 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        border: 0;
        background: transparent;
        color: #0f172a;
        font-size: 15px;
        font-weight: 850;
        cursor: pointer;
        text-align: left;
    }

    .sf-menu-link:hover,
    .sf-menu-button:hover {
        background: #eef6ff;
    }

    .dark .sf-menu-link,
    .dark .sf-menu-button {
        color: #e2e8f0;
    }

    .dark .sf-menu-link:hover,
    .dark .sf-menu-button:hover {
        background: rgba(255, 255, 255, .08);
    }

    .sf-menu-button.danger {
        color: #dc2626;
    }

    .sf-menu-icon {
        color: #94a3b8;
        font-size: 23px;
    }

    .sf-menu-button.danger .sf-menu-icon {
        color: #dc2626;
    }

    @media (max-width: 760px) {
        .sf-topbar-chevron {
            display: none;
        }

        .sf-topbar-profile-trigger {
            padding: 5px;
            min-height: 50px;
        }

        .sf-topbar-profile-menu {
            right: -8px;
            width: min(320px, calc(100vw - 24px));
        }
    }
</style>

<div class="sf-topbar-profile-wrap" id="sfTopbarProfileWrap">
    <button type="button" class="sf-topbar-profile-trigger" onclick="window.sfToggleTopbarProfileMenu(event)">
        <span class="sf-topbar-profile-avatar {{ $avatarUrl ? 'has-image' : '' }}">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $name }}">
            @else
                {{ strtoupper($initials ?: 'U') }}
            @endif
        </span>

        <span class="sf-topbar-profile-text">
            <span class="sf-topbar-profile-name">{{ $name }}</span>
            <span class="sf-topbar-profile-role">{{ strtoupper($role) }}</span>
        </span>

        <span class="material-symbols-rounded sf-topbar-chevron">expand_more</span>
    </button>

    <div class="sf-topbar-profile-menu">
        <div class="sf-menu-head">
            <span class="sf-topbar-profile-avatar {{ $avatarUrl ? 'has-image' : '' }}">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $name }}">
                @else
                    {{ strtoupper($initials ?: 'U') }}
                @endif
            </span>

            <div style="min-width:0;">
                <div class="sf-menu-name">{{ $name }}</div>
                <div class="sf-menu-email">{{ $email }}</div>
            </div>
        </div>

        <div class="sf-theme-row">
            <button type="button" class="sf-theme-btn" onclick="window.sfSetThemeMode('light')" title="Light mode">
                <span class="material-symbols-rounded">light_mode</span>
            </button>

            <button type="button" class="sf-theme-btn" onclick="window.sfSetThemeMode('dark')" title="Dark mode">
                <span class="material-symbols-rounded">dark_mode</span>
            </button>

            <button type="button" class="sf-theme-btn" onclick="window.sfSetThemeMode('system')" title="System mode">
                <span class="material-symbols-rounded">desktop_windows</span>
            </button>
        </div>

        <div class="sf-menu-body">
            <a href="{{ route('admin.my-profile.edit') }}" class="sf-menu-link">
                <span class="material-symbols-rounded sf-menu-icon">account_circle</span>
                My Profile
            </a>

            <form method="POST" action="{{ $logoutRoute }}">
                @csrf
                <button type="submit" class="sf-menu-button danger">
                    <span class="material-symbols-rounded sf-menu-icon">logout</span>
                    Sign out
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    window.sfToggleTopbarProfileMenu = function (event) {
        event.preventDefault();
        event.stopPropagation();

        const wrap = document.getElementById('sfTopbarProfileWrap');
        if (!wrap) return;

        wrap.classList.toggle('is-open');
    };

    document.addEventListener('click', function (event) {
        const wrap = document.getElementById('sfTopbarProfileWrap');
        if (!wrap) return;

        if (!wrap.contains(event.target)) {
            wrap.classList.remove('is-open');
        }
    });

    window.sfSetThemeMode = function (mode) {
        try {
            localStorage.setItem('theme', mode);

            if (mode === 'dark') {
                document.documentElement.classList.add('dark');
            } else if (mode === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', prefersDark);
            }
        } catch (e) {}
    };
</script>


<style id="sf-real-topbar-avatar-name-only-final">
    .sf-topbar-profile-chip,
    .sf-topbar-profile-trigger {
        display: inline-flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-height: 58px !important;
        border-radius: 999px !important;
        padding: 7px 18px 7px 8px !important;
        background: rgba(255,255,255,.94) !important;
        border: 1px solid rgba(148,163,184,.24) !important;
        box-shadow: 0 14px 30px rgba(15,23,42,.10) !important;
        max-width: 320px !important;
        overflow: hidden !important;
    }

    .sf-topbar-profile-avatar {
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        border-radius: 999px !important;
        overflow: hidden !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #020617 !important;
        color: #fff !important;
        border: 3px solid #2563eb !important;
        box-shadow: 0 8px 18px rgba(37,99,235,.18) !important;
        font-size: 18px !important;
        font-weight: 950 !important;
    }

    .sf-topbar-profile-avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        object-position: center !important;
        display: block !important;
    }

    .sf-topbar-profile-name,
    .sf-topbar-profile-chip-name,
    .sf-topbar-profile-text strong,
    .sf-topbar-profile-trigger strong {
        display: inline-block !important;
        color: #0f172a !important;
        font-size: 16px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
        letter-spacing: -.02em !important;
        white-space: nowrap !important;
    }

    .sf-topbar-profile-role,
    .sf-topbar-profile-department,
    .sf-topbar-profile-subtitle,
    .sf-topbar-profile-text small,
    .sf-topbar-profile-trigger small {
        display: none !important;
    }

    .dark .sf-topbar-profile-chip,
    .dark .sf-topbar-profile-trigger {
        background: rgba(15,23,42,.90) !important;
        border-color: rgba(148,163,184,.18) !important;
    }

    .dark .sf-topbar-profile-name,
    .dark .sf-topbar-profile-chip-name,
    .dark .sf-topbar-profile-text strong,
    .dark .sf-topbar-profile-trigger strong {
        color: #f8fafc !important;
    }

    @media (max-width: 900px) {
        .sf-topbar-profile-name,
        .sf-topbar-profile-chip-name,
        .sf-topbar-profile-trigger strong {
            display: none !important;
        }

        .sf-topbar-profile-chip,
        .sf-topbar-profile-trigger {
            padding-right: 8px !important;
            max-width: 64px !important;
        }
    }
</style>

<style id="sf-force-real-profile-chip-visible-final">
    /*
     | Hide default Filament circular initials and show the real Sada chip.
     */
    .fi-topbar .fi-user-menu,
    .fi-topbar .fi-user-menu-trigger {
        display: none !important;
    }

    .sf-topbar-profile-wrap {
        display: inline-flex !important;
        align-items: center !important;
        position: relative !important;
        z-index: 50 !important;
    }

    .sf-topbar-profile-trigger,
    .sf-topbar-profile-chip {
        display: inline-flex !important;
        align-items: center !important;
        gap: 12px !important;
        min-height: 58px !important;
        border-radius: 999px !important;
        padding: 7px 18px 7px 8px !important;
        background: rgba(255,255,255,.96) !important;
        border: 1px solid rgba(148,163,184,.24) !important;
        box-shadow: 0 14px 30px rgba(15,23,42,.10) !important;
        color: #0f172a !important;
        text-decoration: none !important;
    }

    .sf-topbar-profile-avatar {
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        border-radius: 999px !important;
        overflow: hidden !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #020617 !important;
        color: #fff !important;
        border: 3px solid #2563eb !important;
        box-shadow: 0 8px 18px rgba(37,99,235,.18) !important;
        font-size: 18px !important;
        font-weight: 950 !important;
    }

    .sf-topbar-profile-avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        object-position: center !important;
        display: block !important;
    }

    .sf-topbar-profile-name,
    .sf-topbar-profile-text strong,
    .sf-topbar-profile-trigger strong {
        display: inline-block !important;
        color: #0f172a !important;
        font-size: 16px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
        white-space: nowrap !important;
    }

    .sf-topbar-profile-role,
    .sf-topbar-profile-subtitle,
    .sf-topbar-profile-department,
    .sf-topbar-profile-text small {
        display: none !important;
    }

    .dark .sf-topbar-profile-trigger,
    .dark .sf-topbar-profile-chip {
        background: rgba(15,23,42,.92) !important;
        border-color: rgba(148,163,184,.18) !important;
    }

    .dark .sf-topbar-profile-name,
    .dark .sf-topbar-profile-text strong,
    .dark .sf-topbar-profile-trigger strong {
        color: #f8fafc !important;
    }

    @media (max-width: 900px) {
        .sf-topbar-profile-name,
        .sf-topbar-profile-text {
            display: none !important;
        }

        .sf-topbar-profile-trigger,
        .sf-topbar-profile-chip {
            padding-right: 8px !important;
        }
    }
</style>
