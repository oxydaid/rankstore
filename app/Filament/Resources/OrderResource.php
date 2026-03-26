<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Data Transaksi';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Data Pesanan')
                            ->schema([
                                Forms\Components\TextInput::make('uuid')
                                    ->label('Order ID')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('gamertag')
                                    ->required(),

                                Forms\Components\TextInput::make('whatsapp_number')
                                    ->required(),

                                Forms\Components\Select::make('rank_id')
                                    ->relationship('rank', 'name')
                                    ->label('Rank Item')
                                    ->disabled(),

                            ])->columns(2),

                        Forms\Components\Section::make('Rincian Pembayaran')
                            ->schema([
                                Forms\Components\TextInput::make('subtotal_amount')
                                    ->label('Subtotal (Harga Awal)')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->disabled(),

                                Forms\Components\TextInput::make('discount_amount')
                                    ->label('Potongan Diskon')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->disabled(),

                                Forms\Components\Select::make('promo_code_id')
                                    ->label('Kode Promo Digunakan')
                                    ->relationship('promoCode', 'code')
                                    ->disabled()
                                    ->placeholder('Tidak menggunakan kode'),

                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Akhir (Wajib Transfer)')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->disabled()

                                    ->dehydrated(),
                            ])->columns(2),

                        Forms\Components\Section::make('Bukti Pembayaran')
                            ->schema([
                                Forms\Components\FileUpload::make('payment_proof')
                                    ->label('Screenshot Transfer')
                                    ->image()
                                    ->openable()
                                    ->disk('public_img')
                                    ->directory('payment-proofs'),

                                Forms\Components\FileUpload::make('upgrade_proof')
                                    ->label('Bukti Rank Lama')
                                    ->visible(fn ($record) => $record?->is_upgrade ?? false)
                                    ->image()
                                    ->openable()
                                    ->disk('public_img')
                                    ->directory('upgrade-proofs'),
                            ]),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Kontrol Manual')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required(),

                                Forms\Components\FileUpload::make('server_invoice')
                                    ->label('Invoice Server')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->disk('public_img')
                                    ->directory('server-invoices')
                                    ->openable()
                                    ->downloadable(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(4),
                            ]),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('Order ID')
                    ->searchable()
                    ->limit(8)
                    ->copyable()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('gamertag')
                    ->label('Gamertag')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('rank.name')
                    ->label('Rank')
                    ->description(fn (Order $record) => $record->is_upgrade ? 'Upgrade' : 'New Purchase'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Bayar')
                    ->money('IDR', locale: 'id')
                    ->sortable()

                    ->description(fn (Order $record) => $record->discount_amount > 0
                        ? 'Hemat: Rp '.number_format($record->discount_amount, 0, ',', '.')
                        : null
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m H:i')
                    ->sortable(),
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
                Tables\Actions\EditAction::make()
                    ->label('Proses'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $order = Order::whereIn('status', ['pending', 'processing'])->count();

        return $order > 0 ? (string) $order : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
