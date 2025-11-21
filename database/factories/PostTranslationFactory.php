<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostTranslation>
 */
class PostTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'locale' => fake()->randomElement(['tr', 'en']),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(5, true),
            'excerpt' => fake()->optional()->sentence(),
            'meta_title' => fake()->optional()->sentence(),
            'meta_description' => fake()->optional()->text(160),
            'meta_keywords' => fake()->optional()->words(5, true),
            'og_image' => fake()->optional()->imageUrl(),
            'og_title' => fake()->optional()->sentence(),
            'og_description' => fake()->optional()->text(200),
            'robots' => fake()->optional()->randomElement(['noindex, nofollow', 'index, follow', 'noindex, follow']),
            'canonical_url' => fake()->optional()->url(),
            'slug' => fake()->unique()->slug(),
            'published_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the translation is in Turkish.
     */
    public function turkish(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'tr',
        ]);
    }

    /**
     * Indicate that the translation is in English.
     */
    public function english(): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => 'en',
        ]);
    }
}
