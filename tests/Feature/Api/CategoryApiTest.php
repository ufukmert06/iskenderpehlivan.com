<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

uses()->group('api', 'categories');

it('can list categories', function () {
    $categories = Category::factory()->count(3)->create();

    foreach ($categories as $category) {
        $category->translations()->create([
            'locale' => 'tr',
            'name' => 'Kategori '.$category->id,
            'slug' => 'kategori-'.$category->id,
            'description' => 'Açıklama',
        ]);
    }

    $response = $this->getJson('/api/categories');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'slug',
                    'translation' => [
                        'locale',
                        'name',
                        'slug',
                        'description',
                    ],
                    'posts_count',
                ],
            ],
            'meta',
        ]);
});

it('can get a single category by slug', function () {
    $category = Category::factory()->create(['slug_base' => 'tech']);

    $category->translations()->create([
        'locale' => 'tr',
        'name' => 'Teknoloji',
        'slug' => 'teknoloji',
        'description' => 'Teknoloji kategorisi',
    ]);

    $response = $this->getJson('/api/categories/teknoloji?locale=tr');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'slug' => 'teknoloji',
                'translation' => [
                    'name' => 'Teknoloji',
                    'locale' => 'tr',
                ],
            ],
        ]);
});

it('includes posts count', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $category->translations()->create([
        'locale' => 'tr',
        'name' => 'Test',
        'slug' => 'test',
    ]);

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

    $response = $this->getJson('/api/categories');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                [
                    'posts_count' => 3,
                ],
            ],
        ]);
});

it('returns 404 for non-existent category', function () {
    $response = $this->getJson('/api/categories/non-existent');

    $response->assertNotFound();
});
