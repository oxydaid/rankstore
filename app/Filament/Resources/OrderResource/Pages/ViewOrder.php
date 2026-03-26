<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Infolists;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // 1. DATA USER
                Infolists\Components\Section::make('Informasi Pembeli')
                    ->schema([
                        TextEntry::make('uuid')->label('Order ID')->copyable(),
                        TextEntry::make('created_at')->label('Tanggal Order')->dateTime(),
                        TextEntry::make('gamertag')->label('Gamertag')->weight('bold')->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('whatsapp_number')->label('No. WhatsApp')->icon('heroicon-m-phone'),
                        TextEntry::make('discord_username')->label('Discord Username')->placeholder('-'),
                    ])->columns(3),

                // 2. DETAIL KEUANGAN (UPDATE UTAMA DI SINI)
                Infolists\Components\Section::make('Rincian Produk & Keuangan')
                    ->schema([
                        TextEntry::make('rank.name')
                            ->label('Item Dibeli')
                            ->weight('bold'),

                        TextEntry::make('rank.category.name')
                            ->label('Mode')
                            ->badge(),

                        TextEntry::make('paymentMethod.name')
                            ->label('Metode Bayar'),

                        // Breakline / Divider Logic

                        TextEntry::make('subtotal_amount')
                            ->label('Harga Awal')
                            ->money('IDR')
                            ->color('gray'),

                        TextEntry::make('promoCode.code')
                            ->label('Voucher')
                            ->badge()
                            ->color('info')
                            ->visible(fn (Order $record) => $record->promo_code_id),

                        TextEntry::make('discount_amount')
                            ->label('Potongan Diskon')
                            ->money('IDR')
                            ->color('danger') // Merah karena minus
                            ->visible(fn (Order $record) => $record->discount_amount > 0),

                        TextEntry::make('total_amount')
                            ->label('TOTAL TRANSFER')
                            ->money('IDR')
                            ->weight('bold')
                            ->color('success')
                            ->size(TextEntry\TextEntrySize::Large),
                    ])->columns(4),

                // 3. BUKTI GAMBAR & DOKUMEN (GABUNGAN RAPI)
                Infolists\Components\Section::make('Berkas & Dokumen Transaksi')
                    ->description('Kumpulan bukti pembayaran user dan invoice penyelesaian admin.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // KOLOM 1: Bukti Transfer
                                ViewEntry::make('payment_proof')
                                    ->label('Bukti Transfer User')
                                    ->view('filament.components.image-modal'),

                                // KOLOM 2: Bukti Upgrade
                                ViewEntry::make('upgrade_proof')
                                    ->label('Bukti Rank Lama')
                                    ->view('filament.components.image-modal')
                                    ->visible(fn ($record) => $record->is_upgrade),

                                // KOLOM 3: Invoice Admin
                                Group::make([
                                    TextEntry::make('server_invoice')
                                        ->label('Invoice Server')
                                        ->formatStateUsing(fn ($state) => $state ? 'Download File' : 'Belum tersedia')
                                        ->icon('heroicon-m-document-arrow-down')
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray')
                                        ->url(fn ($record) => $record->server_invoice ? Storage::url($record->server_invoice) : null)
                                        ->openUrlInNewTab(),

                                    TextEntry::make('status')
                                        ->label('Status Saat Ini')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'completed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'warning',
                                        }),
                                ])->columnSpan(1),
                            ]),
                    ])->collapsible(),

                // 4. NOTES
                Infolists\Components\Section::make('Catatan')
                    ->schema([
                        TextEntry::make('notes')->label('Catatan Internal')->placeholder('-'),
                        TextEntry::make('tos_agreed_at')->label('Setuju TOS')->dateTime(),
                    ])->columns(2),
            ]);
    }
}
