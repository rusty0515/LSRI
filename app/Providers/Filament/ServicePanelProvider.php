<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
use App\Http\Middleware\PanelRoleMiddleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class ServicePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('service')
            ->path('service')
            ->colors([
                'primary' => Color::Red,
            ])
            ->font('Plus Jakarta Sans')
            ->spa()
            ->brandLogo(asset('imgs/logo-01.png', true))
            ->brandLogoHeight('3rem')
            ->favicon(asset('imgs/logo-01.png'))
            // ->topNavigation()
            ->sidebarCollapsibleOnDesktop(true)
            ->authGuard('web')
            ->discoverResources(in: app_path('Filament/Service/Resources'), for: 'App\\Filament\\Service\\Resources')
            ->discoverPages(in: app_path('Filament/Service/Pages'), for: 'App\\Filament\\Service\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
             ->navigationGroups([

                NavigationGroup::make()
                ->label('Services')
                ->icon('heroicon-o-cog-6-tooth')
                ->collapsed(true),
            ])
            ->discoverWidgets(in: app_path('Filament/Service/Widgets'), for: 'App\\Filament\\Service\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ])
            ->databaseNotifications()
            ->authMiddleware([
                PanelRoleMiddleware::class
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ]);
    }
}
