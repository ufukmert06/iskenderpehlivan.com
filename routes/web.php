<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Default language (English) routes - no prefix
Volt::route('/', 'home')->name('home');
Volt::route('/about', 'about')->name('about');
Volt::route('/contact', 'contact')->name('contact');
Volt::route('/services', 'services')->name('services');
Volt::route('/services/{slug}', 'service')->name('service.show');
Volt::route('/blog', 'blogs')->name('blogs');
Volt::route('/blog/{slug}', 'blog')->name('blog.show');
Volt::route('/book-appointment', 'book-appointment')->name('book-appointment');
Volt::route('/search', 'search')->name('search');
Volt::route('/terms-of-service', 'terms-of-service')->name('terms-of-service');
Volt::route('/privacy-policy', 'privacy-policy')->name('privacy-policy');

// Turkish language routes with /tr prefix
Route::prefix('tr')->group(function (): void {
    Volt::route('/', 'home')->name('tr.home');
    Volt::route('/hakkimda', 'about')->name('tr.about');
    Volt::route('/iletisim', 'contact')->name('tr.contact');
    Volt::route('/hizmetler', 'services')->name('tr.services');
    Volt::route('/hizmetler/{slug}', 'service')->name('tr.service.show');
    Volt::route('/blog', 'blogs')->name('tr.blogs');
    Volt::route('/blog/{slug}', 'blog')->name('tr.blog.show');
    Volt::route('/randevu-al', 'book-appointment')->name('tr.book-appointment');
    Volt::route('/ara', 'search')->name('tr.search');
    Volt::route('/kullanim-kosullari', 'terms-of-service')->name('tr.terms-of-service');
    Volt::route('/gizlilik-politikasi', 'privacy-policy')->name('tr.privacy-policy');
});
