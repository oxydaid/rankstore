<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RankResource\Pages;
use App\Models\Rank;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RankResource extends Resource
{
    protected static ?string $model = Rank::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Daftar Rank';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Data Rank')
                    ->tabs([
                        // TAB 1: INFORMASI UMUM
                        Tab::make('Informasi Umum')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->label('Mode Server')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Rank')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('price')
                                            ->label('Harga Jual (Final)')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->required(),

                                        Forms\Components\TextInput::make('slice_price')
                                            ->label('Harga Coret (Awal)')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->helperText('Kosongkan jika harga normal.'),
                                    ]),

                                Forms\Components\FileUpload::make('image')
                                    ->label('Icon Rank')
                                    ->image()
                                    ->preserveFilenames()
                                    ->disk('public_img')
                                    ->directory('ranks')
                                    ->columnSpanFull(),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->required(),
                            ]),
 
                        // TAB 2: FITUR / COMMANDS (JSON)
                        Tab::make('Fitur & Akses')
                            ->icon('heroicon-m-list-bullet')
                            ->badge(fn($record) => $record?->description ? count($record->description) : null)
                            ->schema([
                                Forms\Components\Repeater::make('description')
                                    ->label('List Keuntungan')
                                    ->schema([
                                        Forms\Components\TextInput::make('feature')
                                            ->label('Fitur / Command')
                                            ->placeholder('Contoh: Akses /fly')
                                            ->required(),
                                    ])
                                    ->grid(2)
                                    ->defaultItems(1)
                                    ->columnSpanFull(),
                            ]),

                        // TAB 3: BONUS KITS (JSON BARU)
                        Tab::make('Bonus Kits')
                            ->icon('heroicon-m-gift') // Ikon Kado
                            ->badge(fn($record) => $record?->kits ? count($record->kits) : null)
                            ->schema([
                                Forms\Components\Repeater::make('kits')
                                    ->label('Item In-Game / Kits')
                                    ->helperText('Daftar item yang akan didapat player.')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Item')
                                            ->placeholder('Contoh: 64x Diamond Block')
                                            ->required(),
                                    ])
                                    ->grid(2)
                                    ->defaultItems(0) // Default kosong biar rapi
                                    ->addActionLabel('Tambah Kit')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull() // Agar Tab memenuhi lebar form
                    ->persistTabInQueryString(), // Agar saat refresh tetap di tab yang sama
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Rank')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'), // Biar tebal

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Mode Server')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->description(
                        fn(Rank $record): ?string => $record->slice_price ? 'Diskon dari: Rp ' . number_format($record->slice_price, 0, ',', '.') : null
                    )
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                // Filter berdasarkan Kategori
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filter Mode'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->label('Duplikat') // Label tombol
                    ->color('secondary') // Warna tombol (opsional)
                    ->modalHeading('Duplikat Rank ini?')
                    ->modalDescription('Rank baru akan dibuat dengan data yang sama persis. Silakan edit setelahnya.')
                    ->modalSubmitActionLabel('Ya, Duplikat'),
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
            'index' => Pages\ListRanks::route('/'),
            'create' => Pages\CreateRank::route('/create'),
            'edit' => Pages\EditRank::route('/{record}/edit'),
        ];
    }
}
