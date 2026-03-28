<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MigrationRequestResource\Pages;
use App\Models\MigrationRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MigrationRequestResource extends Resource
{
    protected static ?string $model = MigrationRequest::class;

    // Icon Panah Bolak Balik (Simbol Migrasi)
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Request Migrasi';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 2; // Di bawah Order

    // --- FORM (EDIT ADMIN) ---
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // KOLOM KIRI: Data Player & Migrasi
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Data Perpindahan')
                            ->schema([
                                Forms\Components\TextInput::make('uuid')
                                    ->label('Ref ID')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('old_gamertag')
                                            ->label('Dari (Akun Lama)')
                                            ->required(),

                                        Forms\Components\TextInput::make('new_gamertag')
                                            ->label('Ke (Akun Baru)')
                                            ->required(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('whatsapp_number')->required(),
                                        Forms\Components\TextInput::make('discord_username'),
                                    ]),
                            ]),

                        Forms\Components\Section::make('Detail Rank & Biaya')
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->label('Mode Server')
                                    ->disabled(),

                                Forms\Components\Select::make('rank_id')
                                    ->relationship('rank', 'name')
                                    ->label('Rank Dipindah')
                                    ->disabled(),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('rank_price_snapshot')
                                            ->label('Harga Rank')
                                            ->prefix('Rp')
                                            ->numeric()
                                            ->disabled(),

                                        Forms\Components\TextInput::make('fee_percent_snapshot')
                                            ->label('Fee (%)')
                                            ->suffix('%')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('total_amount')
                                            ->label('Total Bayar')
                                            ->prefix('Rp')
                                            ->disabled(),
                                    ]),
                            ]),

                        Forms\Components\Section::make('Bukti Pembayaran')
                            ->schema([
                                Forms\Components\FileUpload::make('payment_proof')
                                    ->label('Bukti Transfer (Manual)')
                                    ->image()
                                    ->openable()
                                    ->disk('public_img')
                                    ->directory('migration-proofs'),
                            ]),
                    ])->columnSpan(2),

                // KOLOM KANAN: Status & Action
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status Proses')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('payment_method_id')
                                    ->relationship('paymentMethod', 'name')
                                    ->label('Metode Bayar')
                                    ->disabled(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan Admin')
                                    ->rows(3),
                            ]),

                        Forms\Components\Section::make('Info Gateway')
                            ->visible(fn ($record) => $record->payment_url !== null)
                            ->schema([
                                Forms\Components\TextInput::make('trx_id')
                                    ->label('Gateway Trx ID')
                                    ->disabled(),
                                Forms\Components\TextInput::make('payment_url')
                                    ->label('Link/QR Payment')
                                    ->disabled(),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    // --- TABEL LIST ---
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Tanggal')
                    ->sortable(),

                Tables\Columns\TextColumn::make('old_gamertag')
                    ->label('Dari Akun')
                    ->searchable()
                    ->description(fn (MigrationRequest $record) => 'Ke: '.$record->new_gamertag)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('rank.name')
                    ->label('Rank')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Biaya')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMigrationRequests::route('/'),
            'view' => Pages\ViewMigrationRequest::route('/{record}'),
            // 'create' => Pages\CreateMigrationRequest::route('/create'),
            // 'edit' => Pages\EditMigrationRequest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = MigrationRequest::whereIn('status', ['pending', 'processing'])->count();

        return $count > 0 ? (string) $count : null;
    }
}
