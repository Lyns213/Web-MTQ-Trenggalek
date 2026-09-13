<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use App\Filament\Auth\LoginPenilaian;
use App\Filament\Penilaian\Pages\Dashboard as PenilaianDashboard;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Http\Middleware\SetDefaultTahun;

class PenilaianPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('penilaian')
            ->path('penilaian')
            ->login(LoginPenilaian::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugin(\App\Filament\Plugins\TahunFilterPlugin::make())
            ->discoverResources(in: app_path('Filament/Penilaian/Resources'), for: 'App\\Filament\\Penilaian\\Resources')
            ->discoverPages(in: app_path('Filament/Penilaian/Pages'), for: 'App\\Filament\\Penilaian\\Pages')
            ->pages([
                PenilaianDashboard::class,
            ])
            ->widgets([
                \App\Filament\Penilaian\Widgets\DashboardStatsWidget::class,
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
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => view('filament.penilaian.custom-styles')
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn () => view('filament.penilaian.auth.footer')
            )
            ->brandLogo(fn () => view('filament.penilaian.logo'))
            ->brandName(function () {
                $user = Auth::user();
                $userName = $user ? $user->name : 'Cabang';
                return 'MTQ Trenggalek ' . $userName;
            })
            ->brandLogoHeight('3.6rem')
            ->navigation(false)
            ->maxContentWidth('full')
            ->defaultThemeMode(ThemeMode::Light)
            ->favicon(asset('images/logotgxmini.png'))
            ->breadcrumbs(false);
    }
}
