<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'ufukmert06@gmail.com'],
            [
                'name' => 'Ufuk MERT',
                'password' => Hash::make('12345678'),
            ]
        );

        // Create or update settings with İskender Pehlivan's information
        $setting = Setting::firstOrCreate(
            [],
            [
                'logo' => null,
                'favicon' => null,
                'contact_email' => 'iskender@example.com',
                'contact_phone' => '+1 (604) 341-9584',
                'contact_address' => 'SFU Burnaby Campus V5A 0A8, BC',
                'whatsapp' => '+1 (604) 341-9584',
                'rcc_number' => 'RCC #25598',
                'professional_title' => 'Registered Clinical Counsellor (RCC)',
                'years_of_experience' => 6,
                'rating' => '5.0',
                'credentials' => 'Master\'s degree in Psychology (2019), City University of Seattle graduate (2023), BC Association of Clinical Counsellors member',
                'therapeutic_approach' => 'Primary methodology: Cognitive Behavioural Therapy (CBT); Intensive Short-Term Dynamic Psychotherapy (ISTDP) with children',
                'maintenance_mode' => false,
            ]
        );

        // Add translations for settings
        $setting->translations()->where('locale', 'tr')->firstOrCreate(
            ['locale' => 'tr'],
            [
                'site_name' => 'İskender Pehlivan - Danışmanlık Hizmetleri',
                'site_description' => 'Profesyonel terapi ve danışmanlık hizmetleri',
                'footer_text' => 'Tüm hakları saklıdır.',
            ]
        );

        $setting->translations()->where('locale', 'en')->firstOrCreate(
            ['locale' => 'en'],
            [
                'site_name' => 'İskender Pehlivan - Counselling Services',
                'site_description' => 'Professional therapy and counselling services',
                'footer_text' => 'All rights reserved.',
            ]
        );

        // Create services
        $services = [
            [
                'name' => 'Individual Therapy',
                'description' => 'Support for anxiety, depression, low self-esteem, and emotional challenges',
            ],
            [
                'name' => 'Couples Counselling',
                'description' => 'Communication, trust-building, and conflict resolution',
            ],
            [
                'name' => 'Child & Family Therapy',
                'description' => 'Family dynamics, life transitions, and stress management',
            ],
            [
                'name' => 'Immigration & Cultural Adjustment',
                'description' => 'Specialized support for relocation challenges and cultural adaptation',
            ],
            [
                'name' => 'Bilingual Services',
                'description' => 'Services available in English and Turkish',
            ],
            [
                'name' => 'Online Sessions',
                'description' => 'Virtual counselling options for flexibility and convenience',
            ],
        ];

        // Clear existing services if needed (only on fresh seed)
        if (Service::count() === 0) {
            foreach ($services as $index => $serviceData) {
                $service = Service::create(['sort_order' => $index + 1]);

                $service->translations()->createMany([
                    [
                        'locale' => 'en',
                        'name' => $serviceData['name'],
                        'description' => $serviceData['description'],
                    ],
                    [
                        'locale' => 'tr',
                        'name' => match ($serviceData['name']) {
                            'Individual Therapy' => 'Bireysel Terapi',
                            'Couples Counselling' => 'Çift Danışmanlığı',
                            'Child & Family Therapy' => 'Çocuk ve Aile Terapisi',
                            'Immigration & Cultural Adjustment' => 'Göç ve Kültürel Uyum',
                            'Bilingual Services' => 'İki Dilli Hizmetler',
                            'Online Sessions' => 'Çevrimiçi Seanslar',
                            default => $serviceData['name'],
                        },
                        'description' => match ($serviceData['name']) {
                            'Individual Therapy' => 'Kaygı, depresyon, düşük öz saygı ve duygusal zorluklar için destek',
                            'Couples Counselling' => 'İletişim, güven oluşturma ve çatışma çözümü',
                            'Child & Family Therapy' => 'Aile dinamikleri, yaşam geçişleri ve stres yönetimi',
                            'Immigration & Cultural Adjustment' => 'Yer değiştirme zorlukları ve kültürel uyum için uzmanlaşmış destek',
                            'Bilingual Services' => 'İngilizce ve Türkçede sunulan hizmetler',
                            'Online Sessions' => 'Esneklik ve kolaylık için sanal danışmanlık seçenekleri',
                            default => $serviceData['description'],
                        },
                    ],
                ]);
            }
        }

        $this->call([
            //            CategorySeeder::class,
            //            PostSeeder::class,
        ]);
    }
}
