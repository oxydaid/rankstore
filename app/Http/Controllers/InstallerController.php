<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\User;
use App\Services\MayarLicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InstallerController extends Controller
{
    protected array $requiredExtensions = [
        'bcmath',
        'ctype',
        'fileinfo',
        'json',
        'mbstring',
        'openssl',
        'pdo',
        'tokenizer',
        'xml',
    ];

    public function requirements(): View|RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        return $this->renderStep('requirements', [
            'checks' => $this->requirementChecks(),
        ]);
    }

    public function storeRequirements(Request $request): RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        if (! $this->requirementsPassed()) {
            return back()->withErrors([
                'requirements' => 'Masih ada requirement server yang belum terpenuhi.',
            ]);
        }

        $request->session()->put('installer.requirements', true);

        return redirect()->route('installer.database');
    }

    public function database(Request $request): View|RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        if ($redirect = $this->guardStep($request, 'requirements', 'installer.requirements')) {
            return $redirect;
        }

        return $this->renderStep('database', [
            'defaults' => [
                'db_host' => env('DB_HOST', '127.0.0.1'),
                'db_port' => env('DB_PORT', '3306'),
                'db_database' => env('DB_DATABASE', ''),
                'db_username' => env('DB_USERNAME', ''),
                'db_password' => env('DB_PASSWORD', ''),
            ],
        ]);
    }

    public function storeDatabase(Request $request): RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        if ($redirect = $this->guardStep($request, 'requirements', 'installer.requirements')) {
            return $redirect;
        }

        $data = $request->validate([
            'db_host' => ['required', 'string', 'max:255'],
            'db_port' => ['required', 'integer'],
            'db_database' => ['required', 'string', 'max:255'],
            'db_username' => ['required', 'string', 'max:255'],
            'db_password' => ['nullable', 'string', 'max:255'],
        ]);

        $dbConfig = [
            'db_connection' => 'mariadb',
            'db_host' => $data['db_host'],
            'db_port' => (string) $data['db_port'],
            'db_database' => $data['db_database'],
            'db_username' => $data['db_username'],
            'db_password' => (string) ($data['db_password'] ?? ''),
        ];

        $this->applyDatabaseRuntimeConfig($dbConfig);

        $connection = $dbConfig['db_connection'];
        DB::purge($connection);

        try {
            DB::connection($connection)->getPdo();
        } catch (\Throwable $exception) {
            return back()->withInput()->withErrors([
                'database' => 'Koneksi database gagal: '.$exception->getMessage(),
            ]);
        }

        try {
            @set_time_limit(0);
            @ini_set('max_execution_time', '0');

            $command = $this->databaseHasAnyTables($connection) ? 'migrate:fresh' : 'migrate';
            Artisan::call($command, ['--force' => true]);
        } catch (\Throwable $exception) {
            return back()->withInput()->withErrors([
                'database' => 'Migration gagal dijalankan: '.$exception->getMessage(),
            ]);
        }

        $request->session()->put('installer.db_config', $dbConfig);
        $request->session()->put('installer.database', true);

        return redirect()->route('installer.app-settings');
    }

    public function appSettings(Request $request): View|RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        if ($redirect = $this->ensureDatabaseRuntimeFromSession($request)) {
            return $redirect;
        }

        if ($redirect = $this->guardStep($request, 'database', 'installer.database')) {
            return $redirect;
        }

        return $this->renderStep('app-settings', [
            'defaults' => [
                'app_url' => rtrim($request->getSchemeAndHttpHost() . $request->getBaseUrl(), '/'),
                'site_name' => 'Minecraft Store',
                'site_description' => '',
                'license_code' => '',
                'mayar_product_id' => config('services.mayar.product_id', ''),
                'server_ip' => '',
                'server_port' => '19132',
                'primary_color' => '#d97706',
                'secondary_color' => '#581c87',
            ],
        ]);
    }

    public function storeAppSettings(Request $request, MayarLicenseService $licenseService): RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        if ($redirect = $this->ensureDatabaseRuntimeFromSession($request)) {
            return $redirect;
        }

        if ($redirect = $this->guardStep($request, 'database', 'installer.database')) {
            return $redirect;
        }

        $data = $request->validate([
            'app_url' => ['required', 'url', 'max:255'],
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'license_code' => ['required', 'string', 'max:255'],
            'mayar_product_id' => ['required', 'string', 'max:255'],
            'server_ip' => ['nullable', 'string', 'max:255'],
            'server_port' => ['nullable', 'string', 'max:10'],
            'primary_color' => ['required', 'string', 'max:20'],
            'secondary_color' => ['required', 'string', 'max:20'],
        ]);

        if (! Schema::hasTable('app_settings')) {
            return back()->withInput()->withErrors([
                'app_settings' => 'Tabel app_settings belum tersedia. Pastikan migration berhasil.',
            ]);
        }

        $appUrl = rtrim($data['app_url'], '/');
        unset($data['app_url']);
        $request->session()->put('installer.app_url', $appUrl);

        $settings = AppSetting::firstOrCreate();
        $settings->fill($data);
        $settings->save();

        $result = $licenseService->validateForSettings($settings, true);

        if (! ($result['active'] ?? false)) {
            $request->session()->flash(
                'warning',
                ($result['message'] ?? 'Lisensi belum aktif.').
                    ' Instalasi tetap bisa dilanjutkan, namun storefront akan terkunci sampai lisensi aktif.'
            );
        }

        $request->session()->put('installer.app-settings', true);

        return redirect()->route('installer.admin-user');
    }

    public function adminUser(Request $request): View|RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        if ($redirect = $this->ensureDatabaseRuntimeFromSession($request)) {
            return $redirect;
        }

        if ($redirect = $this->guardStep($request, 'app-settings', 'installer.app-settings')) {
            return $redirect;
        }

        return $this->renderStep('admin-user', []);
    }

    public function storeAdminUser(Request $request): RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        if ($redirect = $this->ensureDatabaseRuntimeFromSession($request)) {
            return $redirect;
        }

        if ($redirect = $this->guardStep($request, 'app-settings', 'installer.app-settings')) {
            return $redirect;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::firstOrNew(['email' => $data['email']]);
        $user->name = $data['name'];
        $user->password = $data['password'];
        $user->save();

        $request->session()->put('installer.admin-user', true);

        return redirect()->route('installer.finish');
    }

    public function finish(Request $request): View|RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        if ($redirect = $this->ensureDatabaseRuntimeFromSession($request)) {
            return $redirect;
        }

        if ($redirect = $this->guardStep($request, 'admin-user', 'installer.admin-user')) {
            return $redirect;
        }

        return $this->renderStep('finish', []);
    }

    public function complete(Request $request): RedirectResponse
    {
        if ($this->alreadyInstalled()) {
            return redirect('/');
        }

        $dbConfig = $request->session()->get('installer.db_config');

        if (! is_array($dbConfig)) {
            return redirect()->route('installer.database')
                ->withErrors(['database' => 'Konfigurasi database installer tidak ditemukan, silakan ulangi step database.']);
        }

        if ($redirect = $this->guardStep($request, 'admin-user', 'installer.admin-user')) {
            return $redirect;
        }

        $this->writeEnv([
            'APP_URL' => (string) $request->session()->get('installer.app_url', rtrim($request->getSchemeAndHttpHost() . $request->getBaseUrl(), '/')),
            'DB_CONNECTION' => (string) ($dbConfig['db_connection'] ?? 'mariadb'),
            'DB_HOST' => (string) ($dbConfig['db_host'] ?? '127.0.0.1'),
            'DB_PORT' => (string) ($dbConfig['db_port'] ?? '3306'),
            'DB_DATABASE' => (string) ($dbConfig['db_database'] ?? ''),
            'DB_USERNAME' => (string) ($dbConfig['db_username'] ?? ''),
            'DB_PASSWORD' => (string) ($dbConfig['db_password'] ?? ''),
            'SESSION_DRIVER' => 'file',
            'CACHE_STORE' => 'file',
        ]);

        if (! is_dir(storage_path('app'))) {
            mkdir(storage_path('app'), 0755, true);
        }

        file_put_contents(storage_path('app/installed'), now()->toDateTimeString());

        $request->session()->forget([
            'installer.requirements',
            'installer.database',
            'installer.db_config',
            'installer.app-settings',
            'installer.admin-user',
        ]);

        return redirect('/admin/login')->with('success', 'Instalasi selesai. Silakan login sebagai admin.');
    }

    protected function renderStep(string $step, array $data): View
    {
        return view('installer.wizard', [
            'step' => $step,
            ...$data,
        ]);
    }

    protected function requirementChecks(): array
    {
        $checks = [
            'php' => [
                'label' => 'PHP >= 8.2',
                'ok' => version_compare(PHP_VERSION, '8.2.0', '>='),
            ],
        ];

        foreach ($this->requiredExtensions as $extension) {
            $checks['ext_'.$extension] = [
                'label' => 'Ekstensi PHP: '.$extension,
                'ok' => extension_loaded($extension),
            ];
        }

        return $checks;
    }

    protected function requirementsPassed(): bool
    {
        return collect($this->requirementChecks())->every(fn (array $check) => $check['ok'] === true);
    }

    protected function guardStep(Request $request, string $requiredStep, string $sessionKey): ?RedirectResponse
    {
        if (! $request->session()->get($sessionKey, false)) {
            return redirect()->route('installer.'.$requiredStep);
        }

        return null;
    }

    protected function writeEnv(array $values): void
    {
        $envPath = base_path('.env');
        $content = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($values as $key => $value) {
            $escaped = $this->escapeEnvValue((string) $value);

            if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $content)) {
                $content = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $key.'='.$escaped, $content);
            } else {
                $content .= PHP_EOL.$key.'='.$escaped;
            }
        }

        file_put_contents($envPath, trim($content).PHP_EOL);
    }

    protected function escapeEnvValue(string $value): string
    {
        if ($value === '') {
            return '""';
        }

        if (preg_match('/\s|#|"|\'/', $value)) {
            return '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }

    protected function alreadyInstalled(): bool
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

    protected function databaseHasAnyTables(string $connection = 'mysql'): bool
    {
        try {
            $tables = DB::connection($connection)->select('SHOW TABLES');

            return count($tables) > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function ensureDatabaseRuntimeFromSession(Request $request): ?RedirectResponse
    {
        $dbConfig = $request->session()->get('installer.db_config');

        if (! is_array($dbConfig)) {
            return redirect()->route('installer.database');
        }

        $this->applyDatabaseRuntimeConfig($dbConfig);

        return null;
    }

    protected function applyDatabaseRuntimeConfig(array $dbConfig): void
    {
        $connection = (string) ($dbConfig['db_connection'] ?? 'mariadb');

        Config::set('database.default', $connection);
        Config::set('database.connections.'.$connection.'.host', (string) ($dbConfig['db_host'] ?? '127.0.0.1'));
        Config::set('database.connections.'.$connection.'.port', (string) ($dbConfig['db_port'] ?? '3306'));
        Config::set('database.connections.'.$connection.'.database', (string) ($dbConfig['db_database'] ?? ''));
        Config::set('database.connections.'.$connection.'.username', (string) ($dbConfig['db_username'] ?? 'root'));
        Config::set('database.connections.'.$connection.'.password', (string) ($dbConfig['db_password'] ?? ''));

        // Keep runtime stores file-based while installer may recreate DB tables.
        Config::set('session.driver', 'file');
        Config::set('cache.default', 'file');
    }
}
