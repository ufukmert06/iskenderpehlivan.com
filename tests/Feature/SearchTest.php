<?php

declare(strict_types=1);

use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('search');

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

    // Create a user for posts
    $this->user = User::factory()->create();
});

test('search page renders successfully', function (): void {
    $response = $this->get(route('search'));

    $response->assertSuccessful();
    $response->assertSeeLivewire('search');
});

test('search page renders with Turkish locale', function (): void {
    $response = $this->get(route('tr.search'));

    $response->assertSuccessful();
    $response->assertSeeLivewire('search');
});

test('search returns results for matching service title', function (): void {
    $service = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'test-service',
        'type' => 'service',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    PostTranslation::create([
        'post_id' => $service->id,
        'locale' => 'en',
        'title' => 'Therapy Service',
        'excerpt' => 'Professional therapy service',
        'content' => 'Full description of the therapy service',
        'slug' => 'therapy-service',
    ]);

    $response = $this->get(route('search', ['q' => 'Therapy']));

    $response->assertSuccessful();
    $response->assertSee('Therapy Service');
    $response->assertSee('1 result');
});

test('search returns results for matching service excerpt', function (): void {
    $service = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'counseling-service',
        'type' => 'service',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    PostTranslation::create([
        'post_id' => $service->id,
        'locale' => 'en',
        'title' => 'Counseling Service',
        'excerpt' => 'Professional counseling for individuals',
        'content' => 'Detailed information about counseling',
        'slug' => 'counseling-service',
    ]);

    $response = $this->get(route('search', ['q' => 'individuals']));

    $response->assertSuccessful();
    $response->assertSee('Counseling Service');
});

test('search returns results for matching service content', function (): void {
    $service = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'family-therapy',
        'type' => 'service',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    PostTranslation::create([
        'post_id' => $service->id,
        'locale' => 'en',
        'title' => 'Family Therapy',
        'excerpt' => 'Help for families',
        'content' => 'Comprehensive family therapy sessions for all ages',
        'slug' => 'family-therapy',
    ]);

    $response = $this->get(route('search', ['q' => 'comprehensive']));

    $response->assertSuccessful();
    $response->assertSee('Family Therapy');
});

test('search returns multiple results', function (): void {
    $service1 = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'service-1',
        'type' => 'service',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    PostTranslation::create([
        'post_id' => $service1->id,
        'locale' => 'en',
        'title' => 'Therapy Service One',
        'excerpt' => 'First service',
        'content' => 'Content for first service',
        'slug' => 'therapy-service-one',
    ]);

    $service2 = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'service-2',
        'type' => 'service',
        'status' => 'published',
        'sort_order' => 2,
    ]);

    PostTranslation::create([
        'post_id' => $service2->id,
        'locale' => 'en',
        'title' => 'Therapy Service Two',
        'excerpt' => 'Second service',
        'content' => 'Content for second service',
        'slug' => 'therapy-service-two',
    ]);

    $response = $this->get(route('search', ['q' => 'Therapy']));

    $response->assertSuccessful();
    $response->assertSee('Therapy Service One');
    $response->assertSee('Therapy Service Two');
    $response->assertSee('2 results');
});

test('search returns no results for non-matching query', function (): void {
    $service = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'test-service',
        'type' => 'service',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    PostTranslation::create([
        'post_id' => $service->id,
        'locale' => 'en',
        'title' => 'Therapy Service',
        'excerpt' => 'Professional therapy',
        'content' => 'Full description',
        'slug' => 'therapy-service',
    ]);

    $response = $this->get(route('search', ['q' => 'nonexistent']));

    $response->assertSuccessful();
    $response->assertSee('No results found');
    $response->assertDontSee('Therapy Service');
});

test('search only shows published posts', function (): void {
    $publishedService = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'published-service',
        'type' => 'service',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    PostTranslation::create([
        'post_id' => $publishedService->id,
        'locale' => 'en',
        'title' => 'Published Service',
        'excerpt' => 'This is published',
        'content' => 'Published content',
        'slug' => 'published-service',
    ]);

    $draftService = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'draft-service',
        'type' => 'service',
        'status' => 'draft',
        'sort_order' => 2,
    ]);

    PostTranslation::create([
        'post_id' => $draftService->id,
        'locale' => 'en',
        'title' => 'Draft Service',
        'excerpt' => 'This is draft',
        'content' => 'Draft content',
        'slug' => 'draft-service',
    ]);

    $response = $this->get(route('search', ['q' => 'Service']));

    $response->assertSuccessful();
    $response->assertSee('Published Service');
    $response->assertDontSee('Draft Service');
});

test('search works with blog posts', function (): void {
    $blog = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'blog-post',
        'type' => 'blog',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    PostTranslation::create([
        'post_id' => $blog->id,
        'locale' => 'en',
        'title' => 'Mental Health Blog Post',
        'excerpt' => 'Blog about mental health',
        'content' => 'Full blog content',
        'slug' => 'mental-health-blog-post',
    ]);

    $response = $this->get(route('search', ['q' => 'Mental Health']));

    $response->assertSuccessful();
    $response->assertSee('Mental Health Blog Post');
    $response->assertSee('Blog Post');
});

test('search handles empty query', function (): void {
    $response = $this->get(route('search'));

    $response->assertSuccessful();
    $response->assertSee('Enter a search term');
});

test('search is case insensitive', function (): void {
    $service = Post::create([
        'user_id' => $this->user->id,
        'slug_base' => 'test-service',
        'type' => 'service',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    PostTranslation::create([
        'post_id' => $service->id,
        'locale' => 'en',
        'title' => 'Therapy Service',
        'excerpt' => 'Professional therapy',
        'content' => 'Full description',
        'slug' => 'therapy-service',
    ]);

    $responseLower = $this->get(route('search', ['q' => 'therapy']));
    $responseUpper = $this->get(route('search', ['q' => 'THERAPY']));
    $responseMixed = $this->get(route('search', ['q' => 'ThErApY']));

    $responseLower->assertSee('Therapy Service');
    $responseUpper->assertSee('Therapy Service');
    $responseMixed->assertSee('Therapy Service');
});
