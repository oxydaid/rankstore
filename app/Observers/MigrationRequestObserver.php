<?php

namespace App\Observers;

use App\Models\MigrationRequest;
use App\Services\DiscordNotificationService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Log;

class MigrationRequestObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the MigrationRequest "created" event.
     */
    public function created(MigrationRequest $migrationRequest): void
    {
        try {
            $discordService = new DiscordNotificationService;
            $waService = new WhatsAppNotificationService;

            // 1. Kirim Notif Discord (Admin)
            $discordService->sendNewOrderNotification($migrationRequest);

            // 2. Kirim WA ke Pembeli (Request Dibuat)
            $waService->sendOrderCreated($migrationRequest);

            // 3. Kirim WA ke Admin
            $waService->sendAdminNotification($migrationRequest);

        } catch (\Exception $e) {
            Log::error('Error MigrationRequestObserver Created: '.$e->getMessage());
        }
    }

    /**
     * Handle the MigrationRequest "updated" event.
     */
    public function updated(MigrationRequest $migrationRequest): void
    {
        // Cek apakah kolom 'status' berubah
        if ($migrationRequest->isDirty('status')) {

            $newStatus = $migrationRequest->status;
            $waService = new WhatsAppNotificationService;

            try {
                if ($newStatus === 'completed') {
                    // Status jadi Selesai -> Kirim WA Sukses
                    $waService->sendOrderSuccess($migrationRequest);
                } elseif ($newStatus === 'processing') {
                    // Status jadi Proses -> Kirim WA Proses
                    $waService->sendOrderProcessing($migrationRequest);
                } elseif ($newStatus === 'cancelled') {
                    // Status jadi Batal -> Kirim WA Gagal
                    $waService->sendOrderFailed($migrationRequest);
                }
            } catch (\Exception $e) {
                Log::error('Error MigrationRequestObserver Updated: '.$e->getMessage());
            }
        }
    }
}
