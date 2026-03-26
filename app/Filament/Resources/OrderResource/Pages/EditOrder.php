<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. ACTION COMPLETE (Modal Upload Invoice)
            Actions\Action::make('complete')
                ->label('Selesaikan Order')
                ->icon('heroicon-o-check-badge') // Ikon Centang
                ->color('success') // Hijau
                ->visible(fn ($record) => $record->status !== 'completed') // Hilang kalau sudah selesai
                ->modalHeading('Selesaikan Order Ini?')
                ->modalDescription('Upload bukti rank sudah dikirim server. Status akan berubah menjadi Completed.')
                ->form([
                    FileUpload::make('server_invoice')
                        ->label('Bukti / Invoice / Testimoni')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->helperText('Bisa upload file PDF atau Gambar (JPG/PNG).')
                        ->directory('server-invoices')
                        ->required()
                        ->openable() // Agar bisa diklik untuk preview
                        ->downloadable() // Agar admin bisa download
                        ->validationMessages([
                            'required' => 'Wajib upload bukti proses server.',
                        ]),

                    Textarea::make('notes')
                        ->label('Catatan untuk User (Opsional)')
                        ->placeholder('Contoh: Rank sudah aktif, silakan relogin.'),
                ])
                ->action(function (array $data, $record) {
                    // Update data record
                    $record->update([
                        'status' => 'completed',
                        'server_invoice' => $data['server_invoice'],
                        'notes' => $data['notes'] ?? $record->notes, // Pakai note baru atau simpan yang lama
                    ]);

                    Notification::make()
                        ->title('Order Berhasil Diselesaikan')
                        ->success()
                        ->send();

                    // Refresh halaman untuk melihat perubahan
                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),

            // 2. ACTION REJECT (Modal Alasan)
            Actions\Action::make('reject')
                ->label('Tolak Order')
                ->icon('heroicon-o-x-circle')
                ->color('danger') // Merah
                ->visible(fn ($record) => $record->status !== 'cancelled')
                ->modalHeading('Tolak Pesanan Ini?')
                ->modalDescription('Pastikan memberikan alasan yang jelas. Status akan berubah menjadi Cancelled.')
                ->form([
                    Textarea::make('notes')
                        ->label('Alasan Penolakan')
                        ->placeholder('Contoh: Bukti pembayaran tidak terbaca / palsu.')
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    $record->update([
                        'status' => 'cancelled',
                        'notes' => $data['notes'],
                    ]);

                    Notification::make()
                        ->title('Order Ditolak')
                        ->danger()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),

            // 3. Tombol Chat WA
            Actions\Action::make('chat_wa')
                ->label('Chat WA')
                ->color('info')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->url(fn ($record) => "https://wa.me/{$record->whatsapp_number}", true),

            // 4. Delete Bawaan
            Actions\DeleteAction::make(),
        ];
    }
}
