<?php

namespace App\Providers\Filament;


use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Scanner;
use App\Filament\Widgets\AbsensiPerKelasWidget;
use App\Filament\Widgets\AbsensiStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PanitiaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('panitia')
            ->path('panitia')
            ->viteTheme('resources/css/filament/panitia/theme.css')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                 \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AbsensiStatsWidget::class,
                AbsensiPerKelasWidget::class,
            ])
            ->navigationItems([
                // NavigationItem::make('Scanner')
                //     ->url(fn (): string => route('scanner.index'))
                //     ->icon('heroicon-o-qr-code')
                //     ->sort(5),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
->renderHook(
    'panels::body.start',
    fn () => request()->routeIs('filament.panitia.auth.login')
        ? Blade::render('<style>
    body {
        background-color: #0f2027;
        background-image:
            radial-gradient(ellipse 80% 60% at 20% 10%, rgba(6, 182, 212, 0.18) 0%, transparent 60%),
            radial-gradient(ellipse 60% 80% at 80% 90%, rgba(16, 185, 129, 0.15) 0%, transparent 60%),
            radial-gradient(ellipse 70% 50% at 60% 40%, rgba(59, 130, 246, 0.10) 0%, transparent 55%),
            radial-gradient(ellipse 90% 70% at 10% 80%, rgba(6, 95, 70, 0.20) 0%, transparent 60%);
        animation: gradientShift 12s ease-in-out infinite alternate;
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }

    body::after {
        content: "";
        position: fixed;
        width: 600px;
        height: 600px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.08) 0%, transparent 70%);
        top: -150px;
        right: -100px;
        animation: orbFloat 18s ease-in-out infinite;
        pointer-events: none;
        z-index: 0;
    }

    @keyframes gradientShift {
        0% {
            background-image:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(6, 182, 212, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 90%, rgba(16, 185, 129, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse 70% 50% at 60% 40%, rgba(59, 130, 246, 0.10) 0%, transparent 55%),
                radial-gradient(ellipse 90% 70% at 10% 80%, rgba(6, 95, 70, 0.20) 0%, transparent 60%);
        }
        33% {
            background-image:
                radial-gradient(ellipse 80% 60% at 70% 30%, rgba(16, 185, 129, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 20% 70%, rgba(6, 182, 212, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse 70% 50% at 80% 80%, rgba(6, 95, 70, 0.12) 0%, transparent 55%),
                radial-gradient(ellipse 90% 70% at 40% 10%, rgba(59, 130, 246, 0.14) 0%, transparent 60%);
        }
        66% {
            background-image:
                radial-gradient(ellipse 80% 60% at 40% 80%, rgba(59, 130, 246, 0.16) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 60% 20%, rgba(6, 95, 70, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 70% 50% at 10% 50%, rgba(16, 185, 129, 0.10) 0%, transparent 55%),
                radial-gradient(ellipse 90% 70% at 85% 60%, rgba(6, 182, 212, 0.14) 0%, transparent 60%);
        }
        100% {
            background-image:
                radial-gradient(ellipse 80% 60% at 55% 15%, rgba(6, 95, 70, 0.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 30% 85%, rgba(59, 130, 246, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse 70% 50% at 75% 55%, rgba(6, 182, 212, 0.12) 0%, transparent 55%),
                radial-gradient(ellipse 90% 70% at 15% 30%, rgba(16, 185, 129, 0.16) 0%, transparent 60%);
        }
    }

    @keyframes orbFloat {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33%       { transform: translate(-60px, 80px) scale(1.1); }
        66%       { transform: translate(40px, -50px) scale(0.9); }
    }

    @keyframes cardReveal {
        from {
            opacity: 0;
            transform: translateY(30px);
            filter: blur(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
    }

    .fi-simple-main {
        background: rgba(0, 0, 0, 0.5) !important;
        border: none !important;
        box-shadow: none !important;
        backdrop-filter: blur(8px) !important;
        border-radius: 16px !important;
        opacity: 0;
        animation: cardReveal 0.8s ease 1s forwards;
        position: relative;
        z-index: 1;
    }

    .fi-simple-main,
    .fi-simple-main * {
        color: white !important;
    }

    .fi-simple-main *:not(button):not(svg):not(path):not(input[type="checkbox"]) {
        background: transparent !important;
        background-color: transparent !important;
    }

    .fi-simple-main input:not([type="checkbox"]) {
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
        color: white !important;
        background: transparent !important;
    }

    .fi-simple-main .fi-input-wrapper,
    .fi-simple-main div:has(> input[type="email"]),
    .fi-simple-main div:has(> input:not([type="password"]):not([type="checkbox"])) {
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 8px !important;
        background: transparent !important;
    }

    .fi-simple-main .fi-input-wrapper:focus-within,
    .fi-simple-main div:has(> input[type="email"]):focus-within {
        border: 1px solid white !important;
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.3) !important;
    }

    .fi-simple-main .fi-input-wrp-suffix {
        border: none !important;
        background: transparent !important;
        display: flex !important;
    }

    .fi-simple-main div:has(> .fi-input-wrp-suffix) {
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 8px !important;
        background: transparent !important;
        display: flex !important;
        align-items: center !important;
    }

    .fi-simple-main div:has(> .fi-input-wrp-suffix):focus-within {
        border: 1px solid white !important;
        box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.3) !important;
    }

    .fi-simple-main input::placeholder {
        color: rgba(255, 255, 255, 0.4) !important;
    }

    .fi-simple-main input[type="checkbox"] {
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        accent-color: white !important;
        width: 16px !important;
        height: 16px !important;
        background: transparent !important;
        flex-shrink: 0 !important;
    }

    .fi-simple-main button:not([type="submit"]) {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        outline: none !important;
    }

    .fi-simple-main button[type="submit"] {
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        color: white !important;
        border-radius: 8px !important;
    }

    .fi-simple-main button[type="submit"]:hover {
        background: rgba(255, 255, 255, 0.25) !important;
    }

    .fi-simple-main input:-webkit-autofill,
    .fi-simple-main input:-webkit-autofill:hover,
    .fi-simple-main input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0px 1000px rgba(0, 0, 0, 0.01) inset !important;
        -webkit-text-fill-color: white !important;
        transition: background-color 5000s ease-in-out 0s;
    }
</style>')
        : ''
)
->renderHook(
    PanelsRenderHook::SIMPLE_PAGE_START,
    fn () => request()->routeIs('filament.panitia.auth.login')
        ? view('filament.hooks.logo-mbg')->render()
        : ''
)
->brandLogo(null)
->brandName('');
    }
}
