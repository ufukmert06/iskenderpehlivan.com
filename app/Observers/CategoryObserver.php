<?php

namespace App\Observers;

use App\Models\Category;
use App\Traits\CachesApiResponses;

class CategoryObserver
{
    use CachesApiResponses;

    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $this->clearCache();
    }

    /**
     * Clear relevant caches when category changes
     */
    protected function clearCache(): void
    {
        // Increment cache versions for categories and posts
        // Posts cache includes category relationships
        self::incrementCacheVersion('categories');
        self::incrementCacheVersion('posts');
    }
}
