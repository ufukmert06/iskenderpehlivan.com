<?php

use App\Filament\Widgets\LatestPosts;
use App\Filament\Widgets\StatsOverview;
use App\Models\Post;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('stats overview widget renders successfully', function () {
    livewire(StatsOverview::class)
        ->assertSuccessful();
});

test('stats overview displays correct post counts', function () {
    Post::factory()->create(['status' => 'published']);
    Post::factory()->create(['status' => 'published']);
    Post::factory()->create(['status' => 'draft']);

    $widget = livewire(StatsOverview::class);

    $stats = $widget->instance->getStats();

    expect($stats)->toHaveCount(6);
});

test('latest posts widget renders successfully', function () {
    livewire(LatestPosts::class)
        ->assertSuccessful();
});

test('latest posts widget shows recent posts', function () {
    $posts = Post::factory()->count(5)->create();

    livewire(LatestPosts::class)
        ->assertCanSeeTableRecords($posts);
});
