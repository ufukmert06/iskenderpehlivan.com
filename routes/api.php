<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Public - Readonly)
|--------------------------------------------------------------------------
|
| These routes are public and readonly. They serve content to the Next.js
| frontend application. No authentication required.
|
*/

// Posts (Blogs and Pages)
Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('api.posts.index');
    Route::get('/{slug}', [PostController::class, 'show'])->name('api.posts.show');
});

// Categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('api.categories.show');
    Route::get('/{slug}/posts', [CategoryController::class, 'posts'])->name('api.categories.posts');
});

// Tags
Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('api.tags.index');
    Route::get('/{slug}', [TagController::class, 'show'])->name('api.tags.show');
    Route::get('/{slug}/posts', [TagController::class, 'posts'])->name('api.tags.posts');
});

// Settings
Route::get('/settings', [SettingController::class, 'index'])->name('api.settings.index');

// Contact Form
Route::post('/contact', [ContactController::class, 'store'])->name('api.contact.store');

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('api.health');

// Authenticated routes (for future use if needed)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
