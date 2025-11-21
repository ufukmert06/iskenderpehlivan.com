<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoryTranslation>
 */
class CategoryTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'locale' => fake()->randomElement(['tr', 'en']),
            'name' => fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'slug' => fake()->unique()->slug(),
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
