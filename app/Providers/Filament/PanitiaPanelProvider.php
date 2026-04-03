<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
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
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->navigationItems([
                NavigationItem::make('Scanner')
                    ->url(fn (): string => route('scanner.index'))
                    ->icon('heroicon-o-qr-code')
                    ->sort(5),
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
            <video autoplay muted loop playsinline style="
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                object-fit: cover;
                z-index: 0;
            ">
                <source src="/videos/background.mp4" type="video/mp4">
            </video>

            <div style="
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 1;
            "></div>

            <style>
                html body .fi-simple-layout {
                    position: relative;
                    z-index: 2;
                }

                html body .fi-simple-layout .fi-simple-main {
                    width: 100%;
                    max-width: 420px;
                    border-radius: 1rem;
                    background: rgba(255, 255, 255, 0.15);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                }

                html body .fi-simple-layout .fi-simple-main .fi-brand-name,
                html body .fi-simple-layout .fi-simple-main h1 {
                    color: white;
                }
            </style>
        ')
        : ''
);
    }
}
