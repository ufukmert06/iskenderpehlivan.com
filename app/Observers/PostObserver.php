<?php

namespace App\Observers;

use App\Models\Post;
use App\Traits\CachesApiResponses;

class PostObserver
{
    use CachesApiResponses;

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->clearCache();
    }

    /**
     * Clear relevant caches when post changes
     */
    protected function clearCache(): void
    {
        // Increment cache versions for posts, categories, and tags
        // This invalidates all cached responses related to posts
        self::incrementCacheVersion('posts');
        self::incrementCacheVersion('categories'); // Because category listings include post counts
        self::incrementCacheVersion('tags'); // Because tag listings include post counts
    }
}
