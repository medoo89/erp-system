<?php

namespace App\Providers\Filament;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
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
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->darkMode(true)
            ->brandName('Sada Fezzan ERP')
            ->brandLogo('/images/sada-horizontal.png')
            ->darkModeBrandLogo('/images/sada-horizontal.png')
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Teal,
                'gray' => Color::Slate,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Red,
                'info' => Color::Blue,
            ])
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
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => <<<'HTML'
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<style>
@font-face {
    font-family: 'Gilroy';
    src: url('/fonts/gilroy/Gilroy-Light.otf') format('opentype');
    font-weight: 300;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'Gilroy';
    src: url('/fonts/gilroy/Gilroy-ExtraBold.otf') format('opentype');
    font-weight: 800;
    font-style: normal;
    font-display: swap;
}

:root{
    --sada-primary:#3C9FA3;
    --sada-primary-hover:#2F878A;
    --sada-light:#97C6C0;
    --sada-dark:#2C5377;
    --sada-bg:#F7F9FA;
    --sada-border:#D9E3E6;
    --sada-danger:#EF4444;
    --sada-warning:#F59E0B;

    --sada-sidebar-light:#F4FBFA;
    --sada-sidebar-light-active:#DDF3F1;
    --sada-sidebar-light-text:#2C5377;

    --sada-sidebar-dark:#0F172A;
    --sada-sidebar-dark-active:#14323A;
    --sada-sidebar-dark-text:#D7F3EF;

    --sada-topbar-light:rgba(255,255,255,.82);
    --sada-topbar-dark:rgba(15,23,42,.88);

    --sada-card-shadow:0 12px 30px rgba(15,23,42,.06);
    --sada-card-shadow-hover:0 18px 38px rgba(15,23,42,.10);
}

/* ================================
   Typography
================================ */
html,
body,
.fi-body,
.fi-layout,
.fi-main,
.fi-page,
.fi-topbar,
.fi-sidebar,
.fi-ta,
.fi-in,
.fi-fo,
.fi-section,
.fi-card,
.fi-modal,
.fi-dropdown,
.fi-input,
.fi-select-input,
.fi-textarea,
.fi-pagination,
table,
th,
td,
span,
p,
small,
label,
a,
input,
select,
textarea {
    font-family: 'Gilroy', Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    font-weight: 300 !important;
    letter-spacing: 0.1px;
}

h1,
h2,
h3,
h4,
h5,
h6,
.fi-header-heading,
.fi-section-header-heading,
.fi-ta-header-heading,
.fi-page-subheading,
.fi-sidebar-group-label,
.fi-in-entry-label,
.fi-fo-field-wrp-label,
.fi-tabs-item-label,
.fi-sidebar-item-label {
    font-family: 'Gilroy', Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    font-weight: 800 !important;
    letter-spacing: 0.2px;
}

.fi-btn,
.fi-btn span,
.fi-badge,
button {
    font-family: 'Gilroy', Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    font-weight: 800 !important;
    letter-spacing: 0.15px;
}

