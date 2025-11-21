<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

uses()->group('api', 'posts');

it('can list posts', function () {
    $user = User::factory()->create();
    $posts = Post::factory()->count(5)->create([
        'user_id' => $user->id,
        'type' => 'blog',
        'status' => 'published',
    ]);

    // Create translations
    foreach ($posts as $post) {
        $post->translations()->create([
            'locale' => 'tr',
            'title' => 'Test Başlık '.$post->id,
            'slug' => 'test-baslik-'.$post->id,
            'content' => '<p>Test içerik</p>',
            'excerpt' => 'Test özet',
        ]);
    }

    $response = $this->getJson('/api/posts');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'slug',
                    'status',
                    'translation' => [
                        'locale',
                        'title',
                        'slug',
                        'content',
                    ],
                ],
            ],
            'meta',
        ]);
});

it('can filter posts by type', function () {
    $user = User::factory()->create();

    $blogPost = Post::factory()->create([
        'user_id' => $user->id,
        'type' => 'blog',
    ]);

    $pagePost = Post::factory()->create([
        'user_id' => $user->id,
        'type' => 'page',
    ]);

    $blogPost->translations()->create([
        'locale' => 'tr',
        'title' => 'Blog Yazısı',
        'slug' => 'blog-yazisi',
        'content' => '<p>Blog içerik</p>',
    ]);

    $pagePost->translations()->create([
        'locale' => 'tr',
        'title' => 'Sayfa',
        'slug' => 'sayfa',
        'content' => '<p>Sayfa içerik</p>',
    ]);

    $response = $this->getJson('/api/posts?type=blog');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can get a single post by slug', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $user->id,
        'slug_base' => 'test-post',
    ]);

    $post->translations()->create([
        'locale' => 'tr',
        'title' => 'Test Yazı',
        'slug' => 'test-yazi',
        'content' => '<p>İçerik</p>',
        'excerpt' => 'Özet',
    ]);

    $response = $this->getJson('/api/posts/test-yazi?locale=tr');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'slug' => 'test-yazi',
                'translation' => [
                    'title' => 'Test Yazı',
                    'locale' => 'tr',
                ],
            ],
        ]);
});

it('returns 404 for non-existent post', function () {
    $response = $this->getJson('/api/posts/non-existent');

    $response->assertNotFound();
});

it('can filter posts by category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['slug_base' => 'tech']);

    $category->translations()->create([
        'locale' => 'tr',
        'name' => 'Teknoloji',
        'slug' => 'teknoloji',
    ]);

    $post = Post::factory()->create(['user_id' => $user->id]);
    $post->translations()->create([
        'locale' => 'tr',
        'title' => 'Tech Post',
        'slug' => 'tech-post',
        'content' => '<p>Content</p>',
    ]);
    $post->categories()->attach($category);

    $response = $this->getJson('/api/posts?category=teknoloji');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('supports locale parameter', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $post->translations()->create([
        'locale' => 'tr',
        'title' => 'Türkçe Başlık',
        'slug' => 'turkce-baslik',
        'content' => '<p>Türkçe içerik</p>',
    ]);

    $post->translations()->create([
        'locale' => 'en',
        'title' => 'English Title',
        'slug' => 'english-title',
        'content' => '<p>English content</p>',
    ]);

    $response = $this->getJson('/api/posts?locale=en');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                [
                    'translation' => [
                        'locale' => 'en',
                        'title' => 'English Title',
                    ],
                ],
            ],
        ]);
});
