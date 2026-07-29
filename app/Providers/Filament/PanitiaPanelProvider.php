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
            
            // ->darkMode(false)
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
        ? Blade::render('
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

            <style>
    * { box-sizing: border-box; }

    /* ── PAKSA DARK MODE & BACKGROUND ── */
    html, html.dark {
        color-scheme: dark !important;
    }

    body {
        background-color: #0a0f1e !important;
        background-image:
            radial-gradient(ellipse at 20% 50%, rgba(30,58,138,0.45) 0%, transparent 60%),
            radial-gradient(ellipse at 80% 20%, rgba(15,23,42,0.7) 0%, transparent 50%),
            radial-gradient(ellipse at 60% 80%, rgba(23,37,84,0.3) 0%, transparent 50%) !important;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        font-family: "Plus Jakarta Sans", sans-serif !important;
    }

    /* Overlay gelap di atas background */
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: linear-gradient(135deg, rgba(0,20,60,0.5) 0%, rgba(0,0,0,0.35) 100%);
        z-index: 0;
    }

    .fi-simple-layout {
        position: relative;
        z-index: 1;
    }

    /* ── CARD ── */
    .fi-simple-main {
        background: rgba(255, 255, 255, 0.07) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 32px 80px rgba(0,0,0,0.45), 0 0 0 1px rgba(255,255,255,0.05) !important;
        backdrop-filter: blur(24px) saturate(1.4) !important;
        -webkit-backdrop-filter: blur(24px) saturate(1.4) !important;
        border-radius: 24px !important;
        padding: 40px 36px 36px !important;
        width: 420px !important;
        max-width: 95vw !important;
        opacity: 0;
        transform: translateY(28px);
        animation: cardReveal 0.75s cubic-bezier(.22,.68,0,1.2) 0.3s forwards;
        position: relative;
        overflow: visible !important;
    }

    /* Garis aksen atas card */
    .fi-simple-main::before {
        content: "";
        position: absolute;
        top: 0; left: 50%;
        transform: translateX(-50%);
        width: 60%;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(99,179,237,0.9), rgba(147,197,253,1), rgba(99,179,237,0.9), transparent);
        border-radius: 0 0 4px 4px;
    }

    @keyframes cardReveal {
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── HEADER LOGO AREA ── */
    .login-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0;
        margin-bottom: 28px;
        animation: fadeUp 0.6s ease 0.55s both;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .login-logos {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        margin-bottom: 18px;
    }

    .login-logos img {
        height: 70px;
        width: auto;
        object-fit: contain;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.4));
        transition: transform 0.3s ease;
    }

    .login-logos img:hover {
        transform: scale(1.06);
    }

    .logo-divider {
        width: 1px;
        height: 48px;
        background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.35), transparent);
    }

    .login-welcome {
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: rgba(147,197,253,0.85) !important;
        margin-bottom: 6px;
    }

    .login-title {
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: white !important;
        letter-spacing: -0.3px;
        text-align: center;
        line-height: 1.2;
    }

    .login-subtitle {
        font-family: "Plus Jakarta Sans", sans-serif;
        font-size: 13px;
        font-weight: 400;
        color: rgba(255,255,255,0.45) !important;
        margin-top: 4px;
        text-align: center;
    }

    /* Sembunyikan brand bawaan Filament di login */
    .fi-simple-main .fi-logo,
    .fi-simple-main [class*="brand"] {
        display: none !important;
    }

    /* ── SEMUA TEKS ── */
    .fi-simple-main,
    .fi-simple-main * {
        color: white !important;
        font-family: "Plus Jakarta Sans", sans-serif !important;
    }

    /* Transparent backgrounds */
    .fi-simple-main *:not(button):not(svg):not(path):not(input[type="checkbox"]) {
        background: transparent !important;
        background-color: transparent !important;
    }

    /* ── LABEL ── */
    .fi-simple-main label {
        font-size: 12.5px !important;
        font-weight: 600 !important;
        letter-spacing: 0.3px !important;
        color: rgba(255,255,255,0.75) !important;
        margin-bottom: 6px !important;
        display: block;
    }

    /* ── INPUT ── */
    .fi-simple-main input:not([type="checkbox"]) {
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
        color: white !important;
        background: transparent !important;
        font-size: 14px !important;
        padding: 10px 14px !important;
        font-family: "Plus Jakarta Sans", sans-serif !important;
    }

    /* Wrapper email */
    .fi-simple-main .fi-input-wrapper,
    .fi-simple-main div:has(> input[type="email"]),
    .fi-simple-main div:has(> input:not([type="password"]):not([type="checkbox"])) {
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        border-radius: 10px !important;
        background: rgba(255,255,255,0.05) !important;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s !important;
    }

    .fi-simple-main .fi-input-wrapper:focus-within,
    .fi-simple-main div:has(> input[type="email"]):focus-within {
        border-color: rgba(147,197,253,0.7) !important;
        box-shadow: 0 0 0 3px rgba(99,179,237,0.15) !important;
        background: rgba(255,255,255,0.08) !important;
    }

    /* Wrapper password */
    .fi-simple-main .fi-input-wrp-suffix {
        border: none !important;
        background: transparent !important;
        display: flex !important;
    }

    .fi-simple-main div:has(> .fi-input-wrp-suffix) {
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        border-radius: 10px !important;
        background: rgba(255,255,255,0.05) !important;
        display: flex !important;
        align-items: center !important;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s !important;
    }

    .fi-simple-main div:has(> .fi-input-wrp-suffix):focus-within {
        border-color: rgba(147,197,253,0.7) !important;
        box-shadow: 0 0 0 3px rgba(99,179,237,0.15) !important;
        background: rgba(255,255,255,0.08) !important;
    }

    .fi-simple-main input::placeholder {
        color: rgba(255, 255, 255, 0.3) !important;
    }

    /* ── CHECKBOX ── */
    .fi-simple-main input[type="checkbox"] {
        border: 1.5px solid rgba(255, 255, 255, 0.4) !important;
        border-radius: 4px !important;
        accent-color: #93c5fd !important;
        width: 16px !important;
        height: 16px !important;
        background: transparent !important;
        flex-shrink: 0 !important;
    }

    /* ── ICON MATA ── */
    .fi-simple-main button:not([type="submit"]) {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        outline: none !important;
        opacity: 0.6;
        transition: opacity 0.2s;
    }
    .fi-simple-main button:not([type="submit"]):hover {
        opacity: 1;
    }

    /* ── TOMBOL SIGN IN ── */
    .fi-simple-main button[type="submit"] {
        background: linear-gradient(135deg, rgba(59,130,246,0.7) 0%, rgba(37,99,235,0.8) 100%) !important;
        border: 1px solid rgba(147,197,253,0.4) !important;
        color: white !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        letter-spacing: 0.4px !important;
        padding: 11px 20px !important;
        width: 100% !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 4px 16px rgba(37,99,235,0.3) !important;
        font-family: "Plus Jakarta Sans", sans-serif !important;
    }

    .fi-simple-main button[type="submit"]:hover {
        background: linear-gradient(135deg, rgba(96,165,250,0.85) 0%, rgba(59,130,246,0.9) 100%) !important;
        box-shadow: 0 6px 24px rgba(37,99,235,0.45) !important;
        transform: translateY(-1px) !important;
    }

    .fi-simple-main button[type="submit"]:active {
        transform: translateY(0) !important;
    }

    /* ── AUTOFILL ── */
    .fi-simple-main input:-webkit-autofill,
    .fi-simple-main input:-webkit-autofill:hover,
    .fi-simple-main input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0px 1000px rgba(15,23,42,0.01) inset !important;
        -webkit-text-fill-color: white !important;
        transition: background-color 5000s ease-in-out 0s;
    }

    /* ── ANIMASI FIELDS ── */
    .fi-simple-main .fi-fo-field-wrp {
        animation: fadeUp 0.5s ease both;
    }
    .fi-simple-main .fi-fo-field-wrp:nth-child(1) { animation-delay: 0.65s; }
    .fi-simple-main .fi-fo-field-wrp:nth-child(2) { animation-delay: 0.75s; }
    .fi-simple-main .fi-fo-field-wrp:nth-child(3) { animation-delay: 0.85s; }
    .fi-simple-main .fi-form-actions { animation: fadeUp 0.5s ease 0.95s both; }

    /* ── ERROR ── */
    .fi-simple-main .fi-fo-field-wrp-error-message {
        color: rgba(252,165,165,0.9) !important;
        font-size: 12px !important;
        margin-top: 4px !important;
    }

    /* ── LINK forgot password ── */
    .fi-simple-main a {
        color: rgba(147,197,253,0.8) !important;
        font-size: 12px !important;
        text-decoration: none !important;
        transition: color 0.2s !important;
    }
    .fi-simple-main a:hover {
        color: white !important;
    }

    /* ── CHECKBOX REMEMBER ME ── */
    .fi-simple-main .fi-checkbox-label-wrapper,
    .fi-simple-main label:has(input[type="checkbox"]),
    .fi-simple-main .flex.items-center {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        flex-direction: row !important;
    }

    .fi-simple-main input[type="checkbox"] {
        position: static !important;
        transform: none !important;
        flex-shrink: 0 !important;
        margin: 0 !important;
        top: unset !important;
    }

    .fi-simple-main .fi-checkbox-label-wrapper span,
    .fi-simple-main label:has(input[type="checkbox"]) span {
        vertical-align: middle !important;
        line-height: 1 !important;
    }
