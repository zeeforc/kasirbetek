<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\PosPage;
use App\Filament\Pages\PosRentalPage;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // ✅ Manifest + meta PWA (INI YANG BIKIN "manifest detected")
        Filament::registerRenderHook(
            'panels::head.end',
            fn() => <<<'HTML'
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0f172a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
HTML
        );

        // ✅ Service worker
        Filament::registerRenderHook(
            'panels::body.end',
            fn() => <<<'HTML'
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/serviceworker.js');
}
</script>
HTML
        );
    }


    public function panel(Panel $panel): Panel
    {
        $hideChrome = request()->is('pos*');

        return $panel
            ->default()
            ->id('admin')
            ->brandName('Toko Tumbler Kereta')
            ->path('/')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages([
                Dashboard::class,
                PosPage::class,
                PosRentalPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->userMenuItems([
                'logout' => MenuItem::make()->label('Log out')->color('danger'),
            ])
            ->topbar(! $hideChrome)
            ->navigation(! $hideChrome)
            ->sidebarCollapsibleOnDesktop()
            ->font('poppins')
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ]);
    }
}
