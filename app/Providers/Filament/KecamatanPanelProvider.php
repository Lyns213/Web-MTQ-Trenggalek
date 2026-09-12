<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Auth\Login;
use Filament\Enums\ThemeMode;
use App\Http\Middleware\SetDefaultTahun;
use Illuminate\Support\Facades\Auth;

class KecamatanPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('kecamatan')
            ->path('')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Kecamatan/Resources'), for: 'App\\Filament\\Kecamatan\\Resources')
            ->discoverPages(in: app_path('Filament/Kecamatan/Pages'), for: 'App\\Filament\\Kecamatan\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Kecamatan/Widgets'), for: 'App\\Filament\\Kecamatan\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
                SetDefaultTahun::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->unsavedChangesAlerts()
            ->brandLogo(fn () => view('filament.kecamatan.logo'))
            ->brandName(function () {
                $user = Auth::user();
                $userName = $user ? $user->name : 'Kecamatan';
                return 'Admin MTQ ' . $userName;
            })
            ->brandLogoHeight('3.4rem')
            ->sidebarWidth('19rem')
            ->defaultThemeMode(ThemeMode::Light)
            ->favicon(asset('images/logotgxmini.png'))
                        ->renderHook(
                "panels::global-search.before",
                fn () => view("filament.plugins.tahun-filter-blade")
            )
            ->breadcrumbs(false);
    }
}