</style>

<script>
    // Paksa dark mode sebelum Filament render
    document.documentElement.classList.add("dark");
    localStorage.setItem("theme", "dark");

    document.addEventListener("DOMContentLoaded", function () {
        // Pastikan dark class tetap ada
        document.documentElement.classList.add("dark");

        const main = document.querySelector(".fi-simple-main");
        if (!main) return;

        // Sembunyikan brand bawaan Filament
        const brand = main.querySelector(".fi-logo, [class*=\"brand\"]");
        if (brand) brand.style.display = "none";

        // Buat header section
        const header = document.createElement("div");
        header.className = "login-header";
        header.innerHTML = `
            <div class="login-logos">
                <img src="/images/logo-bgn.webp" alt="Logo BGN" />
                <div class="logo-divider"></div>
                <img src="/images/Logo_SMKN5_Banda_Aceh.webp" alt="Logo SMKN 5 Banda Aceh" />
            </div>
            <div class="login-welcome">Selamat Datang</div>
            <div class="login-title">Portal Panitia</div>
            <div class="login-subtitle">Silakan masuk dengan akun Anda</div>
        `;

        main.insertBefore(header, main.firstChild);
    });
</script>
        ')
        : ''
)
        ->brandLogo(fn () => view('filament.hooks.logo-mbg'))
        ->brandName('');
    }
}