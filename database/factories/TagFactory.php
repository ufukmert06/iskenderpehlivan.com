<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['post', 'category']),
            'slug_base' => fake()->unique()->slug(),
            'color' => fake()->optional()->hexColor(),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the tag is for posts.
     */
    public function forPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'post',
        ]);
    }

    /**
     * Indicate that the tag is for categories.
     */
    public function forCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'category',
        ]);
    }
}
