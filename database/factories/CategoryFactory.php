<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
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
            'parent_id' => null,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the category is a blog category.
     */
    public function blog(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'blog',
        ]);
    }

    /**
     * Indicate that the category is a page category.
     */
    public function page(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'page',
        ]);
    }

    /**
     * Indicate that the category has a parent.
     */
    public function withParent(?Category $parent = null): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent?->id ?? Category::factory()->create()->id,
        ]);
    }
}
