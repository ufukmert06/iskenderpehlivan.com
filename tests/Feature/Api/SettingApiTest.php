<?php

use App\Models\Setting;

uses()->group('api', 'settings');

it('can get settings', function () {
    $setting = Setting::factory()->create([
        'logo' => 'settings/logo.png',
        'favicon' => 'settings/favicon.ico',
        'contact_email' => 'info@example.com',
        'contact_phone' => '+90 555 123 4567',
        'facebook' => 'https://facebook.com/example',
        'maintenance_mode' => false,
    ]);

    $setting->translations()->create([
        'locale' => 'tr',
        'site_name' => 'My Site',
        'site_description' => 'A great website',
        'footer_text' => '© 2025 My Site',
    ]);

    $response = $this->getJson('/api/settings?locale=tr');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'contact' => [
                    'email' => 'info@example.com',
                    'phone' => '+90 555 123 4567',
                ],
                'social_media' => [
                    'facebook' => 'https://facebook.com/example',
                ],
                'maintenance_mode' => false,
                'translation' => [
                    'locale' => 'tr',
                    'site_name' => 'My Site',
                ],
            ],
        ])
        ->assertJsonStructure([
            'data' => [
                'logo',
                'favicon',
                'contact',
                'social_media',
                'maintenance_mode',
                'translation',
            ],
        ]);
});

it('returns 404 when no settings exist', function () {
    $response = $this->getJson('/api/settings');

    $response->assertNotFound();
});

it('supports multiple locales', function () {
    $setting = Setting::factory()->create();

    $setting->translations()->create([
        'locale' => 'tr',
        'site_name' => 'Türkçe Site',
        'site_description' => 'Türkçe açıklama',
    ]);

    $setting->translations()->create([
        'locale' => 'en',
        'site_name' => 'English Site',
        'site_description' => 'English description',
    ]);

    $trResponse = $this->getJson('/api/settings?locale=tr');
    $enResponse = $this->getJson('/api/settings?locale=en');

    $trResponse->assertJson([
        'data' => [
            'translation' => [
                'locale' => 'tr',
                'site_name' => 'Türkçe Site',
            ],
        ],
    ]);

    $enResponse->assertJson([
        'data' => [
            'translation' => [
                'locale' => 'en',
                'site_name' => 'English Site',
            ],
        ],
    ]);
});
