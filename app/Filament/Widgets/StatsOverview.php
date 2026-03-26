<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Agar widget ini muncul paling atas
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Omset Perbulan
        $totalomsetbulanini = Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('total_amount');

        // Hitung Omset (Hanya yang status 'completed')
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');

        // Hitung Order Pending
        $pendingOrders = Order::where('status', 'pending')->count();

        // Hitung Total Order Sukses
        $successOrders = Order::where('status', 'completed')->count();

        return [
            stat::make('Pendapatan Bulan ini', 'Rp '.number_format($totalomsetbulanini, 0, ',', '.'))
                ->description('Semua transaksi sukses')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([10, 2, 5, 3, 10, 4, 17]), // Chart dekoratif dummy
            Stat::make('Total Pendapatan', 'Rp '.number_format($totalRevenue, 0, ',', '.'))
                ->description('Semua transaksi sukses')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Chart dekoratif dummy

            Stat::make('Order Perlu Proses', $pendingOrders)
                ->description('Order status Pending')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color($pendingOrders > 0 ? 'danger' : 'success'), // Merah kalau ada tunggakan

            Stat::make('Total Transaksi Sukses', $successOrders)
                ->description('Total rank terjual')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
        ];
    }
}
