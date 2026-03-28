<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Metode Bayar';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Metode')
                                    ->placeholder('Contoh: QRIS (Otomatis) atau BCA')
                                    ->helperText('Jika QRIS manual silakan upload qr di halaman App Setting.')
                                    ->required(),

                                Forms\Components\TextInput::make('code')
                                    ->label('Kode Gateway (Khusus Tokopay)')
                                    ->helperText('Hanya diisi jika menggunakan Tokopay (Contoh: QRIS, DANA, OVO). Kosongkan jika Manual atau gateway lain. Jika menggunakan ariepulsa maka isi dengan ARIEPULSA')
                                    ->placeholder('Contoh: QRISREALTIME'),
                            ]),

                        Forms\Components\Toggle::make('is_manual')
                            ->label('Mode Pembayaran Manual?')
                            ->helperText('Aktif: User upload bukti transfer. Mati: Redirect ke Gateway (Tokopay / Ariepulsa).')
                            ->default(true)
                            ->live(),

                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->label('Logo Metode Pembayaran')
                            ->preserveFilenames()
                            ->disk('public_img')
                            ->directory('payment-methods')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->visible(fn (Get $get) => $get('is_manual'))
                            ->schema([
                                Forms\Components\TextInput::make('account_number')
                                    ->label('Nomor Rekening / No. HP')
                                    ->required(fn (Get $get) => $get('is_manual')),

                                Forms\Components\TextInput::make('account_holder')
                                    ->label('Atas Nama')
                                    ->required(fn (Get $get) => $get('is_manual')),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Instruksi Pembayaran')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Metode Ini')
                            ->default(true),
                    ]),

                Section::make('Biaya Layanan (Admin Fee)')
                    ->description('Biaya tambahan yang dibebankan ke pembeli.')
                    ->schema([
                        Forms\Components\Select::make('fee_type')
                            ->label('Tipe Biaya')
                            ->options([
                                'fixed' => 'Nominal Tetap (Rp)',
                                'percent' => 'Persentase (%)',
                                'mixed' => 'Campuran (% + Rp)',
                            ])
                            ->default('fixed')
                            ->live()

                            ->required(),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('fee_percent')
                                    ->label('Persentase Fee (%)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(0)

                                    ->visible(fn (Get $get) => in_array($get('fee_type'), ['percent', 'mixed'])),

                                Forms\Components\TextInput::make('fee_flat')
                                    ->label('Nominal Fee (Rp)')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)

                                    ->visible(fn (Get $get) => in_array($get('fee_type'), ['fixed', 'mixed'])),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->label('Logo'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Metode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('is_manual')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (bool $state) => $state ? 'Manual' : 'Otomatis')
                    ->color(fn (bool $state) => $state ? 'gray' : 'success'),

                Tables\Columns\TextColumn::make('fee_type')
                    ->label('Fee')
                    ->formatStateUsing(function (PaymentMethod $record) {
                        if ($record->fee_type === 'fixed') {
                            return 'Rp '.number_format($record->fee_flat, 0, ',', '.');
                        }
                        if ($record->fee_type === 'percent') {
                            return $record->fee_percent.'%';
                        }

                        return $record->fee_percent.'% + Rp '.number_format($record->fee_flat, 0, ',', '.');
                    }),

                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
