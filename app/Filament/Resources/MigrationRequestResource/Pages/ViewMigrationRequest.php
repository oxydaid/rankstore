<?php

namespace App\Filament\Resources\MigrationRequestResource\Pages;

use App\Filament\Resources\MigrationRequestResource;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewMigrationRequest extends ViewRecord
{
    protected static string $resource = MigrationRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // 1. INFO PERPINDAHAN
                Section::make('Detail Perpindahan')
                    ->schema([
                        TextEntry::make('uuid')->label('Ref ID')->copyable(),
                        TextEntry::make('created_at')->dateTime()->label('Waktu Request'),

                        Grid::make(2)->schema([
                            TextEntry::make('old_gamertag')
                                ->label('DARI AKUN (LAMA)')
                                ->color('danger')
                                ->weight('bold')
                                ->size(TextEntry\TextEntrySize::Large),

                            TextEntry::make('new_gamertag')
                                ->label('KE AKUN (BARU)')
                                ->color('success')
                                ->weight('bold')
                                ->size(TextEntry\TextEntrySize::Large),
                        ]),

                        TextEntry::make('whatsapp_number')->label('WhatsApp')->icon('heroicon-m-phone'),
                        TextEntry::make('discord_username')->label('Discord')->placeholder('-'),
                    ]),

                // 2. INFO RANK & BIAYA
                Section::make('Rank & Biaya Jasa')
                    ->schema([
                        TextEntry::make('category.name')->label('Mode Server'),
                        TextEntry::make('rank.name')->label('Rank')->weight('bold'),

                        TextEntry::make('rank_price_snapshot')
                            ->label('Harga Rank (Snapshot)')
                            ->money('IDR'),

                        TextEntry::make('fee_percent_snapshot')
                            ->label('Persentase Fee')
                            ->suffix('%'),

                        TextEntry::make('total_amount')
                            ->label('TOTAL BIAYA JASA')
                            ->money('IDR')
                            ->color('primary')
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large),
                    ])->columns(3),

                // 3. BUKTI PEMBAYARAN
                Section::make('Dokumen & Pembayaran')
                    ->schema([
                        TextEntry::make('paymentMethod.name')->label('Metode Bayar'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'warning',
                            }),

                        // Gambar Bukti Transfer (Manual)
                        ViewEntry::make('payment_proof')
                            ->label('Bukti Transfer User')
                            ->view('filament.components.image-modal')
                            ->visible(fn ($record) => $record->payment_proof),

                        // INVOICE SERVER (ADMIN) - TAMBAHAN BARU
                        TextEntry::make('server_invoice')
                            ->label('Invoice Server (Admin)')
                            ->formatStateUsing(fn ($state) => $state ? 'Download Invoice' : 'Belum tersedia')
                            ->icon('heroicon-m-document-arrow-down')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'gray')
                            ->url(fn ($record) => $record->server_invoice ? Storage::url($record->server_invoice) : null)
                            ->openUrlInNewTab()
                            ->visible(fn ($record) => $record->server_invoice),

                        // Info Gateway
                        TextEntry::make('trx_id')
                            ->label('Gateway Ref ID')
                            ->visible(fn ($record) => $record->trx_id),
                    ])->columns(2),
            ]);
    }
}
