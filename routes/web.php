<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Default language (English) routes - no prefix
Volt::route('/', 'home')->name('home');

// Turkish language routes with /tr prefix
Route::prefix('tr')->group(function (): void {
    Volt::route('/', 'home')->name('tr.home');
});
