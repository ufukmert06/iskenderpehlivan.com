<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\TagTranslation;

test('can create tag', function () {
    $tag = Tag::create([
        'type' => 'post',
        'slug_base' => 'laravel',
        'color' => '#FF2D20',
        'sort_order' => 0,
    ]);

    expect($tag)->toBeInstanceOf(Tag::class);
    expect($tag->type)->toBe('post');
    expect($tag->slug_base)->toBe('laravel');
});

test('can add translations to tag', function () {
    $tag = Tag::create([
        'type' => 'post',
        'slug_base' => 'php',
    ]);

    $tag->translations()->create([
        'locale' => 'tr',
        'name' => 'PHP',
        'slug' => 'php',
        'description' => 'PHP programlama dili',
    ]);

    $tag->translations()->create([
        'locale' => 'en',
        'name' => 'PHP',
        'slug' => 'php',
        'description' => 'PHP programming language',
    ]);

    expect($tag->translations)->toHaveCount(2);

    $trTranslation = $tag->translation('tr');
    expect($trTranslation)->toBeInstanceOf(TagTranslation::class);
    expect($trTranslation->description)->toBe('PHP programlama dili');

    $enTranslation = $tag->translation('en');
    expect($enTranslation)->toBeInstanceOf(TagTranslation::class);
    expect($enTranslation->description)->toBe('PHP programming language');
});

test('can attach tags to posts', function () {
    $tag = Tag::factory()->create();
    $post = Post::factory()->create();

    $post->tags()->attach($tag);

    expect($post->tags)->toHaveCount(1);
    expect($post->tags->first()->id)->toBe($tag->id);
});

test('can attach multiple tags to post', function () {
    $tags = Tag::factory()->count(3)->create();
    $post = Post::factory()->create();

    $post->tags()->attach($tags);

    expect($post->tags)->toHaveCount(3);
});

test('tag cascades delete to translations', function () {
    $tag = Tag::create([
        'type' => 'post',
        'slug_base' => 'test',
    ]);

    $tag->translations()->create([
        'locale' => 'tr',
        'name' => 'Test',
        'slug' => 'test',
    ]);

    expect(TagTranslation::count())->toBe(1);

    $tag->delete();

    expect(TagTranslation::count())->toBe(0);
});

test('deleting tag removes post associations', function () {
    $tag = Tag::factory()->create();
    $post = Post::factory()->create();

    $post->tags()->attach($tag);

    expect($post->tags)->toHaveCount(1);

    $tag->delete();

    expect($post->fresh()->tags)->toHaveCount(0);
});

test('can query posts by tag', function () {
    $tag = Tag::factory()->create();
    $post1 = Post::factory()->create();
    $post2 = Post::factory()->create();
    $post3 = Post::factory()->create();

    $post1->tags()->attach($tag);
    $post2->tags()->attach($tag);

    $postsWithTag = Post::whereHas('tags', function ($query) use ($tag) {
        $query->where('tags.id', $tag->id);
    })->get();

    expect($postsWithTag)->toHaveCount(2);
    expect($postsWithTag->pluck('id')->toArray())->toContain($post1->id, $post2->id);
    expect($postsWithTag->pluck('id')->toArray())->not->toContain($post3->id);
});
