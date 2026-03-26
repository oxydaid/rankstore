<?php

namespace App\Livewire;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\PromoCode;
use App\Models\Rank;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Title; // Import HTTP Client
use Livewire\Component;

#[Title('Home - Oxyda Store')]
class HomePage extends Component
{
    public $activeCategory = '';

    public $settings;

    // Properti Status Server
    public $isOnline = false;

    public $playerCount = 0;

    public $maxPlayers = 0;

    public $serverVersion = '-';

    public int $paymentMethodTotalCount = 0;

    public function mount()
    {
        $this->settings = AppSetting::first();

        // Ambil status saat pertama kali load
        $this->fetchServerStatus();

        $firstCategory = Category::where('is_active', true)->first();
        if ($firstCategory) {
            $this->activeCategory = $firstCategory->slug;
        }
    }

    // Method untuk cek status ke API (Dipanggil saat mount & polling)
    public function fetchServerStatus()
    {
        try {
            // Pakai settings dari mount agar tidak query ulang tiap polling.
            $settings = $this->settings;

            if (! $settings) {
                $settings = AppSetting::first();
                $this->settings = $settings;
            }

            if ($settings && $settings->server_ip) {
                $ip = $settings->server_ip;
                $port = $settings->server_port ?? '19132'; // Default port bedrock
                $cacheKey = "server-status:{$ip}:{$port}";

                $statusData = Cache::remember($cacheKey, now()->addSeconds(20), function () use ($ip, $port) {
                    // -----------------------------------------------------------
                    // 1. PERCOBAAN PERTAMA: BEDROCK
                    // -----------------------------------------------------------
                    $url = "https://api.mcsrvstat.us/bedrock/3/{$ip}:{$port}";
                    $response = Http::timeout(3)->get($url);
                    $data = $response->successful() ? $response->json() : [];
                    $isOnline = $data['online'] ?? false;

                    // -----------------------------------------------------------
                    // 2. PERCOBAAN KEDUA: JAVA (FALLBACK)
                    // -----------------------------------------------------------
                    if (! $isOnline) {
                        $urlJava = "https://api.mcsrvstat.us/3/{$ip}:{$port}";
                        $response = Http::timeout(3)->get($urlJava);
                        $data = $response->successful() ? $response->json() : [];

                        $isOnline = $data['online'] ?? false;
                    }

                    return [
                        'is_online' => $isOnline,
                        'player_count' => $isOnline ? ($data['players']['online'] ?? 0) : 0,
                        'max_players' => $isOnline ? ($data['players']['max'] ?? 0) : 0,
                        'server_version' => $isOnline ? ($data['version'] ?? 'Unknown') : 'Maintenance',
                    ];
                });

                $this->isOnline = $statusData['is_online'];
                $this->playerCount = $statusData['player_count'];
                $this->maxPlayers = $statusData['max_players'];
                $this->serverVersion = $statusData['server_version'];
            }
        } catch (\Exception $e) {
            $this->isOnline = false;
        }
    }

    public function setCategory($slug)
    {
        $this->activeCategory = $slug;
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $ranks = Rank::query()
            ->where('is_active', true)
            ->whereHas('category', function ($q) {
                $q->where('slug', $this->activeCategory);
            })
            ->with('category')
            ->orderBy('price', 'asc')
            ->get();

        $promoCodes = PromoCode::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();

        $paymentMethods = PaymentMethod::where('is_active', true)
            ->limit(3) // Batasi 3 saja
            ->get();

        $this->paymentMethodTotalCount = PaymentMethod::where('is_active', true)->count();

        return view('livewire.home-page', [
            'categories' => $categories,
            'ranks' => $ranks,
            'promoCodes' => $promoCodes,
            'paymentMethods' => $paymentMethods,
            'paymentMethodTotalCount' => $this->paymentMethodTotalCount,
        ]);
    }
}
