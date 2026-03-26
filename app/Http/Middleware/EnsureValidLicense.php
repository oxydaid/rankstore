<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use App\Services\MayarLicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidLicense
{
    public function __construct(protected MayarLicenseService $licenseService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        if (! config('services.mayar.license_enforce', true)) {
            return $next($request);
        }

        $settings = AppSetting::first();

        if (! $settings) {
            return response()->view('errors.invalid-license', [
                'reason' => 'Pengaturan lisensi belum dikonfigurasi.',
            ], 403);
        }

        $result = $this->licenseService->validateForSettings($settings);

        if (! ($result['active'] ?? false)) {
            return response()->view('errors.invalid-license', [
                'reason' => $result['message'] ?? 'Lisensi tidak valid atau belum diaktifkan.',
            ], 403);
        }

        return $next($request);
    }
}
