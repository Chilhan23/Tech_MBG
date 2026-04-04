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
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
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
        ? Blade::render('
            <style>
    body {
        background-image: url("/images/mbg-bg3.png");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
    }

    .fi-simple-main {
        background: rgba(0, 0, 0, 0.5) !important;
        border: none !important;
        box-shadow: none !important;
        backdrop-filter: blur(8px) !important;
        border-radius: 16px !important;
        opacity: 0;
        animation: cardReveal 0.8s ease 1s forwards;
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

    /* Wrapper input email - border putih */
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

    /* Wrapper password + icon mata nyatu */
    .fi-simple-main .fi-input-wrp-suffix {
        border: none !important;
        background: transparent !important;
        display: flex !important;
    }

    /* Parent dari input + suffix yang jadi border utama password */
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

    /* Checkbox remember me */
    .fi-simple-main input[type="checkbox"] {
        border: 1px solid rgba(255, 255, 255, 0.6) !important;
        accent-color: white !important;
        width: 16px !important;
        height: 16px !important;
        background: transparent !important;
        flex-shrink: 0 !important;
    }

    /* Icon mata - hapus border */
    .fi-simple-main button:not([type="submit"]) {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        outline: none !important;
    }

    /* Button Sign in */
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
</style> 
        ')
        : ''
)
        ->brandLogo(fn () => view('filament.hooks.logo-mbg'))
        ->brandName('');
        // ->renderHook(
        //     PanelsRenderHook::USER_MENU_BEFORE,
        //     fn (): View => view('filament.hooks.logo-mbg'),
        // );
    }
}
