<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pendapatan';

    protected static ?int $sort = 2;

    // Membuat widget selebar halaman penuh agar grafik terlihat jelas
    protected int|string|array $columnSpan = 'full';

    // Filter yang tersedia di pojok kanan atas widget
    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        // Query dasar: Hanya hitung order yang SUDAH SELESAI (uang masuk)
        $query = Order::query()->where('status', 'completed');

        // Logika Trend berdasarkan filter
        $trend = Trend::model(Order::class)
            ->query($query);

        switch ($activeFilter) {
            case 'today':
                $data = $trend->between(start: now()->startOfDay(), end: now()->endOfDay())
                    ->perHour()
                    ->sum('total_amount');
                break;

            case 'week':
                $data = $trend->between(start: now()->startOfWeek(), end: now()->endOfWeek())
                    ->perDay()
                    ->sum('total_amount');
                break;

            case 'year':
                $data = $trend->between(start: now()->startOfYear(), end: now()->endOfYear())
                    ->perMonth()
                    ->sum('total_amount');
                break;

            case 'month':
            default:
                $data = $trend->between(start: now()->startOfMonth(), end: now()->endOfMonth())
                    ->perDay()
                    ->sum('total_amount');
                break;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#d97706', // Warna Garis (Amber - Primary Anda)
                    'backgroundColor' => 'rgba(217, 119, 6, 0.1)', // Warna Arsir di bawah garis (Transparan)
                    'fill' => true, // Mengaktifkan efek Area Chart
                    'tension' => 0.4, // Membuat garis melengkung halus (tidak kaku)
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Jenis Chart
    }
}
