<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Default language (English) routes - no prefix
Volt::route('/', 'home')->name('home');
Volt::route('/about', 'about')->name('about');
Volt::route('/contact', 'contact')->name('contact');
Volt::route('/services', 'services')->name('services');
Volt::route('/services/{slug}', 'service')->name('service.show');
Volt::route('/book-appointment', 'book-appointment')->name('book-appointment');
Volt::route('/search', 'search')->name('search');

// Turkish language routes with /tr prefix
Route::prefix('tr')->group(function (): void {
    Volt::route('/', 'home')->name('tr.home');
    Volt::route('/hakkimda', 'about')->name('tr.about');
    Volt::route('/iletisim', 'contact')->name('tr.contact');
    Volt::route('/hizmetler', 'services')->name('tr.services');
    Volt::route('/hizmetler/{slug}', 'service')->name('tr.service.show');
    Volt::route('/randevu-al', 'book-appointment')->name('tr.book-appointment');
    Volt::route('/ara', 'search')->name('tr.search');
});
