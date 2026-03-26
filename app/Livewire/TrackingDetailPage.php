<?php

namespace App\Livewire;

use App\Models\AppSetting;
use App\Models\Deposit;
use App\Models\Order;
use App\Services\AriePulsaService;
use Livewire\Component;

class TrackingDetailPage extends Component
{
    public Order $order;

    public function mount($uuid)
    {
        $this->order = Order::where('uuid', $uuid)->firstOrFail();
    }

    public function checkPaymentStatus()
    {

        $deposit = Deposit::where('order_id', $this->order->id)->latest()->first();

        if ($deposit) {
            $service = new AriePulsaService;
            $result = $service->checkStatus($deposit);

            if ($result['status']) {
                session()->flash('success_order', 'Status pembayaran berhasil diperbarui!');
                $this->order->refresh();

            } else {
                $this->dispatch('notify', message: 'Status pembayaran belum berubah / masih pending.');
            }
        } else {
            $this->dispatch('notify', message: 'Cek status manual tidak tersedia untuk metode ini.');
        }
    }

    public function render()
    {
        return view('livewire.tracking-detail-page', [
            'settings' => AppSetting::first(),
        ]);
    }
}
