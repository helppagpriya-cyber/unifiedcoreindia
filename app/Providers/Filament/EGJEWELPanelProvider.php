<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\EGJEWEL\Pages\CrmDashboard;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class EGJEWELPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('egjewel')
            ->path('egjewel')
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                NavigationGroup::make('CRM')
                    ->icon(Heroicon::OutlinedUserGroup),
                NavigationGroup::make('ERP Operations')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->collapsed(),
                NavigationGroup::make('Software Catalog')
                    ->icon(Heroicon::OutlinedCube)
                    ->collapsed(),
            ])
            ->viteTheme('resources/css/filament/egjewel/theme.css')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/EGJEWEL/Resources'), for: 'App\Filament\EGJEWEL\Resources')
            ->discoverPages(in: app_path('Filament/EGJEWEL/Pages'), for: 'App\Filament\EGJEWEL\Pages')
            ->pages([
                CrmDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/EGJEWEL/Widgets'), for: 'App\Filament\EGJEWEL\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ]);
    }
}
