<?php

namespace App\Providers;

use App\Models\MigrationRequest;
use App\Models\Order;
use App\Observers\MigrationRequestObserver;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $envPath = base_path('.env');
        
        if (! file_exists($envPath)) {
            $envExamplePath = base_path('.env.example');
            if (file_exists($envExamplePath)) {
                copy($envExamplePath, $envPath);
            } else {
                file_put_contents($envPath, '');
            }
        }

        if (empty(config('app.key'))) {
            $key = 'base64:' . base64_encode(random_bytes(32));
            $content = file_exists($envPath) ? file_get_contents($envPath) : '';
            
            if (preg_match('/^APP_KEY=/m', $content)) {
                $content = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $content);
            } else {
                $content = rtrim($content) . "\nAPP_KEY=" . $key . "\n";
            }
            
            file_put_contents($envPath, $content);
            config(['app.key' => $key]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        MigrationRequest::observe(MigrationRequestObserver::class);
    }
}
