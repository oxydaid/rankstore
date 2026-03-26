<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\DiscordNotificationService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Bungkus try-catch agar jika Discord Down, user tetap bisa checkout sukses
        try {
            $discordService = new DiscordNotificationService;
            $waService = new WhatsAppNotificationService;

            // 1. Kirim Notif Discord
            $discordService->sendNewOrderNotification($order);

            // 2. Kirim WA ke Pembeli (Pesanan Dibuat)
            $waService->sendOrderCreated($order);

            // 3. Kirim WA ke Admin
            $waService->sendAdminNotification($order);

        } catch (\Exception $e) {
            Log::error('Error Observer Created: '.$e->getMessage());
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {

            $newStatus = $order->status;
            $waService = new WhatsAppNotificationService;

            try {
                if ($newStatus === 'completed') {
                    // Status jadi Selesai -> Kirim WA Sukses
                    $waService->sendOrderSuccess($order);
                } elseif ($newStatus === 'processing') {
                    // Status jadi Proses -> Kirim WA Proses
                    $waService->sendOrderProcessing($order);
                } elseif ($newStatus === 'cancelled') {
                    // Status jadi Batal -> Kirim WA Gagal
                    $waService->sendOrderFailed($order);
                }
            } catch (\Exception $e) {
                Log::error('Error Observer Updated: '.$e->getMessage());
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
