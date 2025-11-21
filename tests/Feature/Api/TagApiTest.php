<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

uses()->group('api', 'tags');

it('can list tags', function () {
    $tags = Tag::factory()->count(3)->create();

    foreach ($tags as $tag) {
        $tag->translations()->create([
            'locale' => 'tr',
            'name' => 'Etiket '.$tag->id,
            'slug' => 'etiket-'.$tag->id,
        ]);
    }

    $response = $this->getJson('/api/tags');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'slug',
                    'color',
                    'translation' => [
                        'locale',
                        'name',
                        'slug',
                    ],
                    'posts_count',
                ],
            ],
            'meta',
        ]);
});

it('can filter tags by type', function () {
    $tag1 = Tag::factory()->create(['type' => 'topic']);
    $tag2 = Tag::factory()->create(['type' => 'category']);

    $tag1->translations()->create([
        'locale' => 'tr',
        'name' => 'Konu',
        'slug' => 'konu',
    ]);

    $tag2->translations()->create([
        'locale' => 'tr',
        'name' => 'Kategori',
        'slug' => 'kategori',
    ]);

    $response = $this->getJson('/api/tags?type=topic');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can get a single tag by slug', function () {
    $tag = Tag::factory()->create([
        'slug_base' => 'laravel',
        'color' => '#FF2D20',
    ]);

    $tag->translations()->create([
        'locale' => 'tr',
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $response = $this->getJson('/api/tags/laravel?locale=tr');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                'slug' => 'laravel',
                'color' => '#FF2D20',
                'translation' => [
                    'name' => 'Laravel',
                    'locale' => 'tr',
                ],
            ],
        ]);
});

it('includes posts count', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();

    $tag->translations()->create([
        'locale' => 'tr',
        'name' => 'Test',
        'slug' => 'test',
    ]);

    $posts = Post::factory()->count(5)->create(['user_id' => $user->id]);
    foreach ($posts as $post) {
        $post->translations()->create([
            'locale' => 'tr',
            'title' => 'Post '.$post->id,
            'slug' => 'post-'.$post->id,
            'content' => '<p>Content</p>',
        ]);
        $post->tags()->attach($tag);
    }

    $response = $this->getJson('/api/tags');

    $response->assertSuccessful()
        ->assertJson([
            'data' => [
                [
                    'posts_count' => 5,
                ],
            ],
        ]);
});

it('returns 404 for non-existent tag', function () {
    $response = $this->getJson('/api/tags/non-existent');

    $response->assertNotFound();
});