/* ================================
   App background
================================ */
body,
.fi-body {
    background:
        radial-gradient(circle at top left, rgba(60,159,163,.07), transparent 22%),
        radial-gradient(circle at top right, rgba(44,83,119,.06), transparent 24%),
        linear-gradient(180deg, #f8fbfc 0%, #eef5f7 55%, #f7fafc 100%) !important;
}

.dark body,
.dark .fi-body {
    background:
        radial-gradient(circle at top left, rgba(60,159,163,.10), transparent 22%),
        radial-gradient(circle at top right, rgba(29,78,216,.07), transparent 26%),
        linear-gradient(180deg, #09111f 0%, #0b1324 48%, #0f172a 100%) !important;
}

.fi-main {
    background: transparent !important;
}

.fi-page {
    gap: 1.5rem !important;
    padding-bottom: 1.2rem !important;
    max-width: 100% !important;
    width: 100% !important;
}

.fi-main-ctn,
.fi-page-content,
.fi-page-content > div,
.fi-page > div,
.fi-section-content,
.fi-wi,
.fi-wi-widget,
.fi-wi-stats-overview {
    width: 100% !important;
    max-width: 100% !important;
}

/* نخفي هيدر الداشبورد الافتراضي لو كان فاضي */
.fi-header:has(.fi-header-heading:empty) {
    display: none !important;
}

/* ================================
   Topbar
================================ */
.fi-topbar{
    background: var(--sada-topbar-light) !important;
    border-bottom: 1px solid var(--sada-border) !important;
    backdrop-filter: blur(10px);
}

.dark .fi-topbar{
    background: var(--sada-topbar-dark) !important;
    border-bottom: 1px solid #1F2937 !important;
}

/* ================================
   Sidebar
================================ */
.fi-sidebar{
    background: var(--sada-sidebar-light) !important;
    border-right: 1px solid var(--sada-border) !important;
}

.fi-sidebar-header{
    background: var(--sada-sidebar-light) !important;
}

.fi-sidebar-item-button{
    color: var(--sada-sidebar-light-text) !important;
    border-radius: 14px !important;
    transition: all .2s ease;
}

.fi-sidebar-item-icon{
    color: var(--sada-sidebar-light-text) !important;
}

.fi-sidebar-item-active .fi-sidebar-item-button,
.fi-sidebar-item-button:hover{
    background: var(--sada-sidebar-light-active) !important;
    color: var(--sada-sidebar-light-text) !important;
}

.fi-sidebar-item-active .fi-sidebar-item-icon,
.fi-sidebar-item-button:hover .fi-sidebar-item-icon{
    color: var(--sada-sidebar-light-text) !important;
}

.dark .fi-sidebar{
    background: var(--sada-sidebar-dark) !important;
    border-right: 1px solid #1F2937 !important;
}

.dark .fi-sidebar-header{
    background: var(--sada-sidebar-dark) !important;
}

.dark .fi-sidebar-item-button{
    color: var(--sada-sidebar-dark-text) !important;
}

.dark .fi-sidebar-item-icon{
    color: var(--sada-sidebar-dark-text) !important;
}

.dark .fi-sidebar-item-active .fi-sidebar-item-button,
.dark .fi-sidebar-item-button:hover{
    background: var(--sada-sidebar-dark-active) !important;
    color: #ffffff !important;
}

.dark .fi-sidebar-item-active .fi-sidebar-item-icon,
.dark .fi-sidebar-item-button:hover .fi-sidebar-item-icon{
    color: #97C6C0 !important;
}

.fi-logo{
    max-height: 3rem !important;
}

/* ================================
   Generic cards
================================ */
.fi-section,
.fi-card,
.fi-wi-widget {
    border: 1px solid var(--sada-border) !important;
    border-radius: 24px !important;
    box-shadow: var(--sada-card-shadow) !important;
    overflow: hidden;
    background: rgba(255,255,255,.94) !important;
}

.dark .fi-section,
.dark .fi-card,
.dark .fi-wi-widget {
    border-color: rgba(148,163,184,.12) !important;
    background: linear-gradient(180deg, rgba(15,23,42,.94) 0%, rgba(12,20,35,.96) 100%) !important;
}

/* ================================
   Buttons
================================ */
.fi-btn{
    border-radius: 14px !important;
    box-shadow: none !important;
}

.fi-btn-color-primary{
    background: linear-gradient(135deg, var(--sada-primary) 0%, #55B5BA 100%) !important;
    border-color: var(--sada-primary) !important;
    color: white !important;
}

.fi-btn-color-primary:hover{
    background: linear-gradient(135deg, var(--sada-primary-hover) 0%, #3C9FA3 100%) !important;
    border-color: var(--sada-primary-hover) !important;
}

.sada-public-calendar-btn{
    text-decoration:none !important;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#ffffff !important;
    color:var(--sada-dark) !important;
    border:1px solid rgba(217,227,230,.95) !important;
    padding:.78rem 1.1rem !important;
    font-weight:800 !important;
}

.sada-public-calendar-btn:hover{
    background:#f8fbfc !important;
}

/* ================================
   Tables
================================ */
.fi-ta-content {
    border-radius: 22px !important;
    overflow: hidden !important;
    border: 1px solid rgba(217,227,230,.95) !important;
    background: rgba(255,255,255,.95) !important;
    box-shadow: var(--sada-card-shadow) !important;
}

.dark .fi-ta-content {
    border-color: rgba(148,163,184,.12) !important;
    background: linear-gradient(180deg, rgba(15,23,42,.96) 0%, rgba(12,20,35,.98) 100%) !important;
}

.fi-ta-table thead tr th {
    background: linear-gradient(180deg, #fbfefe 0%, #f3f9f9 100%) !important;
    color: #2c5377 !important;
    border-bottom: 1px solid rgba(217,227,230,.9) !important;
    font-weight: 800 !important;
}

.dark .fi-ta-table thead tr th {
    background: linear-gradient(180deg, rgba(17,24,39,.98) 0%, rgba(15,23,42,.98) 100%) !important;
    color: #dbeafe !important;
    border-bottom: 1px solid rgba(148,163,184,.12) !important;
}

.fi-ta-table tbody td{
    vertical-align: middle;
    background: rgba(255,255,255,.96) !important;
}

.dark .fi-ta-table tbody td{
    background: rgba(15,23,42,.92) !important;
}

/* ================================
   Inputs
================================ */
.fi-input,
.fi-select-input,
.fi-textarea{
    border-radius: 12px !important;
}

/* ================================
   Premium header لكل الصفحات الداخلية
================================ */
.fi-header {
    width: 100% !important;
    max-width: 100% !important;
    border: 1px solid rgba(60,159,163,.14) !important;
    border-radius: 34px !important;
    background:
        radial-gradient(circle at top right, rgba(60,159,163,.13), transparent 24%),
        linear-gradient(135deg, #ffffff 0%, #f3fbfb 100%) !important;
    box-shadow: var(--sada-card-shadow) !important;
    padding: 2rem 1.8rem 1.6rem !important;
    overflow: hidden !important;
}

.dark .fi-header {
    border-color: rgba(148,163,184,.12) !important;
    background:
        radial-gradient(circle at top right, rgba(60,159,163,.10), transparent 28%),
        linear-gradient(135deg, #081224 0%, #0b1730 52%, #0a1325 100%) !important;
}

.fi-header-heading {
    font-size: clamp(2.35rem, 3.2vw, 3.8rem) !important;
    line-height: 1.02 !important;
    letter-spacing: -.04em !important;
    color: var(--sada-dark) !important;
}

.dark .fi-header-heading {
    color: #f8fafc !important;
}

.fi-header-subheading,
.fi-page-subheading {
    margin-top: .8rem !important;
    max-width: 980px !important;
    font-size: 1rem !important;
    line-height: 1.85 !important;
    color: #6b7e99 !important;
}

.dark .fi-header-subheading,
.dark .fi-page-subheading {
    color: #cbd5e1 !important;
}

/* ================================
   Dashboard stats
================================ */
.fi-wi-stats-overview {
    gap: 1.35rem !important;
    padding: 0 !important;
    width: 100% !important;
    margin: 0 !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
}

.fi-wi-stats-overview-stat {
    position: relative;
    overflow: hidden;
    border-radius: 30px !important;
    border: 1px solid rgba(60,159,163,.14) !important;
    box-shadow: 0 16px 34px rgba(15,23,42,.07) !important;
    background:
        radial-gradient(circle at top right, rgba(60,159,163,.08), transparent 26%),
        linear-gradient(180deg, rgba(255,255,255,.98) 0%, rgba(247,250,252,.95) 100%) !important;
    transition: all .24s ease;
    padding: 1.55rem 1.45rem !important;
    min-height: 220px;
}

.dark .fi-wi-stats-overview-stat {
    background:
        radial-gradient(circle at top right, rgba(60,159,163,.12), transparent 28%),
        linear-gradient(180deg, rgba(10,18,36,.98) 0%, rgba(11,19,35,.96) 100%) !important;
    border-color: rgba(148,163,184,.12) !important;
}

.fi-wi-stats-overview-stat::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 6px !important;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--sada-primary) 0%, #7ED0D3 100%);
}

.fi-wi-stats-overview-stat-value {
    font-size: 3.6rem !important;
    line-height: 1 !important;
    font-weight: 800 !important;
    letter-spacing: -0.06em !important;
    color: #274d78 !important;
    margin-top: .55rem !important;
    margin-bottom: .8rem !important;
}

.dark .fi-wi-stats-overview-stat-value {
    color: #ffffff !important;
}

.fi-wi-stats-overview-stat-label {
    font-size: 1.12rem !important;
    font-weight: 800 !important;
    line-height: 1.35 !important;
    color: #5f7594 !important;
}

.dark .fi-wi-stats-overview-stat-label {
    color: #d7e3f4 !important;
}

.fi-wi-stats-overview-stat-description {
    font-size: .95rem !important;
    line-height: 1.65 !important;
    color: #7a8faa !important;
}

.dark .fi-wi-stats-overview-stat-description {
    color: #9fb0c9 !important;
}

.fi-wi-stats-overview-stat svg {
    width: 1.35rem !important;
    height: 1.35rem !important;
    padding: .72rem !important;
    border-radius: 999px !important;
    background: linear-gradient(135deg, rgba(60,159,163,.14) 0%, rgba(126,208,211,.18) 100%) !important;
    color: var(--sada-primary) !important;
    box-sizing: content-box !important;
}

/* ================================
   Executive hero
================================ */
.sada-executive-hero {
    width: 100% !important;
    max-width: 100% !important;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(60,159,163,.14);
    border-radius: 34px;
    background:
        radial-gradient(circle at top right, rgba(60,159,163,.13), transparent 24%),
        linear-gradient(135deg, #ffffff 0%, #f3fbfb 100%);
    box-shadow: var(--sada-card-shadow);
    padding: 2.4rem 2rem 2.1rem !important;
    text-align: center;
    margin: 0 0 1.5rem 0 !important;
    display: block !important;
}

.dark .sada-executive-hero {
    border-color: rgba(148,163,184,.12);
    background:
        radial-gradient(circle at top right, rgba(60,159,163,.10), transparent 28%),
        linear-gradient(135deg, #081224 0%, #0b1730 52%, #0a1325 100%);
}

.sada-executive-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .65rem 1.1rem;
    border-radius: 999px;
    background: rgba(60,159,163,.10);
    color: var(--sada-primary);
    font-size: .82rem;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.sada-executive-title {
    margin: 0;
    font-size: clamp(3rem, 4.8vw, 5.2rem);
    line-height: 1.02;
    font-weight: 800;
    letter-spacing: -0.05em;
    color: var(--sada-dark);
}

.dark .sada-executive-title {
    color: #f8fafc;
}

.sada-executive-subtitle {
    margin: 1rem auto 0;
    max-width: 980px;
    font-size: 1.02rem;
    line-height: 1.9;
    color: #6b7e99;
    text-align: center;
}

.dark .sada-executive-subtitle {
    color: #cbd5e1;
}

.sada-executive-pill {
    margin: 1.2rem auto 0;
    display: inline-flex;
    align-items: center;
    gap: .7rem;
    padding: .82rem 1.08rem;
    border-radius: 999px;
    background: rgba(255,255,255,.82);
    border: 1px solid rgba(217,227,230,.9);
}

.dark .sada-executive-pill {
    background: rgba(15,23,42,.72);
    border-color: rgba(148,163,184,.12);
}

.sada-executive-pill-label {
    font-size: .76rem;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #94a3b8;
}

.sada-executive-pill-value {
    font-size: 1rem;
    font-weight: 800;
    color: var(--sada-dark);
}

.dark .sada-executive-pill-value {
    color: #f8fafc;
}

/* ================================
   Recruitment calendar
================================ */
.sada-calendar-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    width: 100% !important;
    max-width: 100% !important;
}

.sada-calendar-page-header {
    width: 100% !important;
    max-width: 100% !important;
    text-align: center !important;
    display: block !important;
    padding: 0 !important;
}

.sada-calendar-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .6rem 1rem;
    border-radius: 999px;
    background: rgba(60,159,163,.10);
    color: var(--sada-primary);
    font-size: .82rem;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.sada-calendar-title {
    margin: 0;
    font-size: clamp(3rem, 5vw, 5.5rem);
    line-height: 1.02;
    font-weight: 800;
    color: var(--sada-dark);
    letter-spacing: -0.05em;
    text-align: center !important;
}

.dark .sada-calendar-title {
    color: #f8fafc;
}

.sada-calendar-subtitle {
    margin: 1rem auto 0;
    max-width: 1080px;
    font-size: 1.02rem;
    line-height: 1.9;
    color: #6b7e99;
    text-align: center;
}

.dark .sada-calendar-subtitle {
    color: #cbd5e1;
}

.sada-calendar-hero {
    width: 100% !important;
    max-width: 100% !important;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(60,159,163,.14);
    border-radius: 34px;
    background:
        radial-gradient(circle at top right, rgba(60,159,163,.13), transparent 24%),
        linear-gradient(135deg, #ffffff 0%, #f3fbfb 100%);
    box-shadow: var(--sada-card-shadow);
    padding: 2.4rem 2rem 2.1rem !important;
    text-align: center !important;
    margin: 0 0 1.5rem 0 !important;
}

.dark .sada-calendar-hero {
    border-color: rgba(148,163,184,.12);
    background:
        radial-gradient(circle at top right, rgba(60,159,163,.10), transparent 28%),
        linear-gradient(135deg, #081224 0%, #0b1730 52%, #0a1325 100%);
}

.sada-calendar-hero-actions {
    margin-top: 1.25rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: .85rem;
    flex-wrap: wrap;
}

.sada-calendar-pill-btn {
    display: inline-flex;
    align-items: center;
    gap: .7rem;
    padding: .82rem 1.08rem;
    border-radius: 999px;
    background: rgba(255,255,255,.82);
    border: 1px solid rgba(217,227,230,.9);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    cursor: pointer;
}

.dark .sada-calendar-pill-btn {
    background: rgba(15,23,42,.72);
    border-color: rgba(148,163,184,.12);
}

.sada-calendar-pill-label {
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #94a3b8;
    font-weight: 800;
}

.sada-calendar-pill-value {
    font-size: 1rem;
    font-weight: 800;
    color: var(--sada-dark);
}

.dark .sada-calendar-pill-value {
    color: #f8fafc;
}

.sada-calendar-layout {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) 360px !important;
    gap: 1.5rem !important;
    align-items: start !important;
    width: 100% !important;
    max-width: 100% !important;
}

.sada-calendar-main {
    min-width: 0;
    width: 100%;
    display: block !important;
}

.sada-calendar-sidebar {
    display: grid;
    gap: 1.25rem;
    width: 100%;
}

.sada-calendar-card {
    width: 100% !important;
    border-radius: 30px;
    padding: 1.2rem;
    background: rgba(255,255,255,.30);
    overflow: visible !important;
    border: 1px solid rgba(217,227,230,.55);
    box-shadow: 0 16px 34px rgba(15,23,42,.05);
}

.dark .sada-calendar-card {
    background: rgba(15,23,42,.16);
}

.sada-calendar-side-card {
    border: 1px solid rgba(217,227,230,.95);
    border-radius: 24px;
    background: rgba(255,255,255,.94);
    box-shadow: var(--sada-card-shadow);
    padding: 1.25rem;
}

.dark .sada-calendar-side-card {
    border-color: rgba(148,163,184,.12);
    background: linear-gradient(180deg, rgba(15,23,42,.96) 0%, rgba(12,20,35,.98) 100%);
}

.sada-calendar-side-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.sada-calendar-side-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .42rem .8rem;
    border-radius: 999px;
    background: rgba(60,159,163,.10);
    color: var(--sada-primary);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-bottom: .7rem;
}

.sada-calendar-side-title {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--sada-dark);
    line-height: 1.3;
}

.dark .sada-calendar-side-title {
    color: #f8fafc;
}

.sada-calendar-side-text {
    margin: 0 0 1rem 0;
    color: #6b7e99;
    line-height: 1.7;
    font-size: .95rem;
}

.dark .sada-calendar-side-text {
    color: #cbd5e1;
}

.sada-calendar-side-list {
    display: grid;
    gap: .85rem;
}

.sada-empty-box {
    border: 1px solid rgba(217,227,230,.9);
    border-radius: 18px;
    background: rgba(248,250,252,.75);
    padding: .95rem 1rem;
    text-align: center;
    color: #6b7e99;
    line-height: 1.7;
}

.dark .sada-empty-box {
    border-color: rgba(148,163,184,.12);
    background: rgba(15,23,42,.55);
    color: #cbd5e1;
}

.sada-side-item,
.sada-calendar-modal-item,
.sada-upcoming-item {
    display: flex;
    gap: .8rem;
    align-items: flex-start;
    border: 1px solid rgba(217,227,230,.9);
    border-radius: 18px;
    padding: .95rem 1rem;
    background: rgba(248,250,252,.7);
}

.dark .sada-side-item,
.dark .sada-calendar-modal-item,
.dark .sada-upcoming-item {
    border-color: rgba(148,163,184,.12);
    background: rgba(15,23,42,.55);
}

.sada-side-dot,
.sada-upcoming-dot,
.sada-calendar-modal-item-dot {
    width: 12px;
    height: 12px;
    border-radius: 999px;
    margin-top: .42rem;
    flex-shrink: 0;
}

.sada-side-content,
.sada-upcoming-content,
.sada-calendar-modal-item-content {
    flex: 1;
}

.sada-side-item-title,
.sada-upcoming-title,
.sada-calendar-modal-item-title {
    font-size: .98rem;
    font-weight: 800;
    color: var(--sada-dark);
    line-height: 1.5;
}

.dark .sada-side-item-title,
.dark .sada-upcoming-title,
.dark .sada-calendar-modal-item-title {
    color: #f8fafc;
}

.sada-side-item-meta,
.sada-upcoming-meta,
.sada-calendar-modal-item-notes {
    margin-top: .32rem;
    font-size: .88rem;
    color: #6b7e99;
    line-height: 1.7;
}

.dark .sada-side-item-meta,
.dark .sada-upcoming-meta,
.dark .sada-calendar-modal-item-notes {
    color: #cbd5e1;
}

.sada-upcoming-groups {
    display: grid;
    gap: .95rem;
}

.sada-upcoming-group {
    border: 1px solid rgba(217,227,230,.9);
    border-radius: 20px;
    padding: 1rem;
    background: rgba(248,250,252,.7);
}

.dark .sada-upcoming-group {
    border-color: rgba(148,163,184,.12);
    background: rgba(15,23,42,.55);
}

.sada-upcoming-date {
    font-size: .95rem;
    font-weight: 800;
    color: var(--sada-dark);
    margin-bottom: .75rem;
}

.dark .sada-upcoming-date {
    color: #f8fafc;
}

.sada-add-form-inline {
    padding: 0;
}

.sada-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
}

.sada-form-full {
    grid-column: 1 / -1;
}

.sada-form-label {
    display: block;
    margin-bottom: .5rem;
    font-size: .85rem;
    font-weight: 800;
    color: var(--sada-dark);
}

.dark .sada-form-label {
    color: #e2e8f0;
}

.sada-color-options {
    display: flex;
    flex-wrap: wrap;
    gap: .65rem;
}

.sada-color-button {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    border: 2px solid rgba(255,255,255,.9);
    box-shadow: 0 4px 12px rgba(15,23,42,.12);
    cursor: pointer;
}

.sada-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    margin-top: 1.2rem;
}

.sada-manage-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 1.2rem;
    padding: 1rem 1.4rem 1.4rem;
}

.sada-manage-form-column {
    border-left: 1px solid rgba(217,227,230,.9);
    padding-left: 1.2rem;
}

.dark .sada-manage-form-column {
    border-left-color: rgba(148,163,184,.12);
}

.sada-manage-section-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--sada-dark);
    margin-bottom: .9rem;
}

