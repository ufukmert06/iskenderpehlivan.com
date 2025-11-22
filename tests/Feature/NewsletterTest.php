<?php

declare(strict_types=1);

use App\Models\Newsletter;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class)->group('newsletter');

beforeEach(function (): void {
    // Create settings
    $this->settings = Setting::create([
        'site_name' => 'Test Site',
        'email' => 'test@example.com',
        'phone' => '+1234567890',
        'address' => 'Test Address',
        'logo' => 'test-logo.png',
        'favicon' => 'test-favicon.png',
    ]);
});

test('newsletter can be created with valid email', function (): void {
    $newsletter = Newsletter::create([
        'email' => 'test@example.com',
        'status' => 'active',
        'ip_address' => '127.0.0.1',
        'subscribed_at' => now(),
    ]);

    expect($newsletter)->toBeInstanceOf(Newsletter::class);
    expect($newsletter->email)->toBe('test@example.com');
    expect($newsletter->status)->toBe('active');
    expect($newsletter->isActive())->toBeTrue();
});

test('newsletter subscription form works', function (): void {
    Volt::test('components.footer')
        ->set('newsletter_email', 'subscriber@example.com')
        ->call('subscribe')
        ->assertHasNoErrors();

    expect(Newsletter::where('email', 'subscriber@example.com')->exists())->toBeTrue();
});

test('newsletter subscription requires email', function (): void {
    Volt::test('components.footer')
        ->set('newsletter_email', '')
        ->call('subscribe')
        ->assertHasErrors(['newsletter_email' => 'required']);
});

test('newsletter subscription requires valid email', function (): void {
    Volt::test('components.footer')
        ->set('newsletter_email', 'invalid-email')
        ->call('subscribe')
        ->assertHasErrors(['newsletter_email' => 'email']);
});

test('newsletter subscription prevents duplicates', function (): void {
    Newsletter::create([
        'email' => 'duplicate@example.com',
        'status' => 'active',
        'ip_address' => '127.0.0.1',
        'subscribed_at' => now(),
    ]);

    Volt::test('components.footer')
        ->set('newsletter_email', 'duplicate@example.com')
        ->call('subscribe')
        ->assertHasErrors(['newsletter_email' => 'unique']);
});

test('newsletter subscription stores ip address', function (): void {
    Volt::test('components.footer')
        ->set('newsletter_email', 'iptest@example.com')
        ->call('subscribe');

    $newsletter = Newsletter::where('email', 'iptest@example.com')->first();

    expect($newsletter->ip_address)->not->toBeNull();
});

test('newsletter subscription shows success message', function (): void {
    Volt::test('components.footer')
        ->set('newsletter_email', 'success@example.com')
        ->call('subscribe')
        ->assertSessionHas('newsletter_success');
});

test('newsletter subscription clears email field after success', function (): void {
    Volt::test('components.footer')
        ->set('newsletter_email', 'clear@example.com')
        ->call('subscribe')
        ->assertSet('newsletter_email', '');
});

test('newsletter can be unsubscribed', function (): void {
    $newsletter = Newsletter::create([
        'email' => 'unsubscribe@example.com',
        'status' => 'active',
        'ip_address' => '127.0.0.1',
        'subscribed_at' => now(),
    ]);

    expect($newsletter->isActive())->toBeTrue();

    $newsletter->unsubscribe();

    expect($newsletter->fresh()->status)->toBe('unsubscribed');
    expect($newsletter->fresh()->isActive())->toBeFalse();
    expect($newsletter->fresh()->unsubscribed_at)->not->toBeNull();
});

test('newsletter can be resubscribed', function (): void {
    $newsletter = Newsletter::create([
        'email' => 'resubscribe@example.com',
        'status' => 'unsubscribed',
        'ip_address' => '127.0.0.1',
        'subscribed_at' => now()->subDays(10),
        'unsubscribed_at' => now()->subDays(5),
    ]);

    expect($newsletter->isActive())->toBeFalse();

    $newsletter->resubscribe();

    expect($newsletter->fresh()->status)->toBe('active');
    expect($newsletter->fresh()->isActive())->toBeTrue();
    expect($newsletter->fresh()->unsubscribed_at)->toBeNull();
});

test('newsletter stores subscribed_at timestamp', function (): void {
    Volt::test('components.footer')
        ->set('newsletter_email', 'timestamp@example.com')
        ->call('subscribe');

    $newsletter = Newsletter::where('email', 'timestamp@example.com')->first();

    expect($newsletter->subscribed_at)->not->toBeNull();
    expect($newsletter->subscribed_at)->toBeInstanceOf(Carbon\Carbon::class);
});

test('active newsletters can be counted', function (): void {
    Newsletter::create(['email' => 'active1@example.com', 'status' => 'active', 'subscribed_at' => now()]);
    Newsletter::create(['email' => 'active2@example.com', 'status' => 'active', 'subscribed_at' => now()]);
    Newsletter::create(['email' => 'inactive@example.com', 'status' => 'unsubscribed', 'subscribed_at' => now()]);

    $activeCount = Newsletter::where('status', 'active')->count();

    expect($activeCount)->toBe(2);
});

test('newsletter email must be unique', function (): void {
    Newsletter::create([
        'email' => 'unique@example.com',
        'status' => 'active',
        'subscribed_at' => now(),
    ]);

    expect(fn () => Newsletter::create([
        'email' => 'unique@example.com',
        'status' => 'active',
        'subscribed_at' => now(),
    ]))->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});
