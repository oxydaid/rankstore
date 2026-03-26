<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\MigrationRequest;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokopayService
{
    protected string $baseUrl = 'https://api.tokopay.id';

    protected ?AppSetting $settings = null;

    protected function getSettings(): ?AppSetting
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        $this->settings = AppSetting::first();

        return $this->settings;
    }

    public function createOrder(Model $data)
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->tokopay_merchant_id || ! $settings->tokopay_secret_key) {
            throw new \Exception('Konfigurasi Tokopay belum diisi.');
        }

        $data->loadMissing(['rank', 'paymentMethod']);

        $merchantId = $settings->tokopay_merchant_id;
        $secretKey = $settings->tokopay_secret_key;

        $prefix = $settings->ref_id_prefix ?? 'TRX';

        // Deteksi Tipe Model (Order vs Migration)
        $isMigration = $data instanceof \App\Models\MigrationRequest;

        // Mapping Data
        $refId = $prefix.'-'.($isMigration ? 'MIG-' : '').$data->id;
        $gamertag = $isMigration ? $data->old_gamertag : $data->gamertag;
        $itemName = $isMigration ? 'JASA MIGRASI: '.$data->rank->name : $data->rank->name;
        $redirectUrl = $isMigration
            ? route('migration.detail', ['uuid' => $data->uuid])
            : route('tracking.detail', ['uuid' => $data->uuid]);

        $kodeChannel = $data->paymentMethod->code;
        $amount = (int) $data->total_amount;

        $signature = md5($merchantId.':'.$secretKey.':'.$refId);

        $payload = [
            'merchant_id' => $merchantId,
            'kode_channel' => $kodeChannel,
            'reff_id' => $refId,
            'amount' => $amount,
            'customer_name' => $gamertag,
            'customer_email' => 'customer@prow.oxyda.id',
            'customer_phone' => $data->whatsapp_number,
            'redirect_url' => $redirectUrl,
            'callback_url' => route('api.callback.tokopay'),
            'expired_ts' => 0,
            'signature' => $signature,
            'items' => [
                [
                    'product_code' => ($isMigration ? 'MIG-' : 'RANK-').$data->rank->id,
                    'name' => $itemName,
                    'price' => $amount,
                    'product_url' => route('shop'), // Optional
                    'image_url' => $data->rank->image ? asset('img/'.$data->rank->image) : null,
                ],
            ],
        ];

        try {
            $response = Http::timeout(15)
                ->retry(2, 300)
                ->post("$this->baseUrl/v1/order", $payload);
            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === 'Success') {
                $resData = $result['data'];

                return [
                    'success' => true,
                    'pay_url' => $resData['pay_url'] ?? $resData['checkout_url'],
                    'trx_id' => $resData['trx_id'],
                    'ref_id' => $refId,
                ];
            } else {
                $errorMsg = $result['error_msg'] ?? ($result['message'] ?? 'Gagal request ke Gateway.');
                Log::error('Tokopay Error: '.json_encode($result));

                return ['success' => false, 'message' => $errorMsg];
            }

        } catch (\Exception $e) {
            Log::error('Tokopay Exception: '.$e->getMessage());

            return ['success' => false, 'message' => 'Koneksi Error.'];
        }
    }

    public function handleCallback(array $data)
    {
        $settings = $this->getSettings();

        if (! $settings || ! $settings->tokopay_secret_key) {
            return ['status' => false, 'message' => 'Konfigurasi Tokopay belum diisi.'];
        }

        $secretKey = $settings->tokopay_secret_key;

        // 1. Parsing Data
        $merchantId = $data['data']['merchant_id'] ?? ($data['merchant_id'] ?? null);
        $trxId = $data['reference'] ?? null;
        $refId = $data['reff_id'] ?? null;
        $status = $data['status'] ?? null;
        $signature = $data['signature'] ?? null;

        if (! $merchantId || ! $refId || ! $signature) {
            return ['status' => false, 'message' => 'Data callback tidak lengkap'];
        }

        // 2. Validasi Signature
        $localSignature = md5($merchantId.':'.$secretKey.':'.$refId);

        if ($signature !== $localSignature) {
            Log::warning("Tokopay Invalid Signature. Ref: $refId");

            return ['status' => false, 'message' => 'Invalid Signature'];
        }

        // 3. Cari Transaksi (Polimorfik Manual)
        // Cek apakah ini Migrasi (Ada string 'MIG-' di Ref ID)
        $isMigrationRef = str_contains($refId, 'MIG-');
        $transaction = null;

        if ($isMigrationRef) {
            // CARI DI MIGRATION REQUEST
            $transaction = MigrationRequest::where('trx_id', $trxId)->first();

            // Fallback by ID
            if (! $transaction) {
                $parts = explode('-', $refId);
                $id = end($parts);
                if (is_numeric($id)) {
                    $transaction = MigrationRequest::find($id);
                }
            }
        } else {
            // CARI DI ORDER BIASA
            $transaction = Order::where('trx_id', $trxId)->first();

            // Fallback by ID
            if (! $transaction) {
                $parts = explode('-', $refId);
                $id = end($parts);
                if (is_numeric($id)) {
                    $transaction = Order::find($id);
                }
            }
        }

        if (! $transaction) {
            Log::error("Tokopay Callback: Transaction not found. Ref: $refId");

            return ['status' => false, 'message' => 'Transaction Not Found'];
        }

        // 4. Update Status (Berlaku untuk kedua model karena kolomnya sama: status, notes)
        if ($status === 'Success' || $status === 'Completed') {
            if ($transaction->status !== 'completed') {
                $transaction->update([
                    'status' => 'processing', // Lunas
                    'notes' => "Lunas Otomatis via Tokopay (Ref: $trxId)",
                ]);
            }
        } elseif ($status === 'Expired' || $status === 'Failed') {
            $transaction->update([
                'status' => 'cancelled',
                'notes' => "Pembayaran $status via Tokopay",
            ]);
        }

        return ['status' => true];
    }
}