.dark .sada-manage-section-title {
    color: #f8fafc;
}

.sada-calendar-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(2, 6, 23, 0.45);
    backdrop-filter: blur(6px);
    z-index: 80;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.2rem;
}

.sada-calendar-modal {
    width: 100%;
    max-width: 1180px;
    border-radius: 28px;
    background: rgba(255,255,255,.96);
    border: 1px solid rgba(217,227,230,.9);
    box-shadow: 0 24px 60px rgba(15,23,42,.18);
    overflow: hidden;
}

.dark .sada-calendar-modal {
    background: linear-gradient(180deg, rgba(10,18,36,.98) 0%, rgba(11,19,35,.98) 100%);
    border-color: rgba(148,163,184,.12);
}

.sada-calendar-modal-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.35rem 1.4rem 1rem 1.4rem;
    border-bottom: 1px solid rgba(217,227,230,.85);
}

.dark .sada-calendar-modal-top {
    border-bottom-color: rgba(148,163,184,.12);
}

.sada-calendar-modal-close {
    border: none;
    background: rgba(239,68,68,.10);
    color: #dc2626;
    width: 42px;
    height: 42px;
    border-radius: 999px;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
}

/* FullCalendar */
.fc {
    width: 100% !important;
    display: block !important;
    background: transparent !important;
    font-family: 'Gilroy', Inter, sans-serif !important;
}

