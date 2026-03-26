<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    protected string $endpoint = 'https://wa.oxyda.id/send-message';

    protected ?AppSetting $settings = null;

    protected function getSettings(): ?AppSetting
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        $this->settings = AppSetting::first();

        return $this->settings;
    }

    /**
     * Fungsi Inti Pengiriman Pesan (Private)
     */
    protected function sendMessage($targetNumber, $message)
    {
        $settings = $this->getSettings();

        if (! $settings || empty($settings->wa_api_key) || empty($settings->wa_sender_number)) {
            Log::warning('WhatsApp API Key atau Sender Number belum disetting.');

            return;
        }

        $targetNumber = $this->formatNumber($targetNumber);

        $payload = [
            'api_key' => $settings->wa_api_key,
            'sender' => $settings->wa_sender_number,
            'number' => $targetNumber,
            'message' => $message,
            'footer' => $settings->site_name ?? 'Oxyda Store',
        ];

        try {
            $response = Http::timeout(10)
                ->retry(2, 300)
                ->post($this->endpoint, $payload);
            if ($response->failed()) {
                Log::error('Gagal kirim WA ke '.$targetNumber.': '.$response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error koneksi WA Gateway: '.$e->getMessage());
        }
    }

    protected function formatNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '08')) {
            return '62'.substr($number, 1);
        }

        return $number;
    }

    // =========================================================================
    // NOTIFIKASI UNTUK PEMBELI (CUSTOMER)
    // =========================================================================

    public function sendOrderCreated(Model $data)
    {
        $settings = $this->getSettings();
        $total = number_format($data->total_amount, 0, ',', '.');
        $isMigration = $data instanceof \App\Models\MigrationRequest;

        $data->loadMissing(['paymentMethod', 'rank']);

        if ($isMigration) {
            // --- TEMPLATE MIGRASI ---
            $message = "*REQUEST MIGRASI DIBUAT* 🔄\n\n";
            $message .= "Halo,\n";
            $message .= "Permintaan migrasi rank Anda telah kami terima.\n\n";
            $message .= "📝 *Detail Migrasi:*\n";
            $message .= "• Ref ID: `{$data->uuid}`\n";
            $message .= "• Rank: {$data->rank->name}\n";
            $message .= "• Dari: {$data->old_gamertag}\n";
            $message .= "• Ke: *{$data->new_gamertag}*\n";
            $message .= "• Biaya Jasa: *Rp {$total}*\n\n";
            $message .= "Silakan cek pembayaran melalui link berikut:\n";
            $message .= route('migration.detail', ['uuid' => $data->uuid]);
        } else {
            // --- TEMPLATE BELI RANK ---
            $rankName = $data->rank->name.($data->is_upgrade ? ' (Upgrade)' : '');
            $message = "*PESANAN DIBUAT* 🛒\n\n";
            $message .= "Halo *{$data->gamertag}*,\n";
            $message .= "Terima kasih sudah memesan di {$settings->site_name}.\n\n";
            $message .= "📝 *Detail Pesanan:*\n";
            $message .= "• Order ID: `{$data->uuid}`\n";
            $message .= "• Item: {$rankName}\n";
            $message .= "• Total: *Rp {$total}*\n";
            $message .= "• Metode: {$data->paymentMethod->name}\n\n";
            $message .= "Cek status pesanan Anda di sini:\n";
            $message .= route('tracking.detail', ['uuid' => $data->uuid]);
        }

        $this->sendMessage($data->whatsapp_number, $message);
    }

    public function sendOrderProcessing(Model $data)
    {
        $isMigration = $data instanceof \App\Models\MigrationRequest;

        if ($isMigration) {
            $message = "*MIGRASI DALAM PROSES* ⏳\n\n";
            $message .= "Halo,\n";
            $message .= "Permintaan migrasi rank Anda sedang kami proses.\n\n";
            $message .= "Kami akan mengabari Anda kembali setelah proses selesai.\n";
            $message .= "Terima kasih atas kesabaran Anda\n\n.";
            $message .= "Cek status pesanan Anda di sini:\n";
            $message .= route('tracking.detail', ['uuid' => $data->uuid]);
        } else {
            $message = "*PEMBELIAN DALAM PROSES* ⏳\n\n";
            $message .= "Halo *{$data->gamertag}*,\n";
            $message .= "Pesanan rank Anda sedang kami proses.\n\n";
            $message .= "Kami akan mengabari Anda kembali setelah rank aktif.\n";
            $message .= "Terima kasih atas kesabaran Anda.\n\n";
            $message .= "Cek status pesanan Anda di sini:\n";
            $message .= route('tracking.detail', ['uuid' => $data->uuid]);
        }

        $this->sendMessage($data->whatsapp_number, $message);
    }

    public function sendOrderSuccess(Model $data)
    {
        $isMigration = $data instanceof \App\Models\MigrationRequest;

        if ($isMigration) {
            $message = "*MIGRASI SELESAI* ✅\n\n";
            $message .= "Halo,\n";
            $message .= "Proses pemindahan rank dari akun *{$data->old_gamertag}* ke *{$data->new_gamertag}* telah SUKSES!\n\n";
            $message .= "Silakan cek akun baru Anda sekarang.\n";
            $message .= 'Terima kasih telah menggunakan jasa kami.';
        } else {
            $message = "*PEMBELIAN SELESAI* ✅\n\n";
            $message .= "Halo *{$data->gamertag}*,\n";
            $message .= "Rank sudah aktif!\n\n";
            $message .= "Invoice server dapat didownload pada halaman tracking.\n";
            $message .= 'Selamat bermain!';
        }

        $this->sendMessage($data->whatsapp_number, $message);
    }

    public function sendOrderFailed(Model $data)
    {
        $isMigration = $data instanceof \App\Models\MigrationRequest;
        $id = $data->uuid;

        $header = $isMigration ? '*MIGRASI DIBATALKAN* ❌' : '*PEMBELIAN DIBATALKAN* ❌';

        $message = "{$header}\n\n";
        $message .= "Mohon maaf, transaksi dengan ID `{$id}` telah dibatalkan.\n\n";
        if ($data->notes) {
            $message .= "⚠️ *Alasan:* {$data->notes}\n\n";
        }
        $message .= 'Hubungi Admin jika ini kesalahan.';

        $this->sendMessage($data->whatsapp_number, $message);
    }

    // =========================================================================
    // NOTIFIKASI UNTUK ADMIN
    // =========================================================================

    public function sendAdminNotification(Model $data)
    {
        $settings = $this->getSettings();

        if (empty($settings->admin_phone)) {
            return;
        }

        $targetAdmin = $settings->admin_phone;
        $total = number_format($data->total_amount, 0, ',', '.');
        $isMigration = $data instanceof \App\Models\MigrationRequest;

        $data->loadMissing(['rank']);

        if ($isMigration) {
            $message = "*NEW MIGRATION REQUEST!* 🔄\n\n";
            $message .= "User: {$data->old_gamertag} -> {$data->new_gamertag}\n";
            $message .= "Rank: {$data->rank->name}\n";
            $message .= "Fee: Rp {$total}\n\n";
            $message .= 'Segera cek panel admin!';
        } else {
            $type = $data->is_upgrade ? 'UPGRADE' : 'BARU';
            $message = "*NEW ORDER MASUK!* 🛒\n\n";
            $message .= "Tipe: *{$type}*\n";
            $message .= "User: {$data->gamertag}\n";
            $message .= "Item: {$data->rank->name}\n";
            $message .= "Total: Rp {$total}\n\n";
            $message .= 'Segera cek panel admin!';
        }

        $this->sendMessage($targetAdmin, $message);
    }
}
