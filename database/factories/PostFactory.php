<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['blog', 'page']),
            'slug_base' => fake()->unique()->slug(),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'featured_image' => fake()->optional()->imageUrl(),
            'user_id' => User::factory(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the post is a blog post.
     */
    public function blog(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'blog',
        ]);
    }

    /**
     * Indicate that the post is a page.
     */
    public function page(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'page',
        ]);
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
