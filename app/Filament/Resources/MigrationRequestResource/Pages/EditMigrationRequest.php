<?php

namespace App\Filament\Resources\MigrationRequestResource\Pages;

use App\Filament\Resources\MigrationRequestResource;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification; // Tambahkan import ini
use Filament\Resources\Pages\EditRecord;

class EditMigrationRequest extends EditRecord
{
    protected static string $resource = MigrationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. ACTION SELESAIKAN (Updated with Invoice Upload)
            Actions\Action::make('complete_migration')
                ->label('Selesaikan Migrasi')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => $record->status !== 'completed')
                ->requiresConfirmation()
                ->modalHeading('Selesaikan Permintaan Migrasi?')
                ->modalDescription('Upload bukti bahwa rank sudah dipindahkan (Invoice/SS). Status akan berubah menjadi Completed.')
                ->form([
                    FileUpload::make('server_invoice')
                        ->label('Bukti / Invoice / Testimoni')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->helperText('Bisa upload file PDF atau Gambar (JPG/PNG).')
                        ->directory('server-invoices-migration')
                        ->required()
                        ->openable()
                        ->downloadable()
                        ->validationMessages([
                            'required' => 'Wajib upload bukti proses server.',
                        ]),

                    Textarea::make('notes')
                        ->label('Catatan untuk User (Opsional)')
                        ->placeholder('Contoh: Rank sudah dipindah, silakan cek.'),
                ])
                ->action(function (array $data, $record) {
                    $record->update([
                        'status' => 'completed',
                        'server_invoice' => $data['server_invoice'],
                        'notes' => $data['notes'] ?? $record->notes,
                    ]);

                    Notification::make()
                        ->title('Migrasi Berhasil Diselesaikan')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),

            // 2. ACTION TOLAK
            Actions\Action::make('reject_migration')
                ->label('Tolak Permintaan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn ($record) => $record->status !== 'cancelled')
                ->requiresConfirmation()
                ->modalHeading('Tolak Migrasi Ini?')
                ->form([
                    Textarea::make('notes')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->placeholder('Contoh: Bukti pembayaran tidak valid.'),
                ])
                ->action(function (array $data, $record) {
                    $record->update([
                        'status' => 'cancelled',
                        'notes' => $data['notes'],
                    ]);

                    Notification::make()
                        ->title('Permintaan Ditolak')
                        ->danger()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                }),

            // 3. CHAT WA
            Actions\Action::make('chat_wa')
                ->label('Chat WA')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('info')
                ->url(fn ($record) => "https://wa.me/{$record->whatsapp_number}?text=Halo%20{$record->old_gamertag},%20terkait%20request%20migrasi%20rank...", true),

            // 4. DELETE BAWAAN
            Actions\DeleteAction::make(),
        ];
    }
}