.fc-view-harness {
    min-height: 760px !important;
}

.fc .fc-toolbar {
    margin-bottom: 1.15rem !important;
    display: grid !important;
    grid-template-columns: 1fr auto !important;
    align-items: center !important;
    gap: 1rem !important;
}

.fc .fc-toolbar-chunk:first-child {
    display: flex !important;
    justify-content: center !important;
}

.fc .fc-toolbar-chunk:last-child {
    display: flex !important;
    justify-content: flex-end !important;
    gap: .45rem !important;
}

.fc .fc-toolbar-title {
    text-align: center !important;
    width: 100% !important;
    font-size: 3rem !important;
    letter-spacing: -.04em !important;
    font-weight: 800 !important;
    color: var(--sada-dark) !important;
}

.dark .fc .fc-toolbar-title {
    color: #f8fafc !important;
}

.fc .fc-button {
    background: linear-gradient(135deg, var(--sada-primary) 0%, #55B5BA 100%) !important;
    border-color: var(--sada-primary) !important;
    border-radius: 14px !important;
    box-shadow: none !important;
    font-weight: 800 !important;
    text-transform: none !important;
    min-height: 52px;
    min-width: 66px;
}

.fc .fc-scrollgrid {
    width: 100% !important;
    border-radius: 26px !important;
    overflow: hidden !important;
    border: 1px solid rgba(217,227,230,.95) !important;
    background: rgba(255,255,255,.92) !important;
    box-shadow: var(--sada-card-shadow) !important;
}

.dark .fc .fc-scrollgrid {
    border-color: rgba(148,163,184,.12) !important;
    background: linear-gradient(180deg, rgba(10,18,36,.98) 0%, rgba(11,19,35,.96) 100%) !important;
}

.fc-theme-standard td,
.fc-theme-standard th {
    border-color: rgba(217,227,230,.8) !important;
}

.fc-col-header-cell {
    background: linear-gradient(180deg, #fbfefe 0%, #f3f9f9 100%) !important;
}

.fc .fc-col-header-cell-cushion {
    padding: .95rem .5rem !important;
    font-weight: 800 !important;
    color: var(--sada-dark) !important;
}

.fc .fc-daygrid-day-number {
    font-weight: 800 !important;
    color: #475569 !important;
    padding: .65rem !important;
}

.fc .fc-daygrid-day.fc-day-today {
    background: rgba(60,159,163,.08) !important;
}

.fc .fc-daygrid-day-frame {
    min-height: 132px !important;
}

.fc .fc-daygrid-body,
.fc .fc-daygrid-body table,
.fc .fc-scrollgrid-sync-table {
    width: 100% !important;
}

.fc .fc-daygrid-day-events {
    min-height: 2rem !important;
}

.fc .fc-event {
    border: none !important;
    border-radius: 12px !important;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.10);
    padding: 2px 6px !important;
    font-weight: 800 !important;
}

/* Responsive */
@media (max-width: 1200px) {
    .sada-calendar-layout {
        grid-template-columns: 1fr !important;
    }

    .fc-view-harness {
        min-height: 620px !important;
    }

    .sada-manage-layout {
        grid-template-columns: 1fr;
    }

    .sada-manage-form-column {
        border-left: none;
        border-top: 1px solid rgba(217,227,230,.9);
        padding-left: 0;
        padding-top: 1.2rem;
    }
}

@media (max-width: 900px) {
    .sada-calendar-title,
    .sada-executive-title {
        font-size: 2.4rem !important;
    }

    .fc .fc-toolbar {
        grid-template-columns: 1fr !important;
        gap: .9rem !important;
    }

    .fc .fc-toolbar-chunk:first-child,
    .fc .fc-toolbar-chunk:last-child {
        justify-content: center !important;
    }

    .fc .fc-toolbar-title {
        font-size: 2rem !important;
    }
}

@media (max-width: 768px) {
    .sada-calendar-pill-btn,
    .sada-executive-pill {
        flex-direction: column;
        gap: .3rem;
        border-radius: 18px;
    }

    .sada-form-grid {
        grid-template-columns: 1fr;
    }

    .sada-calendar-side-top {
        flex-direction: column;
        align-items: stretch;
    }

    .sada-form-actions {
        flex-direction: column;
    }

    .fc-view-harness {
        min-height: 520px !important;
    }
}

/* Calendar unified box layout */
.sada-calendar-page .sada-calendar-card > .sada-calendar-layout {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) 380px !important;
    gap: 1.5rem !important;
    align-items: start !important;
}

