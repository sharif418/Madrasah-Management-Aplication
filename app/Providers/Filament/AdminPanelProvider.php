<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
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
            ->colors([
                'primary' => Color::Emerald,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->font('Hind Siliguri') // Bengali font
            ->brandName('মাদরাসা ম্যানেজমেন্ট')
            ->favicon('/favicon.ico')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('ছাত্র ব্যবস্থাপনা')
                    ->icon('heroicon-o-academic-cap'),
                NavigationGroup::make()
                    ->label('শিক্ষক ও স্টাফ')
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make()
                    ->label('একাডেমিক সেটআপ')
                    ->icon('heroicon-o-cog-6-tooth'),
                NavigationGroup::make()
                    ->label('উপস্থিতি ব্যবস্থাপনা')
                    ->icon('heroicon-o-clipboard-document-check'),
                NavigationGroup::make()
                    ->label('পরীক্ষা ব্যবস্থাপনা')
                    ->icon('heroicon-o-document-text'),
                NavigationGroup::make()
                    ->label('ফি ব্যবস্থাপনা')
                    ->icon('heroicon-o-banknotes'),
                NavigationGroup::make()
                    ->label('হিসাব ব্যবস্থাপনা')
                    ->icon('heroicon-o-calculator'),
                NavigationGroup::make()
                    ->label('হিফজ ও কিতাব')
                    ->icon('heroicon-o-book-open'),
                NavigationGroup::make()
                    ->label('লাইব্রেরি')
                    ->icon('heroicon-o-building-library'),
                NavigationGroup::make()
                    ->label('হোস্টেল ও পরিবহন')
                    ->icon('heroicon-o-home-modern'),
                NavigationGroup::make()
                    ->label('যোগাযোগ')
                    ->icon('heroicon-o-chat-bubble-left-right'),
                NavigationGroup::make()
                    ->label('ওয়েবসাইট')
                    ->icon('heroicon-o-globe-alt'),
                NavigationGroup::make()
                    ->label('সেটিংস')
                    ->icon('heroicon-o-cog-8-tooth')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
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
            ]);
    }
}

