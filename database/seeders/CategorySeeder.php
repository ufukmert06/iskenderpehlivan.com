<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Blog categories
        $blogCategories = Category::factory()
            ->count(5)
            ->blog()
            ->create()
            ->each(function (Category $category) {
                CategoryTranslation::factory()->turkish()->for($category)->create();
                CategoryTranslation::factory()->english()->for($category)->create();
            });

        // Page categories
        Category::factory()
            ->count(3)
            ->page()
            ->create()
            ->each(function (Category $category) {
                CategoryTranslation::factory()->turkish()->for($category)->create();
                CategoryTranslation::factory()->english()->for($category)->create();
            });

        // Nested categories (subcategories)
        $blogCategories->take(2)->each(function (Category $parent) {
            Category::factory()
                ->count(2)
                ->blog()
                ->withParent($parent)
                ->create()
                ->each(function (Category $category) {
                    CategoryTranslation::factory()->turkish()->for($category)->create();
                    CategoryTranslation::factory()->english()->for($category)->create();
                });
        });
    }
}
