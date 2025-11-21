<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

uses()->group('api', 'categories');

it('can get all posts in a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['slug_base' => 'tech']);

    $category->translations()->create([
        'locale' => 'tr',
        'name' => 'Teknoloji',
        'slug' => 'teknoloji',
    ]);

    // Create posts in this category
    $posts = Post::factory()->count(3)->create(['user_id' => $user->id]);
    foreach ($posts as $post) {
        $post->translations()->create([
            'locale' => 'tr',
            'title' => 'Post '.$post->id,
            'slug' => 'post-'.$post->id,
            'content' => '<p>Content</p>',
        ]);
        $post->categories()->attach($category);
    }

    // Create a post NOT in this category
    $otherPost = Post::factory()->create(['user_id' => $user->id]);
    $otherPost->translations()->create([
        'locale' => 'tr',
        'title' => 'Other Post',
        'slug' => 'other-post',
        'content' => '<p>Content</p>',
    ]);

    $response = $this->getJson('/api/categories/teknoloji/posts?locale=tr');

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'slug',
                    'translation' => [
                        'title',
                        'content',
                    ],
                ],
            ],
            'meta',
        ]);
});

it('can filter category posts by type', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['slug_base' => 'tech']);

    $category->translations()->create([
        'locale' => 'tr',
        'name' => 'Teknoloji',
        'slug' => 'teknoloji',
    ]);

    $blogPost = Post::factory()->create([
        'user_id' => $user->id,
        'type' => 'blog',
    ]);
    $pagePost = Post::factory()->create([
        'user_id' => $user->id,
        'type' => 'page',
    ]);

    foreach ([$blogPost, $pagePost] as $post) {
        $post->translations()->create([
            'locale' => 'tr',
            'title' => 'Post '.$post->id,
            'slug' => 'post-'.$post->id,
            'content' => '<p>Content</p>',
        ]);
        $post->categories()->attach($category);
    }

    $response = $this->getJson('/api/categories/teknoloji/posts?type=blog');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('supports pagination for category posts', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['slug_base' => 'tech']);

    $category->translations()->create([
        'locale' => 'tr',
        'name' => 'Teknoloji',
        'slug' => 'teknoloji',
    ]);

    $posts = Post::factory()->count(25)->create(['user_id' => $user->id]);
    foreach ($posts as $post) {
        $post->translations()->create([
            'locale' => 'tr',
            'title' => 'Post '.$post->id,
            'slug' => 'post-'.$post->id,
            'content' => '<p>Content</p>',
        ]);
        $post->categories()->attach($category);
    }

    $response = $this->getJson('/api/categories/teknoloji/posts?per_page=10&page=1');

    $response->assertSuccessful()
        ->assertJsonCount(10, 'data')
        ->assertJson([
            'meta' => [
                'total' => 25,
                'per_page' => 10,
                'current_page' => 1,
                'last_page' => 3,
            ],
        ]);
});

it('returns 404 for non-existent category posts endpoint', function () {
    $response = $this->getJson('/api/categories/non-existent/posts');

    $response->assertNotFound();
});
