<?php

declare(strict_types=1);

use App\Models\Appointment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user for testing (posts require user_id)
    $this->user = \App\Models\User::factory()->create();

    // Create settings for testing (layout requires settings)
    $this->settings = \App\Models\Setting::create([
        'contact_email' => 'test@example.com',
        'contact_phone' => '+1234567890',
    ]);

    $this->settings->translations()->create([
        'locale' => 'en',
        'site_name' => 'Test Site',
        'site_description' => 'Test Description',
    ]);

    // Create a service for testing
    $this->service = Post::create([
        'type' => 'service',
        'slug_base' => 'test-service',
        'status' => 'published',
        'sort_order' => 1,
        'user_id' => $this->user->id,
    ]);

    $this->service->translations()->create([
        'locale' => 'en',
        'title' => 'Test Service',
        'slug' => 'test-service',
        'content' => 'Test service content',
    ]);
});

test('appointment page can be rendered', function () {
    $response = $this->get('/book-appointment');

    $response->assertOk();
});

test('appointment form displays services from database', function () {
    Volt::test('book-appointment')
        ->assertSee('Test Service');
});

test('can submit appointment with valid data', function () {
    $appointmentData = [
        'name' => 'John Doe',
        'phone' => '+1234567890',
        'service_id' => $this->service->id,
        'preferred_date' => now()->addDays(3)->format('Y-m-d'),
        'preferred_time' => '14:00',
        'message' => 'Test message',
    ];

    Volt::test('book-appointment')
        ->set('name', $appointmentData['name'])
        ->set('phone', $appointmentData['phone'])
        ->set('service_id', $appointmentData['service_id'])
        ->set('preferred_date', $appointmentData['preferred_date'])
        ->set('preferred_time', $appointmentData['preferred_time'])
        ->set('message', $appointmentData['message'])
        ->call('submit')
        ->assertHasNoErrors();

    expect(Appointment::count())->toBe(1);

    $appointment = Appointment::first();
    expect($appointment->name)->toBe($appointmentData['name']);
    expect($appointment->phone)->toBe($appointmentData['phone']);
    expect($appointment->service_id)->toBe($appointmentData['service_id']);
    expect($appointment->status)->toBe('pending');
});

test('appointment form validates required fields', function () {
    Volt::test('book-appointment')
        ->set('name', '')
        ->set('phone', '')
        ->set('service_id', '')
        ->set('preferred_date', '')
        ->set('preferred_time', '')
        ->call('submit')
        ->assertHasErrors(['name', 'phone', 'service_id', 'preferred_date', 'preferred_time']);
});

test('appointment form validates service exists', function () {
    Volt::test('book-appointment')
        ->set('name', 'John Doe')
        ->set('phone', '+1234567890')
        ->set('service_id', 9999) // Non-existent service
        ->set('preferred_date', now()->addDays(3)->format('Y-m-d'))
        ->set('preferred_time', '14:00')
        ->call('submit')
        ->assertHasErrors(['service_id']);
});

test('appointment form validates date is not in the past', function () {
    Volt::test('book-appointment')
        ->set('name', 'John Doe')
        ->set('phone', '+1234567890')
        ->set('service_id', $this->service->id)
        ->set('preferred_date', now()->subDays(1)->format('Y-m-d'))
        ->set('preferred_time', '14:00')
        ->call('submit')
        ->assertHasErrors(['preferred_date']);
});

test('appointment stores ip address and user agent', function () {
    Volt::test('book-appointment')
        ->set('name', 'John Doe')
        ->set('phone', '+1234567890')
        ->set('service_id', $this->service->id)
        ->set('preferred_date', now()->addDays(3)->format('Y-m-d'))
        ->set('preferred_time', '14:00')
        ->call('submit');

    $appointment = Appointment::first();

    expect($appointment->ip_address)->not->toBeNull();
    expect($appointment->user_agent)->not->toBeNull();
});

test('appointment form resets after successful submission', function () {
    Volt::test('book-appointment')
        ->set('name', 'John Doe')
        ->set('phone', '+1234567890')
        ->set('service_id', $this->service->id)
        ->set('preferred_date', now()->addDays(3)->format('Y-m-d'))
        ->set('preferred_time', '14:00')
        ->set('message', 'Test message')
        ->call('submit')
        ->assertSet('name', null)
        ->assertSet('phone', null)
        ->assertSet('service_id', null)
        ->assertSet('preferred_date', null)
        ->assertSet('preferred_time', null)
        ->assertSet('message', null);
});

test('appointment belongs to service', function () {
    $appointment = Appointment::create([
        'name' => 'John Doe',
        'phone' => '+1234567890',
        'service_id' => $this->service->id,
        'preferred_date' => now()->addDays(3),
        'preferred_time' => '14:00',
        'status' => 'pending',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Browser',
    ]);

    expect($appointment->service)->toBeInstanceOf(Post::class);
    expect($appointment->service->id)->toBe($this->service->id);
});
