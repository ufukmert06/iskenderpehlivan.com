<?php

use App\Models\Setting;
use App\Models\SettingTranslation;

test('can create setting record', function () {
    $setting = Setting::create([
        'logo' => 'settings/logo.png',
        'favicon' => 'settings/favicon.ico',
        'contact_email' => 'info@example.com',
        'contact_phone' => '+90 555 123 4567',
        'contact_address' => 'Istanbul, Turkey',
        'facebook' => 'https://facebook.com/example',
        'twitter' => 'https://twitter.com/example',
        'instagram' => 'https://instagram.com/example',
        'linkedin' => 'https://linkedin.com/example',
        'youtube' => 'https://youtube.com/example',
        'maintenance_mode' => false,
    ]);

    expect($setting)->toBeInstanceOf(Setting::class);
    expect($setting->contact_email)->toBe('info@example.com');
    expect($setting->maintenance_mode)->toBeFalse();
});

test('can add translations to setting', function () {
    $setting = Setting::create([
        'contact_email' => 'info@example.com',
    ]);

    $setting->translations()->create([
        'locale' => 'tr',
        'site_name' => 'Test Sitesi',
        'site_description' => 'Bu bir test sitesidir',
        'footer_text' => '© 2025 Test Sitesi',
    ]);

    $setting->translations()->create([
        'locale' => 'en',
        'site_name' => 'Test Site',
        'site_description' => 'This is a test site',
        'footer_text' => '© 2025 Test Site',
    ]);

    expect($setting->translations)->toHaveCount(2);

    $trTranslation = $setting->translation('tr');
    expect($trTranslation)->toBeInstanceOf(SettingTranslation::class);
    expect($trTranslation->site_name)->toBe('Test Sitesi');

    $enTranslation = $setting->translation('en');
    expect($enTranslation)->toBeInstanceOf(SettingTranslation::class);
    expect($enTranslation->site_name)->toBe('Test Site');
});

test('translation returns correct locale', function () {
    $setting = Setting::create([
        'contact_email' => 'info@example.com',
    ]);

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

    app()->setLocale('tr');
    expect($setting->translation()->site_name)->toBe('Türkçe Site');

    app()->setLocale('en');
    expect($setting->translation()->site_name)->toBe('English Site');
});

test('setting cascades delete to translations', function () {
    $setting = Setting::create([
        'contact_email' => 'info@example.com',
    ]);

    $setting->translations()->create([
        'locale' => 'tr',
        'site_name' => 'Test',
        'site_description' => 'Test',
    ]);

    expect(SettingTranslation::count())->toBe(1);

    $setting->delete();

    expect(SettingTranslation::count())->toBe(0);
});

test('maintenance mode can be toggled', function () {
    $setting = Setting::create([
        'maintenance_mode' => false,
    ]);

    expect($setting->maintenance_mode)->toBeFalse();

    $setting->update(['maintenance_mode' => true]);

    expect($setting->fresh()->maintenance_mode)->toBeTrue();
});
