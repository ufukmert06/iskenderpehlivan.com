<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

uses()->group('api', 'tags');

it('can get all posts with a tag', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create(['slug_base' => 'laravel']);

    $tag->translations()->create([
        'locale' => 'tr',
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    // Create posts with this tag
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

    // Create a post WITHOUT this tag
    $otherPost = Post::factory()->create(['user_id' => $user->id]);
    $otherPost->translations()->create([
        'locale' => 'tr',
        'title' => 'Other Post',
        'slug' => 'other-post',
        'content' => '<p>Content</p>',
    ]);

    $response = $this->getJson('/api/tags/laravel/posts?locale=tr');

    $response->assertSuccessful()
        ->assertJsonCount(5, 'data')
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

it('can filter tag posts by type', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create(['slug_base' => 'php']);

    $tag->translations()->create([
        'locale' => 'tr',
        'name' => 'PHP',
        'slug' => 'php',
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
        $post->tags()->attach($tag);
    }

    $response = $this->getJson('/api/tags/php/posts?type=page');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('supports pagination for tag posts', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create(['slug_base' => 'javascript']);

    $tag->translations()->create([
        'locale' => 'tr',
        'name' => 'JavaScript',
        'slug' => 'javascript',
    ]);

    $posts = Post::factory()->count(30)->create(['user_id' => $user->id]);
    foreach ($posts as $post) {
        $post->translations()->create([
            'locale' => 'tr',
            'title' => 'Post '.$post->id,
            'slug' => 'post-'.$post->id,
            'content' => '<p>Content</p>',
        ]);
        $post->tags()->attach($tag);
    }

    $response = $this->getJson('/api/tags/javascript/posts?per_page=15&page=1');

    $response->assertSuccessful()
        ->assertJsonCount(15, 'data')
        ->assertJson([
            'meta' => [
                'total' => 30,
                'per_page' => 15,
                'current_page' => 1,
                'last_page' => 2,
            ],
        ]);
});

it('returns 404 for non-existent tag posts endpoint', function () {
    $response = $this->getJson('/api/tags/non-existent/posts');

    $response->assertNotFound();
});
