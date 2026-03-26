<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Models\AppSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession; // 1. Import Facade Schema

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $primaryColor = Color::Amber;
        $faviconUrl = 'favicon.ico';
        $logoUrl = 'logo.png';
        $siteName = 'Oxyda Prow';

        try {
            if (Schema::hasTable('app_settings')) {
                $settings = AppSetting::first();

                if ($settings) {
                    // Jika user set warna, gunakan Color::hex() agar support string '#ffffff'
                    if ($settings->primary_color) {
                        $primaryColor = Color::hex($settings->primary_color);
                    }

                    // Setup gambar (pastikan path img benar)
                    if ($settings->favicon) {
                        $faviconUrl = 'img/'.$settings->favicon;
                    }

                    if ($settings->logo) {
                        $logoUrl = 'img/'.$settings->logo;
                    }

                    if ($settings->site_name) {
                        $siteName = $settings->site_name;
                    }
                }
            }
        } catch (\Exception $e) {

        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => $primaryColor, // Gunakan variabel yang sudah diproses
            ])
            ->profile(EditProfile::class)
            ->favicon(asset($faviconUrl))
            ->brandLogo(asset($logoUrl))
            ->brandLogoHeight('32px')
            ->brandName($siteName)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
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
            ]);
    }
}
