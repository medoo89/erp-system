<?php

namespace App\Providers\Filament;

use App\Http\Middleware\AuditAdminRequest;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Illuminate\Support\HtmlString;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): HtmlString => new HtmlString('<link rel="stylesheet" href="' . asset('css/sf-md3-admin.css') . '?v=' . @filemtime(public_path('css/sf-md3-admin.css')) . '">')
            )
            ->default()
            ->id('admin')
            ->path('admin')
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => '<style id="sf-sidebar-material-icons-final-fix">    .fi-sidebar .material-symbols-rounded,    .fi-sidebar [class*="material-symbols"],    .fi-sidebar .sf-md3-icon,    .fi-sidebar .sf-sidebar-icon {        font-family: "Material Symbols Rounded" !important;        font-weight: 400 !important;        font-style: normal !important;        font-size: 21px !important;        line-height: 1 !important;        letter-spacing: normal !important;        text-transform: none !important;        display: inline-flex !important;        align-items: center !important;        justify-content: center !important;        white-space: nowrap !important;        word-wrap: normal !important;        direction: ltr !important;        -webkit-font-feature-settings: "liga" !important;        -webkit-font-smoothing: antialiased !important;        font-feature-settings: "liga" !important;        width: 22px !important;        min-width: 22px !important;        height: 22px !important;        overflow: hidden !important;    }    .fi-sidebar a,    .fi-sidebar button {        overflow: hidden !important;    }    .fi-sidebar .fi-sidebar-item-label,    .fi-sidebar span:not(.material-symbols-rounded):not([class*="material-symbols"]) {        font-family: inherit !important;        letter-spacing: normal !important;    }    .fi-sidebar .fi-sidebar-item {        min-height: 44px !important;    }</style>'
            )
            ->renderHook(
                'panels::head.end',
                fn (): HtmlString => new HtmlString('<link rel="stylesheet" href="/sada-admin-emergency.css?v=' . time() . '">')
            )
            ->renderHook(
                'panels::head.end',
                fn (): string => view('filament.admin.sada-sidebar-material-design')->render(),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(
                    '<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
                     <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>'
                )
            )


            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn (): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString('
                    <div class="sf-cancello-fixed-footer">
                        <div class="sf-cancello-fixed-footer-inner">
                            <span class="sf-cancello-powered">Powered by</span>

                            <img
                                src="/images/cancello-studio-logo.png"
                                alt="Cancello Studio"
                                class="sf-cancello-fixed-logo"
                                onerror="this.outerHTML=\'<span class=&quot;sf-cancello-fixed-logo-fallback&quot;>CS</span>\'"
                            >

                            <span class="sf-cancello-dot">•</span>
                            <span>© 2026</span>
                            <span class="sf-cancello-dot">•</span>
                            <span>ERP Version 1.2</span>
                        </div>
                    </div>

                    <style id="sf-cancello-admin-fixed-footer-final">
                        :root {
                            --sf-admin-sidebar-width: 18rem;
                            --sf-admin-footer-height: 66px;
                        }

                        body {
                            padding-bottom: var(--sf-admin-footer-height) !important;
                        }

                        .sf-cancello-fixed-footer {
                            position: fixed;
                            right: 0;
                            bottom: 0;
                            left: var(--sf-admin-sidebar-width);
                            height: var(--sf-admin-footer-height);
                            z-index: 40;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 7px 18px;
                            pointer-events: none;
                            background: linear-gradient(180deg, rgba(248,250,252,0), rgba(248,250,252,.88) 34%, rgba(248,250,252,.96));
                            backdrop-filter: blur(18px);
                        }

                        .sf-cancello-fixed-footer-inner {
                            pointer-events: auto;
                            width: fit-content;
                            max-width: calc(100vw - var(--sf-admin-sidebar-width) - 42px);
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            gap: 9px;
                            border-radius: 999px;
                            padding: 6px 18px;
                            background: rgba(255, 255, 255, .90);
                            border: 1px solid rgba(15, 23, 42, .08);
                            box-shadow: 0 12px 34px rgba(15, 23, 42, .10);
                            color: #64748b;
                            font-size: 12px;
                            font-weight: 850;
                            line-height: 1;
                        }

                        .sf-cancello-powered {
                            color: #94a3b8;
                            font-size: 9px;
                            font-weight: 950;
                            letter-spacing: .12em;
                            text-transform: uppercase;
                        }

                        .sf-cancello-fixed-logo {
                            width: 76px;
                            height: 42px;
                            object-fit: contain;
                            border-radius: 0;
                            background: transparent;
                            border: 0;
                            padding: 0;
                        }

                        .sf-cancello-fixed-logo-fallback {
                            width: 38px;
                            height: 38px;
                            border-radius: 999px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            background: #020617;
                            color: #ffffff;
                            font-size: 11px;
                            font-weight: 950;
                            border: 1px solid rgba(15, 23, 42, .10);
                        }

                        .sf-cancello-dot {
                            color: #cbd5e1;
                        }

                        .dark .sf-cancello-fixed-footer {
                            background: linear-gradient(180deg, rgba(2,6,23,0), rgba(2,6,23,.82) 34%, rgba(2,6,23,.95));
                        }

                        .dark .sf-cancello-fixed-footer-inner {
                            background: rgba(15, 23, 42, .86);
                            border-color: rgba(148, 163, 184, .18);
                            color: #94a3b8;
                            box-shadow: 0 12px 34px rgba(0, 0, 0, .24);
                        }

                        body:has(.fi-sidebar.fi-sidebar-collapsed) .sf-cancello-fixed-footer,
                        body:has(.fi-sidebar-collapsed) .sf-cancello-fixed-footer {
                            left: 5.25rem;
                        }

                        body:has(.fi-sidebar.fi-sidebar-collapsed) .sf-cancello-fixed-footer-inner,
                        body:has(.fi-sidebar-collapsed) .sf-cancello-fixed-footer-inner {
                            max-width: calc(100vw - 5.25rem - 42px);
                        }

                        @media (max-width: 1024px) {
                            .sf-cancello-fixed-footer {
                                left: 0;
                                padding-inline: 12px;
                            }

                            .sf-cancello-fixed-footer-inner {
                                max-width: calc(100vw - 24px);
                                flex-wrap: wrap;
                                gap: 7px;
                            }
                        }

                        @media print {
                            body {
                                padding-bottom: 0 !important;
                            }

                            .sf-cancello-fixed-footer {
                                display: none !important;
                            }
                        }
                    </style>
                ')
            )


            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString('
                    <style id="sf-admin-dark-mode-background-footer-fix-final">
                        /*
                         | Global Admin background fix
                         | Makes the whole workspace react to light/dark mode.
                         */
                        html,
                        body,
                        .fi-body,
                        .fi-layout,
                        .fi-main,
                        .fi-main-ctn,
                        .fi-page,
                        .fi-page-content {
                            transition: background .22s ease, color .22s ease !important;
                        }

                        body,
                        .fi-body,
                        .fi-layout,
                        .fi-main,
                        .fi-main-ctn,
                        .fi-page,
                        .fi-page-content {
                            background:
                                radial-gradient(circle at top right, rgba(20,184,166,.08), transparent 34%),
                                linear-gradient(180deg, #f8fbfd 0%, #eef7f8 100%) !important;
                        }

                        .dark body,
                        .dark .fi-body,
                        .dark .fi-layout,
                        .dark .fi-main,
                        .dark .fi-main-ctn,
                        .dark .fi-page,
                        .dark .fi-page-content,
                        html.dark body,
                        html.dark .fi-body,
                        html.dark .fi-layout,
                        html.dark .fi-main,
                        html.dark .fi-main-ctn,
                        html.dark .fi-page,
                        html.dark .fi-page-content {
                            background:
                                radial-gradient(circle at top right, rgba(20,184,166,.14), transparent 36%),
                                radial-gradient(circle at bottom left, rgba(37,99,235,.10), transparent 34%),
                                linear-gradient(180deg, #020617 0%, #071426 48%, #0f172a 100%) !important;
                            color: #e2e8f0 !important;
                        }

                        /*
                         | Keep page cards readable in dark mode.
                         */
                        .dark .fi-section,
                        .dark .fi-ta,
                        .dark .fi-fo-component-ctn,
                        .dark .fi-in-entry-wrp,
                        html.dark .fi-section,
                        html.dark .fi-ta,
                        html.dark .fi-fo-component-ctn,
                        html.dark .fi-in-entry-wrp {
                            background-color: rgba(15, 23, 42, .72) !important;
                            border-color: rgba(148, 163, 184, .16) !important;
                        }

                        /*
                         | Footer dark-mode improvement.
                         | Logo appears on a white capsule so the PNG stays clear.
                         */
                        .dark .sf-cancello-fixed-footer,
                        html.dark .sf-cancello-fixed-footer {
                            background:
                                linear-gradient(180deg, rgba(2,6,23,0), rgba(2,6,23,.82) 34%, rgba(2,6,23,.97)) !important;
                        }

                        .dark .sf-cancello-fixed-footer-inner,
                        html.dark .sf-cancello-fixed-footer-inner {
                            background: rgba(15, 23, 42, .92) !important;
                            border-color: rgba(148, 163, 184, .20) !important;
                            color: #cbd5e1 !important;
                            box-shadow: 0 16px 40px rgba(0, 0, 0, .32) !important;
                        }

                        .dark .sf-cancello-powered,
                        html.dark .sf-cancello-powered {
                            color: #94a3b8 !important;
                        }

                        .dark .sf-cancello-fixed-logo,
                        html.dark .sf-cancello-fixed-logo {
                            background: #ffffff !important;
                            border-radius: 14px !important;
                            padding: 5px 8px !important;
                            width: 88px !important;
                            height: 44px !important;
                            object-fit: contain !important;
                            box-shadow: 0 8px 22px rgba(255, 255, 255, .08), 0 10px 24px rgba(0, 0, 0, .22) !important;
                        }

                        .dark .sf-cancello-dot,
                        html.dark .sf-cancello-dot {
                            color: #475569 !important;
                        }

                        /*
                         | Light mode logo remains clean and transparent.
                         */
                        html:not(.dark) .sf-cancello-fixed-logo {
                            background: transparent !important;
                            border-radius: 0 !important;
                            padding: 0 !important;
                            width: 76px !important;
                            height: 42px !important;
                            box-shadow: none !important;
                        }

                        /*
                         | Some custom premium pages have their own white shells.
                         | This keeps the external workspace dark while preserving cards.
                         */
                        .dark [style*="background: #fff"],
                        .dark [style*="background:#fff"],
                        html.dark [style*="background: #fff"],
                        html.dark [style*="background:#fff"] {
                            background: rgba(15, 23, 42, .72) !important;
                        }

                        /*
                         | Do not force print pages.
                         */
                        @media print {
                            body,
                            .fi-body,
                            .fi-layout,
                            .fi-main,
                            .fi-main-ctn,
                            .fi-page,
                            .fi-page-content {
                                background: #ffffff !important;
                                color: #0f172a !important;
                            }
                        }
                    </style>
                ')
            )


            ->login()
            ->userMenuItems([
                MenuItem::make()
                    ->label('My Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => route('admin.my-profile.edit')),
            ])
            ->darkMode(true)
->brandName('Sada Fezzan ERP')
            ->brandLogo('/images/sada-horizontal.png')
            ->darkModeBrandLogo('/images/sada-horizontal.png')
            ->brandLogoHeight('3.8rem')
            ->navigationGroups([
                'Recruitment',
                'HR',
                'Finance',
                'Archive',
                'Admin Settings',
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.admin.sada-admin-notifications-polish')->render(),
            )
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->resources([
                ArchivedJobApplicationResource::class,
            ])
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->pages([])
            ->colors([
                'primary' => Color::Teal,
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => ''
            )
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn () => ''
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn () => view('filament.admin.sada-topbar-profile-chip')->render()
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                AuditAdminRequest::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
