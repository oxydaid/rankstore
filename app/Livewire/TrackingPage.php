<?php

namespace App\Livewire;

use App\Models\MigrationRequest;
use App\Models\Order; // Import Model Migrasi
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cek Pesanan - Oxyda Store')]
class TrackingPage extends Component
{
    public $orderId = '';

    public function search()
    {
        $this->validate([
            'orderId' => 'required|string|min:5',
        ], [
            'orderId.required' => 'Harap masukkan Order ID.',
            'orderId.min' => 'Order ID terlalu pendek.',
        ]);

        // 1. Cek di Tabel Order (Pembelian Biasa)
        $order = Order::where('uuid', $this->orderId)->first();

        if ($order) {
            return redirect()->route('tracking.detail', ['uuid' => $order->uuid]);
        }

        // 2. Cek di Tabel Migrasi (Jasa Migrasi)
        $migration = MigrationRequest::where('uuid', $this->orderId)->first();

        if ($migration) {
            // Arahkan ke halaman detail khusus migrasi
            return redirect()->route('migration.detail', ['uuid' => $migration->uuid]);
        }

        // 3. Jika tidak ketemu di keduanya
        $this->addError('orderId', 'ID Transaksi tidak ditemukan. Silakan cek kembali.');
    }

    public function render()
    {
        return view('livewire.tracking-page');
    }
}
