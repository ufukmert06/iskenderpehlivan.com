<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TagTranslation>
 */
class TagTranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tag_id' => Tag::factory(),
            'locale' => fake()->randomElement(['tr', 'en']),
            'name' => fake()->words(2, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->optional()->sentence(),
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
