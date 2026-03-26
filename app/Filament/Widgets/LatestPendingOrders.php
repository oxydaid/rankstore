<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPendingOrders extends BaseWidget
{
    // Urutan ke-3 (Paling Bawah)
    protected static ?int $sort = 3;

    // Membuat tabel melebar penuh (Full Width) agar enak dilihat
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Hanya ambil order yang BELUM SELESAI (Pending/Processing)
                Order::query()
                    ->whereIn('status', ['pending', 'processing'])
                    ->latest() // Urutkan dari yang terbaru
            )
            ->heading('Antrian Order Perlu Diproses')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since() // Tampil: "5 menit yang lalu"
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('uuid')
                    ->label('Order ID')
                    ->searchable()
                    ->limit(8)
                    ->fontFamily('mono')
                    ->copyable(),

                Tables\Columns\TextColumn::make('gamertag')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('rank.name')
                    ->label('Item')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Nominal')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',   // Kuning
                        'processing' => 'info',   // Biru
                        default => 'gray',
                    }),
            ])
            ->actions([
                // Tombol Shortcut langsung ke halaman Edit/Proses Order tersebut
                Tables\Actions\Action::make('proses')
                    ->label('Proses')
                    ->icon('heroicon-m-arrow-right-circle')
                    ->button()
                    ->size('xs')
                    ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false) // Matikan pagination biar simpel (cuma tampil 5-10 data terbaru)
            ->emptyStateHeading('Tidak ada antrian order')
            ->emptyStateDescription('Semua pesanan sudah diproses. Kerja bagus!')
            ->emptyStateIcon('heroicon-o-check-badge');
    }
}
