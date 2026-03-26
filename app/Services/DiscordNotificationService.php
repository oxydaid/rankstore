<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordNotificationService
{
    protected ?AppSetting $settings = null;

    protected function getSettings(): ?AppSetting
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        $this->settings = AppSetting::first();

        return $this->settings;
    }

    public function sendNewOrderNotification(Model $data)
    {
        // 1. Ambil Webhook URL dari Database
        $settings = $this->getSettings();
        $webhookUrl = $settings?->discord_webhook;

        if (empty($webhookUrl)) {
            return;
        }

        // 2. Deteksi Tipe (Order vs Migrasi)
        $isMigration = $data instanceof \App\Models\MigrationRequest;

        if ($isMigration) {
            $data->loadMissing(['paymentMethod', 'rank', 'category']);
        } else {
            $data->loadMissing(['paymentMethod', 'rank']);
        }

        // 3. Siapkan Data Umum
        $refId = $data->uuid;
        $price = 'Rp '.number_format($data->total_amount, 0, ',', '.');
        $paymentMethod = $data->paymentMethod->name;
        $whatsappLink = "[Chat WA](https://wa.me/{$data->whatsapp_number})";

        // Admin Tag (Dari AppSetting)
        $adminTag = $settings->discord_admin_id ? "<@{$settings->discord_admin_id}>" : 'Admin';

        // 4. Logic Percabangan Konten
        if ($isMigration) {
            // --- FORMAT MIGRASI ---
            $title = '🔄 REQUEST MIGRASI BARU!';
            $color = 10181046; // Ungu (Purple)
            $description = "Ada permintaan pemindahan rank dari **{$data->old_gamertag}**.";
            $adminUrl = url("/admin/migration-requests/{$data->id}/edit"); // Sesuaikan route admin

            $fields = [
                ['name' => '🆔 Ref ID', 'value' => "`{$refId}`", 'inline' => true],
                ['name' => '💰 Biaya Jasa', 'value' => "**{$price}**", 'inline' => true],
                ['name' => '💳 Metode', 'value' => $paymentMethod, 'inline' => true],
                ['name' => '📤 Dari Akun (Lama)', 'value' => "`{$data->old_gamertag}`", 'inline' => true],
                ['name' => '📥 Ke Akun (Baru)', 'value' => "`{$data->new_gamertag}`", 'inline' => true],
                ['name' => '💎 Rank Dipindah', 'value' => $data->rank->name." ({$data->category->name})", 'inline' => true],
                ['name' => '📱 Kontak', 'value' => $whatsappLink, 'inline' => true],
            ];

            $imageUrl = null; // Migrasi mungkin tidak butuh gambar rank besar

        } else {
            // --- FORMAT ORDER BELI RANK ---
            $isUpgrade = $data->is_upgrade;
            $title = $isUpgrade ? '⚡ PESANAN UPGRADE BARU!' : '🛒 PESANAN RANK BARU!';
            $color = $isUpgrade ? 16753920 : 5763719; // Orange (Upgrade) / Hijau (New)
            $description = "Detail pesanan dari **{$data->gamertag}**.";
            $adminUrl = url("/admin/orders/{$data->id}/edit");

            $fields = [
                ['name' => '🆔 Order ID', 'value' => "`{$refId}`", 'inline' => true],
                ['name' => '👤 Gamertag', 'value' => "**{$data->gamertag}**", 'inline' => true],
                ['name' => '💎 Item', 'value' => $data->rank->name.($isUpgrade ? ' (Upgrade)' : ''), 'inline' => true],
                ['name' => '💰 Total Bayar', 'value' => "**{$price}**", 'inline' => true],
                ['name' => '💳 Metode', 'value' => $paymentMethod, 'inline' => true],
                ['name' => '📱 Kontak', 'value' => $whatsappLink, 'inline' => true],
            ];

            $imageUrl = $data->rank->image ? asset('img/'.$data->rank->image) : null;
        }

        // 5. Susun Payload Akhir
        $payload = [
            'username' => $settings->site_name ?? 'Oxyda Bot',
            'avatar_url' => $settings->logo ? asset('img/'.$settings->logo) : null,
            'content' => "Halo {$adminTag}, ada transaksi baru masuk! Segera cek ya. 🚀",
            'embeds' => [
                [
                    'title' => $title,
                    'url' => $adminUrl,
                    'description' => $description,
                    'color' => $color,
                    'fields' => $fields,
                    'footer' => [
                        'text' => 'Menunggu Konfirmasi • '.now()->format('d M Y H:i'),
                    ],
                    'thumbnail' => [
                        'url' => $imageUrl,
                    ],
                ],
            ],
        ];

        // 6. Kirim Request
        try {
            Http::timeout(8)
                ->retry(1, 250)
                ->post($webhookUrl, $payload);
        } catch (\Exception $e) {
            Log::error('Gagal kirim notif Discord: '.$e->getMessage());
        }
    }
}
