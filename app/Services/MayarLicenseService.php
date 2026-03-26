<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MayarLicenseService
{
    public function validateForSettings(AppSetting $settings, bool $forceOnline = false): array
    {
        $licenseCode = trim((string) ($settings->license_code ?: env('LICENSE_CODE')));
        $productId = trim((string) ($settings->mayar_product_id ?: config('services.mayar.product_id')));

        if ($licenseCode === '' || $productId === '') {
            return $this->updateSettingStatus($settings, [
                'active' => false,
                'status' => 'missing_config',
                'message' => 'License code atau product ID belum diisi.',
            ]);
        }

        $cacheMinutes = (int) config('services.mayar.cache_minutes', 10);
        $cacheKey = 'license:mayar:'.sha1($licenseCode.'|'.$productId);

        if ($forceOnline) {
            Cache::forget($cacheKey);
            $result = $this->verifyOnline($licenseCode, $productId);
        } else {
            $result = Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), function () use ($licenseCode, $productId) {
                return $this->verifyOnline($licenseCode, $productId);
            });
        }

        if (! Arr::get($result, 'active', false) && $this->canUseGracePeriod($settings)) {
            $result = [
                'active' => true,
                'status' => 'grace_period',
                'message' => 'Layanan validasi lisensi sedang bermasalah, menggunakan grace period sementara.',
                'payload' => $settings->license_verified_payload,
                'expires_at' => $settings->license_expires_at,
            ];
        }

        return $this->updateSettingStatus($settings, $result);
    }

    public function activate(string $licenseCode, string $productId): array
    {
        return $this->performSaaSAction('activate', $licenseCode, $productId);
    }

    public function deactivate(string $licenseCode, string $productId): array
    {
        return $this->performSaaSAction('deactivate', $licenseCode, $productId);
    }

    protected function performSaaSAction(string $action, string $licenseCode, string $productId): array
    {
        $result = $this->sendRequest($action, $licenseCode, $productId);

        if ($result['status'] === 'missing_api_credentials') {
            return [
                'success' => false,
                'message' => $result['message'],
            ];
        }

        if (! $result['ok']) {
            return [
                'success' => false,
                'message' => "Operasi {$action} lisensi gagal. HTTP {$result['http_status']}.",
                'payload' => $result['json'],
            ];
        }

        $json = $result['json'];
        $apiMessage = Arr::get($json, 'message') ?: Arr::get($json, 'messages');

        return [
            'success' => true,
            'message' => $apiMessage ?: "Operasi {$action} lisensi berhasil.",
            'payload' => $json,
        ];
    }

    protected function verifyOnline(string $licenseCode, string $productId): array
    {
        $result = $this->sendRequest('verify', $licenseCode, $productId);

        if ($result['status'] === 'missing_api_credentials') {
            return [
                'active' => false,
                'status' => 'missing_api_credentials',
                'message' => $result['message'],
            ];
        }

        if ($result['status'] === 'request_failed') {
            return [
                'active' => false,
                'status' => 'request_failed',
                'message' => 'Tidak dapat terhubung ke server verifikasi lisensi.',
            ];
        }

        if (! $result['ok']) {
            return [
                'active' => false,
                'status' => 'http_error',
                'message' => 'Validasi lisensi gagal. HTTP '.$result['http_status'].'.',
                'payload' => $result['json'],
            ];
        }

        $json = $result['json'];
        $isActive = (bool) Arr::get($json, 'isLicenseActive', false);
        $expiresAt = $this->parseMayarDate(Arr::get($json, 'licenseCode.expiredAt'));
        $licenseStatus = Arr::get($json, 'licenseCode.status');
        $apiMessage = Arr::get($json, 'messages') ?: Arr::get($json, 'message');

        if (! $isActive) {
            Log::info('Mayar license reported inactive.', [
                'product_id' => $productId,
                'license_status' => $licenseStatus,
                'api_message' => $apiMessage,
                'response' => $json,
            ]);
        }

        return [
            'active' => $isActive,
            'status' => $isActive ? 'active' : 'inactive',
            'message' => $isActive
                ? 'Lisensi aktif.'
                : $this->buildInactiveMessage($licenseStatus, $apiMessage),
            'payload' => $json,
            'expires_at' => $expiresAt,
        ];
    }

    protected function sendRequest(string $action, string $licenseCode, string $productId): array
    {
        $apiKey = config('services.mayar.api_key');
        $verifyUrl = config('services.mayar.license_verify_url', 'https://api.mayar.id/software/v1/license/verify');

        if (! $apiKey || ! $verifyUrl) {
            return [
                'ok' => false,
                'status' => 'missing_api_credentials',
                'message' => 'MAYAR_API_KEY atau endpoint (license_verify_url) belum dikonfigurasi.',
            ];
        }

        // Adjust endpoint for SaaS actions based on the mapped base URL
        $actionUrl = $verifyUrl;
        if ($action !== 'verify') {
            if (str_contains($verifyUrl, '/software/v1/license/verify')) {
                $actionUrl = str_replace('/software/v1/license/verify', "/saas/v1/license/{$action}", $verifyUrl);
            } else {
                $actionUrl = str_replace('/verify', "/{$action}", $verifyUrl);
            }
        }

        try {
            $response = Http::asJson()
                ->timeout(12)
                ->acceptJson()
                ->withToken($apiKey)
                ->post($actionUrl, [
                    'licenseCode' => $licenseCode,
                    'productId' => $productId,
                ]);

            return [
                'ok' => $response->ok(),
                'status' => 'success',
                'http_status' => $response->status(),
                'json' => $response->json() ?? [],
            ];
        } catch (\Throwable $exception) {
            Log::warning("Mayar license request for action [{$action}] failed.", [
                'message' => $exception->getMessage(),
            ]);

            return [
                'ok' => false,
                'status' => 'request_failed',
                'message' => $exception->getMessage(),
            ];
        }
    }

    protected function parseMayarDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function buildInactiveMessage(?string $licenseStatus, mixed $apiMessage): string
    {
        $segments = ['Lisensi tidak aktif.'];

        if (is_string($licenseStatus) && trim($licenseStatus) !== '') {
            $segments[] = 'Status Mayar: '.strtoupper($licenseStatus).'.';
        }

        if (is_string($apiMessage) && trim($apiMessage) !== '') {
            $segments[] = 'Pesan API: '.trim($apiMessage).'.';
        }

        return implode(' ', $segments);
    }

    protected function canUseGracePeriod(AppSetting $settings): bool
    {
        if ($settings->license_status !== 'active' || ! $settings->license_last_checked_at) {
            return false;
        }

        $graceHours = (int) config('services.mayar.grace_hours', 24);

        return $settings->license_last_checked_at->addHours($graceHours)->isFuture();
    }

    protected function updateSettingStatus(AppSetting $settings, array $result): array
    {
        $settings->forceFill([
            'license_status' => Arr::get($result, 'status', 'inactive'),
            'license_last_checked_at' => now(),
            'license_expires_at' => Arr::get($result, 'expires_at'),
            'license_verified_payload' => Arr::get($result, 'payload'),
        ])->save();

        return $result;
    }
}
