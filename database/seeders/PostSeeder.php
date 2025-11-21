<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        // Blog posts
        Post::factory()
            ->count(10)
            ->blog()
            ->published()
            ->for($user)
            ->create()
            ->each(function (Post $post) {
                PostTranslation::factory()->turkish()->for($post)->create();
                PostTranslation::factory()->english()->for($post)->create();
            });

        // Pages
        Post::factory()
            ->count(5)
            ->page()
            ->published()
            ->for($user)
            ->create()
            ->each(function (Post $post) {
                PostTranslation::factory()->turkish()->for($post)->create();
                PostTranslation::factory()->english()->for($post)->create();
            });

        // Draft posts
        Post::factory()
            ->count(3)
            ->blog()
            ->draft()
            ->for($user)
            ->create()
            ->each(function (Post $post) {
                PostTranslation::factory()->turkish()->for($post)->create();
            });
    }
}
