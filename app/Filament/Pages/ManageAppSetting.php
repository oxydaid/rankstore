<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use App\Services\MayarLicenseService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class ManageAppSetting extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Web';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.manage-app-setting';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = AppSetting::firstOrCreate();
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas Website')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Nama Website')
                            ->required(),
                        Textarea::make('site_description')
                            ->label('Deskripsi')
                            ->rows(2),
                        FileUpload::make('logo')
                            ->image()
                            ->disk('public_img')
                            ->directory('settings'),
                        FileUpload::make('favicon')
                            ->image()
                            ->disk('public_img')
                            ->directory('settings')
                            ->imageCropAspectRatio('1:1'),
                    ])
                    ->columns(2),

                Section::make('Integrasi API')
                    ->description('Konfigurasi untuk notifikasi otomatis.')
                    ->schema([
                        TextInput::make('admin_phone')
                            ->label('Nomor HP Admin (Penerima)')
                            ->helperText(
                                'Notifikasi order baru akan dikirim ke nomor ini.'
                            )
                            ->tel()
                            ->placeholder('08xxxxxxxx'),
                        TextInput::make('wa_api_key')
                            ->label('WhatsApp API Key')
                            ->password()
                            ->helperText('Dapatkan di https://wa.oxyda.id/id/')
                            ->revealable(),
                        TextInput::make('wa_sender_number')
                            ->label('Nomor Pengirim WA')
                            ->helperText(
                                'Nomor WhatsApp yang akan mengirim pesan. terdaftar gateway.'
                            )
                            ->tel()
                            ->placeholder('08xxxxxxxx'),
                        TextInput::make('discord_admin_id')
                            ->label('Discord User ID (Admin Tag)')
                            ->helperText(
                                'Masukkan User ID (Angka) untuk di-mention saat ada order baru.'
                            )
                            ->numeric(),
                        TextInput::make('discord_webhook')
                            ->label('Discord Webhook URL')
                            ->url()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Lisensi Aplikasi')
                    ->description('Validasi lisensi menggunakan provider Mayar.')
                    ->schema([
                        TextInput::make('license_code')
                            ->label('License Code')
                            ->password()
                            ->revealable()
                            ->required(),
                        TextInput::make('mayar_product_id')
                            ->label('Mayar Product ID')
                            ->required()
                            ->helperText('Jika kosong, sistem akan memakai MAYAR_PRODUCT_ID dari file env.'),
                        Placeholder::make('license_status')
                            ->label('Status Lisensi')
                            ->content(fn ($get) => strtoupper((string) ($get('license_status') ?? 'INACTIVE'))),
                    ])
                    ->columns(2),

                Section::make('Social Media')->schema([
                    KeyValue::make('social_media')
                        ->label('Daftar Sosmed')
                        ->keyLabel('Platform (Contoh: Instagram)')
                        ->valueLabel('Link Profile')
                        ->addActionLabel('Tambah Sosmed')
                        ->reorderable(),
                ]),

                Section::make('Pembayaran')
                    ->description('Pengaturan metode pembayaran.')
                    ->schema([
                        FileUpload::make('qris_image')
                            ->label('QRIS Image')
                            ->helperText(
                                'Gambar QRIS. Format: JPG/PNG. Max: 2MB. QRIS Manual.'
                            )
                            ->image()
                            ->disk('public_img')
                            ->directory('settings')
                            ->imageCropAspectRatio('1:1')
                            ->imageEditorAspectRatios(['1:1'])
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('tokopay_merchant_id')
                            ->label('Tokopay Merchant ID')
                            ->password()

                            ->revealable(),

                        Forms\Components\TextInput::make('tokopay_secret_key')
                            ->label('Tokopay Secret Key')
                            ->password()
                            ->revealable(),
                        Forms\Components\TextInput::make('ref_id_prefix')
                            ->label('Prefix Ref ID')
                            ->placeholder('Contoh: OXY')
                            ->default('TRX')
                            ->helperText(
                                'Kode depan unik untuk order di Tokopay.'
                            )
                            ->maxLength(5)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('ariepulsa_api_key')
                            ->label('AriePulsa API Key')
                            ->password()
                            ->revealable(),
                    ])
                    ->columns(2),

                Section::make('Tampilan Hero & Server')
                    ->description(
                        'Mengatur gambar background utama dan informasi server Minecraft.'
                    )
                    ->schema([
                        Forms\Components\FileUpload::make('hero_background')
                            ->label('Background Image')
                            ->helperText(
                                'Gambar besar yang muncul di halaman utama. Format: JPG/PNG. Max: 2MB.'
                            )
                            ->image()
                            ->disk('public_img')
                            ->directory('settings')

                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('server_ip')
                                ->label('IP Server Minecraft')
                                ->placeholder('contoh: play.prownetwork.com')
                                ->required(),

                            Forms\Components\TextInput::make('server_port')
                                ->label('Port Server')
                                ->default('19132')
                                ->numeric()
                                ->placeholder('Default: 19132'),
                        ]),
                    ]),

                Section::make('Tema Website')
                    ->description(
                        'Warna ini berlaku untuk Admin Panel dan Gradient Frontend.'
                    )
                    ->schema([
                        Forms\Components\ColorPicker::make('primary_color')
                            ->label('Primary Color')
                            ->required()
                            ->live(),

                        Forms\Components\ColorPicker::make('secondary_color')
                            ->label('Secondary Color')
                            ->required()
                            ->live(),

                        Forms\Components\Placeholder::make('preview')
                            ->label('Preview Gradient')
                            ->content(
                                fn ($get) => new \Illuminate\Support\HtmlString(
                                    '
                <div style="
                    width: 100%; 
                    height: 50px; 
                    border-radius: 8px; 
                    background: linear-gradient(to right, '.
                                        ($get('primary_color') ?? '#333').
                                        ', '.
                                        ($get('secondary_color') ?? '#333').
                                        ');
                    display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; text-shadow: 0 1px 2px rgba(0,0,0,0.3);
                ">
                    Tampilan Header Nanti
                </div>
            '
                                )
                            )
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $settings = AppSetting::first();
            $settings->update($data);

            $result = app(MayarLicenseService::class)->validateForSettings($settings, true);
            $this->form->fill($settings->fresh()->toArray());

            if (! ($result['active'] ?? false)) {
                Notification::make()
                    ->danger()
                    ->title('Lisensi tidak valid')
                    ->body($result['message'] ?? 'Lisensi tidak aktif.')
                    ->send();

                return;
            }

            Notification::make()
                ->success()
                ->title('Pengaturan berhasil disimpan dan lisensi aktif')
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }
}
