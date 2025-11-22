<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Default language (English) routes - no prefix
Volt::route('/', 'home')->name('home');
Volt::route('/contact', 'contact')->name('contact');
Volt::route('/services/{slug}', 'service')->name('service.show');

// Turkish language routes with /tr prefix
Route::prefix('tr')->group(function (): void {
    Volt::route('/', 'home')->name('tr.home');
    Volt::route('/iletisim', 'contact')->name('tr.contact');
    Volt::route('/hizmetler/{slug}', 'service')->name('tr.service.show');
});
