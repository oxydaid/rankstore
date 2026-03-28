<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // View Composer: Hanya berjalan saat 'components.layouts.app' dirender
        View::composer('components.layouts.app', function ($view) {
            $settings = null;

            try {
                // Cek Database (Aman dari error migrate fresh dan koneksi database putus)
                if (Schema::hasTable('app_settings')) {
                    $settings = AppSetting::first();
                }
            } catch (\Exception $e) {
                // Ignore error, $settings will be null
            }

            // Siapkan variable siap pakai (Logic dipindah ke sini)
            $view->with([
                'settings' => $settings,
                'siteName' => $settings?->site_name ?? 'Oxyda Store',
                'primary' => $settings?->primary_color ?? '#d97706',
                'secondary' => $settings?->secondary_color ?? '#4f46e5',
                'logo' => ($settings?->logo) ? asset('img/'.$settings->logo) : null,
                'heroBackground' => ($settings?->hero_background)
                    ? asset('img/'.$settings->hero_background)
                    : 'https://images.unsplash.com/photo-1697479670670-d2a299df749c?q=80&w=2000&auto=format&fit=crop', // Fallback ke gambar default jika kosong
                'serverIp' => $settings?->server_ip ?? 'play.prownetwork.com',
                'serverPort' => $settings?->server_port ?? '19132',
            ]);
        });
    }
}
