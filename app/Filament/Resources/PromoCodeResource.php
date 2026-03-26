<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Kode Promo';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Voucher')
                    ->description('Buat kode unik untuk diskon.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('code')
                                    ->label('Kode Promo')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: BERKAH10')
                                    ->helperText('Gunakan huruf kapital dan angka. Tanpa spasi.')
                                    ->maxLength(20),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Tipe Potongan')
                                    ->options([
                                        'fixed' => 'Nominal Tetap (Rp)',
                                        'percent' => 'Persentase (%)',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Besar Potongan')
                                    ->required()
                                    ->numeric()
                                    ->prefix(fn (Forms\Get $get) => $get('type') === 'percent' ? '%' : 'Rp'),
                            ]),
                    ]),

                Section::make('Batasan & Periode')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('start_date')
                                    ->label('Mulai Berlaku'),

                                Forms\Components\DateTimePicker::make('end_date')
                                    ->label('Berakhir Pada')
                                    ->afterOrEqual('start_date'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('max_uses')
                                    ->label('Batas Pemakaian (Kuota)')
                                    ->numeric()
                                    ->placeholder('Kosongkan jika tidak terbatas')
                                    ->helperText('Contoh: Isi 100 jika hanya untuk 100 orang pertama.'),

                                Forms\Components\TextInput::make('used_count')
                                    ->label('Sudah Dipakai')
                                    ->disabled()
                                    ->dehydrated(false) // Jangan kirim ke DB saat save
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->weight('bold')
                    ->copyable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Diskon')
                    ->formatStateUsing(fn (PromoCode $record) => $record->type === 'percent'
                        ? $record->amount.'%'
                        : 'Rp '.number_format($record->amount, 0, ',', '.')
                    )
                    ->badge()
                    ->color(fn (PromoCode $record) => $record->type === 'percent' ? 'info' : 'success'),

                Tables\Columns\TextColumn::make('usage')
                    ->label('Pemakaian')
                    ->state(fn (PromoCode $record) => $record->used_count.' / '.($record->max_uses ?? '∞')
                    ),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Berakhir')
                    ->dateTime('d M Y')
                    ->placeholder('Selamanya'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'fixed' => 'Nominal (Rp)',
                        'percent' => 'Persen (%)',
                    ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
