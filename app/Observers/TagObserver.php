<?php

namespace App\Observers;

use App\Models\Tag;
use App\Traits\CachesApiResponses;

class TagObserver
{
    use CachesApiResponses;

    /**
     * Handle the Tag "created" event.
     */
    public function created(Tag $tag): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Tag "updated" event.
     */
    public function updated(Tag $tag): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Tag "deleted" event.
     */
    public function deleted(Tag $tag): void
    {
        $this->clearCache();
    }

    /**
     * Clear relevant caches when tag changes
     */
    protected function clearCache(): void
    {
        // Increment cache versions for tags and posts
        // Posts cache includes tag relationships
        self::incrementCacheVersion('tags');
        self::incrementCacheVersion('posts');
    }
}
