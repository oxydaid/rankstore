<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Deposit;
use App\Models\MigrationRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AriePulsaService
{
    protected string $baseUrl = 'https://ariepulsa.my.id/api/qrisrealtime';

    protected ?AppSetting $settings = null;

    protected function getSettings(): ?AppSetting
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        $this->settings = AppSetting::first();

        return $this->settings;
    }

    public function createTransaction(Model $data)
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->ariepulsa_api_key) {
            return ['success' => false, 'message' => 'API Key AriePulsa belum disetting.'];
        }

        $nominal = (int) $data->total_amount;

        // Deteksi Tipe Model
        $isMigration = $data instanceof MigrationRequest;

        $payload = [
            'api_key' => $settings->ariepulsa_api_key,
            'action' => 'get-deposit',
            'jumlah' => $nominal,
            'reff_id' => $data->uuid,
            'kode_channel' => 'QRISREALTIME',
        ];

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->retry(2, 300)
                ->post($this->baseUrl, $payload);
            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == true) {
                $resData = $result['data'];

                // Simpan ke Deposit sesuai tipe model
                $depositData = [
                    'kode_deposit' => $resData['kode_deposit'],
                    'metode' => $resData['metode'] ?? 'QRISREALTIME',
                    'nominal_request' => $resData['nominal'] ?? $nominal,
                    'admin_fee' => $resData['fee'] ?? 0,
                    'total_bayar' => $resData['jumlah_transfer'],
                    'qr_link' => $resData['link_qr'],
                    'status' => 'Pending',
                ];

                if ($isMigration) {
                    $depositData['migration_request_id'] = $data->id;
                } else {
                    $depositData['order_id'] = $data->id;
                }

                $deposit = Deposit::create($depositData);

                return [
                    'success' => true,
                    'payment_url' => $resData['link_qr'],
                    'total_bayar' => $resData['jumlah_transfer'],
                    'trx_id' => $resData['kode_deposit'],
                    'is_direct_image' => true,
                ];

            } else {
                $msg = $result['data']['pesan'] ?? 'Gagal request ke AriePulsa.';
                Log::error('AriePulsa API Error: '.json_encode($result));

                return ['success' => false, 'message' => $msg];
            }

        } catch (\Exception $e) {
            Log::error('AriePulsa Exception: '.$e->getMessage());

            return ['success' => false, 'message' => 'Koneksi ke server AriePulsa gagal.'];
        }
    }

    public function handleCallback(array $data)
    {
        $status = $data['status'] ?? false;
        $callbackData = $data['data'] ?? $data;

        if (! $status || ! isset($callbackData['kode_deposit'])) {
            return ['status' => false, 'message' => 'Data callback tidak valid'];
        }

        $kodeDeposit = $callbackData['kode_deposit'];
        $statusTrx = $callbackData['status'] ?? '';

        // 1. Cari Deposit
        $deposit = Deposit::where('kode_deposit', $kodeDeposit)->first();

        if (! $deposit) {
            Log::error("AriePulsa Callback: Deposit not found for kode: $kodeDeposit");

            return ['status' => false, 'message' => 'Deposit not found'];
        }

        // 2. Update Status
        if ($statusTrx === 'Success') {
            // Update Deposit
            $deposit->update(['status' => 'Success']);

            // CEK APAKAH INI ORDER ATAU MIGRATION
            if ($deposit->order_id) {
                // Update Order
                $order = $deposit->order;
                if ($order && $order->status !== 'completed') {
                    $order->update([
                        'status' => 'processing',
                        'notes' => "Lunas via AriePulsa (Kode: $kodeDeposit)",
                        'payment_url' => $deposit->qr_link,
                    ]);
                }
            } elseif ($deposit->migration_request_id) {
                // Update Migration Request
                $migration = $deposit->migrationRequest;
                if ($migration && $migration->status !== 'completed') {
                    $migration->update([
                        'status' => 'processing',
                        'notes' => "Lunas via AriePulsa (Kode: $kodeDeposit)",
                        'payment_url' => $deposit->qr_link,
                    ]);
                }
            }

        } elseif ($statusTrx === 'Gagal' || $statusTrx === 'Expired') {
            $deposit->update(['status' => 'Failed']);
            // Opsional: Update status order/migration jadi cancelled
            if ($deposit->order_id) {
                $deposit->order()->update(['status' => 'cancelled', 'notes' => 'Pembayaran Gagal/Expired (AriePulsa)']);
            } elseif ($deposit->migration_request_id) {
                $deposit->migrationRequest()->update(['status' => 'cancelled', 'notes' => 'Pembayaran Gagal/Expired (AriePulsa)']);
            }
        }

        return ['status' => true];
    }

    public function checkStatus(Deposit $deposit)
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->ariepulsa_api_key) {
            return ['status' => false, 'message' => 'API Key AriePulsa belum disetting.'];
        }

        // Payload Cek Status (Asumsi)
        $payload = [
            'api_key' => $settings->ariepulsa_api_key,
            'action' => 'status-deposit', // Mencoba action ini
            'kode_deposit' => $deposit->kode_deposit, // Biasanya butuh kode deposit
        ];

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->retry(2, 300)
                ->post($this->baseUrl, $payload);
            $result = $response->json();

            // Jika API merespon status transaksi
            if ($response->successful() && isset($result['status']) && $result['status'] == true) {
                // Gunakan logika yang sama dengan handleCallback
                return $this->handleCallback($result);
            }

            return ['status' => false, 'message' => 'Status masih pending atau belum ada respon update.'];

        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Gagal cek status: '.$e->getMessage()];
        }
    }
}