.sada-calendar-page .sada-calendar-main {
    min-width: 0 !important;
    width: 100% !important;
}

.sada-calendar-page .sada-calendar-sidebar {
    width: 100% !important;
    display: grid !important;
    gap: 1.25rem !important;
    align-self: start !important;
    padding-top: 75px !important;
}

.sada-calendar-page .sada-calendar-side-card {
    margin: 0 !important;
}

.sada-calendar-page .fc {
    position: relative !important;
}

.sada-calendar-page .fc .fc-toolbar {
    margin-bottom: 1.35rem !important;
    display: block !important;
    width: calc(100% + 420px) !important;
    position: relative !important;
}

.sada-calendar-page .fc .fc-toolbar-chunk:first-child {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
}

.sada-calendar-page .fc .fc-toolbar-chunk:last-child {
    position: absolute !important;
    top: 50% !important;
    right: 0 !important;
    transform: translateY(-50%) !important;
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
    gap: .45rem !important;
}

.sada-calendar-page .fc .fc-toolbar-title {
    width: 100% !important;
    text-align: center !important;
    margin: 0 !important;
    font-size: 3.45rem !important;
    line-height: 1 !important;
}


@media (max-width: 1200px) {
    .sada-calendar-page .sada-calendar-card > .sada-calendar-layout {
        grid-template-columns: 1fr !important;
    }

    .sada-calendar-page .sada-calendar-sidebar {
        padding-top: 0 !important;
    }

    .sada-calendar-page .fc .fc-toolbar {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: .9rem !important;
    }

    .sada-calendar-page .fc .fc-toolbar-chunk:last-child {
        position: static !important;
        transform: none !important;
        justify-content: center !important;
    }

    .sada-calendar-page .fc .fc-toolbar-title {
        font-size: 2.6rem !important;
    }
    
}

</style>
HTML
            );
    }
}