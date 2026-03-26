<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        if ($request->routeIs('installer.*') || $this->isInstalled()) {
            return $next($request);
        }

        return redirect()->route('installer.requirements');
    }

    protected function isInstalled(): bool
    {
        if (file_exists(storage_path('app/installed'))) {
            return true;
        }

        try {
            if (! Schema::hasTable('app_settings') || ! Schema::hasTable('users')) {
                return false;
            }

            return AppSetting::query()->exists() && User::query()->exists();
        } catch (\Throwable) {
            return false;
        }
    }
}
