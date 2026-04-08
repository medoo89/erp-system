<?php

namespace App\Providers\Filament;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
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
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                ArchivedJobApplicationResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //
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
    --sada-export:#10B981;
    --sada-export-hover:#059669;
    --sada-danger:#EF4444;
    --sada-warning:#F59E0B;
    --sada-gray:#9CA3AF;

    --sada-sidebar-light:#F4FBFA;
    --sada-sidebar-light-active:#DDF3F1;
    --sada-sidebar-light-text:#2C5377;

    --sada-sidebar-dark:#0F172A;
    --sada-sidebar-dark-active:#14323A;
    --sada-sidebar-dark-text:#D7F3EF;

    --sada-topbar-light:#FFFFFF;
    --sada-topbar-dark:#111827;
}

/* الخط العام */
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

/* العناوين */
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

/* الأزرار والبادجات */
.fi-btn,
.fi-btn span,
.fi-badge,
button {
    font-family: 'Gilroy', Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    font-weight: 800 !important;
    letter-spacing: 0.15px;
}

/* القيم داخل الجداول والإنفو */
.fi-ta-text,
.fi-in-entry-content,
.fi-input-wrp,
.fi-select-input,
.fi-textarea {
    font-family: 'Gilroy', Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    font-weight: 300 !important;
}

/* Light mode */
.fi-topbar{
    background: var(--sada-topbar-light) !important;
    border-bottom: 1px solid var(--sada-border) !important;
    backdrop-filter: blur(8px);
}

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

/* Dark mode */
.dark .fi-topbar{
    background: var(--sada-topbar-dark) !important;
    border-bottom: 1px solid #1F2937 !important;
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

/* الشعار */
.fi-logo{
    max-height: 3rem !important;
}

/* الكروت والسكاشن */
.fi-section,
.fi-card{
    border: 1px solid var(--sada-border) !important;
    border-radius: 16px !important;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04) !important;
    overflow: hidden;
}

.fi-section-header{
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
}

/* الأزرار */
.fi-btn{
    border-radius: 12px !important;
    box-shadow: none !important;
}

.fi-btn-color-primary{
    background-color: var(--sada-primary) !important;
    border-color: var(--sada-primary) !important;
    color: white !important;
}

.fi-btn-color-primary:hover{
    background-color: var(--sada-primary-hover) !important;
    border-color: var(--sada-primary-hover) !important;
}

/* البادجات */
.fi-badge{
    font-weight: 800 !important;
    border-radius: 999px !important;
    padding-inline: 0.7rem !important;
    min-height: 1.8rem !important;
}

/* الحقول */
.fi-input,
.fi-select-input,
.fi-textarea{
    border-radius: 12px !important;
}

/* روابط */
a{
    color: var(--sada-dark);
}

/* تحسينات على الجداول */
.fi-ta-table thead th{
    font-weight: 800 !important;
}

.fi-ta-table tbody td{
    vertical-align: middle;
}

/* تحسينات على صفحة العرض */
.fi-in-entry-label{
    opacity: 0.85;
}

.fi-in-entry-content{
    font-weight: 300 !important;
}

.fi-section-header-heading{
    font-size: 1.02rem !important;
}

/* Status badges أجمل */
.fi-badge {
    font-weight: 800 !important;
    border-radius: 999px !important;
    padding-inline: 0.8rem !important;
    min-height: 1.9rem !important;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
}

.fi-color-gray .fi-badge,
.fi-badge-color-gray {
    background: #f3f4f6 !important;
    color: #374151 !important;
}

.dark .fi-color-gray .fi-badge,
.dark .fi-badge-color-gray {
    background: #1f2937 !important;
    color: #e5e7eb !important;
}

.fi-color-success .fi-badge,
.fi-badge-color-success {
    background: #dcfce7 !important;
    color: #166534 !important;
}

.dark .fi-color-success .fi-badge,
.dark .fi-badge-color-success {
    background: rgba(34, 197, 94, 0.18) !important;
    color: #bbf7d0 !important;
}

.fi-color-danger .fi-badge,
.fi-badge-color-danger {
    background: #fee2e2 !important;
    color: #991b1b !important;
}

.dark .fi-color-danger .fi-badge,
.dark .fi-badge-color-danger {
    background: rgba(239, 68, 68, 0.18) !important;
    color: #fecaca !important;
}

.fi-color-warning .fi-badge,
.fi-badge-color-warning {
    background: #fef3c7 !important;
    color: #92400e !important;
}

.dark .fi-color-warning .fi-badge,
.dark .fi-badge-color-warning {
    background: rgba(245, 158, 11, 0.18) !important;
    color: #fde68a !important;
}

.fi-color-info .fi-badge,
.fi-badge-color-info,
.fi-color-primary .fi-badge,
.fi-badge-color-primary {
    background: #dbeafe !important;
    color: #1d4ed8 !important;
}

.dark .fi-color-info .fi-badge,
.dark .fi-badge-color-info,
.dark .fi-color-primary .fi-badge,
.dark .fi-badge-color-primary {
    background: rgba(59, 130, 246, 0.18) !important;
    color: #bfdbfe !important;
}

/* Purple status for Interview */
.fi-color-purple .fi-badge,
.fi-badge-color-purple {
    background: #ede9fe !important;
    color: #6d28d9 !important;
}

.dark .fi-color-purple .fi-badge,
.dark .fi-badge-color-purple {
    background: rgba(139, 92, 246, 0.18) !important;
    color: #ddd6fe !important;
}

/* Interview / Purple status */
.fi-badge-color-purple,
.fi-color-purple .fi-badge,
.fi-badge.fi-color-purple,
[data-color="purple"] .fi-badge {
    background: #ede9fe !important;
    color: #6d28d9 !important;
    border-color: #c4b5fd !important;
}

.dark .fi-badge-color-purple,
.dark .fi-color-purple .fi-badge,
.dark .fi-badge.fi-color-purple,
.dark [data-color="purple"] .fi-badge {
    background: rgba(139, 92, 246, 0.18) !important;
    color: #ddd6fe !important;
    border-color: rgba(139, 92, 246, 0.35) !important;
}

.fi-ta-header-cell,
.fi-ta-header-cell * {
    font-weight: 800 !important;
}
</style>
HTML
            );
    }
}