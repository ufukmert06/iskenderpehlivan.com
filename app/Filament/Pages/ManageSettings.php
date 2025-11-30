<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Site Ayarları';

    protected static ?string $title = 'Site Ayarları';

    protected static ?string $navigationGroup = 'Ayarlar';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = Setting::with('translations')->first();

        if (! $setting) {
            $setting = Setting::create([
                'maintenance_mode' => false,
            ]);

            // Varsayılan Türkçe çeviri
            $setting->translations()->create([
                'locale' => 'tr',
                'site_name' => 'Site Adı',
                'site_description' => null,
                'footer_text' => null,
            ]);

            $setting->refresh();
        }

        // Veriyi düzleştir
        $data = $setting->toArray();

        // Translations'ı düzleştir
        $data['translations'] = $setting->translations->map(function ($translation) {
            return [
                'id' => $translation->id,
                'locale' => $translation->locale,
                'site_name' => $translation->site_name,
                'site_description' => $translation->site_description,
                'footer_text' => $translation->footer_text,
                'about_welcome_title' => $translation->about_welcome_title,
                'about_welcome_description' => $translation->about_welcome_description,
                'about_mission_title' => $translation->about_mission_title,
                'about_mission_content' => $translation->about_mission_content,
                'about_vision_title' => $translation->about_vision_title,
                'about_vision_content' => $translation->about_vision_content,
                'counter_years_label' => $translation->counter_years_label,
                'counter_customers_label' => $translation->counter_customers_label,
                'counter_sessions_label' => $translation->counter_sessions_label,
                'counter_certifications_label' => $translation->counter_certifications_label,
            ];
        })->toArray();

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Genel Bilgiler')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Site Logosu')
                            ->image()
                            ->directory('settings')
                            ->disk('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->helperText('Normal (açık) tema için logo')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('dark_logo')
                            ->label('Site Logosu (Dark)')
                            ->image()
                            ->directory('settings')
                            ->disk('public')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->helperText('Koyu tema veya özel durumlar için alternatif logo')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('favicon')
                            ->label('Favicon')
                            ->image()
                            ->directory('settings')
                            ->disk('public')
                            ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/svg+xml'])
                            ->maxSize(512)
                            ->helperText('16x16 veya 32x32 piksel boyutunda olmalı')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('maintenance_mode')
                            ->label('Bakım Modu')
                            ->helperText('Aktif olduğunda site ziyaretçilere kapalı olur')
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('İletişim Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('contact_email')
                            ->label('E-posta')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Textarea::make('contact_address')
                            ->label('Adres')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('google_maps_url')
                            ->label('Google Maps Embed')
                            ->rows(3)
                            ->helperText('Google Maps\'ten alınan iframe kodunun TAMAMINI yapıştırın. Örnek: <iframe src="https://www.google.com/maps/embed?pb=..." ...></iframe>')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Profesyonel Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('professional_title')
                            ->label('Profesyonel Unvan')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('rcc_number')
                            ->label('RCC Numarası')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('years_of_experience')
                            ->label('Deneyim Yılı')
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('rating')
                            ->label('Puanlandırma')
                            ->maxLength(10),

                        Forms\Components\Textarea::make('credentials')
                            ->label('Sertifika ve Yeterlilikler')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('therapeutic_approach')
                            ->label('Terapi Yaklaşımı')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Hakkımızda Sayfa Sayaçları')
                    ->schema([
                        Forms\Components\TextInput::make('happy_customers')
                            ->label('Mutlu Müşteri Sayısı')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('therapy_sessions')
                            ->label('Terapi Seansı Sayısı')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('certifications_awards')
                            ->label('Sertifika/Ödül Sayısı')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Sosyal Medya')
                    ->schema([
                        Forms\Components\TextInput::make('facebook')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-globe-alt')
                            ->placeholder('https://facebook.com/...'),

                        Forms\Components\TextInput::make('twitter')
                            ->label('Twitter / X')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-globe-alt')
                            ->placeholder('https://twitter.com/...'),

                        Forms\Components\TextInput::make('instagram')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-globe-alt')
                            ->placeholder('https://instagram.com/...'),

                        Forms\Components\TextInput::make('linkedin')
                            ->label('LinkedIn')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-globe-alt')
                            ->placeholder('https://linkedin.com/...'),

                        Forms\Components\TextInput::make('youtube')
                            ->label('YouTube')
                            ->url()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-globe-alt')
                            ->placeholder('https://youtube.com/...'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Çeviriler')
                    ->schema([
                        Forms\Components\Repeater::make('translations')
                            ->schema([
                                Forms\Components\Select::make('locale')
                                    ->label('Dil')
                                    ->options([
                                        'tr' => 'Türkçe',
                                        'en' => 'English',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->distinct(),

                                Forms\Components\TextInput::make('site_name')
                                    ->label('Site Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('site_description')
                                    ->label('Site Açıklaması')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->helperText('Site hakkında kısa açıklama (SEO için önemli)')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('footer_text')
                                    ->label('Footer Metni')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->helperText('Site alt bilgi metni (copyright vb.)')
                                    ->columnSpanFull(),

                                Forms\Components\Section::make('Hakkımızda Sayfa İçeriği')
                                    ->schema([
                                        Forms\Components\TextInput::make('about_welcome_title')
                                            ->label('Hoş Geldiniz Başlığı')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('about_welcome_description')
                                            ->label('Hoş Geldiniz Açıklaması')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('about_mission_title')
                                            ->label('Misyon Başlığı')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('about_vision_title')
                                            ->label('Vizyon Başlığı')
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('about_mission_content')
                                            ->label('Misyon İçeriği')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('about_vision_content')
                                            ->label('Vizyon İçeriği')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('counter_years_label')
                                            ->label('Deneyim Yılı Etiketi')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('counter_customers_label')
                                            ->label('Mutlu Müşteri Etiketi')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('counter_sessions_label')
                                            ->label('Terapi Seansı Etiketi')
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('counter_certifications_label')
                                            ->label('Sertifika/Ödül Etiketi')
                                            ->maxLength(255),
                                    ])
                                    ->columns(2)
                                    ->collapsible(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['locale'] ?? null)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->collapsible()
                            ->reorderable(false)
                            ->addActionLabel('Yeni Çeviri Ekle'),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = Setting::first();

        if (! $setting) {
            $setting = Setting::create([
                'logo' => $data['logo'] ?? null,
                'dark_logo' => $data['dark_logo'] ?? null,
                'favicon' => $data['favicon'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'contact_address' => $data['contact_address'] ?? null,
                'google_maps_url' => $data['google_maps_url'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'twitter' => $data['twitter'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'linkedin' => $data['linkedin'] ?? null,
                'youtube' => $data['youtube'] ?? null,
                'professional_title' => $data['professional_title'] ?? null,
                'rcc_number' => $data['rcc_number'] ?? null,
                'years_of_experience' => $data['years_of_experience'] ?? null,
                'rating' => $data['rating'] ?? null,
                'credentials' => $data['credentials'] ?? null,
                'therapeutic_approach' => $data['therapeutic_approach'] ?? null,
                'maintenance_mode' => $data['maintenance_mode'] ?? false,
                'happy_customers' => $data['happy_customers'] ?? 0,
                'therapy_sessions' => $data['therapy_sessions'] ?? 0,
                'certifications_awards' => $data['certifications_awards'] ?? 0,
            ]);
        } else {
            $setting->update([
                'logo' => $data['logo'] ?? null,
                'dark_logo' => $data['dark_logo'] ?? null,
                'favicon' => $data['favicon'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'contact_address' => $data['contact_address'] ?? null,
                'google_maps_url' => $data['google_maps_url'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? null,
                'facebook' => $data['facebook'] ?? null,
                'twitter' => $data['twitter'] ?? null,
                'instagram' => $data['instagram'] ?? null,
                'linkedin' => $data['linkedin'] ?? null,
                'youtube' => $data['youtube'] ?? null,
                'professional_title' => $data['professional_title'] ?? null,
                'rcc_number' => $data['rcc_number'] ?? null,
                'years_of_experience' => $data['years_of_experience'] ?? null,
                'rating' => $data['rating'] ?? null,
                'credentials' => $data['credentials'] ?? null,
                'therapeutic_approach' => $data['therapeutic_approach'] ?? null,
                'maintenance_mode' => $data['maintenance_mode'] ?? false,
                'happy_customers' => $data['happy_customers'] ?? 0,
                'therapy_sessions' => $data['therapy_sessions'] ?? 0,
                'certifications_awards' => $data['certifications_awards'] ?? 0,
            ]);
        }

        // Translations'ı kaydet
        if (isset($data['translations'])) {
            foreach ($data['translations'] as $translationData) {
                if (isset($translationData['id'])) {
                    // Mevcut çeviriyi güncelle
                    $setting->translations()->where('id', $translationData['id'])->update([
                        'locale' => $translationData['locale'],
                        'site_name' => $translationData['site_name'],
                        'site_description' => $translationData['site_description'] ?? null,
                        'footer_text' => $translationData['footer_text'] ?? null,
                        'about_welcome_title' => $translationData['about_welcome_title'] ?? null,
                        'about_welcome_description' => $translationData['about_welcome_description'] ?? null,
                        'about_mission_title' => $translationData['about_mission_title'] ?? null,
                        'about_mission_content' => $translationData['about_mission_content'] ?? null,
                        'about_vision_title' => $translationData['about_vision_title'] ?? null,
                        'about_vision_content' => $translationData['about_vision_content'] ?? null,
                        'counter_years_label' => $translationData['counter_years_label'] ?? null,
                        'counter_customers_label' => $translationData['counter_customers_label'] ?? null,
                        'counter_sessions_label' => $translationData['counter_sessions_label'] ?? null,
                        'counter_certifications_label' => $translationData['counter_certifications_label'] ?? null,
                    ]);
                } else {
                    // Yeni çeviri oluştur
                    $setting->translations()->create([
                        'locale' => $translationData['locale'],
                        'site_name' => $translationData['site_name'],
                        'site_description' => $translationData['site_description'] ?? null,
                        'footer_text' => $translationData['footer_text'] ?? null,
                        'about_welcome_title' => $translationData['about_welcome_title'] ?? null,
                        'about_welcome_description' => $translationData['about_welcome_description'] ?? null,
                        'about_mission_title' => $translationData['about_mission_title'] ?? null,
                        'about_mission_content' => $translationData['about_mission_content'] ?? null,
                        'about_vision_title' => $translationData['about_vision_title'] ?? null,
                        'about_vision_content' => $translationData['about_vision_content'] ?? null,
                        'counter_years_label' => $translationData['counter_years_label'] ?? null,
                        'counter_customers_label' => $translationData['counter_customers_label'] ?? null,
                        'counter_sessions_label' => $translationData['counter_sessions_label'] ?? null,
                        'counter_certifications_label' => $translationData['counter_certifications_label'] ?? null,
                    ]);
                }
            }
        }

        Notification::make()
            ->success()
            ->title('Ayarlar kaydedildi')
            ->send();
    }
}
